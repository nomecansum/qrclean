<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\niveles_acceso;
use App\Models\users;
use App\Models\puestos;
use App\Models\plantas_usuario;
use App\Models\puestos_asignados;
use Illuminate\Http\Request;
use Exception;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use Auth;
use Session;
use Redirect;
use \Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Container\BindingResolutionException;

class UsersController extends Controller
{

    /**
     * Display a listing of the users.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        
        $usersObjects = DB::table('users')
        ->leftjoin('niveles_acceso','users.cod_nivel', 'niveles_acceso.cod_nivel')
        ->where(function($q){
            if (!isAdmin()) {
                $q->wherein('users.id_cliente',clientes());
            }
        })
        ->where(function($q){
            if (isSupervisor(Auth::user()->id)) {
                $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('id')->toArray();
                $q->wherein('users.id',$usuarios_supervisados);
            }
        })
        ->get();

        $supervisores_perfil=[0];
        $supervisores_usuario=[0];

    
        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->get()->pluck('id_perfil')->unique();

        $supervisores_usuario=DB::table('permisos_usuarios')->where('id_seccion',$permiso)->get()->pluck('id_usuario')->unique();

        $supervisores=DB::table('users')
            ->where(function ($q) use($supervisores_perfil,$supervisores_usuario){
                $q->wherein('cod_nivel',$supervisores_perfil);
                $q->orwherein('id',$supervisores_usuario);
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->wherein('users.id_cliente',clientes());
                }
            })
            ->orderby('name')
            ->get();
            

        //$usersObjects = users::with('grupo','perfile')->paginate(25);

        return view('users.index', compact('usersObjects','supervisores'));
    }

    /**
     * Show the form for creating a new users.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        //$Grupos = Grupo::pluck('grupo','id_grupo')->all();
        $Perfiles = niveles_acceso::all();

        return view('users.create', compact('Perfiles'));
    }

    /**
     * Store a new users in the storage.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        //Vamos a comprbar el email, porque no puedo pasarlo por el validado
        if(DB::table('users')->where('email',$request->email)->exists()){
            flash('ERROR: El e-mail ya existe '.$request->email)->error();
            return back()->withInput();
        }
        $data = $this->getData($request);

        $img_usuario = "";
        try {
             if ($request->hasFile('img_usuario')) {
                $file = $request->file('img_usuario');
                $path = '/img/users/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
            }

            $data['img_usuario']=$img_usuario;
            $data["password"]=Hash::make($request->password);
            $data["cod_nivel"]=$request->cod_nivel;
            $data["nivel_acceso"]=DB::table('niveles_acceso')->where('cod_nivel',$data['cod_nivel'])->first()->val_nivel_acceso;

            users::create($data);
            savebitacora('Usuario '.$request->email. ' creado',"Usuarios","Store","OK");
            return [
                'title' => "Usuarios",
                'message' => 'Usuario '.$request->name. ' creado con exito',
                'url' => url('users')
            ];
        } catch (Exception $exception) {
            savebitacora('ERROR: Ocurrio un error creando el usuario '.$request->name.' '.$exception->getMessage() ,"Usuarios","Store","ERROR");
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error creando el usuario '.$request->name.' '.$exception->getMessage(),
                'url' => url('users')
            ];
            // flash('ERROR: Ocurrio un error creando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
        }
    }


    /**
     * Show the form for editing the specified users.
     *
     * @param int $id
     *
     * @return Illuminate\View\View
     */
    public function edit($id)
    {
        validar_acceso_tabla($id,"users");
        $users = users::findOrFail($id);
        $Perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->get();
        // dd($Perfiles);

        $permiso=DB::table('secciones')->where('des_seccion','Supervisor')->first()->cod_seccion??0;

        $supervisores_perfil=DB::table('secciones_perfiles')->where('id_seccion',$permiso)->get()->pluck('id_perfil')->unique();

        $supervisores_usuario=DB::table('permisos_usuarios')->where('id_seccion',$permiso)->get()->pluck('id_usuario')->unique();

        $supervisores=DB::table('users')
            ->where(function ($q) use($supervisores_perfil,$supervisores_usuario){
                $q->wherein('cod_nivel',$supervisores_perfil);
                $q->orwherein('id',$supervisores_usuario);
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->wherein('users.id_cliente',clientes());
                }
            })
            ->orderby('name')
            ->get();

