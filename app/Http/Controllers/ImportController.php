<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\puestos;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\users;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use File;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Redirect;
use Str;
Use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Hash;

class ImportController extends Controller
{
    //

    public function user_to_object($spreadsheet,$i){

        $fila = $spreadsheet->setActiveSheetIndex(1)->rangeToArray('A'.$i.':F'.$i)[0];
        //error_log(json_encode($fila));
        $vacio=true;
        for($n=0;$n<6;$n++){
            if($fila[$n]<>''){
                $vacio=false;
            }
        }
        if ($vacio){ //Fila vacio, ya no hay mas que procesar
            return false;
        }

        $user = new Request([
            'name' => $fila[0],
            'email' => $fila[1],
            'foto' => $fila[2],
            'id_cliente' => Auth::user()->id_cliente
        ]);
        return $user;
    }

    public function puesto_to_object($spreadsheet,$i){

        $fila = $spreadsheet->setActiveSheetIndex(0)->rangeToArray('A'.$i.':F'.$i)[0];
        //error_log(json_encode($fila));
        $vacio=true;
        for($n=0;$n<6;$n++){
            if($fila[$n]<>''){
                $vacio=false;
            }
        }
        if ($vacio){ //Fila vacio, ya no hay mas que procesar
            return false;
        }

        $puesto = new Request([
            'cod_puesto' => $fila[0],
            'des_puesto' => $fila[1],
            'des_edificio' => $fila[2],
            'des_planta' => $fila[3],
            'mca_anonimo' => $fila[4],
            'mca_reserva' => $fila[5],
            'id_cliente' => Auth::user()->id_cliente
        ]);
        return $puesto;
    }

