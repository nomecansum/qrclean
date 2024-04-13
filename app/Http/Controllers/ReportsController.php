<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Spipu\Html2Pdf\Html2Pdf;
use DB;
use Auth;
use File;
use Excel;
use App\Exports\ExportExcel;
use Illuminate\Support\Facades\Storage;
use Log;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Models\clientes;
use App\Models\users;
use App\Models\informes_programados;

class ReportsController extends Controller
{
    /////////////FUNCIONES AUXILIARES/////////////////////
    protected function enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero = null, $plantilla = null, $datos_informe = array()) {

        if(!is_array($r->destinatarios))
            $destinatarios = array($r->destinatarios);
        else $destinatarios = $r->destinatarios;

        //hacemos merge de todos los datos
        $datos_informe = array_merge($datos_informe, ['usuario' => $usuario->nom_usuario, 'str_informe' => $prepend.' '.$nombre_informe, 'r' => $r]);
        log::debug("Enviando por mail informe : ".$plantilla . " a " . json_encode($destinatarios));
        foreach ($destinatarios as $recipient)
        {
            //Log::info("Email para " . $recipient);
            $datos_mail=array('prepend' => $prepend, 'r' => $r, 'fichero' => $fichero, 'nombre_informe' => $nombre_informe, 'plantilla' => $plantilla, 'datos_informe' => $datos_informe, 'recipient' => $recipient);
            try{
                $resp = \Mail::send(empty($plantilla) ? 'emails.mail_informe_programado' : $plantilla, $datos_mail, function ($m) use ($prepend, $r, $fichero, $nombre_informe, $recipient) {
                    $m->from(config('mail.from.address'), config('app.name'));
                    if(config('app.env')=='local' || config('app.env')=='qa'){//Para que en desarrollo solo me mande los mail a mi
                        Log::debug('Capturado email para '.$recipient);
                        $m->to('nomecansum@gmail.com')->subject($prepend.' '.$nombre_informe);
                    } else {
                        $m->to($recipient);
                    }
                    $m->subject($prepend.' '.$nombre_informe);
                    if(!empty($fichero)) //adjuntamos si existe
                        $m->attach($fichero);
                });
                log::notice("Mail enviado a " . $recipient); 
            } catch(\Exception $e){
                Log::error('Error enviando email '.$e->getMessage());
            }
            
        }
        if(!empty($fichero)) //borramos si existe
            file::delete($fichero);
    }
    function rand_float($st_num=0,$end_num=1,$mul=1000000)
    {
        if ($st_num>$end_num) return false;
        return mt_rand($st_num*$mul,$end_num*$mul)/$mul;
    }
    
    ///////////////INFORME DE PUESTOS POR USUARIO /////////////////
    public function users_index(){
        return view('reports.users.index');
    }

    public function users(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        log::debug("Solicitado informe de asignacion de usuarios: ".json_encode($r->all()));
        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        $informe=DB::table('puestos')
        ->select('puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.des_edificio','plantas.des_planta','clientes.nom_cliente','users.name','users.img_usuario','clientes.id_cliente','puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo','log_cambios_estado.id_user','log_cambios_estado.id_estado','log_cambios_estado.fecha as fecha_log')
        ->selectraw("date(log_cambios_estado.fecha) as fecha")
        ->join('edificios','puestos.id_edificio','edificios.id_edificio')
        ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
        ->join('plantas','puestos.id_planta','plantas.id_planta')
        ->join('clientes','puestos.id_cliente','clientes.id_cliente')
        ->join('log_cambios_estado','puestos.id_puesto','log_cambios_estado.id_puesto')
        ->join('users','log_cambios_estado.id_user','users.id')
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.id_cliente',clientes());
            }
        })
        ->where(function($q) use($r){
            if ($r->cliente) {
                $q->WhereIn('puestos.id_cliente',$r->cliente);
            }
        })
        ->where(function($q) use($r){
            if ($r->edificio) {
                $q->WhereIn('puestos.id_edificio',$r->edificio);
            }
        })
        ->where(function($q) use($r){
            if ($r->planta) {
                $q->whereIn('puestos.id_planta',$r->planta);
            }
        })
        ->where(function($q) use($r){
            if ($r->puesto) {
                $q->whereIn('puestos.id_puesto',$r->puesto);
            }
        })
        ->where(function($q) use($r){
            if ($r->tipo) {
                $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
            }
        })
        ->where(function($q) use($r){
            if ($r->user) {
                $q->whereIn('log_cambios_estado.id_user',$r->user);
            }
        })
        ->where(function($q) use($r){
            if ($r->id_departamento) {
                $q->whereIn('users.id_departamento',$r->id_departamento);
            }
        })
        ->when($r->cod_colectivo, function($q) use($r){
            $q->whereRaw('users.id in (select id_usuario from colectivos_usuarios where cod_colectivo in ('.implode(",",$r->cod_colectivo).'))');
        })
        ->where(function($q) use($r){
            if ($r->tags) {
                if($r->andor){//Busqueda con AND
                    $puestos_tags=DB::table('tags_puestos')
                        ->select('id_puesto')
                        ->wherein('id_tag',$r->tags)
                        ->groupby('id_puesto')
                        ->havingRaw('count(id_tag)='.count($r->tags))
                        ->pluck('id_puesto')
                        ->toarray();
                    $q->whereIn('puestos.id_puesto',$puestos_tags);
                } else { //Busqueda con OR
                    $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                    $q->whereIn('puestos.id_puesto',$puestos_tags); 
                }
            }
        })
        ->where(function($q){
            if (isSupervisor(Auth::user()->id)) {
                $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                $q->wherein('puestos.id_puesto',$puestos_usuario);
            }
        })
        ->wherein('log_cambios_estado.id_estado',[1,2])
        ->orderby('users.name')
        ->orderby('log_cambios_estado.fecha')
        ->wherebetween('log_cambios_estado.fecha',[$f1,$f2])
        ->get();


        $lista_puestos=$informe->pluck('id_puesto')->unique();

        $reservas=DB::table('reservas')
            ->wherein('id_puesto',$lista_puestos)
            ->wherebetween('fec_reserva',[$f1,$f2])
            ->get();

        $incidencias=DB::table('incidencias')
            ->wherein('id_puesto',$lista_puestos)
            ->wherebetween('fec_apertura',[$f1,$f2])
            ->get();


        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe Actividad de usuarios";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
        $view='reports.users.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe, "reservas" => $reservas, "incidencias" => $incidencias, 'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','reservas','incidencias','r','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','reservas','incidencias','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
						$this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
				libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('informe','reservas','incidencias','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','r','reservas','incidencias','executionTime')),$filename);
                }
            break;
        }
    }

    ///////////////INFORME DE USO DE PUESTOS /////////////////
    public function puestos_index(){
        return view('reports.puestos.index');
    }

    public function puestos(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        log::debug("Solicitado informe de estado de puestos: ".json_encode($r->all()));
        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        $informe=DB::table('puestos')
        ->select('puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','clientes.nom_cliente','clientes.id_cliente','puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
        ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
        ->join('clientes','puestos.id_cliente','clientes.id_cliente')
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.id_cliente',clientes());
            }
        })
        ->where(function($q) use($r){
            if ($r->cliente) {
                $q->WhereIn('puestos.id_cliente',$r->cliente);
            }
        })
        ->where(function($q) use($r){
            if ($r->edificio) {
                $q->WhereIn('puestos.id_edificio',$r->edificio);
            }
        })
        ->where(function($q) use($r){
            if ($r->planta) {
                $q->whereIn('puestos.id_planta',$r->planta);
            }
        })
        ->where(function($q) use($r){
            if ($r->puesto) {
                $q->whereIn('puestos.id_puesto',$r->puesto);
            }
        })
        ->where(function($q) use($r){
            if ($r->tipo) {
                $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
            }
        })
        ->where(function($q) use($r){
            if ($r->tags) {
                if($r->andor){//Busqueda con AND
                    $puestos_tags=DB::table('tags_puestos')
                        ->select('id_puesto')
                        ->wherein('id_tag',$r->tags)
                        ->groupby('id_puesto')
                        ->havingRaw('count(id_tag)='.count($r->tags))
                        ->pluck('id_puesto')
                        ->toarray();
                    $q->whereIn('puestos.id_puesto',$puestos_tags);
                } else { //Busqueda con OR
                    $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                    $q->whereIn('puestos.id_puesto',$puestos_tags);
                }
            }
        })
        ->where(function($q){
            if (isSupervisor(Auth::user()->id)) {
                $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                $q->wherein('puestos.id_puesto',$puestos_usuario);
            }
        })
        ->where('puestos.id_puesto','<>',0)
        ->get();

        $lista_puestos=$informe->pluck('id_puesto')->unique();
        $usos=DB::table('log_cambios_estado')
            ->join('puestos','log_cambios_estado.id_puesto','puestos.id_puesto')
            ->select('puestos.id_puesto','id_cliente','log_cambios_estado.id_estado')
            ->selectraw('count(log_cambios_estado.id_puesto) as cuenta')
            ->wherein('puestos.id_puesto',$lista_puestos)
            ->wherebetween('log_cambios_estado.fecha',[$f1,$f2])
            ->groupby('puestos.id_puesto','id_cliente','log_cambios_estado.id_estado')
            ->get();
        foreach($informe as $i){
            $i->usado=$usos->where('id_estado',2)->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
            $i->disponible=$usos->where('id_estado',1)->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
            $i->limpieza=$usos->where('id_estado',3)->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
            $i->cambios=$usos->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
        }

        $reservas=DB::table('reservas')
            ->select('id_puesto','id_cliente')
            ->selectraw('count(id_puesto) as cuenta')
            ->wherein('id_puesto',$lista_puestos)
            ->wherebetween('fec_reserva',[$f1,$f2])
            ->groupby('id_puesto','id_cliente')
            ->get();

        $reservas_usadas=DB::table('reservas')
            ->select('id_puesto','id_cliente')
            ->selectraw('count(id_puesto) as cuenta')
            ->wherein('id_puesto',$lista_puestos)
            ->wherebetween('fec_reserva',[$f1,$f2])
            ->where('fec_utilizada','<>',null)
            ->groupby('id_puesto','id_cliente')
            ->get();

        $reservas_anuladas=DB::table('reservas')
            ->select('id_puesto','id_cliente')
            ->selectraw('count(id_puesto) as cuenta')
            ->wherein('id_puesto',$lista_puestos)
            ->wherebetween('fec_reserva',[$f1,$f2])
            ->where('mca_anulada','S')
            ->groupby('id_puesto','id_cliente')
            ->get();
        foreach($informe as $i){
            $i->reservas=$reservas->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
            $i->reservas_usadas=$reservas_usadas->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
            $i->reservas_anuladas=$reservas_anuladas->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
        }

        $incidencias_abiertas=DB::table('incidencias')
            ->select('id_puesto','id_cliente')
            ->selectraw('count(id_incidencia) as cuenta')
            ->wherein('id_puesto',$lista_puestos)
            ->wherebetween('fec_apertura',[$f1,$f2])
            ->groupby('id_puesto','id_cliente')
            ->get();
        
        $incidencias_cerradas=DB::table('incidencias')
            ->select('id_puesto','id_cliente')
            ->selectraw('count(id_incidencia) as cuenta')
            ->wherein('id_puesto',$lista_puestos)
            ->wherebetween('fec_apertura',[$f1,$f2])
            ->where('fec_cierre','<>',null)
            ->groupby('id_puesto','id_cliente')
            ->get();
        foreach($informe as $i){
            $i->incidencias_abiertas=$incidencias_abiertas->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
            $i->incidencias_cerradas=$incidencias_cerradas->where('id_puesto',$i->id_puesto)->first()->cuenta??null;
        }

        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe de uso de puestos";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
        $view='reports.puestos.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe, "r" => $r,'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','r','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
						$this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
				libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('informe','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','r','executionTime')),$filename);
                }
            break;
        }
    }

        ///////////////INFORME DE PUESTOS POR TIPO /////////////////
        public function puestosxtipo_index(){
            return view('reports.puestos_por_tipo.index');
        }
    
        public function puestosxtipo(Request $r){
            
            //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
            if(isset($r->cod_usuario))
                Auth::loginUsingId($r->cod_usuario);
            $f = explode(' - ',$r->fechas);
            $f1 = adaptar_fecha($f[0]);
            $f2 = adaptar_fecha($f[1]);
    
            ///////////////////////////
            ///CONTENIDO DEL INFORME///
            ///////////////////////////
            $informe=DB::table('puestos')
            ->select('clientes.nom_cliente','clientes.id_cliente','puestos_tipos.id_tipo_puesto','puestos_tipos.des_tipo_puesto','puestos_tipos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo','edificios.des_edificio')
            ->selectraw('count(puestos.id_puesto) as total_puestos')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('clientes.id_cliente',clientes());
                }
            })
            ->where(function($q) use($r){
                if ($r->cliente) {
                    $q->WhereIn('puestos.id_cliente',$r->cliente);
                }
            })
            ->where(function($q) use($r){
                if ($r->edificio) {
                    $q->WhereIn('puestos.id_edificio',$r->edificio);
                }
            })
            ->where(function($q) use($r){
                if ($r->planta) {
                    $q->whereIn('puestos.id_planta',$r->planta);
                }
            })
            ->where(function($q) use($r){
                if ($r->puesto) {
                    $q->whereIn('puestos.id_puesto',$r->puesto);
                }
            })
            ->where(function($q) use($r){
                if ($r->tipo) {
                    $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
                }
            })
            ->where(function($q) use($r){
                if ($r->tags) {
                    if($r->andor){//Busqueda con AND
                        $puestos_tags=DB::table('tags_puestos')
                            ->select('id_puesto')
                            ->wherein('id_tag',$r->tags)
                            ->groupby('id_puesto')
                            ->havingRaw('count(id_tag)='.count($r->tags))
                            ->pluck('id_puesto')
                            ->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    } else { //Busqueda con OR
                        $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    }
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->groupby('clientes.nom_cliente','clientes.id_cliente','puestos_tipos.des_tipo_puesto','puestos_tipos.val_color','puestos_tipos.val_icono','puestos_tipos.val_color','edificios.des_edificio','puestos_tipos.id_tipo_puesto')
            ->get();
    
    
            $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            ///////////////////////////////////////////////////
            ///////////SALIDA DEL INFORME/////////////////////
            //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
            //dd($r->all());
            $nombre_informe="Informe de puestos por tipo";
            $cliente=clientes::find($r->id_cliente);
            $rango_safe=str_replace(" - ","_",$r->fechas);
            $rango_safe=str_replace("/","",$rango_safe);
            $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
            $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
            $view='reports.puestos_por_tipo.filter';
    
    
            switch($r->output){
                case "pantalla":
                    if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                        $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe, "r" => $r,'executionTime' => $executionTime));
                    } else {  //Navegacion
                        return view($view,compact('informe','r','executionTime'))->render();
                    }
    
                break;
    
                case "pdf":
                    $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                    $pdf = PDF::loadView($view,compact('informe','r','executionTime'));
                    $pdf->setPaper('legal', $orientation);
                    $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                    $fichero = storage_path() . "/exports/" . $filename;
    
                    if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                        try{
                            $pdf->save($fichero);
                            $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                        } catch(\Exception $e){
                            Log::error('Error generando PDF '.$e->getMessage());
                        }
    
                    } else {  //Navegacion
                        try{
                            return $pdf->download($filename);
                        } catch(\Exception $e){
                            Log::error('Error generando PDF '.$e->getMessage());
                            flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                            return redirect()->back()->withInput();
                        }
                    }
    
                break;
    
                case "excel":
                    $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                    $fichero = storage_path()."/exports/".$filename;
                    libxml_use_internal_errors(true); //para quitar los errores de libreria
                    if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                        Excel::store(new ExportExcel($view, compact('informe','r','executionTime')),$filename,'exports');
                        $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } else {  //Navegacion
                        return Excel::download(new ExportExcel($view,compact('informe','r','executionTime')),$filename);
                    }
                break;
            }
        }

    ///////////////INFORME DE RESERVAS CANCELADAS /////////////////
    public function canceladas_index(){
        return view('reports.canceladas.index');
    }

    public function canceladas(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);
        
        log::debug("Solicitado informe de reservas canceladas: ".json_encode($r->all()));
        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        $informe=DB::table('reservas')
        ->select('puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','clientes.nom_cliente','clientes.id_cliente','puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo','users.name','reservas.fec_reserva','reservas.fec_fin_reserva','reservas.fec_utilizada')
        ->join('puestos','puestos.id_puesto','reservas.id_puesto')
        ->join('users','users.id','reservas.id_usuario')
        ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
        ->join('clientes','puestos.id_cliente','clientes.id_cliente')
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.id_cliente',clientes());
            }
        })
        ->where(function($q) use($r){
            if ($r->cliente) {
                $q->WhereIn('puestos.id_cliente',$r->cliente);
            }
        })
        ->where(function($q) use($r){
            if ($r->edificio) {
                $q->WhereIn('puestos.id_edificio',$r->edificio);
            }
        })
        ->where(function($q) use($r){
            if ($r->planta) {
                $q->whereIn('puestos.id_planta',$r->planta);
            }
        })
        ->where(function($q) use($r){
            if ($r->puesto) {
                $q->whereIn('puestos.id_puesto',$r->puesto);
            }
        })
        ->where(function($q) use($r){
            if ($r->tipo) {
                $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
            }
        })
        ->where(function($q) use($r){
            if ($r->tags) {
                if($r->andor){//Busqueda con AND
                    $puestos_tags=DB::table('tags_puestos')
                        ->select('id_puesto')
                        ->wherein('id_tag',$r->tags)
                        ->groupby('id_puesto')
                        ->havingRaw('count(id_tag)='.count($r->tags))
                        ->pluck('id_puesto')
                        ->toarray();
                    $q->whereIn('puestos.id_puesto',$puestos_tags);
                } else { //Busqueda con OR
                    $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                    $q->whereIn('puestos.id_puesto',$puestos_tags); 
                }
            }
        })
        ->where(function($q){
            if (isSupervisor(Auth::user()->id)) {
                $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                $q->wherein('puestos.id_puesto',$puestos_usuario);
            }
        })
        ->where('reservas.mca_anulada','S')
        ->wherebetween('reservas.fec_reserva',[$f1,$f2])
        ->orderby('reservas.fec_reserva')
        ->orderby('reservas.id_usuario')
        ->get();


        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe de reservas canceladas";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
        $view='reports.canceladas.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe, "r" => $r,'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','r','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
                        $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
                libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('informe','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','r','executionTime')),$filename);
                }
            break;
        }
    }

     ///////////////INFORME DE FERIAS /////////////////
     public function ferias_index(){
        return view('reports.ferias.index');
    }

    //////////////////////////////INFORME DE ASISTENCIA Y REGISTRO DE FERIAS///////////////////////////////
    public function ferias(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);
        log::debug("Solicitado informe de ferias: ".json_encode($r->all()));
        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        $informe=DB::table('contactos_producto')
        ->select('contactos_producto.*','contactos.*','ferias_marcas.*','clientes.nom_cliente','clientes.img_logo','contactos_producto.fec_audit as fecha_contacto','users.name')
        ->join('contactos','contactos.id_contacto','contactos_producto.id_contacto')
        ->leftjoin('clientes','clientes.id_cliente','contactos.id_cliente')
        ->join('ferias_marcas','ferias_marcas.id_marca','contactos_producto.id_producto')
        ->leftjoin('users','users.id','contactos_producto.id_usuario_com')
        ->where(function($q) use($r){
            if ($r->mark) {
                $q->whereIn('contactos_producto.id_producto',$r->mark);
            }
        })
        ->wherebetween('contactos_producto.fec_audit',[$f1,$f2])
        ->get();


        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe de asistencia a eventos";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
        $view='reports.ferias.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe, "r" => $r,'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','r','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
                        $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
                libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('informe','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','r','executionTime')),$filename);
                }
            break;
        }
    }

    ////////////////////////////////INFORME DE USO DE ESPACIOS (MAPAS DE CALOR)////////////////////////////

    public function heatmap_index(){
        return view('reports.heatmap.index');
    }

    public function heatmap(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        log::debug("Solicitado informe de heatmap: ".json_encode($r->all()));
        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        if($r->mostrar=="R"){ //RESERVAS
            $informe=DB::table('puestos')
            ->select('puestos.id_puesto','puestos.cod_puesto','puestos.offset_top','puestos.offset_left','puestos.id_planta','edificios.id_edificio','edificios.des_edificio')
            ->selectraw("count(reservas.id_reserva) as cuenta")
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->join('reservas','puestos.id_puesto','reservas.id_puesto')
        
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('clientes.id_cliente',clientes());
                }
            })
            ->where(function($q) use($r){
                if ($r->cliente) {
                    $q->WhereIn('puestos.id_cliente',$r->cliente);
                }
            })
            ->where(function($q) use($r){
                if ($r->edificio) {
                    $q->WhereIn('puestos.id_edificio',$r->edificio);
                }
            })
            ->where(function($q) use($r){
                if ($r->planta) {
                    $q->whereIn('puestos.id_planta',$r->planta);
                }
            })
            ->where(function($q) use($r){
                if ($r->puesto) {
                    $q->whereIn('puestos.id_puesto',$r->puesto);
                }
            })
            ->where(function($q) use($r){
                if ($r->tipo) {
                    $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
                }
            })
            ->where(function($q) use($r){
                if ($r->tags) {
                    if($r->andor){//Busqueda con AND
                        $puestos_tags=DB::table('tags_puestos')
                            ->select('id_puesto')
                            ->wherein('id_tag',$r->tags)
                            ->groupby('id_puesto')
                            ->havingRaw('count(id_tag)='.count($r->tags))
                            ->pluck('id_puesto')
                            ->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    } else { //Busqueda con OR
                        $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags); 
                    }
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->groupby(['puestos.id_puesto','puestos.cod_puesto','puestos.offset_top','puestos.offset_left','puestos.id_planta','edificios.id_edificio','edificios.des_edificio'])
            ->orderby('puestos.id_puesto')
            ->wherebetween('reservas.fec_reserva',[$f1,$f2])
            ->get();
        } else{ //CHECKINS
            $informe=DB::table('puestos')
                ->select('puestos.id_puesto','puestos.cod_puesto','puestos.offset_top','puestos.offset_left','puestos.id_planta','edificios.id_edificio','edificios.des_edificio')
                ->selectraw("count(log_cambios_estado.id_log) as cuenta")
                ->join('edificios','puestos.id_edificio','edificios.id_edificio')
                ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
                ->join('plantas','puestos.id_planta','plantas.id_planta')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->join('log_cambios_estado','puestos.id_puesto','log_cambios_estado.id_puesto')
            
                ->where(function($q){
                    if (!isAdmin()){
                        $q->WhereIn('clientes.id_cliente',clientes());
                    }
                })
                ->where(function($q) use($r){
                    if ($r->cliente) {
                        $q->WhereIn('puestos.id_cliente',$r->cliente);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->edificio) {
                        $q->WhereIn('puestos.id_edificio',$r->edificio);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->planta) {
                        $q->whereIn('puestos.id_planta',$r->planta);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->puesto) {
                        $q->whereIn('puestos.id_puesto',$r->puesto);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->tipo) {
                        $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->tags) {
                        if($r->andor){//Busqueda con AND
                            $puestos_tags=DB::table('tags_puestos')
                                ->select('id_puesto')
                                ->wherein('id_tag',$r->tags)
                                ->groupby('id_puesto')
                                ->havingRaw('count(id_tag)='.count($r->tags))
                                ->pluck('id_puesto')
                                ->toarray();
                            $q->whereIn('puestos.id_puesto',$puestos_tags);
                        } else { //Busqueda con OR
                            $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                            $q->whereIn('puestos.id_puesto',$puestos_tags); 
                        }
                    }
                })
                ->where(function($q){
                    if (isSupervisor(Auth::user()->id)) {
                        $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                        $q->wherein('puestos.id_puesto',$puestos_usuario);
                    }
                })
                ->groupby(['puestos.id_puesto','puestos.cod_puesto','puestos.offset_top','puestos.offset_left','puestos.id_planta','edificios.id_edificio','edificios.des_edificio'])
                ->orderby('puestos.id_puesto')
                ->wherebetween('log_cambios_estado.fecha',[$f1,$f2])
                ->get();
        }
        


        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe de uso de espacios";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;
        $view='reports.heatmap.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe, "r" => $r,'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','r','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
                        $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
                libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('informe','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','r','executionTime')),$filename);
                }
            break;
        }
    }

    ///////////////INFORME DE TRABAJOS PROGRAMADOS /////////////////
    public function trabajos_index(){
        return view('reports.trabajos.index');
    }

    public function trabajos(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);
        log::debug("Solicitado informe de trabajos: ".json_encode($r->all()));
        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        $planes = DB::table('trabajos_planes')
            ->join('clientes','trabajos_planes.id_cliente','clientes.id_cliente')
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('clientes.id_cliente',clientes());
                }
            })
            ->where(function($q) use($r){
                if ($r->id_plan) {
                    $q->WhereIn('trabajos_planes.id_plan',$r->id_plan);
                }
            })
            ->get();

        $detalle=DB::table('trabajos_planes_detalle')
            ->where(function($q) use($r){
                if ($r->id_plan) {
                    $q->WhereIn('trabajos_planes_detalle.id_plan',$r->id_plan);
                }
            })
            ->get();

        $tareas = DB::table('trabajos')
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('trabajos.id_cliente',clientes());
                }
            })
            ->wherein('id_trabajo',$detalle->pluck('id_trabajo')->unique()->toarray())
            ->get();
        $grupos=DB::table('grupos_trabajos')
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('grupos_trabajos.id_cliente',clientes());
                }
            })
            ->wherein('id_grupo',$detalle->pluck('id_grupo_trabajo')->unique()->toarray())
            ->get();

        $trabajos= DB::table('trabajos')
            ->join('trabajos_tipos', 'trabajos_tipos.id_tipo_trabajo', 'trabajos.id_tipo_trabajo')
            ->join('trabajos_grupos', 'trabajos_grupos.id_trabajo', 'trabajos.id_trabajo')
            ->wherein('trabajos.id_trabajo',$detalle->pluck('id_trabajo')->unique()->toarray())
            ->where('trabajos.id_cliente',Auth::user()->id_cliente)
            ->orderby('num_orden')
            ->get();
        $contratas =DB::table('contratas')
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('contratas.id_cliente',clientes());
                }
            })
            ->wherein('id_contrata',$detalle->pluck('id_contrata')->unique()->toarray())
            ->get();
        $operarios =DB::table('contratas_operarios')
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('contratas_operarios.id_cliente',clientes());
                }
            })
            ->wherein('id_contrata',$detalle->pluck('id_contrata')->unique()->toarray())
            ->get();
        $plantas=DB::table('plantas')
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('plantas.id_cliente',clientes());
                }
            })
            ->wherein('id_planta',$detalle->pluck('id_planta')->unique()->toarray())
            ->get();
    
        $zonas = DB::table('plantas_zonas')
            ->select('plantas_zonas.*','plantas.des_planta')
            ->join('plantas', 'plantas_zonas.id_planta', 'plantas.id_planta')
            ->wherein('key_id',$detalle->pluck('id_zona')->unique()->toarray())
            ->get();

        $programaciones=DB::Table('trabajos_programacion')
            ->select('trabajos_programacion.*')
            ->selectraw("date(fec_programada) as fecha_corta")
            ->wherein('id_plan',$planes->pluck('id_plan')->unique()->toarray())
            ->wherebetween('trabajos_programacion.fec_programada',[Carbon::parse($f1),Carbon::parse($f2)])
            ->get();


        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe trabajos planificados";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
        $view='reports.trabajos.filter';

        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("dato" => $dato, "detalle" => $detalle, "tareas" => $tareas, "grupos" => $grupos, "trabajos" => $trabajos, "contratas" => $contratas, "operarios" => $operarios, "plantas" => $plantas, "zonas" => $zonas, "programaciones" => $programaciones, 'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('planes','detalle','tareas','grupos','trabajos','contratas','operarios','plantas','zonas','programaciones','r','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('planes','detalle','tareas','grupos','trabajos','contratas','operarios','plantas','zonas','programaciones','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
                        $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
                libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('planes','detalle','tareas','grupos','trabajos','contratas','operarios','plantas','zonas','programaciones','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('planes','detalle','tareas','grupos','trabajos','contratas','operarios','plantas','zonas','programaciones','r','executionTime')),$filename);
                }
            break;
        }
    }

    ///////////////INFORME DE ESTADO DE USUARIOS /////////////////
    public function estado_usu_index(){
        return view('reports.estado_usuarios.index');
    }

    public function estado_usu(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);

        log::debug("Solicitado informe de stado de usuarios: ".json_encode($r->all()));
        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        $informe=DB::table('users')
        ->select('users.*','edificios.des_edificio','clientes.nom_cliente','clientes.id_cliente')
        ->join('edificios','users.id_edificio','edificios.id_edificio')
        ->join('clientes','users.id_cliente','clientes.id_cliente')
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.id_cliente',clientes());
            }
        })
        ->where(function($q) use($r){
            if ($r->cliente) {
                $q->WhereIn('users.id_cliente',$r->cliente);
            }
        })
        ->where(function($q) use($r){
            if ($r->edificio) {
                $q->WhereIn('users.id_edificio',$r->edificio);
            }
        })
        ->where(function($q) use($r){
            if ($r->planta) {
                $users_filtro=DB::table('plantas_usuario')
                    ->select('id_usuario')
                    ->wherein('id_planta',$r->planta)
                    ->pluck('id_usuario');
                $q->whereIn('users.id',$users_filtro);
            }
        })
        ->where(function($q) use($r){
            if ($r->id_turno) {
                $users_filtro=DB::table('turnos_usuarios')
                    ->select('id_usuario')
                    ->wherein('id_turno',$r->id_turno)
                    ->pluck('id_usuario');
                $q->whereIn('users.id',$users_filtro);
            }
        })
        ->where(function($q) use($r){
            if ($r->planta) {
                $users_filtro=DB::table('plantas_usuario')
                    ->select('id_usuario')
                    ->wherein('id_planta',$r->planta)
                    ->pluck('id_usuario');
                $q->whereIn('users.id',$users_filtro);
            }
        })
        ->where(function($q) use($r){
            if ($r->user) {
                $q->whereIn('users.id',$r->user);
            }
        })
        ->when($r->user_id_list, function($q) use($r){
            try{     
                $r->user_id_list=str_replace(" ","",$r->user_id_list);
                // $r->user_list=str_replace("\r","",$r->user_list);
                // $r->user_list=str_replace("\n","",$r->user_list);
                $r->user_id_list=strtolower($r->user_id_list);
                if(strpos($r->user_id_list,","))
                    $arr_lista=explode(",",$r->user_id_list);
                else if(strpos($r->user_id_list,";"))
                    $arr_lista=explode(";",$r->user_id_list);
                else if(strpos($r->user_id_list,"|"))
                    $arr_lista=explode("|",$r->user_id_list);
                else if(strpos($r->user_id_list,"\r\n"))
                    $arr_lista=explode("\r\n",$r->user_id_list);
                $q->wherein('users.id',$arr_lista);
            } catch(Exception $e){
                
            }
        })
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','reservas.id_puesto','puestos.id_puesto')
            ->select('reservas.fec_reserva','puestos.cod_puesto','reservas.id_usuario','puestos.id_tipo_puesto')
            ->where(function($q) use($r){
                if ($r->fechas) {
                    $q->wheredate('reservas.fec_reserva',adaptar_fecha($r->fechas));
                }
            })
            ->where(function($q) use($r){
                if ($r->tipo) {
                    $q->wherein('puestos.id_tipo_puesto',$r->tipo);
                }
            })
            ->get();

        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe de estado de usuarios";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
        $view='reports.estado_usuarios.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe, "reservas" => $reservas, "r" => $r,'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','r','reservas','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','reservas','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
                        $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
                libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('informe','reservas','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','reservas','r','executionTime')),$filename);
                }
            break;
        }
    }

      ////////////////INFORME DE INCIDENCIAS ///////////////////////
      public function incidencias_index(){
        return view('reports.incidencias.index');
    }

    public function incidencias(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        log::debug("Solicitado informe de incidencias con parametros: ".json_encode($r->all()));
        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        if($r->or=='I'){
            $informe=DB::table('incidencias')
                ->select('incidencias.*','incidencias_tipos.*','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','edificios.*','plantas.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','users.name','users.email')
                ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
                ->selectraw("(select count(id_accion) from incidencias_acciones where incidencias_acciones.id_incidencia=incidencias.id_incidencia) as num_acciones")
                ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
                ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
                ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
                ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
                ->join('edificios','puestos.id_edificio','edificios.id_edificio')
                ->join('plantas','puestos.id_planta','plantas.id_planta')
                ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
                ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
                ->join('clientes','puestos.id_cliente','clientes.id_cliente')
                ->where(function($q){
                    $q->wherein('puestos.id_cliente',clientes());
                })
                ->where(function($q) use($r){
                    if ($r->cliente) {
                        $q->WhereIn('puestos.id_cliente',$r->cliente);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->edificio) {
                        $q->WhereIn('puestos.id_edificio',$r->edificio);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->planta) {
                        $q->whereIn('puestos.id_planta',$r->planta);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->tipo) {
                        $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->user) {
                        $q->whereIn('incidencias.id_usuario_apertura',$r->user);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->or) {
                    if($r->or=='I'){
                            $q->where('incidencias.id_puesto','<>',0);
                    } else {
                            $q->where('incidencias.id_puesto',0);
                    }
                    }
                })
                ->where(function($q) use($r){
                    if ($r->tags) {
                        if($r->andor){//Busqueda con AND
                            $puestos_tags=DB::table('tags_puestos')
                                ->select('id_puesto')
                                ->wherein('id_tag',$r->tags)
                                ->groupby('id_puesto')
                                ->havingRaw('count(id_tag)='.count($r->tags))
                                ->pluck('id_puesto')
                                ->toarray();
                            $q->whereIn('puestos.id_puesto',$puestos_tags);
                        } else { //Busqueda con OR
                            $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                            $q->whereIn('puestos.id_puesto',$puestos_tags); 
                        }
                    }
                })
                ->whereBetween('fec_apertura',[Carbon::parse($f1),Carbon::parse($f2)])
                ->where(function($q) use($r){
                    if($r->ac=='C'){
                        $q->wherenotnull('fec_cierre');
                    }
                    if($r->ac=='A'){
                        $q->wherenull('fec_cierre');
                    }
                })
                ->where(function($q) use($r){
                    if ($r->estado_inc) {
                        $q->whereIn('incidencias.id_estado',$r->estado_inc);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->procedencia) {
                        $q->whereIn('incidencias.val_procedencia',$r->procedencia);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->tipoinc) {
                        $q->whereIn('incidencias.id_tipo_incidencia',$r->tipoinc);
                    }
                })
                ->where(function($q) use($r){
                    if ($r->user) {
                        $q->whereIn('incidencias.id_usuario_apertura',$r->user);
                    }
                })
                ->orderby('fec_apertura','desc')
                ->get();
        } else { //Solicitudes, no llevan puesto
            $informe=DB::table('incidencias')
            ->select('incidencias.*','incidencias_tipos.*','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','users.name','users.email')
            ->selectraw("date_format(fec_apertura,'%Y-%m-%d') as fecha_corta")
            ->selectraw("(select count(id_accion) from incidencias_acciones where incidencias_acciones.id_incidencia=incidencias.id_incidencia) as num_acciones")
            ->leftjoin('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
            ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
            ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
            ->leftjoin('users','incidencias.id_usuario_apertura','users.id')
            ->join('clientes','incidencias.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->wherein('incidencias.id_cliente',clientes());
            })
            ->where(function($q) use($r){
                if ($r->cliente) {
                    $q->WhereIn('incidencias.id_cliente',$r->cliente);
                }
            })
            ->where(function($q) use($r){
                if ($r->user) {
                    $q->whereIn('incidencias.id_usuario_apertura',$r->user);
                }
            })
            ->whereBetween('fec_apertura',[Carbon::parse($f1),Carbon::parse($f2)])
            ->where(function($q) use($r){
                if($r->ac=='C'){
                    $q->wherenotnull('fec_cierre');
                }
                if($r->ac=='A'){
                    $q->wherenull('fec_cierre');
                }
            })
            ->where(function($q) use($r){
                if ($r->estado_inc) {
                    $q->whereIn('incidencias.id_estado',$r->estado_inc);
                }
            })
            ->where(function($q) use($r){
                if ($r->procedencia) {
                    $q->whereIn('incidencias.val_procedencia',$r->procedencia);
                }
            })
            ->where(function($q) use($r){
                if ($r->tipoinc) {
                    $q->whereIn('incidencias.id_tipo_incidencia',$r->tipoinc);
                }
            })
            ->where(function($q) use($r){
                if ($r->user) {
                    $q->whereIn('incidencias.id_usuario_apertura',$r->user);
                }
            })
            ->orderby('fec_apertura','desc')
            ->get();
        }

        


        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe de incidencias y solicitudes";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
        $view='reports.incidencias.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe,  'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','r','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
						$this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
				libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('informe','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','r','executionTime')),$filename);
                }
            break;
        }
    }

    public function reservas_slots_index(){
        return view('reports.reservas_slots.index');
    }

    public function reservas_slots(Request $r){
        
        //PARAMETROS DE ENTRADA COMUNES, USUARIO Y FECHAS
        if(isset($r->cod_usuario))
            Auth::loginUsingId($r->cod_usuario);
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        $informe=DB::table('puestos')
            ->select('puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','clientes.nom_cliente','clientes.id_cliente','puestos.id_edificio','puestos.id_tipo_puesto','puestos.id_estado')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->wherenotnull('puestos_tipos.slots_reserva')
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('clientes.id_cliente',clientes());
                }
            })
            ->where(function($q) use($r){
                if ($r->cliente) {
                    $q->WhereIn('puestos.id_cliente',$r->cliente);
                }
            })
            ->where(function($q) use($r){
                if ($r->edificio) {
                    $q->WhereIn('puestos.id_edificio',$r->edificio);
                }
            })
            ->where(function($q) use($r){
                if ($r->planta) {
                    $q->whereIn('puestos.id_planta',$r->planta);
                }
            })
            ->where(function($q) use($r){
                if ($r->puesto) {
                    $q->whereIn('puestos.id_puesto',$r->puesto);
                }
            })
            ->where(function($q) use($r){
                if ($r->tipo) {
                    $q->whereIn('puestos.id_tipo_puesto',$r->tipo);
                }
            })
            ->where(function($q) use($r){
                if ($r->tags) {
                    if($r->andor){//Busqueda con AND
                        $puestos_tags=DB::table('tags_puestos')
                            ->select('id_puesto')
                            ->wherein('id_tag',$r->tags)
                            ->groupby('id_puesto')
                            ->havingRaw('count(id_tag)='.count($r->tags))
                            ->pluck('id_puesto')
                            ->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    } else { //Busqueda con OR
                        $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    }
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->where('puestos.id_puesto','<>',0)
            ->get();

        
        $lista_puestos=$informe->pluck('id_puesto')->unique();

        $reservas=DB::table('reservas')
            ->select('puestos.id_puesto','puestos.id_cliente','fec_reserva','id_edificio','id_tipo_puesto')
            ->join('puestos','reservas.id_puesto','puestos.id_puesto')
            ->wherein('puestos.id_puesto',$lista_puestos)
            ->wherebetween('fec_reserva',[$f1,$f2])
            ->get();

        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        ///////////////////////////////////////////////////
        ///////////SALIDA DEL INFORME/////////////////////
        //Para añadir a los nomres de fichero y hacerlos un poco mas unicos
        //dd($r->all());
        $nombre_informe="Informe de reservas por slot de tiempo";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario)??Auth::user()->id;;
        $view='reports.reservas_slots.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe,"reservas" => $reservas, "r" => $r,'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','reservas','r','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','reservas','r','executionTime'));
                $pdf->setPaper('legal', $orientation);
                $filename = str_replace(' ', '_', $prepend . '_' . $nombre_informe . '.pdf');
                $fichero = storage_path() . "/exports/" . $filename;

                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    try{
                        $pdf->save($fichero);
                        $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                    }

                } else {  //Navegacion
                    try{
                        return $pdf->download($filename);
                    } catch(\Exception $e){
                        Log::error('Error generando PDF '.$e->getMessage());
                        flash("Error al solicitar el informe: afine los filtros para evitar grandes cargas de datos al navegador (".mensaje_excepcion($e) . ")")->error();  
                        return redirect()->back()->withInput();
                    }
                }

            break;

            case "excel":
                $filename = str_replace(' ', '_', $prepend.'_'.$nombre_informe.'.xlsx');
                $fichero = storage_path()."/exports/".$filename;
                libxml_use_internal_errors(true); //para quitar los errores de libreria
                if(isset($r->email_schedule) && $r->email_schedule == 1) { //Programado
                    Excel::store(new ExportExcel($view, compact('informe','reservas','r','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','reservas','r','executionTime')),$filename);
                }
            break;
        }
}

    //////////////////////INFORMES PROGRAMADOS ////////////////////////////////
    //Crear nuevo informe programado
    public function programar_informe(Request $r){
        $programado= new informes_programados();
        $programado->cod_usuario=Auth::user()->id_usuario;
        $programado->des_informe_programado=$r->des_informe_programado;
        $programado->dia_desde=$r->dias_desde;
        $programado->dia_hasta=$r->dias_hasta;
        $programado->fec_creacion=Carbon::now();
        $programado->fec_inicio=adaptar_fecha($r->val_fecha);
        $programado->fec_prox_ejecucion=adaptar_fecha($r->val_fecha);
        $programado->list_usuarios=$r->list_usuarios;
        $programado->url_informe=$r->url_orig;
        $programado->val_parametros=$r->request_orig;
        $programado->val_periodo=$r->fechas_prog;
        $programado->val_intervalo=$r->val_intervalo;
        $programado->cod_cliente=Auth::user()->id_cliente;
        $programado->cod_usuario=Auth::user()->id;
        $programado->controller=$r->controller;
        $programado->save();

        return [
            'title' => 'Programar informe',
            'message' => "Informe programado correctamente",
        ];
    }
    //Gestor
    public function informes_programados_index (){
        $informes=DB::table('informes_programados')
        ->join('clientes','clientes.id_cliente','informes_programados.cod_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('informes_programados.cod_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('informes_programados.cod_cliente',session('CL')['id_cliente']);
            }
        })
        ->where(function($q){
            if (Auth::user()->val_nivel_acceso == 1){
                $q->where('informes_programados.cod_usuario',Auth::user()->id);
            }
        })
        ->get();
        return view('reports.index_informes_programados',compact('informes'));
    }
    public function prog_report(Request $r){
        return view('reports.informes_programados');
    }
    public function delete_informe_programado($id){

        $inf = informes_programados::findOrFail($id);
        $inf->delete();
        savebitacora("Borrado de Informe programado [".$id."] ".$inf->des_informe_programado." completado con éxito", null);
		flash("Borrado de Informe programado [".$id."] ".$inf->des_informe_programado." completado con éxito")->success();
        return redirect()->back();

    }
    public function edit_informe_programado($id){
        $inf = informes_programados::where('cod_informe_programado', $id)->first();
        $edit=true;
        return view('resources.programacion_informe', compact('inf','edit'));
    }
    public function save_informe_programado(Request $r){
        try {
            $inf = informes_programados::where('cod_informe_programado', $r->cod_informe_programado)->first();
            $inf->des_informe_programado = $r->des_informe_programado;
            $inf->val_periodo = $r->fechas_prog;
            $inf->val_intervalo = $r->val_intervalo;
            $inf->fec_inicio = adaptar_fecha($r->val_fecha);
            $inf->list_usuarios = $r->list_usuarios;
            $inf->cod_usuario = Auth::user()->id;
            $inf->save();
            return [
                'title' => "Informes programados",
                'message' => "Programacion de informe " . "(" . $r->cod_informe_programado . ") " . $inf->des_informe_programado. " actualizada",
                'url' => url('/prog_report')
            ];
        } catch (\Exception $e) {
            return [
                'title' => "Informes programados",
                'error' => "ERROR: Ocurrio un error al actualizar el informe programado  ".$inf->des_informe_programado.": ".mensaje_excepcion($e),
                //'url' => url('employees')
            ];
        }

    }

        ///////////////INFORME DE USO DE PUESTOS /////////////////

}