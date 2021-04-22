<?php

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\users;
use App\Models\config_clientes;
use App\Models\clientes;
use Jenssegers\Agent\Agent;

function stripAccents($str) {
    return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

function getProfilePic()
{
    $e = \DB::table('cug_empleados')->where('cod_empleado',Auth::user()->cod_empleado)->first();
    if ($e) {
        if ($e->img_empleado) {
            return url('uploads/employees/images',$e->img_empleado);
        }
    }
    return url('default.png');
}

function savebitacora($des_bitacora,$modulo=null,$seccion=null,$tipo='OK')
{
    if(isset(Auth::user()->name)){
        $user=Auth::user()->name;
    }

    \DB::table('bitacora')->insert([
        'accion' => substr($des_bitacora,0,5000),
        'id_usuario' =>Auth::user()->id??0,
        'id_modulo' => $modulo,
        'id_seccion' => $seccion,
        'status' => $tipo,
        'fecha' => Carbon::now()
    ]);
}

function clientes()
{
    try{
        if(fullAccess())
        {
            $clientes = DB::table('clientes')->select('id_cliente')->wherenull('fec_borrado')->pluck('id_cliente')->toarray();
            if(session('id_cliente')){
                $clientes=DB::table('clientes')->where('id_cliente',session('id_cliente'))->pluck('id_cliente')->toarray();
            }
        }
        else
        {
            $clientes = [];
            if(isset(Auth::user()->clientes)){
                foreach (explode(',',Auth::user()->clientes) as $key => $value) {
                    if ($value != "") {
                        $clientes[] = $value;
                    }
                }
            }
        }

        if(count($clientes)==0){
            return [Auth::user()->id_cliente];
        } else{
            return $clientes;
        }
    } catch(\Exception $e){
        return [0];
    }
}

function puede_ver_cliente($id){
    try{
        $cus=\DB::table('clientes')
        ->where('clientes.id_cliente',$id)
        ->whereNull('clientes.fec_borrado')
        ->where(function($q){
            if (!fullAccess()) {
                $q->Wherein('clientes.id_cliente',clientes());
            }
        })->exists();
        return $cus;
    } catch(\Exception $e){
        return false;
    }
}

function lista_clientes(){
    $clientes=DB::table('clientes')
    ->wherein('id_cliente',clientes())
    ->orderby('nom_cliente')
    ->get();
    return $clientes;
}

function isJson($string) {
    try{
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    } catch(\Exception $e){
        return false;
    }
    
}

function isxml($xmlstr){
    try{
        libxml_use_internal_errors(true);

        $doc = simplexml_load_string($xmlstr);
        $xml = explode("\n", $xmlstr);

        if (!$doc) {
            $errors = libxml_get_errors();

            // foreach ($errors as $error) {
            //     echo display_xml_error($error, $xml);
            // }

            libxml_clear_errors();
            return false;
        } else {
            return true;
        }
    } catch(\Exception $e){
        return false;
    }
}

//Añadir ceros por la izquierda\
function lz($num)
{
    return (strlen($num) < 2) ? "0{$num}" : $num;
}

//Devuelve el valor de cualquiera de los elementos del array de parametros de un comando para tareas/Eventos
function valor($parametros,$nombre){
    $salida=null;
    try{
        foreach($parametros as $param){
            if($param->name==$nombre){
                return $param->value;
            }
        }
    } catch(\Exception $e){

    }
    return $salida;
}

//Decodificar JSON mejorado
function decodeComplexJson($string) { # list from www.json.org: (\b backspace, \f formfeed)
    $string = preg_replace("/[\r\n]+/", "", $string); //Retornos de carro
    $string = preg_replace('/[ ]{2,}|[\t]/', '', trim($string));  //tabs
    $json = utf8_encode($string);
    $json = json_decode($json);
    return $json;
}

//Funcion para que las tareas programadas escriban su log
function log_tarea($mensaje,$id,$tipo='info'){
    DB::table('tareas_programadas_log')->insert([
        'txt_log'=>$mensaje,
        'cod_tarea'=>$id,
        'tip_mensaje'=>$tipo,
        'fec_log'=>Carbon::now()
    ]);
}

function fullAccess(){
    return isAdmin();
}

function isAdmin(){
    try{
        return Auth::User()->nivel_acceso == 200 ? true : false;
    } catch(\Exception $e){
        return false;
    }
}

function isSupervisor($id){
    try{
        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;
        $usuario=users::findorfail($id);

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->where('id_perfil',$usuario->cod_nivel)->first();
        return isset($supervisores_perfil)&&$supervisores_perfil->mca_read=="1"?true:false;
    } catch(\Exception $e){
        return false;
    }
}

function mensaje_excepcion($e){
    if(isAdmin()){
        return $e->getMessage().' {'.get_class($e).'}  ['.debug_backtrace()[1]['function'].']';
    } else {
        return substr($e->getMessage(),1,15);
    }
}

function isCustomerAdmin(){
    try{
        return Auth::User()->val_nivel_acceso >= 100 ? true : false;
    } catch(\Exception $e){
        return false;
    }
}

function formatmysqldate($date){
    return \Carbon\Carbon::createFromFormat('d/m/Y H:i', $date)->format('Y-m-d H:i');
}

function formatmysqldate2($date){
    return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
}

function formatspdate($date){
    return \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date)->format('d/m/Y H:i');
}