    /**
     * @param mixed $Request 
     * @param mixed $r 
     * @return void 
     */
    public function process_import(Request $r){
        try
        {

            $directorio = public_path().'/uploads/temp/'.Auth::user()->id_cliente.'/';

            if(!File::exists($directorio)) {
                File::makeDirectory($directorio);
            }

            $files = $r->file('file');
            //Subiumos los ficheros a la carpeta temporal
            foreach($files as $file)
            {
                $file->move($directorio,$file->getClientOriginalName());
                if(File::extension($file->getClientOriginalName())=='xls' || File::extension($file->getClientOriginalName())=='xlsx'){
                    $fichero_plantilla=$directorio.$file->getClientOriginalName();
                }
            }

            if (isset($fichero_plantilla))
            {
                if(File::extension($fichero_plantilla)=='xls')
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                else $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                
                $spreadsheet = $reader->load($fichero_plantilla);
               
                //Vuelta de comprobacion
                $errores = "";
                $cuenta_errores = 0;
                $cuenta_usuarios = 0;
                $cuenta_puestos = 0;
                $mensajes_adicionales = "";
                $nombres_usuarios = "";
                $nombres_puestos = "";
                
                //Comproboamos los datos de puestos
                $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
                for ($i = 2; $i <= $highestRow; $i++) 
                {
                    $puesto = $this->puesto_to_object($spreadsheet, $i);
                    if($puesto == false)
                    {   
                        //El fichero esta vacio
                        break;
                    } 
                    else
                    {
                        if($puesto->cod_puesto!='' && $puesto->des_edificio!='' && $puesto->des_planta!=''){
                            $cuenta_puestos++;
                        } else {
                            $cuenta_errores++;
                            $errores.='PUESTOS - Fila '.$i.' Falta un campo requerido <br>';
                        }
                        
                    }
                }
                //Comprobamos los datos de usuarios
                $highestRow = $spreadsheet->setActiveSheetIndex(1)->getHighestRow();
                for ($i = 2; $i <= $highestRow; $i++) 
                {
                    $user = $this->user_to_object($spreadsheet, $i);
                    if($user == false)
                    {   
                        //El fichero esta vacio
                        break;
                    } 
                    else
                    {
                        if($user->name!='' && $user->email!=''){
                            $cuenta_usuarios++;
                            $u=users::where('email',$user->email)->first();
                            if($u!=null){
                                $cuenta_errores++;
                                $errores.='USUARIOS - Fila '.$i.' la direccin de email ya existe <br>';
                            }
                        } else {
                            $cuenta_errores++;
                            $errores.='USUARIOS - Fila '.$i.' Falta un campo requerido <br>';
                        }
                        
                    }
                }

                //Vemos si hay errores, si los hay, se acabo, no seguimos procesando
                if ($errores != "")
                {
                    return [
                        'title' => 'Se detectaron errores en el fichero de importacion',
                        'message' => $errores,
                        'tipo' => 'error'
                    ];
                }
                
                //Tutuben, pues a insertar
                DB::beginTransaction();
                try
                {
                    
                    for ($i = 2; $i < ($cuenta_usuarios+2); $i++) 
                    {
                        $user = $this->user_to_object($spreadsheet, $i);
                        $u=new users;
                        $u->email=$user->email;
                        $u->name=$user->name;
                        $u->id_cliente=Auth::user()->id_cliente;
                        $u->password=Hash::make('NuevoUsuario');
                        $u->cod_nivel=1;

                        if ($user->foto!='') {
                            $file = File::get($directorio.$user->foto);
                            $path = '/img/users/';
                            $img_usuario = uniqid().rand(000000,999999).'.'.File::extension($directorio.$user->foto);
                            //$file->move($path,$img_usuario);
                            Storage::disk(config('app.img_disk'))->putFileAs($path,$directorio.$user->foto,$img_usuario);
                            $u->img_usuario=$img_usuario;
                        }
                        $u->save();
                        
                        savebitacora("Creado usuario en importacion " . $u->id . " " . $u->name, $u->id_cliente);
                        $nombres_usuarios .= "[".$u->id."] " . $u->name. "<br>";
                    }

                    $highestRow = $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
                    for ($i = 2; $i < ($cuenta_puestos+2); $i++) 
                    {
                        $puesto = $this->puesto_to_object($spreadsheet, $i);
                        $p=new puestos;
                        $p->cod_puesto=$puesto->cod_puesto;
                        $p->des_puesto=$puesto->des_puesto;
                        $p->id_cliente=Auth::user()->id_cliente;
                        $p->mca_reservar=$puesto->mca_reservar?$puesto->mca_reservar:'S';
                        $p->mca_acceso_anonimo=$puesto->mca_acceso_anonimo?$puesto->mca_acceso_anonimo:'S';
                        $p->token=Str::random(50);
                        //Edificio y planta
                        $edificio=edificios::where('des_edificio',$puesto->des_edificio)->first();
                        if(!$edificio){
                            $edificio = new edificios;
                            $edificio->des_edificio=$puesto->des_edificio;
                            $edificio->id_cliente=$p->id_cliente;
                            $edificio->save();
                        }
                        $planta=plantas::where('des_planta',$puesto->des_planta)->first();
                        if(!$planta){
                            $planta = new plantas;
                            $planta->des_planta=$puesto->des_planta;
                            $planta->id_cliente=$p->id_cliente;
                            $planta->id_edificio=$edificio->id_edificio;
                            $planta->save();
                        }
                        $p->id_edificio=$edificio->id_edificio;
                        $p->id_planta=$planta->id_planta;
                        $p->save();
                        
                        savebitacora("Creado puesto en importacion " . $p->id_puesto . " " . $p->cod_puesto, $p->id_cliente);
                        $nombres_puestos .= "[".$p->id_puesto."] " . $u->des_puesto. "<br>";
                    }

                    DB::commit();

                    //Borramos el excel y la carpeta de importacion
                    File::deleteDirectory($directorio);
					
                    return [
                        'title' => 'Proceso de importacion finalizada con exito',
                        'message' => $cuenta_usuarios . " usuarios importados correctamente:<br>" . $nombres_usuarios . "<br>" .'<br>'. $cuenta_puestos . " puestos importados correctamente:<br>" . $nombres_puestos . "<br>" . $mensajes_adicionales,
                        'tipo' => 'ok'
                    ];
                } catch (Exception $e){
                    DB::rollback();
                    savebitacora("Error en el proceso de importacion ". $e->getMessage(), null);
                    return [
                        'title' => 'Error comprobando los datos de empleados',
                        'message' => "Error en el proceso de importacion " . $e->getMessage(),
                        'tipo' => 'error'
                    ];
                }
            }
            else return \Response::json(array('success' => true));
        } 
        catch(Exception $e){
            return \Response::json(array('error' => false));
        }
    }
}
