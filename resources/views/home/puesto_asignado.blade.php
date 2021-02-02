@php
    $reservas=DB::table('reservas')
        ->join('puestos','puestos.id_puesto','reservas.id_puesto')
        ->join('users','reservas.id_usuario','users.id')
        ->wheredate('fec_reserva',Carbon\Carbon::now()->format('Y-m-d'))
        ->where('reservas.id_usuario',Auth::user()->id)
        ->get();
    
    $asignado_usuario=DB::table('puestos_asignados')
        ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
        ->join('users','users.id','puestos_asignados.id_usuario')    
        ->where('puestos_asignados.id_usuario','=',Auth::user()->id)
        ->where(function($q){
            $q->wherenull('fec_desde');
            $q->orwhereraw("'".Carbon\Carbon::now()."' between fec_desde AND fec_hasta");
        })
        ->first();
    $asignado_miperfil=[];
    $asignado_otroperfil=[];

    if(!$reservas->isempty()){
        $mispuestos=DB::table('puestos')
            ->select('puestos.*','plantas.*','puestos_asignados.id_perfil','puestos_asignados.id_usuario','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color','puestos_tipos.val_icono as icono_tipo', 'puestos_tipos.val_color as color_tipo','puestos_tipos.des_tipo_puesto')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
            ->wherein('puestos.id_puesto',$reservas->pluck('id_puesto')->toArray())
            ->get();
    }

    if(isset($asignado_usuario)){
        $misasignados=DB::table('puestos')
            ->select('puestos.*','plantas.*','puestos_asignados.id_perfil','puestos_asignados.id_usuario','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color','puestos_tipos.val_icono as icono_tipo', 'puestos_tipos.val_color as color_tipo','puestos_tipos.des_tipo_puesto')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
            ->where('puestos_asignados.id_usuario','=',Auth::user()->id)
            ->where(function($q){
                $q->wherenull('puestos_asignados.fec_desde');
                $q->orwhereraw("'".Carbon\Carbon::now()."' between puestos_asignados.fec_desde AND puestos_asignados.fec_hasta");
            })
            ->where('puestos.id_puesto',$asignado_usuario->id_puesto)
            ->get();
            if(isset($mispuestos)){
                $mispuestos=$mispuestos->merge($misasignados);
            } else {
                $mispuestos=$misasignados;
            }
    }
@endphp
@if(isset($mispuestos))
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title"><h2>{{ Carbon\Carbon::now()->locale('es')->isoformat('LLLL') }}</h2></div>
        {{--  ('% %d de %B %Y')  --}}
        
    </div>
    <div class="panel-body">
        @foreach($mispuestos as $puesto)
            @php
                $puesto->factor_puesto=5;
                $puesto->factor_letra=1.5/(strlen($puesto->cod_puesto)*0.25);
                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);
            @endphp
            <div class="row filamipuesto hover-this" data-token="{{ $puesto->token }}" >
                <div class="col-md-1">
                    <div class="text-center rounded mt-2 add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto draggable" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!! $puesto->des_puesto." \r\n ".$cuadradito['title'] !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $puesto->id_planta }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw; {{ $cuadradito['borde'] }}">
                        <span class="h-100 align-middle text-center" style="font-size: {{ $puesto->factor_letra }}vw; ">
                                {{ $puesto->cod_puesto }}
                                @include('resources.adornos_iconos_puesto')
                        </span>
                    </div>
                </div>
                
                <div class="col-md-11 text-primary mt-3">
                    <h4 style=""><span style="color: {{ $puesto->color_tipo }}">[<i class="{{ $puesto->icono_tipo }} }}"></i> {!! $puesto->des_tipo_puesto !!}]</span> <i class="fa fa-arrow-right"></i> Tiene el puesto <b>{{ $puesto->des_puesto }}</b> {!! $cuadradito['title'] !!}</h4>
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