function isDesktop(){
    $agent = new \Jenssegers\Agent\Agent;
    return $agent->isDesktop();
}

function isMobile(){
    $agent = new \Jenssegers\Agent\Agent;
    return $agent->isMobile();
}

function decimal_to_time($dec)
{
    // start by converting to seconds
    $seconds = ($dec * 3600);
    // we're given hours, so let's get those the easy way
    $hours = floor($dec);
    // since we've "calculated" hours, let's remove them from the seconds variable
    $seconds -= $hours * 3600;
    // calculate minutes left
    $minutes = floor($seconds / 60);
    // remove those from seconds as well
    $seconds -= $minutes * 60;
    // return the time formatted HH:MM:SS
    if ($hours==0 && $minutes==0){
        return 0;
    } else {
        return lz($hours).":".lz($minutes);
    }
}

///Convertir fecha en español a mysql
function adaptar_fecha($d){
    if(!isset($d)){
        return null;
    }
    try{
        if (Carbon::createFromFormat('d/m/Y H:i:s', $d)!== false) {
            return Carbon::createFromFormat('d/m/Y H:i:s', $d)->format('Y-m-d H:i:s');
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::createFromFormat('d/m/Y H:i', $d)!== false) {
            return Carbon::createFromFormat('d/m/Y H:i', $d)->format('Y-m-d H:i');
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::createFromFormat('d/m/Y', $d)!== false) {
            return Carbon::createFromFormat('d/m/Y', $d)->format('Y-m-d');
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::createFromFormat('Y-m-d', $d)!== false) {
            return Carbon::createFromFormat('d/m/Y', $d)->format('Y-m-d');
        }
    } catch (\Exception $e){}
    try{
        if (Carbon::parse($d)!== false) {
            return Carbon::parse($d)->format('Y-m-d');
        }
    } catch (\Exception $e){}
    return  $d;
}

function random_readable_pwd($length=10){

    // the wordlist from which the password gets generated
    // (change them as you like)
    $words = 'dog,cat,sheep,sun,sky,red,ball,happy,ice,';
    $words .= 'green,blue,music,movies,radio,green,turbo,';
    $words .= 'mouse,computer,paper,water,fire,storm,chicken,';
    $words .= 'boot,freedom,white,nice,player,small,eyes,';
    $words .= 'path,kid,box,black,flower,ping,pong,smile,';
    $words .= 'coffee,colors,rainbow,plus,king,tv,ring';

    // Split by ",":
    $words = explode(',', $words);
    if (count($words) == 0){ die('Wordlist is empty!'); }

    // Add words while password is smaller than the given length
    $pwd = '';
    while (strlen($pwd) < $length){
        $r = mt_rand(0, count($words)-1);
        $pwd .= $words[$r];
    }

    // append a number at the end if length > 2 and
    // reduce the password size to $length
    $num = mt_rand(1, 99);
    if ($length > 2){
        $pwd = substr($pwd,0,$length-strlen($num)).$num;
    } else {
        $pwd = substr($pwd, 0, $length);
    }

    return $pwd;
}

