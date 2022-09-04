@php
    $puestos_usu=\App\Http\Controllers\UsersController::mis_puestos(auth()->user()->id);

    $mispuestos= $puestos_usu['mispuestos'];
    $reservas= $puestos_usu['reservas'];
    $asignado_usuario= $puestos_usu['asignado_usuario'];
    $asignado_miperfil= $puestos_usu['asignado_miperfil'];
    $asignado_otroperfil= $puestos_usu['asignado_otroperfil'];
@endphp
@if(isset($mispuestos))
<div class="card mt-3">
    <div class="card-header">
        <div class="card-title"><h2>{{ Carbon\Carbon::now()->locale('es')->isoformat('LLLL') }}</h2></div>
        {{--  ('% %d de %B %Y')  --}}
        
    </div>
    <div class="card-body">
        @foreach($mispuestos as $puesto)
            @php
                $puesto->factor_puesto=5;
                $puesto->factor_letra=1.5/(strlen($puesto->cod_puesto)*0.25);
                if(strlen($puesto->cod_puesto)<4){
                    $puesto->factor_letra=1.8;
                }
                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);
            @endphp
            <div class="row filamipuesto hover-this" data-token="{{ $puesto->token }}" >
                <div class="col-md-1">
                    <div class="text-center rounded mt-2 add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto draggable" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!! nombrepuesto($puesto)." \r\n ".$cuadradito['title'] !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $puesto->id_planta }}" style="height: 3vw ; width: 3vw; {{ $cuadradito['borde'] }}">
                        <span class="h-100 align-middle text-center" style="font-size: 0.7vw; ">
                                {{ nombrepuesto($puesto) }}
                                @include('resources.adornos_iconos_puesto')
                        </span>
                    </div>
                </div>
                
                <div class="col-md-11 text-primary mt-3">
                    <h4 style=""><span style="color: {{ $puesto->color_tipo }}">[<i class="{{ $puesto->icono_tipo }} }}"></i> {!! $puesto->des_tipo_puesto !!}]</span> <i class="fa fa-arrow-right"></i> Tiene el puesto <b>{{ $puesto->cod_puesto }}</b> {!! $cuadradito['title'] !!}</h4>
                </div>
            </div>
            
        @endforeach
    </div>
</div>
@endif

@section('scripts4')
<script>
    $('.filamipuesto').click(function(){
        window.location.href="{{ url('/puestos/vmapa') }}/"+$(this).data('token');
    })
</script>

@endsection
