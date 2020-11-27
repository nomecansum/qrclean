@php
    $reservas=DB::table('reservas')
        ->join('puestos','puestos.id_puesto','reservas.id_puesto')
        ->join('users','reservas.id_usuario','users.id')
        ->where(function($q){
            $q->where('fec_reserva',Carbon\Carbon::now()->format('Y-m-d'));
            $q->orwhereraw("'".Carbon\Carbon::now()."' between fec_reserva AND fec_fin_reserva");
        })
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
        $puestos=DB::table('puestos')
            ->select('puestos.*','plantas.*','puestos_asignados.id_perfil','puestos_asignados.id_usuario','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
            ->wherein('puestos.id_puesto',$reservas->pluck('id_puesto')->toArray())
            ->get();
    }
        
    if(isset($asignado_usuario)){
        $puestos=DB::table('puestos')
            ->select('puestos.*','plantas.*','puestos_asignados.id_perfil','puestos_asignados.id_usuario','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->leftjoin('puestos_asignados','puestos.id_puesto','puestos_asignados.id_puesto')
            ->where('puestos_asignados.id_usuario','=',Auth::user()->id)
            ->where(function($q){
                $q->wherenull('puestos_asignados.fec_desde');
                $q->orwhereraw("'".Carbon\Carbon::now()."' between puestos_asignados.fec_desde AND puestos_asignados.fec_hasta");
            })
            ->where('puestos.id_puesto',$asignado_usuario->id_puesto)
            ->get();
    }
@endphp
@if(isset($puestos))
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title"><h2>{{ Carbon\Carbon::now()->locale('es')->isoformat('LLLL') }}</h2></div>
        {{--  ('% %d de %B %Y')  --}}
        
    </div>
    <div class="panel-body">
        @foreach($puestos as $puesto)
            @php
                $puesto->factor_puesto=$puesto->factor_puesto*2??6;
                $puesto->factor_letra=$puesto->factor_letra*2??1.3;
                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);
            @endphp
            <div class="row">
                <div class="col-md-1">
                    <div class="text-center rounded mt-2 add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto draggable" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!! $puesto->des_puesto." \r\n ".$cuadradito['title'] !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $puesto->id_planta }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw; {{ $cuadradito['borde'] }}">
                        <span class="h-100 align-middle text-center" style="font-size: {{ $puesto->factor_letra }}vw; ">
                                {{ $puesto->cod_puesto }}
                                @include('resources.adornos_iconos_puesto')
                        </span>
                    </div>
                </div>
                
                <div class="col-md-8 text-primary mt-3">
                    <h4>Tiene el puesto <b>{{ $puesto->des_puesto }}</b> {!! $cuadradito['title'] !!}</h4>
                </div>
            </div>
            
        @endforeach
    </div>
</div>
@endif