//Rellena en el texto pasado los comodines indicados por los corchetes con su correspondiente valor de la lista de datos. Esto es para las notificaciones de evebntos
function comodines_texto($texto,$campos,$datos){
    preg_match_all("/\[([^\]]*)\]/", $texto, $matches);
    foreach($matches[0] as $match){
        foreach($campos as $campo){
            if($match==$campo->label){
                $nom_campo=str_replace("[","",$campo->label);
                $nom_campo=str_replace("]","",$nom_campo);
                if($match=="[fecha]")
                {
                    $texto=str_replace("[fecha]",Carbon::now()->format('d/m/Y'),$texto);
                } else if($match=="[hora]")
                {
                    $texto=str_replace("[hora]",Carbon::now()->format('H:i'),$texto);
                } else {
                    $texto=str_replace($match,$datos->$nom_campo,$texto);
                }
            }
        }
    }
    return $texto;
}

function notificar_usuario($user,$subject,$plantilla,$body,$metodo=1){
    try{
        switch ($metodo) {
            case 0:
                //No hacer nada
            break;
            case 1: //Mail
                $cliente=clientes::find($user->id_cliente);
                \Mail::send($plantilla, ['user' => $user,'body'=>$body,'cliente'=>$cliente], function ($m) use ($user,$subject) {
                    if(config('app.env')=='dev'){//Para que en desarrollo solo me mande los mail a mi
                        $m->to('nomecansum@gmail.com', $user->name)->subject($subject);
                    } else {
                        $m->to($user->email, $user->name)->subject($subject);
                    }
                    $m->from(config('mail.from.address'),config('mail.from.name'));
                });
                break;
            case 2:
            case 3:  //Notificacion push
                # code...
                break;
        }
    } catch(\Exception $e){
        dump($e);
        return $e->getMessage();
    }
    return true;
}

function tags($string, $encoding = 'UTF-8'){
    $string = trim(strip_tags(html_entity_decode(urldecode($string))));
    if(empty($string)){ return false; }

    $stopWords = array('a','ante', 'bajo', 'con', 'contra','de', 'desde', 'durante','en', 'entre','hacia', 'hasta', 'mediante', 'para', 'por', 'pro', 'segun','sin', 'sobre', 'tras', 'via','los', 'las', 'una', 'unos', 'unas', 'este', 'estos', 'ese',
    'esos', 'aquel', 'aquellos', 'esta', 'estas', 'esa', 'esas','aquella', 'aquellas', 'usted', 'nosotros', 'vosotros','ustedes', 'nos', 'les', 'nuestro', 'nuestra', 'vuestro','vuestra', 'mis', 'tus', 'sus', 'nuestros', 'nuestras',
   'vuestros', 'vuestras','esto', 'que','Planta', '/','-');
 
    $string = preg_replace('/\s\s+/i', '', $string); // replace whitespace
   
    $string = trim($string); // trim the string

    $string = preg_replace('/[^a-zA-Z0-9À-ÿ -]/', '', $string); // only take alphanumerical characters, but keep the spaces and dashes too…
    
    $string = mb_strtolower($string,$encoding); // make it lowercase

    $matchWords=preg_replace('/\b(' . implode('|', $stopWords) . ')\b/u', '', $string);
    
    return $matchWords;
}

function acronimo($nombre,$height=10){

    try{
        $words=tags($nombre);
        // $words = explode(" ", $nombre);
        // if(sizeof($words)==0){
        //     $words = explode(".", $nombre);
        // }
        $acronym = "";
        if(sizeof($words)<4){
            $corte=round(12/sizeof($words));
        } else {
            $corte=3;
        }
        if(sizeof($words)>1){
            foreach ($words as $w) {
                $acronym .= ucfirst(substr($w,0,$corte));
            }
        } else {
            $acronym=$nombre;
        }

        $acronym=substr($acronym,0,$height);
    } catch(\Exception $e){
        $acronym=substr($nombre,0,$height);
    }
    return $acronym;
}

