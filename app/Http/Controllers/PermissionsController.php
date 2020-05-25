<?php

namespace App\Http\Controllers;
use DB;
use Validator;
use Auth;

use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    //
	public function profiles()
	{
		$nivel_acceso = \DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->val_nivel_acceso;
		$niveles =
		  DB::table('niveles_acceso')
			->where('val_nivel_acceso','<=',$nivel_acceso)
			->get();
		return view('permisos.profiles',compact('niveles','nivel_acceso'));
	}
	public function profilesEdit($id)
	{
	    $nivel_acceso = \DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->num_nivel_acceso)->first()->val_nivel_acceso;
        $niveles =
          DB::table('niveles_acceso')
            ->where('val_nivel_acceso','<=',$nivel_acceso)
            ->get();
		$n = DB::table('niveles_acceso')->where('cod_nivel',$id)->first();
		return view('permisos.profiles',compact('n', 'niveles', 'nivel_acceso'));
	}
	public function profilesSave(Request $r)
	{
		//dd($r);
		if ($r->id!=0) {
			DB::table('niveles_acceso')->where('cod_nivel',$r->id)->update(
				[
				    'des_nivel_acceso' => $r->des_nivel_acceso,
				    'val_nivel_acceso' => $r->num_nivel_acceso
				]
			);

			$n = DB::table('niveles_acceso')->where('cod_nivel',$r->id)->first();
		}
		else
		{
			$n = DB::table('niveles_acceso')->insert(
				[
					'val_nivel_acceso' => $r->num_nivel_acceso,
					'des_nivel_acceso' => $r->des_nivel_acceso
				]
			);
			$n = DB::table('niveles_acceso')->where('des_nivel_acceso',$r->des_nivel_acceso)->orderby('cod_nivel','desc')->first();
		}


		if($r->hereda_de && $r->hereda_de!=''){
			DB::table('secciones_perfiles')
			->where('id_perfil',$n->cod_nivel)
			->delete();

			$padre=DB::table('secciones_perfiles')->where('id_perfil',$r->hereda_de)->get();
			foreach($padre as $p){
				DB::table('secciones_perfiles')->insert([
					'id_seccion' => $p->id_seccion,
					'id_perfil' => $n->cod_nivel,
					'mca_read' => $p->mca_read,
					'mca_write' => $p->mca_write,
					'mca_create' => $p->mca_create,
					'mca_delete' => $p->mca_delete,
				]);
			}
		}
        else
        {
            DB::table('secciones_perfiles')
            ->where('id_perfil',$n->cod_nivel)
            ->delete();

            $padre = DB::table('secciones_perfiles')->where('id_perfil',1)->get();
            foreach($padre as $p){
                DB::table('secciones_perfiles')->insert([
                    'id_seccion' => $p->id_seccion,
                    'id_perfil' => $n->cod_nivel,
                    'mca_read' => $p->mca_read,
                    'mca_write' => $p->mca_write,
                    'mca_create' => $p->mca_create,
                    'mca_delete' => $p->mca_delete,
                ]);
            }
        }
		savebitacora("Creaado perfil ".$r->des_nivel_acceso,Auth::user()->id,"Secciones","OK");
		return [
            'title' => "Perfiles",
            'message' => "Perfil ".$r->des_nivel_acceso.': Guardado',
            'url' => url('profiles')
        ];
	}
	public function profilesDelete($id)
	{
		savebitacora("Eliminado perfil ".$id." ".DB::table('niveles_acceso')->where('cod_nivel',$id)->value('des_nivel_acceso'),Auth::user()->id,"Perfiles","OK");
		DB::table('niveles_acceso')->where('cod_nivel',$id)->delete();
		flash('Perfil '.$id.' Borrado')->success();
		return redirect('profiles');
	}
	public function sections()
	{
		$secciones = DB::table('secciones')
		->where(function($q){
			if (!isAdmin()) {
				$q->wherein('des_seccion',array_column(session('P'), 'des_seccion'));
			}
		})
		->get();
		$tipos = $secciones->pluck('val_tipo', 'val_tipo')->toArray();
		$grupos = DB::table('secciones')->select('des_grupo','icono')->distinct()->get();

		return view('permisos.sections',compact('secciones','grupos','tipos'));
	}
	public function sectionsEdit($id)
	{
	    $secciones = DB::table('secciones')
        ->where(function($q){
            if (!isAdmin()) {
                $q->wherein('des_seccion',array_column(session('P'), 'des_seccion'));
            }
        })
        ->get();
        $tipos = $secciones->pluck('val_tipo', 'val_tipo')->toArray();
        $grupos = DB::table('secciones')->select('des_grupo','icono')->distinct()->get();
		$s = DB::table('secciones')->where('cod_seccion',$id)->first();
		return view('permisos.sections',compact('s','grupos','tipos','secciones'));
	}
	public function sectionsSave(Request $r)
	{
		//dd($r);
		$grupos = DB::table('secciones')->select('des_grupo','icono')->distinct()->get()->pluck('icono', 'des_grupo');
		error_log(json_encode($grupos));
		//dd($r);
		if ($r->id!=0) {
			$n = DB::table('secciones')->where('cod_seccion',$r->id)->update(
				['des_seccion' => $r->des_seccion,
				'val_tipo' => $r->val_tipo,
				'des_grupo' => $r->des_grupo,
				'icono' => $grupos[$r->des_grupo]
				]);
		}else{
			$n = DB::table('secciones')->insert(
				[
					'des_seccion' => $r->des_seccion,
					'val_tipo' => $r->val_tipo,
					'des_grupo' => $r->des_grupo,
					'icono' => $grupos[$r->des_grupo]
				]);
		}
		savebitacora("Actualizado seccion ".$r->des_seccion,Auth::user()->id,"Secciones","OK");
		return [
            'title' => "Secciones",
            'message' => "Seccion ".$r->des_seccion." guardada",
            'url' => url('sections')
        ];
	}
	public function sectionsDelete($id)
	{
		savebitacora("Eliminado seccion ".$id." ".DB::table('secciones')->where('cod_seccion',$id)->value('des_seccion'),Auth::user()->id,"Secciones","OK");
		$s = DB::table('secciones')->where('cod_seccion',$id);
		$s->delete();
		return [
            'title' => "Secciones",
            'message' => "Seccion ".$id." borrada",
            'url' => url('sections')
        ];
	}
	public function profilePermissions()
	{
		$permisos = DB::table('secciones')
			->select('secciones_perfiles.id_seccion',
					'secciones_perfiles.id_perfil',
					'secciones_perfiles.mca_read',
					'secciones_perfiles.mca_write',
					'secciones_perfiles.mca_create',
					'secciones_perfiles.mca_delete')
			->join('secciones_perfiles', 'secciones.cod_seccion', '=', 'secciones_perfiles.id_seccion')
			->join('niveles_acceso', 'secciones_perfiles.id_perfil', '=', 'niveles_acceso.cod_nivel')
			->get();

		$secciones = DB::table('secciones')
				->orderby('des_grupo')
				->orderby('des_seccion')
				->get();

		$nivel_acceso = \DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->val_nivel_acceso;
		$niveles = DB::table('niveles_acceso')
				->where('val_nivel_acceso','<=',$nivel_acceso)
				->get();

		$grupos = DB::table('secciones')
			->selectraw('distinct(des_grupo) as des_grupo, icono')
			->orderby('des_grupo')
			->get();


		return view('permisos.profilePermissions',compact('permisos','secciones','niveles','grupos'));
	}

    public function getProfiles(Request $r)
    {
        $nivel_acceso = \DB::table('niveles_acceso')->where('cod_nivel',Auth::user()->cod_nivel)->first()->val_nivel_acceso;
        $niveles = DB::table('niveles_acceso')
                ->where('val_nivel_acceso', '<=', $nivel_acceso)
                ->where('cod_cliente', $r->cli)
                ->get();
        return $niveles;
    }


	public function addPermissions(Request $r)
	{
		if ($r->type == "R") {$type = "mca_read";}
		if ($r->type == "W") {$type = "mca_write";}
		if ($r->type == "C") {$type = "mca_create";}
		if ($r->type == "D") {$type = "mca_delete";}
		if (DB::table('secciones_perfiles')->where(['id_seccion' => $r->section,'id_perfil' => $r->level])->exists()) {
			DB::table('secciones_perfiles')->where(['id_seccion' => $r->section,'id_perfil' => $r->level])->update([
				$type => 1
			]);
		}else{
			DB::table('secciones_perfiles')->insert([
				'id_seccion' => $r->section,
				'id_perfil' => $r->level,
				$type => 1
			]);
		}
		savebitacora("Añadidos permisos para seccion ".$r->section." y perfil ".$r->level,Auth::user()->id,"Permisos","OK");
	}
	public function removePermissions(Request $r)
	{
		if ($r->type == "R") {$type = "mca_read";}
		if ($r->type == "W") {$type = "mca_write";}
		if ($r->type == "C") {$type = "mca_create";}
		if ($r->type == "D") {$type = "mca_delete";}
		savebitacora("Eliminados permisos para seccion ".$r->section." y perfil ".$r->level,Auth::user()->id,"Permisos","OK");
		DB::table('secciones_perfiles')->where(['id_seccion' => $r->section,'id_perfil' => $r->level])->update([
			$type => NULL
		]);
	}

	public function addPermissions_user(Request $r)
	{
		if ($r->type == "R") {$type = "mca_read";}
		if ($r->type == "W") {$type = "mca_write";}
		if ($r->type == "C") {$type = "mca_create";}
		if ($r->type == "D") {$type = "mca_delete";}
		if (DB::table('permisos_usuarios')->where(['id_seccion' => $r->section,'cod_usuario' => $r->level])->exists()) {
			DB::table('permisos_usuarios')->where(['id_seccion' => $r->section,'cod_usuario' => $r->level])->update([
				$type => 1
			]);
		}else{
			DB::table('permisos_usuarios')->insert([
				'id_seccion' => $r->section,
				'cod_usuario' => $r->level,
				$type => 1
			]);
		}
		savebitacora("Añadidos permisos para seccion ".$r->section." y usuario ".$r->level,Auth::user()->id,"Permisos","OK");
	}
	public function removePermissions_user(Request $r)
	{
		if ($r->type == "R") {$type = "mca_read";}
		if ($r->type == "W") {$type = "mca_write";}
		if ($r->type == "C") {$type = "mca_create";}
		if ($r->type == "D") {$type = "mca_delete";}
		savebitacora("permisos_usuarios permisos para seccion ".$r->section." y perfil ".$r->level,Auth::user()->id,"Permisos","OK");
		DB::table('permisos_usuarios')->where(['id_seccion' => $r->section,'cod_usuario' => $r->level])->update([
			$type => NULL
		]);
	}
}
