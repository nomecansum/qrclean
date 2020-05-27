<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\clientes;
use App\Models\edificios;
use App\Models\plantas;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        try {
            $data = $this->getData($request);
            
            plantas::create($data);

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
        try {
            validar_acceso_tabla($id,"plantas");
            $data = $this->getData($request);
            
            $plantas = plantas::findOrFail($id);
            $plantas->update($data);

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
        ];
        
        $data = $request->validate($rules);


        return $data;
    }

}