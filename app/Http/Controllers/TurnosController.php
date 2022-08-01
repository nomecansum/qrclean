<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\turnos;
use Validator;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TurnosController extends Controller
{
    //
    function index(){

        $turnos=DB::table('turnos')
        ->join('clientes','turnos.id_cliente','clientes.id_cliente')
        ->where(function($q){
            $q->where('turnos.id_cliente',Auth::user()->id_cliente);
        })
        ->get();

        return view('turnos.index',compact('turnos'));
    }

    public function edit($id)
    {
        if ($id==0){
            $c=new turnos();
            $c->id_cliente=Auth::user()->id_cliente;
        } else {
            $c = DB::table('clientes')
            ->whereNull('clientes.fec_borrado')
            ->where('id_cliente',$id)
            ->where(function($q){
                if (!isAdmin()){
                    $q->WhereIn('clientes.id_cliente',clientes());
                }
            })
            ->first();
        }
        $config=DB::table('config_clientes')->where('id_cliente',$id)->first();
        if(!isset($config) && $id!=0){
            $config= new config_clientes;
            $config->id_cliente=$id;
            $config->save();
        }

    	return view('customers.create',compact('c','config'));
    }
}