        $usuarios_supervisables = DB::table('users')
            ->leftjoin('niveles_acceso','users.cod_nivel', 'niveles_acceso.cod_nivel')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->wherein('users.id_cliente',clientes());
                }
            })
            ->where('users.id','<>',Auth::user()->id)
            ->get();


        $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',$id)->pluck('id')->toarray();

        return view('users.edit', compact('users','Perfiles','supervisores','usuarios_supervisados','usuarios_supervisables'));
    }

    /**
     * Update the specified users in the storage.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        validar_acceso_tabla($id,"users");
       
        $img_usuario = "";
        $data = $this->getData($request);
        
        try {
            if ($request->hasFile('img_usuario')) {
                $file = $request->file('img_usuario');
                $path = config('app.ruta_public').'/img/users/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
            }

            $data['img_usuario']=$img_usuario;

            
            if (isset($request->password)){
                $data["password"]=Hash::make($request->password);
            }
            $users = users::findOrFail($id);
            $data["email_verified_at"]=Carbon::now();
            $data["nivel_acceso"]=DB::table('niveles_acceso')->where('cod_nivel',$data['cod_nivel'])->first()->val_nivel_acceso;
            $data["id_usuario_supervisor"]=$request->id_usuario_supervisor??null;

            $users->update($data);

            //Añadimos los usuarios supervisados

            if(isset($request->lista_id)){
                DB::table('users')->where('id_usuario_supervisor',$id)->update([
                    'id_usuario_supervisor'=>null
                ]);
                foreach($request->lista_id as $id_supervisado){
                    DB::table('users')->where('id',$id_supervisado)->update([
                        'id_usuario_supervisor'=>$id
                    ]);
                }
            }
            savebitacora('Usuario '.$request->email. ' actualizado',"Usuarios","Update","OK");
            return [
                'title' => "Usuarios",
                'message' => 'Usuario '.$request->name. ' actualizado con exito',
                'url' => url('users')
            ];
            // flash('Usuario '.$request->name. 'actualizado con exito')->success();
            // return redirect()->route('users.users.index');
        } catch (Exception $exception) {
            // flash('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage())->error();
            // return back()->withInput();
            savebitacora('ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage() ,"Usuarios","Update","ERROR");
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error actualizando el usuario '.$request->name.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    public function update_perfil($id, Request $request)
    {
        validar_acceso_tabla($id,"users");
       
        
        $data = $this->getData($request);
        $users = users::findOrFail($id);
        $img_usuario = $users->img_usuario;

        try {
            if ($request->hasFile('img_usuario')) {
                $file = $request->file('img_usuario');
                $path = config('app.ruta_public').'/img/users/';
                $img_usuario = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                //$file->move($path,$img_usuario);
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_usuario);
                $users->img_usuario=$img_usuario;
            }

            if (isset($request->password)){
                $users->password=Hash::make($request->password);
            }
            
            $users->name=$request->name;
            $users->email=$request->email;
            $users->val_timezone=$request->val_timezone;
            $users->save();

        
            savebitacora('Perfil de usuario '.$request->email. ' actualizado',"Usuarios","update_perfil","OK");
            return [
                'title' => "Usuarios",
                'message' => 'Perfil de usuario '.$request->name. ' actualizado con exito',
                'url' => url('/')
            ];
        } catch (Exception $exception) {
            savebitacora('ERROR: Ocurrio un error actualizando el perfil de usuario '.$request->name.' '.$exception->getMessage() ,"Usuarios","update_perfil","ERROR");
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error actualizando el perfil deusuario '.$request->name.' '.$exception->getMessage(),
                //'url' => url('sections')
            ];

        }
    }

    /**
     * Remove the specified users from the storage.
     *
     * @param int $id
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        validar_acceso_tabla($id,"users");
        try {
            $users = users::findOrFail($id);
            $users->delete();
            savebitacora('Usuario '.$users->email. ' borrado',"Usuarios","Destroy","OK");
            flash('Usuario '.$id. ' eliminado con exito')->success();
            return redirect()->route('users.index');
        } catch (Exception $exception) {
            flash('ERROR: Ocurrio un error al eliminar el usuario '.$id.' '.$exception->getMessage())->error();
            savebitacora('ERROR: Ocurrio un error borrando el usuario '.$users->name.' '.$exception->getMessage() ,"Usuarios","destroy","ERROR");
            return back()->withInput();
                //->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.']);
        }
    }

    public function borrar_usuarios(Request $r)
    {
        try {
            $id_ok=[];
            foreach($r->lista_id as $id){
                validar_acceso_tabla($id,"users");
                $users = users::find($id);
                if($users){
                    $id_ok[]=$users->name;
                    $users->delete();
                    savebitacora('Usuario '.$users->email. ' borrado',"Usuarios","Destroy","OK");
                }
                
                
            }
        } catch (Exception $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error borrando los usuarios el usuario '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }

        return [
            'title' => "Usuarios",
            'message' => 'Usuarios '.implode(",",$id_ok). ' borrados',
            'url' => url('users')
        ];
    }

    /**
     * Get the request's data from the request.
     *
     * @param Illuminate\Http\Request\Request $request
     * @return array
     */
    protected function getData(Request $request)
    {
        $rules = [
            'name'=>'required',
            'collapse' => 'nullable|numeric|min:-2147483648|max:2147483647',
            'email' => 'required|string|min:1|max:255',
            'email_verified_at' => 'nullable|date_format:j/n/Y g:i A',
            'id_cliente' => 'required',
            'cod_nivel' => 'nullable',
            'remember_token' => 'nullable|string|min:0|max:100',
            'theme' => 'nullable|string|min:0|max:150',
            'val_timezone' => 'nullable|string|min:0|max:100',
            'nivel_acceso'=>'nullable',
            'token_acceso'=>'nullable'
        ];


        $data = $request->validate($rules);
        return $data;
    }

    public function plantas_usuario($id,$check){
        validar_acceso_tabla($id,'users');
        $user=users::findorfail($id);

        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$user->id_cliente)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$user->id_cliente)
        ->get();

        $plantas_usuario=DB::table('plantas_usuario')->where('id_usuario',$id)->pluck('id_planta')->toarray();
        return view('users.selector_plantas',compact('puestos','edificios','plantas_usuario','id','check'));
    }

    public function addplanta($usuario,$planta){
        validar_acceso_tabla($usuario,'users');
        savebitacora('Permiso de reserva en planta '.$planta. ' para el usuario '.$usuario,"Usuarios","addplanta","OK");
        $pl=plantas_usuario::insert(['id_planta'=>$planta,'id_usuario'=>$usuario]);
        return [
            'title' => "Asociar planta a usuario",
            'message' => 'Planta asociada ',
            'id' =>$planta
        ];
    }

    public function delplanta($usuario,$planta){
        validar_acceso_tabla($usuario,'users');
        $pl=plantas_usuario::where(['id_planta'=>$planta,'id_usuario'=>$usuario])->delete();
        savebitacora('Quitado permiso de reserva en planta '.$planta. ' para el usuario '.$usuario,"Usuarios","addplanta","OK");
        return [
            'title' => "Asociar planta a usuario",
            'message' => 'Planta eliminada ',
            'id' =>$planta
        ];
    }

    public function setdefcamera($id){
        $u=users::find(Auth::user()->id);
        $u->def_camera=$id;
        $u->save();
        return;
    }

    public function pwd_hash($pwd){

        return Hash::make($pwd);
    }


    public function reback(){
        $this->authwith(session('back_id'));
        session(['back_id'=>null]);
        return redirect('/');
    }

    public function authwith($id)
    {
        $back_id=Auth::user()->id;
        //validar_acceso_tabla($id,"cug_usuarios");
        savebitacora("Relogin al usuario [".$id."]");
        Auth::logout();
        Auth::loginUsingId($id);

        //session(['lang' => Auth::user()->lang]);
        session(['back_id'=>$back_id]);
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

        //Imagen del usuario
        $imagen=DB::table('users')
        ->select('img_usuario')
        ->where('id',Auth::user()->cod_usuario)
        ->value('img_usuario');
        session(['profile_pic'=>$imagen]);
        Session::forget('id_cliente');

        //Logo de cliente
        $cliente=DB::table('clientes')->where('id_cliente',Auth::user()->cod_cliente)->first();

        if(!empty($cliente->fec_borrado))
           return response()->json(["Tu empresa se encuentra dada de baja en el sistema, para cualquier pregunta contacte al 917 373 295 "],422);

        if($cliente && $cliente->img_logo!=''){
            session(['logo_cliente' => $cliente->img_logo]);
        }
        else Session::forget('logo_cliente');
    

        savebitacora("Cambio de sesion del usuario",null);
        return redirect('/');
    }

    public function asignar_plantas(Request $r){

        try {
            //Primero borramos lo que ya tuvieran si se ha escogido la opcion
            if($r->borrar_ant){
                DB::table('plantas_usuario')->wherein('id_usuario',$r->lista_id)->wherein('id_planta',$r->lista_plantas)->delete();
            }
            foreach($r->lista_id as $id){
                foreach($r->lista_plantas as $p){
                    DB::table('plantas_usuario')->insert([
                        "id_usuario"=>$id,
                        "id_planta"=>$p
                    ]);
                }
                
            }
            savebitacora('Asignado permiso de  de reserva en plantas '.implode(",",$r->lista_plantas). ' para los usuarios '.implode(",",$r->lista_id),"Usuarios","asignar_plantas","OK");

            return [
                'title' => "Usuarios",
                'message' => 'Asignado permiso de  de reserva en plantas '.implode(",",$r->lista_plantas). ' para los usuarios '.implode(",",$r->lista_id),
                //'url' => url('users')
            ];

        } catch (Exception $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error asignando permisos de planta a los usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function asignar_supervisor(Request $r){
        try {
            $supervisor=$r->supervisor==0?null:$r->supervisor;
            foreach($r->lista_id as $id){
                DB::table('users')->where('id',$id)->update([
                    "id_usuario_supervisor"=>$supervisor
                ]);
            }
            savebitacora('Asignado supervisor' .$r->supervisor.' para los usuarios '.implode(",",$r->lista_id),"Usuarios","asignar_supervisor","OK");

            return [
                'title' => "Usuarios",
                'message' =>'Asignado supervisor' .$r->supervisor.' para los usuarios '.implode(",",$r->lista_id),
                //'url' => url('users')
            ];

        } catch (Exception $exception) {
            return [
                'title' => "Usuarios",
                'error' => 'ERROR: Ocurrio un error asignando permisos de planta a los usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function puestos_supervisor($id){
        $usuario=users::find($id);

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$usuario->id_cliente)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$usuario->id_cliente)
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where('fec_reserva',Carbon::now()->format('Y-m-d'))
            ->where('reservas.id_cliente',$usuario->id_cliente)
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',$id)
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',$usuario->cod_nivel)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',$usuario->cod_nivel)
            ->get();

        $puestos_check=DB::table('puestos_usuario_supervisor')->where('id_usuario',$id)->pluck('id_puesto')->toarray();
        $checks=1; //Para que la vista muestre los checkbox
        $id_check=$id;
        $url_check="users/add_puesto_supervisor/";

        
        return view('puestos.content_mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','checks','puestos_check','id_check','url_check'));
    }

    /**
     * @param mixed $id 
     * @return View|Factory 
     * @throws BindingResolutionException 
     */
    public function puestos_usuario($id){
        $usuario=users::find($id);

        $puestos_check=DB::table('puestos_usuario_supervisor')->where('id_usuario',$id)->pluck('id_puesto')->toarray();

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$usuario->id_cliente)
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->wherenotin('puestos.id_estado',[4,5])
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$usuario->id_cliente)
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where('fec_reserva',Carbon::now()->format('Y-m-d'))
            ->where('reservas.id_cliente',0)
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',$id)
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',0)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',0)
            ->get();

        $puestos_check=[];
        $checks=0; //Para que la vista muestre los checkbox
        $id_check=$id;
        $url_check="users/add_puesto_usuario/";

        
        return view('puestos.content_mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','checks','puestos_check','id_check','url_check'));
    }

    public function add_puesto_supervisor($id,$puesto,$accion){
        try {
            if($accion=="A"){
                DB::table('puestos_usuario_supervisor')->insert([
                    "id_puesto"=>$puesto,
                    "id_usuario"=>$id
                ]);
            } else {
                DB::table('puestos_usuario_supervisor')->where(["id_puesto"=>$puesto,"id_usuario"=>$id])->delete();
            }
            return [
                'result' => "OK"
            ];
        } catch (Exception $exception) {
            return [
                'result' => "ERROR",
                'error' => 'ERROR: Ocurrio un error asignando puestos al usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }
    }

    public function asignar_temporal(Request $r){

        try {
            $puesto=puestos::find($r->puesto);
            $idusuario=is_array($r->id_usuario)?$r->id_usuario[0]:$r->id_usuario;
            $usuario=users::find($idusuario);
            $f = explode(' - ',$r->rango);
            $f1 = Carbon::parse(adaptar_fecha($f[0]));
            $f2 = Carbon::parse(adaptar_fecha($f[1]));
           
            //Vamos a ver si alguien tiene ese puesto asignado permanentemente
            $puesto_asignado=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                ->join('users','puestos_asignados.id_usuario','users.id')
                ->where('puestos.id_puesto',$puesto->id_puesto)
                ->wherenull('puestos_asignados.id_perfil')
                ->where(function($q) use($f1,$f2){
                    $q->orwhere(function($q) {
                        $q->wherenull('fec_desde');
                        $q->wherenull('fec_hasta');
                    });
                    $q->orwhere(function($q) use($f1,$f2){
                        $q->whereraw("'".$f1."' between fec_desde AND fec_hasta");
                        $q->orwhereraw("'".$f2."' between fec_desde AND fec_hasta");
                        $q->orwherebetween('fec_desde',[$f1,$f2]);
                        $q->orwherebetween('fec_hasta',[$f1,$f2]);
                    });
                })
                ->get();
            
            $reservas=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->where('puestos.id_puesto',$puesto->id_puesto)
                ->wherebetween('fec_reserva',[$f1,$f2])
                ->where('puestos.id_cliente',$usuario->id_cliente)
                ->get();
            
            if($r->accion=="A"){  //Alta, Añadir
                if(!$puesto_asignado->isempty()){
                    $respuesta=[];
                    foreach ($puesto_asignado as $p){
                        if(!$p->fec_desde){  //El usuario saliente tenia el puesto asignado indefinidamente
                            $str_respuesta="El puesto esta asignado permanentemente a ".$p->name.' como esta asignacion tiene prioridad sobre la permanante, se le notificará a '.$p->name.' que entre '.Carbon::parse($f1)->format('d/m/Y').' y '.Carbon::parse($f2)->format('d/m/Y').' no podrá usar su puesto';
                        } else {
                            $p->fec_desde=Carbon::parse($p->fec_desde);
                            $p->fec_hasta=Carbon::parse($p->fec_hasta); 
                            $str_respuesta='El puesto esta asignado temporalmente a '.$p->name.' entre el '.Carbon::parse($p->fec_desde)->format('d/m/Y').' y '.Carbon::parse($p->fec_hasta)->format('d/m/Y').' si continúa, se modificará la asignacion temporal de puesto y se notificará a '.$p->name.' que ';
                            if(($p->fec_desde>=$f1 && $p->fec_hasta<=$f2) || ($p->fec_desde==$f1 && $p->fec_hasta==$f2) || ($p->fec_desde>$f1 && $p->fec_hasta=$f2)){ //La nueva asignacion es mas grande que la actual--> Se cancela la existente
                                $str_respuesta.=' se cancelará la asignacion temporal de puesto que tenía';
                            }else if($p->fec_desde<=$f1 && $p->fec_hasta>$f2){ //La nueva asignacion es mas pequeña que la actual y esta en medio de esta, se parte la existente en dos
                                $str_respuesta.=' se producirá una interrupcion en el intervalo de su asignacion entre el '.$f1->format('d/m/Y').' y '.$f2->format('d/m/Y');
                            }else if($p->fec_desde>$f1 && $p->fec_hasta>$f2){ //La nueva asignacion empieza enmedio de la actual y acaba despues --> se corta por la izquierda
                                $str_respuesta.=' su asignacion de puesto comenzará el '.$f2->addDay()->format('d/m/Y');
                            }else if($p->fec_hasta>$f1 && $p->fec_hasta<$f2){  //La nueva asignacion  empieza antes de la actual y acaba en medio ->se corta por la derecha
                                $str_respuesta.=' su asignacion de puesto finalizará el '.$f1->subDay()->format('d/m/Y');
                            }
                        }
                        $respuesta[]=$str_respuesta;
                    }
                    foreach($reservas as $res){
                        $respuesta[]="Se cancelará la reserva que ".$res->name."  tiene para el día ".Carbon::parse($res->fec_reserva)->format('d/m/Y');
                    }
                    return view('users.asignacion_temporal_pedir_confirmacion',compact('respuesta','r'));
                }
                //Si no hay nada mas, creamos la asignacion para el usuario
                DB::table('puestos_asignados')->insert([
                    'id_puesto'=>$puesto->id_puesto,
                    'id_usuario'=>$usuario->id,
                    'fec_desde'=>$f1,
                    'fec_hasta'=>$f2,
                    'id_tipo_asignacion'=>1
                ]);
                savebitacora('Asignado puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango,"Usuarios","asignar_temporal","OK");
                //Notificar al usuario entrante
                $str_notificacion=Auth::user()->name.' ha creado una nueva asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') para usted en el intervalo '.$r->rango;
                notificar_usuario($usuario,"Se le ha asignado un nuevo puesto",'emails.asignacion_puesto',$str_notificacion,1);
                return [
                    'result' => "OK",
                    'title' => "Usuarios",
                    'message' =>'Asignado puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango,
                ];
            } else if($r->accion=="B") {  //Baja, borrar
                
                DB::table('puestos_asignados')->where('key_id',$r->key_id)->delete();
                savebitacora('Borrada asignacion del puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango,"Usuarios","asignar_temporal","OK");
                //Notificar al usuario
                $str_notificacion=Auth::user()->name.' ha eliminado la asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') que tenía';
                notificar_usuario($usuario,"Se eliminado la asignacion temporal de su puesto",'emails.asignacion_puesto',$str_notificacion,1);

                return [
                    'result' => "OK",
                    'title' => "Usuarios",
                    'message' =>'Borrada asignacion de puesto '.$puesto->cod_puesto.' para el usuario '.$usuario->name.' en el intervalo '.$r->rango,
                ];

            } else if($r->accion=="C") { //Confirmar  en caso de que en el alta haya habido algun liop
                $str_respuesta="";
                
                foreach ($puesto_asignado as $p){
                    $p->fec_desde=Carbon::parse($p->fec_desde);
                    $p->fec_hasta=Carbon::parse($p->fec_hasta);
                    $str_notificacion=Auth::user()->name.' ha creado una nueva asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') que entra en conflicto con una que usted tenia entre '.Carbon::parse($p->fec_desde)->format('d/m/Y').' y '.Carbon::parse($p->fec_hasta)->format('d/m/Y');
                    if(($p->fec_desde>=$f1 && $p->fec_hasta<=$f2) || ($p->fec_desde==$f1 && $p->fec_hasta==$f2) || ($p->fec_desde>$f1 && $p->fec_hasta=$f2)){ //Se borra la asigancion que tenia
                        DB::table('puestos_asignados')->where('key_id',$p->key_id)->delete();
                        $str_respuesta='Se ha cancelado la asignacion temporal de puesto que tenía '.$p->name.' para el intervalo entre '.$f1->format('d/m/Y').' y '.$f2->format('d/m/Y');
                    }else if($p->fec_desde<$f1 && $p->fec_hasta>$f2){ //Se parte la asignacion que tenia en dos, metiendo en medio la nueva
                        $asignacion=puestos_asignados::find($p->key_id); //Cacho 1
                        $fec_hasta_temp=$asignacion->fec_hasta;
                        $asignacion->fec_hasta=$f1->subDay();
                        $asignacion->save();

                        $asignacion= new puestos_asignados;  //Creamos el cacho 2
                        $asignacion->fec_desde->$f2->addDay();
                        $asignacion->fec_hasta=$fec_hasta_temp;
                        $asignacion->id_puesto=$puesto->id_puesto;
                        $asignacion->id_usuario=$usuario->id;
                        $asignacion->id_tipo_asignacion=1;
                        $asignacion->save();
                        $str_respuesta=' Se ha interrumpido  el intervalo de asignacion de puesto de '.$p->name.' entre el '.$f1->format('d/m/Y').' y '.$f2->format('d/m/Y');
                    }else if($p->fec_desde>$f1 && $p->fec_hasta>$f2){ 
                        $asignacion=puestos_asignados::find($p->key_id); 
                        $asignacion->fec_desde=Carbon::parse($f2)->addDay();
                        $asignacion->save();
                        $str_respuesta=' su asignacion de puesto comenzará el '.$f2->addDay()->format('d/m/Y');
                    }else if($p->fec_hasta>$f1 && $p->fec_hasta<$f2){
                        $asignacion=puestos_asignados::find($p->key_id); 
                        $asignacion->fec_hasta=Carbon::parse($f1)->subDay();
                        $asignacion->save();
                        $str_respuesta=' su asignacion de puesto acabará el '.$f1->subDay()->format('d/m/Y');
                    }
                    //Notificar al usuario saliente
                    $user_puesto=users::find($p->id_usuario);
                    notificar_usuario($user_puesto,"Se han producido cambios en su asignacion de puesto",'emails.asignacion_puesto',$str_notificacion.$str_respuesta,1); 
                    savebitacora($str_notificacion.$str_respuesta,"Usuarios","asignar_temporal","OK");
                }

                foreach($reservas as $res){
                    DB::table('reservas')->where('id_reserva',$res->id_reserva)->delete();
                    $user_puesto=users::find($res->id_usuario);
                    $str_respuesta=' Se ha cancelado su reserva de puesto que tenía para el día entre '.Carbon::parse($res->fec_reserva)->format('d/m/Y');
                    savebitacora(' Se ha cancelado su reserva de puesto '.$puesto->cod_puesto.' al usuario '.$user_puesto->name.' para el dia  '.Carbon::parse($res->fec_reserva)->format('d/m/Y').' por una asignacion temporal de puesto creada por '.Auth::user()->name,"Usuarios","asignar_temporal","OK");
                    notificar_usuario($user_puesto,"Se han producido cambios en su reserva de puesto",'emails.asignacion_puesto',$str_notificacion.$str_respuesta,1); 
                }
                //Si no hay nada mas, creamos la asignacion para el usuario
                DB::table('puestos_asignados')->insert([
                    'id_puesto'=>$puesto->id_puesto,
                    'id_usuario'=>$usuario->id,
                    'fec_desde'=>$f1,
                    'fec_hasta'=>$f2,
                    'id_tipo_asignacion'=>1
                ]);
                savebitacora('Asignado puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango.' '.$str_respuesta,"Usuarios","asignar_temporal","OK");
                //Notificar al usuario entrante
                $str_notificacion=Auth::user()->name.' ha creado una nueva asignacion temporal del puesto '.$puesto->cod_puesto.' ('.$puesto->des_puesto.') para usted';
                notificar_usuario($usuario,"Se le ha asignado un nuevo puesto",'emails.asignacion_puesto',$str_notificacion,1);
                return [
                    'result' => "OK",
                    'title' => "Usuarios",
                    'message' =>'Asignado puesto '.$puesto->cod_puesto.' al usuario '.$usuario->name.' para el intervalo '.$r->rango,
                ];
            }
            
        } catch (Exception $exception) {
            return [
                'result' => "ERROR",
                'error' => 'ERROR: Ocurrio un error asignando temporalmente puestos al usuarios '.$exception->getMessage(),
                //'url' => url('sections')
            ];
        }


    }

    public function miperfil($id){
        validar_acceso_tabla($id,"users");
        if($id!=Auth::user()->id){
            return back();
        }
        $users = users::findOrFail($id);

        return view('users.miperfil', compact('users',));
    }
}

