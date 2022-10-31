@php
    use Carbon\Carbon;
    $edificios=$datos->pluck('des_edificio','id_edificio')->unique();
@endphp
<h3>{{ $fecha->isoFormat('dddd D [de] MMMM [de] YYYY') }}</h3>
@foreach($edificios as $id_edificio=>$des_edificio)
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><span class="fs-3 ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $des_edificio }}</span></h3>
    </div>
    <div class="card-body">
        @php
            $plantas=$datos->where('id_edificio',$id_edificio)->wherenotnull('des_planta')->pluck('des_planta','id_planta')->unique();
        @endphp
        @foreach($plantas as $id_planta=>$des_planta)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><span class="fs-3 ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $des_planta }}</span></h3>
            </div>
            <div class="card-body">
                @php
                    $listagrupos=$datos->where('id_planta',$id_planta)->pluck('id_grupo')->unique()->toarray();
                    $grupos=App\Models\grupos::wherein('id_grupo',$listagrupos)->get();
                @endphp
                @foreach($grupos as $grupo)
                    <div class="card b-all">
                        <div class="row">
                            <div class="col-md-2" style="background-color: {{ $grupo->val_color }}">
                                <i class="{{ $grupo->val_icono }}"></i> {{ $grupo->des_grupo }}
                            </div>
                            <div class="col-md-10">
                                @php
                                    $trabajos=$datos->where('id_grupo',$grupo->id_grupo)->where('id_planta',$id_planta)->where('id_edificio',$id_edificio);
                                @endphp
                                 @foreach($trabajos as $item)
                                    <div class="card mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <i class="{{ $item->icono_trabajo }}" style="color: {{ $item->color_trabajo }}"></i> {{  $item->des_trabajo }} 
                                                <i class="fa-solid fa-person-simple add-tooltip " title="{{ $item->num_operarios }} Operarios asignados"></i> {{ $item->num_operarios }}
                                                <i class="fa-regular fa-stopwatch add-tooltip " title="Duracion total {{ $item->val_tiempo }} minuos"></i> {{ $item->val_tiempo }}' 
                                            </div>
                                            <div class="col-md-2 text-center text-info mb-2">
                                                @if($item->fec_inicio!=null)
                                                <i class="fa-solid fa-play"></i> {{ $item->nom_operario_ini }}<br>
                                                    {{ Carbon::parse($item->fec_inicio)->format('d/m/Y H:i') }}
                                                @elseif(session('id_operario')!=null)
                                                    <button class="btn btn-info btn_accion @mobile btn-lg  w-100 @endmobile" data-accion="iniciar" data-id="{{ $item->id_programacion }}"><i class="fa-solid fa-play"></i> Iniciar trabajo</button>
                                                @endif
                                            </div>
                                            <div class="col-md-2 text-success text-center mb-2">
                                                @if($item->fec_fin!=null)
                                                <i class="fa-duotone fa-flag-checkered"></i> {{ $item->nom_operario_fin }}<br>
                                                    {{ Carbon::parse($item->fec_fin)->format('d/m/Y H:i') }}
                                                @elseif(session('id_operario')!=null)
                                                    <button class="btn btn-success btn_accion @mobile btn-lg w-100 @endmobile"data-accion="finalizar"  data-id="{{ $item->id_programacion }}"><i class="fa-duotone fa-flag-checkered"></i> Fin de trabajo</button>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center mb-2">
                                                @if($item->observaciones!=null)
                                                    <i class="fa-duotone fa-comments"></i> {{ substr($item->observaciones,0,80) }}<br>
                                                @elseif(session('id_operario')!=null)
                                                    <button class="btn btn-light btn_comentarios @mobile btn-lg  w-100 @endmobile" data-id="{{ $item->id_programacion }}"><i class="fa-duotone fa-comments"></i> Observaciones</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                 @endforeach
                            </div>
                        </div>
                        
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

<script>
    $('.btn_accion').click(function(){
        $.get('{{ url('trabajos/mistrabajos') }}/'+$(this).data('accion')+'/'+$(this).data('id'),function(data){
            if(!data.error){
                toast_ok(data.title,data.message);
                $('#modal').modal('hide');
                $('#modal').on('hidden.bs.modal', function (e) {
                    $('#modal').remove();
                });
            }else{
                toast_error(data.title,data.error);
            }
        });
        $(this).hide();
    })

</script>