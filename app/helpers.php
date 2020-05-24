<?php

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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

function savebitacora($des_bitacora,$seccion=null,$tipo=null)
{
   if(isset(Auth::user()->name)){
       $user=Auth::user()->name;
   }

    \DB::table('bitacora')->insert([
        'accion' => $des_bitacora,
        'id_usuario' =>Auth::user()->id,
        'id_modulo' => $seccion,
        'status' => $tipo,
        'fecha' => Carbon::now()
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

function mensaje_excepcion($e){
    if(isAdmin()){
        return $e->getMessage().' {'.get_class($e).'}  ['.debug_backtrace()[1]['function'].']';
    } else {
        return substr($e->getMessage(),1,15);
    }
}

function isCustomerAdmin(){
    try{
        return Auth::User()->val_nivel_acceso == 200 ? true : false;
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

function adaptar_fecha($d){
    try{
        if (Carbon::createFromFormat('d/m/Y', $d)!== false) {
            return Carbon::createFromFormat('d/m/Y', $d)->format('Y-m-d');
        }
        if (Carbon::createFromFormat('Y-m-d', $d)!== false) {
            return Carbon::createFromFormat('d/m/Y', $d)->format('Y-m-d');
        }
        if (Carbon::parse($d)!== false) {
            return Carbon::parse($d)->format('Y-m-d');
        }
    } catch (\Exception $e){
        return  $d." ".$e->getMessage();
    }
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


function enviar_email($user,$from,$to,$to_name,$subject,$plantilla){
    try{
        \Mail::send($plantilla, ['user' => $user], function ($m) use ($from,$to,$to_name,$subject) {
            $m->from($from, 'Cuco360');
            $m->to($to, $to_name)->subject($subject);
        });
    } catch(\Exception $e){
        return $e->getMessage();
    }
    return true;

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


function icono_nombre($nombre,$height=50,$font=18){

    try{
        $padding=intdiv($height,11);
        $rand=Str::random(9);
        $words = explode(" ", $nombre);
        $acronym = "";
        $i = 0;
        foreach ($words as $w) {
            $acronym .= $w[0];
            if (++$i == 2) break;
        }
    } catch(\Exception $e){
        $acronym=substr($nombre,1,2);
    }
    //return '<span class="round" id="'.$rand.'" style="text-transform: uppercase; background-color: '.App\Classes\RandomColor::one().'">'.$acronym.'</span>';
    return '<span class="round" id="'.$rand.'" style="font-weight: bold; font-size: '.$font.'px; width: '.$height.'px;height: '.$height.'px; padding-top:'.$padding.'px; text-transform: uppercase; background-color: '.genColorCodeFromText($nombre).'" data-toggle="tooltip" data-placement="bottom" title="'.$nombre.'">'.$acronym.'</span>';
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
