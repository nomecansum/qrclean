<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Auth;
use App\Services\ColectivoService;
use App\Models\avisos;
use Carbon\Carbon;

class AvisosController extends Controller
{
    //
    public function index()
    {
        $avisos = avisos::all();
        return view('avisos.index', compact('avisos'));
    }

    public function edit($id=0){
        if($id==0){
            $aviso = new avisos();
        }else{
            $aviso = avisos::find($id);
        }
        $Clientes =lista_clientes()->pluck('nom_cliente','id_cliente')->all();
        return view('avisos.editor', compact('aviso','id','Clientes'));
    }


    public function save(Request $r)
    {
        // Validar los datos del aviso
        $r->validate([
            'titulo' => 'txt_aviso',
            // Agrega aquí más campos si los necesitas
        ]);
        $fechas=explode(" - ",$r->fechas);
        $fec_inicio=Carbon::parse(adaptar_fecha($fechas[0]));
        $fec_fin=Carbon::parse(adaptar_fecha($fechas[1]));

        // Si id_aviso es 0, crear un nuevo aviso, de lo contrario, actualizar el existente
        if ($r->id_aviso == 0) {
            $aviso = new avisos();
        } else {
            $aviso = avisos::find($r->id);
        }

        dd($aviso);
        // Asignar los valores del formulario al aviso
        $aviso->txt_aviso = $r->txt_aviso;
        $aviso->val_color = $r->val_color;
        $aviso->val_icono = $r->val_icono;
        $aviso->mca_activo = $r->mca_activo??'S';
        $aviso->val_edificios = isset($r->val_edificios)?implode(",", $r->val_edificios):null;
        $aviso->val_plantas = isset($r->val_plantas)?implode(",", $r->val_plantas):null;
        $aviso->val_perfiles = isset($r->val_perfiles)?implode(",", $r->val_perfiles):null;
        $aviso->val_turnos = isset($r->val_turnos)?implode(",", $r->val_turnos):null;
        $aviso->id_cliente=(int)$r->cliente;
        $aviso->fec_inicio=$fec_inicio;
        $aviso->fec_fin=$fec_fin;
        // Asigna aquí más campos si los necesitas

        // Guardar el aviso
        $aviso->save();

        // Redirigir al usuario a la página de avisos con un mensaje de éxito
        return redirect()->route('avisos.index')->with('success', 'Aviso guardado con éxito');
    }

}
