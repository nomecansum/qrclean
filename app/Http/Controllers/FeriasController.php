<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\clientes;
use App\Models\ferias;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FeriasController extends Controller
{
    //
    /**
     * Display a listing of the plantas.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $ferias=DB::table('ferias')
            ->join('clientes','clientes.id_cliente','ferias.id_cliente')
            ->where(function($q){
                $q->where('ferias.id_cliente',Auth::user()->id_cliente);
            })
            ->orderby('ferias.fec_feria','desc')
            ->get();

        return view('ferias.index', compact('ferias'));
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
        
        return view('ferias.create', compact('Clientes'));
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
        $data = $this->getData($request);
        try {
            
            ferias::create($data);
            savebitacora('Feria '.$request->des_feria. ' creada',"Ferias","store","OK");
            return [
                'title' => "Ferias",
                'message' => 'Feria '.$request->des_feria. ' creada',
                'url' => url('ferias')
            ];
        } catch (Exception $exception) {

            return [
                'title' => "Ferias",
                'error' => 'ERROR: Ocurrio un error creando la feria '.$request->des_feria.' '.mensaje_excepcion($exception),
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
        $ferias = ferias::findOrFail($id);
        $Clientes = clientes::where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            }
        })
        ->pluck('nom_cliente','id_cliente')
        ->all();


        return view('ferias.edit', compact('ferias','Clientes'));
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

        $data = $this->getData($request);
        
            validar_acceso_tabla($id,"ferias");
            
            $ferias = ferias::findOrFail($id);
            $ferias->update($data);
            savebitacora('Feria '.$request->des_feria. ' actualizada',"Ferias","update","OK");
            return [
                'title' => "Ferias",
                'message' => 'Feria '.$request->des_feria. ' actualizada',
                'url' => url('ferias')
            ];
            try {} catch (Exception $exception) {

            return [
                'title' => "Ferias",
                'error' => 'ERROR: Ocurrio un error actualizando la feria '.$request->des_feria.' '.mensaje_excepcion($exception),
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
                'des_feria' => 'nullable|string|min:0|max:50',
            'id_cliente' => 'nullable',
            'fec_fecia' => 'date',
        ];
        
        $data = $request->validate($rules);


        return $data;
    }
}
