<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\niveles_acceso;
use App\Models\users;
use App\Models\plantas_usuario;
use Illuminate\Http\Request;
use Exception;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use Auth;

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
        ->join('niveles_acceso','users.cod_nivel', 'niveles_acceso.cod_nivel')
        ->where(function($q){
            if (!isAdmin()) {
                $q->wherein('users.id_cliente',clientes());
            }
        })
        ->get();
        //$usersObjects = users::with('grupo','perfile')->paginate(25);

        return view('users.index', compact('usersObjects'));
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

        return view('users.edit', compact('users','Perfiles'));
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
            $data["nivel_acceso"]=DB::table('niveles_acceso')->where('cod_nivel',$data['cod_nivel'])->first()->val_nivel_acceso;
            $users->update($data);
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
            return redirect()->route('users.users.index');
        } catch (Exception $exception) {
            flash('ERROR: Ocurrio un error al eliminar el usuario '.$id.' '.$exception->getMessage())->error();
            savebitacora('ERROR: Ocurrio un error borrando el usuario '.$users->name.' '.$exception->getMessage() ,"Usuarios","destroy","ERROR");
            return back()->withInput();
                //->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.']);
        }
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


    public function plantas_usuario($id){
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
        return view('users.selector_plantas',compact('puestos','edificios','plantas_usuario','id'));
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

}

