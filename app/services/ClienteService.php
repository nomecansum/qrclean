<?php
namespace App\Services;
use DB;
use Validator;
use App\User;
use App\Models\clientes;
use App\Models\configclientes;
use App\Models\sistemas;
use App\Models\colectivos;
use App\Models\centros;
use App\Models\departamentos;
use App\Models\empleados;
use App\Models\horarios;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class ClienteService
{
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function validar_request($r,$metodo_notif){
        $validator = Validator::make($r->all(), [
            'nom_cliente' => 'required|string|max:500',
            'num_max_empleados' => 'required|int',
            'cod_sistema' => ['int', Rule::unique('cug_sistema','COD_SISTEMA')->ignore($r->id, 'cod_cliente')]
            //'cod_sistema' => 'required|int|unique:cug_sistema,cod_sistema,cod_cliente,'.$r->cod_cliente,
        ],
        [
            'nom_cliente.required' => 'El campo NOMBRE es obligatorio',
            'num_max_empleados.required' => 'El campo MAX_EMPLEADOS es obligatorio',
            'cod_sistema.required' => 'El campo CODIGO DE SISTEMA es obligatorio',
        ]); 
        if($validator->fails()) {
            $mensaje_error="ERROR: Ocurrio un error al validar los datos de cliente ".$r->nom_cliente."<br>".implode("<br>",$validator->messages()->all());

            switch($metodo_notif){
                case "flash":
                    flash($mensaje_error)->error();  
                    return redirect()->back()->withInput();
                    break;
                
                case "toast":
                return response()->json(['title' => "Clientes",
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

    public function subir_imagen($r){
        $img_logo='';
        if ($r->hasFile('img_logo')) {
            $file = $r->file('img_logo');
            $path = public_path().'/uploads/customers/images';
            $img_logo = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
            $file->move($path,$img_logo);
        }
        return $img_logo;
    }

    public function insertar($r){
        $cl=new clientes;
        if ($r->hasFile('img_logo')) {
            $cl->img_logo=$this->subir_imagen($r);
        }
        $cl->nom_cliente = $r->nom_cliente;
        $cl->nom_contacto = $r->nom_contacto;
        $cl->num_max_empleados = $r->num_max_empleados;
        $cl->cod_supracliente = $r->cod_supracliente;
        $cl->val_apikey = $r->val_apikey;
        $cl->token_1uso = $r->token_1uso;
        $cl->mca_appmovil = isset($r->mca_appmovil) ? $r->mca_appmovil : 'N';
        $cl->tel_cliente = $r->tel_cliente;
        $cl->cif = isset($r->cif) ? $r->cif : '';
        $cl->fec_caducidad = $r->fec_caducidad;
        $cl->mca_actualizacion = "A"; //$r->mca_actualizacion;
        $cl->completado = $r->completado;
        $cl->custumer_stripe = $r->custumer_stripe;
        $cl->cod_tipo_cliente = $r->cod_tipo_cliente ? $r->cod_tipo_cliente : 1; 
        $cl->save();
        return $cl->cod_cliente;
    }

    public function actualizar($r){
        $cl=clientes::find($r->id);
        if ($r->hasFile('img_logo')) {
            $cl->img_logo=$this->subir_imagen($r);
        }
        $cl->nom_cliente = $r->nom_cliente;
        $cl->nom_contacto = $r->nom_contacto;
        $cl->num_max_empleados = $r->num_max_empleados;
        $cl->cod_supracliente = $r->cod_supracliente;
        $cl->val_apikey = $r->val_apikey;
        $cl->token_1uso = $r->token_1uso;
        $cl->mca_appmovil = isset($r->mca_appmovil) ? $r->mca_appmovil : 'N';
        $cl->tel_cliente = $r->tel_cliente;
        $cl->fec_caducidad = $r->fec_caducidad;
        $cl->mca_actualizacion = 'M'; //$r->mca_actualizacion;
        $cl->completado = $r->completado;
        $cl->cod_tipo_cliente = $r->cod_tipo_cliente ? $r->cod_tipo_cliente : 1;
        $cl->save();
        return $cl->cod_cliente;
    }

    public function delete($id){

        //Quitamos el cliente de la lista de acceso de los usuarios
        DB::statement("UPDATE cug_usuarios set clientes = replace(clientes,',".$id."','')");
        
        $clientes = clientes::find($id);
        $app = $clientes->mca_appmovil;
        $clientes->fec_borrado = Carbon::now();
        $clientes->mca_appmovil = "N";
        $clientes->mca_actualizacion = "B";
        $clientes->completado = "N";
        
        $clientes->save();

        $empleados_cliente = empleados::where('cod_cliente',$id);
        $empleados_cliente->update(["fec_borrado"=>Carbon::now()]);
        
        //Baja en app
        if($app == "S")
        {
            $app_svc = new APPApiService;
            $resultado_app = $app_svc->update_cliente([$id]);
            if($resultado_app["result"] == "ERROR"){
                throw new \Exception("Error en la provision del cliente en la APP: " . $resultado_app["msg"]);
            }
        }
        
        //Borramos en las tablas principales para que se disparen el resto de cascade
        //DB::table('cug_centros')->where('cod_cliente',$id)->delete();
        //DB::table('cug_empleados')->where('cod_cliente',$id)->delete();
        //DB::table('cug_colectivos')->where('cod_cliente',$id)->delete();
        //DB::table('cug_clientes')->where('cod_cliente',$id)->delete();
        return true;
    }
        
    public function insertar_sistema($r, $id){
        $des_sistema = $r->nom_cliente;
        if (isset($r->cod_sistema)) 
        {
            $cod_sistema = $r->cod_sistema;
            $sistema = sistemas::where('cod_cliente',$id)->first();
            if($sistema===null)
                $sistema = new sistemas;
        } 
        else 
        {
            $cod_sistema = DB::table('cug_sistema')->where('COD_SISTEMA','>=',10000)->orderby('COD_SISTEMA','desc')->first()->COD_SISTEMA;//+1;
            if(empty($cod_sistema))
                $cod_sistema = 10000;
            else $cod_sistema++;
            $sistema = new sistemas;
        }
        $sistema->COD_SISTEMA=$cod_sistema;
        $sistema->DES_SISTEMA=$des_sistema;
        $sistema->cod_cliente=$id;
        //print_r($id);
        $sistema->save();
        return $cod_sistema;
    }

    public function insetar_datos_defecto($id){
        //Crear colectivo por defecto del cliente
        $colectivo = new colectivos; 
        $colectivo->des_colectivo = 'GENERAL';
        $colectivo->cod_cliente = $id;
        $colectivo->save();
        
        //Crear centro por defecto del cliente
        $centro = new centros; 
        $centro->des_centro = 'GENERAL';
        $centro->cod_cliente = $id;
        $centro->save();
        

        //Crear departamento por defecto del cliente
        $departamento = new departamentos; 
        $departamento->nom_departamento = 'GENERAL';
        $departamento->num_nivel = 1;
        $departamento->cod_centro = $centro->cod_centro;
        $departamento->cod_cliente = $id;
        $departamento->save();
        
        //Asignamos el departamento al centro
        DB::table('cur_departamentos_centro')->insert([
            'cod_centro' => $centro->cod_centro,
            'cod_departamento' => $departamento->cod_departamento
        ]);

        //Añadimos estructura empresarial del cliente basica
        $arbol1 = DB::table('cug_arbol_jerarquia')->where('cod_cliente','1')->get();
        foreach ($arbol1 as $arbol){
            DB::table('cug_arbol_jerarquia')->insert([
                    'des_puesto' => $arbol->des_puesto,
                    'num_nivel' => $arbol->num_nivel,
                    'val_superior' => $arbol->val_superior,
                    'cod_cliente' => $id
                ]);
        }
        
        //Añadimos los tipos de incidencias
        $incidencias = DB::table('cug_tipos_incidencia')->where('cod_cliente','1')->get();
        foreach ($incidencias as $incidencia){
            DB::table('cug_tipos_incidencia')->insert([
                'des_tipo_incidencia' => $incidencia->des_tipo_incidencia,
                'val_color' => $incidencia->val_color,
                'cod_cliente' => $id
            ]);
        }
/*        
        //Añadimos las incidencias
        $incidencias = DB::table('cug_incidencias')->where('cod_cliente','1')->get();
        foreach ($incidencias as $incidencia){
            DB::table('cug_incidencias')->insert([
                'des_tipo_incidencia' => $incidencia->des_tipo_incidencia,
                'val_color' => $incidencia->val_color,
                'cod_cliente' => $id
            ]);
        }
*/
        //Ciclos del cliente
        $ciclos = DB::table('cug_ciclos')->where('cod_cliente','1')->first();
            DB::table('cug_ciclos')->insert([
                'nom_ciclo' => $ciclos->nom_ciclo,
                'num_dias' => $ciclos->num_dias,
                'cod_cliente' => $id
            ]);
        $ciclo = DB::table('cug_ciclos')->get()->last();

        //Horarios del cliente
        $horarios = DB::table('cug_horarios')->where('cod_cliente','1')->first();
            DB::table('cug_horarios')->insert([
                'des_horario' => $horarios->des_horario,
                'val_horas_teoricas' => $horarios->val_horas_teoricas,
                'val_tiempo_pausa' => $horarios->val_tiempo_pausa,
                'cod_cliente' => $id
            ]);
        $hora = DB::table('cug_horarios')->get()->last();   

        //Composicion del ciclo
        $horarios_ciclo = DB::table('cur_horarios_ciclo')->where('cod_ciclo',$ciclos->cod_ciclo)->get();
        foreach ($horarios_ciclo as $hc){
            DB::table('cur_horarios_ciclo')->insert([
                    'cod_horario' => $hora->cod_horario,
                    'cod_ciclo' => $ciclo->cod_ciclo,
                    'num_dia' => $hc->num_dia
                ]);
        }
        
        //Añadimos la composicion del horario
        DB::table('cur_bloques_horario')->insert([
            'cod_horario' => $hora->cod_horario,
            'tip_bloque' => 5,
            'hor_inicio' => '00:00:01',
            'hor_fin' => '23:59:59',
            'num_orden' => 1
        ]);
/*                
        //Perfiles del cliente y secciones para el cliente
        //Hay que revisar si es necesario hacerlo o podemos usar los de tipo fijo
        if(DB::table('cug_clientes')->where('cod_cliente', $id)->first()->cod_tipo_cliente == 3) //SumaResta
        {
            $perfiles = DB::table('cug_niveles_acceso')->where('cod_tipo_cliente','3')->where('val_nivel_acceso', '!=', '200')->whereIn('val_nivel_acceso', [1, 100])->get();
        }
        elseif(DB::table('cug_clientes')->where('cod_cliente', $id)->first()->cod_tipo_cliente == 2) //CucoTime
        {
            $perfiles = DB::table('cug_niveles_acceso')->where('cod_cliente','1')->where('val_nivel_acceso', '!=', '200')->whereIn('val_nivel_acceso', [1, 90])->get();
        }
        else $perfiles = DB::table('cug_niveles_acceso')->where('cod_cliente','1')->where('val_nivel_acceso', '!=', '200')->get();
        
        foreach ($perfiles as $per){
            $perfil_id = 
                DB::table('cug_niveles_acceso')->insertGetId([
                    'val_nivel_acceso' => $per->val_nivel_acceso,
                    'des_nivel_acceso' => $per->des_nivel_acceso,
                    'cod_cliente' => $id
                ]);
            
            $secciones = DB::table('cur_secciones_perfiles')->where('id_perfil',$per->cod_nivel)->get();
            foreach ($secciones as $secc){
                DB::table('cur_secciones_perfiles')->insert([
                    'id_seccion' => $secc->id_seccion,
                    'id_perfil' => $perfil_id,
                    'mca_read' => $secc->mca_read,
                    'mca_write' => $secc->mca_write,
                    'mca_create' => $secc->mca_create,
                    'mca_delete' => $secc->mca_delete
                ]);
            }
        }
*/
        //configuracion del cliente
        $cc = configclientes::find(1);
        $nuevocc = $cc->replicate();
        $nuevocc->cod_cliente = $id;
        $nuevocc->save();
    }

    public function add_a_supracliente($id,$r){
        //damos permisos para este cliente a todos los usuarios del supracliente
        DB::table('cug_usuarios')->where('cod_cliente',$r->cod_supracliente)->update([
            'clientes' => DB::raw('CONCAT(clientes,\','.$id.',\')')
            ]);
    }

    public function add_a_usuario($id,$usuario){
        $u = User::find($usuario);
        $clientes = explode(',',$u->clientes);
        $aux = [];
        foreach ($clientes as $key => $value) {
            if ($value != "") {
                $aux[] = $value;
            }
        }
        $aux[] = $id;
        $u->clientes = ','.implode(',',$aux).',';
        $u->save();
        return true;
    }

    public function provisionar_appmpovil($id, $r, $accion)
    {
        $cliente = clientes::find($id);
        if($r->mca_appmovil == 'S')
        {
            $cliente->mca_appmovil = 'S';
            if(!isset($accion))
            {
                if($r->mca_appmovil = 'S')
                    $cliente->mca_actualizacion = 'A';
                else $cliente->mca_actualizacion = 'B';
            }
            //else $cliente->mca_actualizacion = $accion;
            else $cliente->mca_actualizacion = 'M';
        }
        else
        {
            $cliente->mca_appmovil = 'N';
            $cliente->mca_actualizacion = 'B';
        }
        $cliente->completado = 'N';
        $cliente->save();
        if($r->mca_appmovil == 'S')
        {
            $app_svc = new APPApiService;
            $resultado_app = $app_svc->update_cliente([$id]);
            if($resultado_app["result"] == "ERROR"){
                throw new \Exception("Error en la provision del cliente en la APP: ".$resultado_app["msg"]);
            }
        }
    }
}