function iniciales ($nombre,$cantidad){
    try{
        $nombre=stripAccents($nombre);
        $words = explode(" ", $nombre);
        $acronym = "";
        $i = 0;
        foreach ($words as $w) {
            $acronym .= $w[0];
            if (++$i == $cantidad) break;
        }
    } catch(\Exception $e){
        $acronym=substr($nombre,1,$cantidad);
    }
    return $acronym;
}

function icono_nombre($nombre,$height=50,$font=18){

    $padding=intdiv($height,11);
    $rand=Str::random(9);
    $acronym = iniciales($nombre,2);
    $top_letras=0;
    $left_letras=2;
    //return '<span class="round" id="'.$rand.'" style="text-transform: uppercase; background-color: '.App\Classes\RandomColor::one().'">'.$acronym.'</span>';
    return '<div class="round add-tooltip" id="'.$rand.'" style="line-height: 50px; padding: 0px; font-weight: bold; font-size: '.$font.'px; width: '.$height.'px;height: '.$height.'px; text-transform: uppercase; background-color: '.genColorCodeFromText($nombre).'" data-toggle="tooltip" data-placement="bottom" title="'.$nombre.'"><span style="position: relative; top: '.$top_letras.'%; left:'.$left_letras.'%;">'.$acronym.'</span></div>';
}

function randomcolor(){
    return App\Classes\RandomColor::one(['luminosity' => 'light']);
}

function imagen_usuario($user,$height=50){
    if(isset($user->img_usuario) && $user->img_usuario<>'')
        return '<img class="direct-chat-img" src="'.url("/img/users/",$user->img_usuario).'" height="'.$height.'px" alt="" onerror="this.remove()" class="b-all">';
    else
        return icono_nombre($user->name);
}

function genColorCodeFromText($text,$min_brightness=100,$spec=10)
{
    // Check inputs
    if(!is_int($min_brightness)) throw new Exception("$min_brightness is not an integer");
    if(!is_int($spec)) throw new Exception("$spec is not an integer");
    if($spec < 2 or $spec > 10) throw new Exception("$spec is out of range");
    if($min_brightness < 0 or $min_brightness > 255) throw new Exception("$min_brightness is out of range");


    $hash = md5($text);  //Gen hash of text
    $colors = array();
    for($i=0;$i<3;$i++)
        $colors[$i] = max(array(round(((hexdec(substr($hash,$spec*$i,$spec)))/hexdec(str_pad('',$spec,'F')))*255),$min_brightness)); //convert hash into 3 decimal values between 0 and 255

    if($min_brightness > 0)  //only check brightness requirements if min_brightness is about 100
        while( array_sum($colors)/3 < $min_brightness )  //loop until brightness is above or equal to min_brightness
            for($i=0;$i<3;$i++)
                $colors[$i] += 10;	//increase each color by 10

    $output = '';

    for($i=0;$i<3;$i++)
        $output .= str_pad(dechex($colors[$i]),2,0,STR_PAD_LEFT);  //convert each color to hex and append to output

    return '#'.$output;
}

function checkPermissions($secciones = [],$permisos = [])
{
    $a = 0;
    $b = 0;
    if (!$secciones) {
        return false;
    }
    if (!$permisos) {
        return false;
    }
    $encontrado=false;
    $mispermisos=Session::get('P');
    if(!isset($mispermisos)){
        return false;
    }
    foreach ($secciones as $key => $s) {
        foreach($mispermisos as $per){
            if($per->des_seccion===$s){
                $encontrado=true;
            }
        }
        if (!$encontrado){
            return false;
        }
        foreach ($permisos as $key => $p) {
            if ($p == "R") {$type = "mca_read";}
            if ($p == "W") {$type = "mca_write";}
            if ($p == "C") {$type = "mca_create";}
            if ($p == "D") {$type = "mca_delete";}
            foreach($mispermisos as $per){
                if($per->des_seccion==$s && $per->$type==1){
                    $b++;
                }
            }
        }
    }
    if ($encontrado && $b >= (count($permisos)*count($secciones))) {
        return true;
    }
    return false;
}

