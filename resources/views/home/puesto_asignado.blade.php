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
                if(isMobile()){
                    if($puesto->factor_puestow<3.5){
                        $puesto->factor_puestow=15;
                        $puesto->factor_puestoh=15;
                        $puesto->factor_letra=2.8;
                    } else {
                        //En  mosaico los queremos curadrados siempre
                        $puesto->factor_puestow=$puesto->factor_puestow*4;
                        $puesto->factor_puestoh=$puesto->factor_puestow*4;
                        $puesto->factor_letra=$puesto->factor_letra*4;
                    }
                } else if($puesto->factor_puestow<3.5){
                    $puesto->factor_puestow=3.7;
                    $puesto->factor_puestoh=3.7;
                    $puesto->factor_letra=0.8;
                }
                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);
            @endphp
            <div class="d-flex filamipuesto hover-this flex-row" data-token="{{ $puesto->token }}" >
                {{-- <div class="col-md-1">
                    <div class="text-center rounded mt-2 add-tooltip puesto_parent bg-{{ $puesto->color_estado }} align-middle flpuesto draggable" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!! nombrepuesto($puesto)." \r\n ".$cuadradito['title'] !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $puesto->id_planta }}" style="height: 3.5vw ; width: 3.5vw; {{ $cuadradito['borde'] }}">
                        <span class="h-100 align-middle text-center puesto_child" style="font-size: 1vw; ">
                                {{ nombrepuesto($puesto) }}
                                @include('resources.adornos_iconos_puesto')
                        </span>
                    </div>
                </div> --}}
                <div class="text-center rounded add-tooltip flpuesto puesto_parent  p-2 mr-2 mb-2 " id="puesto{{ $puesto->id_puesto }}" title="{!! strip_tags( nombrepuesto($puesto)." \r\n ".$cuadradito['title']) !!}  @if(config('app.env')=='local')[#{{ $puesto->id_puesto }}]@endif" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" style="height: {{ $puesto->factor_puestoh }}vw ; width: {{ $puesto->factor_puestow }}vw; background-color: {{ $cuadradito['color'] }}; color: {{ $cuadradito['font_color'] }}; {{ $cuadradito['borde'] }}">
                    <div class="puesto_child " style="font-size: {{ $puesto->factor_letra }}vw; color: {{ $cuadradito['font_color'] }};">{{ nombrepuesto($puesto) }}
                        
                    </div>
                    @include('resources.adornos_iconos_puesto')
                </div>
                
                <div class="text-primary mt-3">
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
