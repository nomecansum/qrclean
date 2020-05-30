<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\bitacora;
use Illuminate\Http\Request;
use Exception;
use DB;
use Carbon\Carbon;

class BitacorasController extends Controller
{

    /**
     * Display a listing of the bitacoras.
     *
     * @return Illuminate\View\View
     */
    public function index()
    {
        $bitacoras = DB::table('bitacora')
        ->join('users','bitacora.id_usuario','users.id')
        ->join('clientes','clientes.id_cliente','users.id_cliente')
        ->paginate(50);

        $todas_bitacoras = DB::table('bitacora')
        ->join('users','bitacora.id_usuario','users.id')
        ->join('clientes','clientes.id_cliente','users.id_cliente')
        ->get();

        $usuarios=$todas_bitacoras->pluck('name','id_usuario')->unique();

        $modulos=$todas_bitacoras->pluck('id_modulo')->unique();

        return view('bitacoras.index', compact('bitacoras','usuarios','modulos'));
    }
    
    public function search(Request $r)
    {
           // dd($r->all());
            $todas_bitacoras = DB::table('bitacora')
            ->join('users','bitacora.id_usuario','users.id')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->get();

            $usuarios=$todas_bitacoras->pluck('name','id_usuario')->unique();

            $modulos=$todas_bitacoras->pluck('id_modulo')->unique();

            //D($r->modulos);
           
            if (isset($r->fechas) && $r->fechas[0]!=null && $r->fechas[1]!=null){
                $fechas=explode(" - ",$r->fechas);
                $fechas[0]=Carbon::parse($fechas[0]);
                $fechas[1]=Carbon::parse($fechas[1]);
                //dd($fechas);
            } else {
                $fechas=null;
            }
            //dd($fechas);
            //dd($fechas[0].' '.$fechas[1]);
            $bitacoras=DB::table('bitacora')
            ->join('users','bitacora.id_usuario','users.id')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->when($r->tipo_log, function($query) use ($r) {
               return  $query->where('status', $r->tipo_log);
              })
            ->when($r->usuario, function($query) use ($r) {
                return  $query->where('id_usuario', $r->usuario);
               })
            ->when($fechas, function($query) use ($fechas) {
                return  $query->whereBetween('fecha', [$fechas[0],$fechas[1]]);
               })
            ->when($r->modulos, function($query) use ($r) {
                return  $query->whereIn('id_modulo', $r->modulos);
                })
            ->when($r->texto && $r->texto!='', function($query) use ($r) {
                return  $query->whereraw("UPPER(accion) like '%".strtoupper($r->texto)."%'");
                })
            ->orderby('fecha','desc')
            ->paginate(50);
            return view('bitacoras.index', compact('bitacoras'), compact('r','usuarios','modulos'));
            try {     } catch (Exception $exception) {
            flash('ERROR: Ocurrio un error al hacer la busqueda '.$exception->getMessage())->error();
            return back()->withInput();
        }        
    }

    protected function getData(Request $request)
    {
        $rules = [
                'id_usuario' => 'required|string|min:1|max:100',
            'id_modulo' => 'required|string|min:1|max:50',
            'accion' => 'required|string|min:1|max:200',
            'status' => 'required|string|min:1|max:10',
            'fecha' => 'required|date_format:j/n/Y g:i A', 
        ];

        
        $data = $request->validate($rules);




        return $data;
    }

}