function beauty_fecha($date,$mostrar_hora=-1){
    setlocale(LC_TIME, App::getLocale());
    $hora="";
    if((strlen($date)>10 && $mostrar_hora==-1) || $mostrar_hora==1){
        $hora=Carbon::parse($date)->format('H:i');
    }
    if(Carbon::parse($date)->format('Y')==Carbon::now()->format('Y')){
        $fecha=Carbon::parse($date)->formatLocalized('%d %b');
    } else {
        $fecha=Carbon::parse($date)->format('d/m/Y');
    }
    return "<b>".$fecha."</b> ".$hora;
}

function validar_request($r,$metodo_notif,$tipo,$reglas,$mensajes=[]){
    $validator = Validator::make($r->all(), $reglas,$mensajes);
    if($validator->fails()) {
        $mensaje_error="ERROR: Ocurrio un error al validar los datos de ".$tipo." <br>".implode("<br>",$validator->messages()->all());

        switch($metodo_notif){
            case "flash":
                flash($mensaje_error)->error();
                return redirect()->back()->withInput();
                break;

            case "toast":
            return response()->json(['title' => $tipo,
                    'error' => $mensaje_error,
                ],200)->throwResponse();
                break;

            case "texto":
                return $mensaje_error;
                break;

            case "json":
                $mensaje_error=str_replace("<br>"," ",$mensaje_error);
                return response()->json([
                    "response" => "ERROR",
                    "message" => "Error de validacion de datos ". $mensaje_error,
                    "TS" => Carbon::now()->format('Y-m-d h:i:s')
                    ],400)->throwResponse();
                break;

            default:
                return redirect()->to($this->getRedirectUrl())
                ->withInput($r->input())
                ->withErrors($mensaje_error, $this->errorBag());
                break;
        }
    }  else{
        return true;
    }
}

function validar_acceso_tabla($id,$tabla){
    switch($tabla){
        case "clientes":
            $descriptivo="cliente";
            $campo="id_cliente";
            $ruta="clientes.index";
            break;
        case "plantas":
            $descriptivo="planta";
            $campo="id_planta";
            $ruta="plantas.plantas.index";
            break;
        case "edificios":
            $descriptivo="edificio";
            $campo="id_edificio";
            $ruta="edificios.edificios.index";
            break;
        case "users":
            $descriptivo="usuario";
            $campo="id";
            $ruta="users.index";
            break;
        case "puestos":
            $descriptivo="puesto";
            $campo="id_puesto";
            $ruta="puestos.index";
            break;
        case "rondas_limpieza":
            $descriptivo="ronda";
            $campo="id_ronda";
            $ruta="rondas.index";
            break;
        case "incidencias_tipos":
            $descriptivo="tipo";
            $campo="id_tipo_incidencia";
            $ruta="incidencias_tipos.index";
            break;
        case "incidencias":
            $descriptivo="incidencia";
            $campo="id_incidencia";
            $ruta="incidencias.index";
            break;
        case "encuestas":
            $descriptivo="encuesta";
            $campo="id_encuesta";
            $ruta="encuestas.index";
            break;
        default:
            $descriptivo=$tabla;
    }

    $c = DB::table($tabla)
        ->where($campo,$id)
        ->when($tabla!='clientes', function($q) use($tabla){
            $q->join('clientes','clientes.id_cliente',$tabla.'.id_cliente');
        })
        ->whereNull('clientes.fec_borrado')
        ->where(function($q){
            if (!isAdmin()){
                $q->orWhereIn('clientes.id_cliente',clientes());
            }
        })
    ->first();
    if(empty($c))
    {
        savebitacora("BLOQUEO DE ACCESO --> ERROR: El ".$descriptivo." ".$id." no existe o no tienes acceso",'validar_acceso_tabla',$tabla);
        flash("ERROR: El ".$descriptivo." ".$id." no existe o no tienes acceso")->error();
        return redirect()->route($ruta);
    }
}

