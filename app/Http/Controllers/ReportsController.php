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
use App\Models\clientes;
use App\Models\users;

class ReportsController extends Controller
{
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
            $q->where('puestos.id_cliente',Auth::user()->id_cliente);
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
        $usuario = users::find($r->cod_usuario);
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

        ///////////////////////////
        ///CONTENIDO DEL INFORME///
        ///////////////////////////
        $informe=DB::table('puestos')
        ->select('puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','clientes.nom_cliente','clientes.id_cliente','puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
        ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
        ->join('clientes','puestos.id_cliente','clientes.id_cliente')
        ->where(function($q){
            $q->where('puestos.id_cliente',Auth::user()->id_cliente);
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
        ->get();

        $lista_puestos=$informe->pluck('id_puesto')->unique();

        $usos=DB::table('log_cambios_estado')
            ->wherein('id_puesto',$lista_puestos)
            ->wherebetween('log_cambios_estado.fecha',[$f1,$f2])
            ->get();

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
        $nombre_informe="Informe de uso de puestos";
        $cliente=clientes::find($r->id_cliente);
        $rango_safe=str_replace(" - ","_",$r->fechas);
        $rango_safe=str_replace("/","",$rango_safe);
        $prepend=$r->cod_cliente."_".$cliente->nom_cliente."_".$rango_safe."_";
        $usuario = users::find($r->cod_usuario);
        $view='reports.puestos.filter';


        switch($r->output){
            case "pantalla":
                if(isset($r->email_schedule) && $r->email_schedule == 1){ //Programado
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, null, $view, array("informe" => $informe, "r" => $r, "usos" => $usos, "reservas" => $reservas, "incidencias" => $incidencias,'executionTime' => $executionTime));
                } else {  //Navegacion
                    return view($view,compact('informe','usos','reservas','r','incidencias','executionTime'))->render();
                }

            break;

            case "pdf":
                $orientation = $r->orientation == 'h' ? 'landscape' : 'portrait';
                $pdf = PDF::loadView($view,compact('informe','usos','reservas','r','incidencias','executionTime'));
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
                    Excel::store(new ExportExcel($view, compact('informe','usos','reservas','r','incidencias','executionTime')),$filename,'exports');
                    $this->enviar_fichero_email($r, $nombre_informe, $usuario, $prepend, $fichero);
                } else {  //Navegacion
                    return Excel::download(new ExportExcel($view,compact('informe','usos','reservas','r','incidencias','executionTime')),$filename);
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
            $q->where('puestos.id_cliente',Auth::user()->id_cliente);
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
        $usuario = users::find($r->cod_usuario);
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
}