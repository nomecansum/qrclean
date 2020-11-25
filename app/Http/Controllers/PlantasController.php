<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\clientes;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\puestos;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlantasController extends Controller
{

    /**
     * Display a listing of the plantas.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $plantasObjects=DB::table('plantas')
            ->join('clientes','clientes.id_cliente','plantas.id_cliente')
            ->join('edificios','edificios.id_edificio','plantas.id_edificio')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->orderby('plantas.id_cliente')
            ->orderby('plantas.id_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.id_planta')
            ->get();

        return view('plantas.index', compact('plantasObjects'));
    }

    /**
     * Show the form for creating a new plantas.
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        $Clientes = clientes::where(function($q){
                if (!isAdmin()) {
                    $q->where('id_cliente',Auth::user()->id_cliente);
                }
            })
            ->pluck('nom_cliente','id_cliente')
            ->all();
        $Edificios = edificios::where(function($q){
                if (!isAdmin()) {
                    $q->where('id_cliente',Auth::user()->id_cliente);
                }
            })
            ->pluck('des_edificio','id_edificio')
            ->all();
        
        return view('plantas.create', compact('Clientes','Edificios'));
    }

    /**
     * Store a new plantas in the storage.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $img_planta="";
        $data = $this->getData($request);
        try {
            if ($request->hasFile('img_plano')) {
                $file = $request->file('img_plano');
                $path = config('app.ruta_public').'/img/plantas/';
                $img_planta = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_planta);
                //$file->move($path,$img_planta);
                $data['img_plano']=$img_planta;
            }
            plantas::create($data);
            savebitacora('Planta '.$request->des_planta. ' creada',"Plantas","store","OK");
            return [
                'title' => "Plantas",
                'message' => 'Planta '.$request->des_planta. ' creada',
                'url' => url('plantas')
            ];
        } catch (Exception $exception) {

            return [
                'title' => "Plantas",
                'error' => 'ERROR: Ocurrio un error creando la planta '.$request->des_planta.' '.mensaje_excepcion($exception),
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
        $plantas = plantas::findOrFail($id);
        $Clientes = clientes::pluck('nom_cliente','id_cliente')->all();
        $Edificios = edificios::pluck('des_edificio','id_edificio')->all();

        return view('plantas.edit', compact('plantas','Clientes','Edificios'));
    }

    /**
     * Update the specified plantas in the storage.
     *
     * @param int $id
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse | Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        $img_planta = "";
        $data = $this->getData($request);
        try {
            validar_acceso_tabla($id,"plantas");
            if ($request->hasFile('img_plano')) {
                $file = $request->file('img_plano');
                $path = config('app.ruta_public').'/img/plantas/';
                $img_planta = uniqid().rand(000000,999999).'.'.$file->getClientOriginalExtension();
                Storage::disk(config('app.img_disk'))->putFileAs($path,$file,$img_planta);
                $data['img_plano']=$img_planta;
            } 
            $plantas = plantas::findOrFail($id);
            $plantas->update($data);
            savebitacora('Planta '.$request->des_planta. ' actualizada',"Plantas","update","OK");
            return [
                'title' => "Plantas",
                'message' => 'Planta '.$request->des_planta. ' actualizada',
                'url' => url('plantas')
            ];
        } catch (Exception $exception) {

            return [
                'title' => "Plantas",
                'error' => 'ERROR: Ocurrio un error actualizando la planta '.$request->des_planta.' '.mensaje_excepcion($exception),
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
            validar_acceso_tabla($id,"plantas");
            $plantas = plantas::findOrFail($id);
            $plantas->delete();
            savebitacora('Planta '.$plantas->des_planta. ' borrada',"Plantas","destroy","OK");
            return redirect()->route('plantas.plantas.index')
                ->with('success_message', 'Planta borrada.');
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
                'des_planta' => 'nullable|string|min:0|max:50',
            'id_cliente' => 'nullable',
            'id_edificio' => 'nullable', 
            'num_orden' => 'required', 
        ];
        
        $data = $request->validate($rules);


        return $data;
    }

    public function puestos($id)
    {
        validar_acceso_tabla($id,'plantas');
        $plantas = plantas::findOrFail($id);
        $puestos= DB::Table('puestos')
            ->select('puestos.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','plantas.factor_puesto','plantas.factor_letra','puestos.val_color as hex_color')
            ->join('plantas','plantas.id_planta','puestos.id_planta')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->where('puestos.id_planta',$id)
            ->get();
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->where(function($q){
                $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
            })
            ->get();

        return view('plantas.editor_puestos', compact('plantas','puestos','reservas'));
    }

    public function puestos_save(Request $r){
        validar_acceso_tabla($r->id_planta,'plantas');
        $planta = plantas::findOrFail($r->id_planta);
        $planta->posiciones=$r->json;
        $planta->factor_puesto=$r->factor_puesto;
        $planta->factor_letra=$r->factor_letra;
        $planta->save();
        savebitacora('Distribucion de puestos en  '.$planta->des_planta. ' actualizada',"Plantas","puestos_save","OK");
        return [
            'title' => "Plantas",
            'message' => 'Distribucion de puestos en  '.$planta->des_planta. ' actualizada',
            'url' => url('plantas')
        ];
    }

}