//Devuelve texto blanco o negro en funcion del color sobre el que esta
function txt_blanco($hex) {
    // returns brightness value from 0 to 255
    // strip off any leading #
    try{
        $hex = str_replace('#', '', $hex);

        $c_r = hexdec(substr($hex, 0, 2));
        $c_g = hexdec(substr($hex, 2, 2));
        $c_b = hexdec(substr($hex, 4, 2));

        $result=(($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
        if($result<130){
            return "text-white";
        }
    } catch(\Exception $e){

    }
    
}

function color_porcentaje($pct,$modo="bootstrap"){
    
    if($modo=="bootstrap"){
        if($pct<50){
            return "danger";
        }
    
        if($pct>=50 && $pct<75){
            return "warning";
        }
    
        if($pct>=75 && $pct<100){
            return "info";
        }
    
        if($pct==100){
            return "success";
        }
    
        if($pct>100){
            return "mint";
        }
    }
    if($modo=="hex"){
        if($pct<50){
            return "#ff6347";
        }
    
        if($pct>=50 && $pct<75){
            return "#ffd700";
        }
    
        if($pct>=75 && $pct<100){
            return "#1e90ff";
        }
    
        if($pct==100){
            return "#adff2f";
        }
    
        if($pct>100){
            return "#008000";
        }
    }   
}

function color_porcentaje_inv($pct){
    
    if($pct<50){
        return "success";
    }

    if($pct>=50 && $pct<75){
        return "warning";
    }

    if($pct>=75 && $pct<=100){
        return "danger";
    }
}

function js_array($array)
{
    function js_str($s)
    {
        return '"' . addcslashes($s, "\0..\37\"\\") . '"';
    }
    $temp = array_map('js_str', $array);
    return '[' . implode(',', $temp) . ']';
}

function authbyToken($token){
    $usuario=users::where('token_acceso',$token)->first();
    if($usuario){
        Auth::logout();
        Auth::loginUsingId($usuario->id);
        session(['lang' => auth()->user()->lang]);
        //Permisos del usuario
        $permisos = DB::select(DB::raw("
        SELECT
                des_seccion,
                max(mca_read)as mca_read,
                max(mca_write) as mca_write,
                max(mca_create) as mca_create,
                max(mca_delete) as mca_delete
        FROM
            (SELECT
                `permisos_usuarios`.`id_seccion`,
                `permisos_usuarios`.`mca_read`,
                `permisos_usuarios`.`mca_write`,
                `permisos_usuarios`.`mca_create`,
                `permisos_usuarios`.`mca_delete`,
                `secciones`.`des_seccion`
            FROM
                `permisos_usuarios`
                INNER JOIN `secciones` ON (`permisos_usuarios`.`id_seccion` = `secciones`.`cod_seccion`)
            WHERE
                `cod_usuario` = ".auth()->user()->id."
            UNION
            SELECT
                `secciones_perfiles`.`id_seccion`,
                `secciones_perfiles`.`mca_read`,
                `secciones_perfiles`.`mca_write`,
                `secciones_perfiles`.`mca_create`,
                `secciones_perfiles`.`mca_delete`,
                `secciones`.`des_seccion`
            FROM
                `secciones_perfiles`
                INNER JOIN `secciones` ON (`secciones_perfiles`.`id_seccion` = `secciones`.`cod_seccion`)
            WHERE
                id_perfil=".auth()->user()->cod_nivel.") sq
        GROUP BY sq.des_seccion"));
        session(['P' => $permisos]);
        return true;
    } else {
        return false;
    }
}

function nombrepuesto($puesto){
    try{
        if(isset(session('CL')['val_campo_puesto_mostrar'])){
            switch (session('CL')['val_campo_puesto_mostrar']) {
                case 'D':
                    return $puesto->des_puesto;
                    break;
                case 'I':
                    return $puesto->cod_puesto;
                    break;
                case 'A':
                    return '['.$puesto->cod_puesto.'] '.$puesto->des_puesto ;
                    break;
                
                default:
                    return '['.$puesto->cod_puesto.'] '.$puesto->des_puesto ;
                    break;
            }
        } else {
            return $puesto->des_puesto;
        }
    } catch(\Exception $e){
        return $puesto->des_puesto;
    }  

}

function config_cliente($clave,$cliente=null){
    try{
        if (!isset($cliente)){
            $cliente=Auth::user()->id_cliente;
        }
        $config=config_clientes::find($cliente)->$clave;
        return $config;
    } catch (\Exception $e){
        return null;
    }
    

}