@extends('layout')




@section('styles')

@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestor de plantas por usuario</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
	<li class="breadcrumb-item">configuracion</li>
	<li class="breadcrumb-item">usuarios</li>
    <li class="breadcrumb-item">plabntas - usuarios</li>
	{{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
</ol>
@endsection

@section('content')
@php
    //dd($incidencias);
@endphp

<div class="row botones_accion">
	<div class="col-md-4">

	</div>
	<div class="col-md-7">
		<br>
	</div>
	<div class="col-md-1 text-right">
		
	</div>
</div>

<div class="row mt-2">
	<div class="col-12">
		<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title">Gestor de plantas por usuarios</h3>
			</div>
			<div class="panel-body">
				<table id="tabla" class="table table-condensed table-hover table-vcenter table-responsive-lg">
					<thead>
						<tr>
							
							<th></th>
                            @foreach ($plantas as $planta)
                                <th data-sortable="true" style="width: 100px" class="text-center tdplanta" data-checked="false" data-planta="{{ $planta->id_planta }}" title="{{ $planta->des_planta }}">{{ acronimo(str_replace('Planta ','',$planta->des_planta)) }}</th>
                            @endforeach
						</tr>
					</thead>
					<tbody>
                        @foreach($usuarios as $u)
                            @php
                                $asignadas=$plantas_users->where('id_usuario',$u->id)->pluck('id_planta')->toArray();
                            @endphp
                            <tr>
                                <td class="tduser" data-usuario="{{ $u->id }}" data-checked="false">{{ $u->name }}</td>
                                @foreach($plantas as $pl)
                                    <td class="text-center" title="{{ $pl->des_planta }} -> {{ $u->name }}" id="pastilla_{{ $u->id }}_{{ $pl->id_planta }}" >
                                        <input type="checkbox" class="form-control  magic-checkbox chkplanta" name="chk_{{ $u->id }}_{{ $pl->id_planta }}"  id="chk_{{ $u->id }}_{{ $pl->id_planta }}" data-usuario="{{ $u->id }}"  data-planta="{{ $pl->id_planta }}" {{ in_array($pl->id_planta,$asignadas)?'checked':'' }}> 
								        <label class="custom-control-label"   for="chk_{{ $u->id }}_{{ $pl->id_planta }}"></label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
	<script>

	$('.configuracion').addClass('active active-sub');
    $('.menu_usuarios').addClass('active active-sub');
	$('.plantas_usuarios').addClass('active-link');
    
    $('.chkplanta').click(function(){
        if($(this).is(':checked')){
            $.get("{{ url('users/addplanta') }}/"+$(this).data('usuario')+"/"+$(this).data('planta'),function(data){
                $('#pastilla'+$(this).data('usuario')+"_"+$(this).data('planta')).css("background-color",'#02c59b');
            })
        } else {
            $.get("{{ url('users/delplanta') }}/"+$(this).data('usuario')+"/"+$(this).data('planta'),function(data){
                $('#pastilla'+$(this).data('usuario')+"_"+$(this).data('planta')).css("background-color",'#eae3b8');
            })
        }
    })

    $('.tdplanta').dblclick(function(){
        $(this).data('checked',!$(this).data('checked'));
        console.log($(this).data('checked'));
        estado=$(this).data('checked');
        $.get("{{ url('users/addtodaplanta') }}/"+estado+"/"+$(this).data('planta'),function(data){
            $('[data-planta='+data.planta+']').prop('checked',data.estado);
        })
    })

    $('.tduser').dblclick(function(){
        $(this).data('checked',!$(this).data('checked'));
        console.log($(this).data('checked'));
        estado=$(this).data('checked');
        $.get("{{ url('users/addtodouser') }}/"+estado+"/"+$(this).data('usuario'),function(data){
            $('[data-usuario='+data.usuario+']').prop('checked',data.estado);
        })
    })
	

	</script>

@endsection