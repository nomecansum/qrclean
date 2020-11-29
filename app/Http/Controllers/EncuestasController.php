<?php

namespace App\Http\Controllers;
use App\Models\clientes;
use App\Models\encuestas;
use App\Models\puestos;
use App\Models\niveles_acceso;
use Illuminate\Http\Request;
use Exception;
use Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EncuestasController extends Controller
{
    public function index()
    {
        $encuestas=DB::table('encuestas')
            ->join('clientes','clientes.id_cliente','encuestas.id_cliente')
            ->join('encuestas_tipos','encuestas_tipos.id_tipo_encuesta','encuestas.id_tipo_encuesta')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('encuestas.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->orderby('encuestas.id_cliente')
            ->orderby('encuestas.id_encuesta')
            ->get();

        return view('encuestas.index', compact('encuestas'));
    }

    /**
     * Show the form for creating a new plantas.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        
        $encuesta=new encuestas();
        $clientes = clientes::where(function($q){
                if (!isAdmin()) {
                    $q->where('id_cliente',Auth::user()->id_cliente);
                }
            })
            ->pluck('nom_cliente','id_cliente')
            ->all();
        $tipos = DB::table('encuestas_tipos')->get();
        $encuesta->id_tipo_encuesta=$tipos->first()->id_tipo_encuesta;
        $encuesta->des_tipo_encuesta=$tipos->first()->des_tipo_encuesta;
        $encuesta->img_tipo=$tipos->first()->img_tipo;
        $encuesta->id_encuesta=0;
        $encuesta->token=Str::random(64);
        $perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->wherein('id_cliente',[Auth::user()->id_cliente,1])->get();
        
        return view('encuestas.edit', compact('encuesta','clientes','tipos','perfiles'));
    }

    /**
     * Store a new plantas in the storage.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function store(Request $r)
    {
        $fechas=explode(" - ",$r->fechas);
        $fechas[0]=Carbon::parse(adaptar_fecha($fechas[0]));
        $fechas[1]=Carbon::parse(adaptar_fecha($fechas[1]));

        if(isset($r->list_perfiles)){
            $r->list_perfiles=implode(",",$r->list_perfiles);
        }

        $data = $this->getData($r);
        try {
            
            encuestas::create($data);
            savebitacora('Encuesta '.$r->titulo. ' creada',"Encuestas","store","OK");
            return [
                'title' => "Encuestas",
                'message' => 'Encuesta '.$r->titulo. ' creada',
                'url' => url('encuestas')
            ];
        } catch (Exception $exception) {

            return [
                'title' => "Encuestas",
                'error' => 'ERROR: Ocurrio un error creando la encuesta '.$r->titulo.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }
    }

    /**
     * Show the form for editing the specified plantas.
     *
     * @param int $id
     *
     * @return Illuminate\View\View
     */
    public function edit($id)
    {
        $encuesta=DB::table('encuestas')
            ->join('clientes','clientes.id_cliente','encuestas.id_cliente')
            ->join('encuestas_tipos','encuestas_tipos.id_tipo_encuesta','encuestas.id_tipo_encuesta')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('encuestas.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where('id_encuesta',$id)
            ->first();
        $tipos = DB::table('encuestas_tipos')->get();
        $clientes = clientes::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
                }
            })
            ->pluck('nom_cliente','id_cliente')
            ->all();
        $perfiles = niveles_acceso::where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->wherein('id_cliente',[Auth::user()->id_cliente,1])->get();

        return view('encuestas.edit', compact('encuesta','tipos','clientes','perfiles'));
    }

    /**
     * Update the specified plantas in the storage.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function update($id, Request $r)
    {
        $data = $this->getData($request);
        try {
            validar_acceso_tabla($id,"encuestas");
            $encuesta = encuestas::findOrFail($id);
            $encuesta->update($data);
            savebitacora('Encuesta '.$r->titulo. ' actualizada',"Encuestas","update","OK");
            return [
                'title' => "Encuestas",
                'message' => 'Encuesta '.$r->titulo. ' actualizada',
                'url' => url('encuestas')
            ];
        } catch (Exception $exception) {

            return [
                'title' => "Encuestas",
                'error' => 'ERROR: Ocurrio un error actualizando la encuesta '.$r->titulo.' '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }        
    }

    /**
     * Remove the specified plantas from the storage.
     *
     * @param int $id
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        try {
            validar_acceso_tabla($id,"encuestas");
            $encuesta = encuestas::findOrFail($id);
            $encuesta->delete();
            savebitacora('Planta '.$encuesta->des_planta. ' borrada',"Encuestas","destroy","OK");
            return redirect()->route('encuestas.index')
                ->with('success_message', 'Encuesta borrada.');
        } catch (Exception $exception) {

            return back()->withInput()
                ->withErrors(['unexpected_error' => 'Unexpected error occurred while trying to process your request.'.mensaje_excepcion($exception)]);
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
                'titulo' => 'required|string|min:0|max:300',
            'id_cliente' => 'required',
            'id_tipo_encuesta' => 'required'
        ];
        $data = $request->validate($rules);
        return $data;
    }


}
