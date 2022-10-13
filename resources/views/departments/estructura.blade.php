{{-- @php
	global $empleados;
	$empleados = \DB::table('cur_supervisor_empleados')->where('cod_empleado_supervisor', $emp->cod_empleado)->get();

	function checkSupervisor2($emp){
		global $empleados;
		foreach($empleados as $e){
			if($e->cod_empleado==$emp){
				return true;
			}
		}
		return false;
	}
@endphp --}}
<style type="text/css">
    .round{
        line-height: 20px;
    }
    .noborders td {
        border:0;
    }
</style>
<ul id="listas-usuarios">

    @foreach (DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $cen)
        @php
            $cli = DB::table('clientes')->where('id_cliente', $cen->id_cliente)->first()
        @endphp
        <br>
        <h4><span style="background-color: #fffd8e"><u><b>{{session('CL')['nom_cliente']}}</b></u></span></h4>
		<li class="clickable" style="font-size:20px">
			<label style="font-size: 22px;">
				<i class="mdi mdi-minus-box"></i>
				<i class="mdi mdi-store icon-box" style="color:cornflowerblue"></i>
				<input type="checkbox" data-name="cod_centro[]" class="chkcentro" data-centro="{{ $cen->id_edificio }}"  value="{{$cen->id_edificio}}"> {{$cen->des_edificio}}
			</label>
			<ul>
				@php
					$departamentos=DB::table('departamentos')
						->select('departamentos.cod_departamento','departamentos.nom_departamento')
                        ->selectraw($cen->id_edificio.' as id_edificio')
						->wherenull('departamentos.cod_departamento_padre')
						// ->whereraw("cod_departamento in (select distinct id_departamento as cod_departamento from users where id_edificio = ".$cen->id_edificio.")")
						->orderBy('nom_departamento','asc')
						->get();
				@endphp
				@each('departments.fill_fila_departamento_estructura', $departamentos, 'dep','departments.fill_fila_departamento_final')
			</ul>
		</li>
	@endforeach
</ul>