
@php
    // dd($pl);   
@endphp

@if(isset($pl->img_plano))
{{--  {!! json_encode($pl->posiciones) !!}  --}}

    <div class="row container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$pl->img_plano) }}" style="width: 100%" id="img_fondo{{ $pl->id_planta }}">
        @php
            $left=0;
            $top=0;
            $puestos= DB::Table('puestos')
                ->select('puestos.*','plantas.*','estados_puestos.val_color as color_estado','estados_puestos.hex_color','estados_puestos.des_estado')
                ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
                ->join('plantas','puestos.id_planta','plantas.id_planta')
                ->where('puestos.id_planta',$pl->id_planta)
                ->get();

            $agent = new \Jenssegers\Agent\Agent;

        @endphp
        @foreach($puestos as $puesto)
            @php
                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
                $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
                $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
                $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  

                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);

            @endphp
            <div class="text-center rounded add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto draggable" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!! $puesto->des_puesto." \r\n ".$cuadradito['title'] !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw;top: {{ $top }}px; left: {{ $left }}px; {{ $cuadradito['borde'] }}">
                <span class="h-100 align-middle text-center" style="font-size: {{ $puesto->factor_letra }}vw; ">
                        {{ $puesto->cod_puesto }}
                        @include('resources.adornos_iconos_puesto')
                </span>
            </div>
        @php
            $left+=50;
            if($left==500){
                $left=0;
                $top+=50;
            }
         @endphp
        @endforeach
    </div>
    <script>

        try{
            posiciones={!! json_encode($pl->posiciones)??'[]' !!};
            //posiciones=JSON.parse(posiciones); 
        } catch($err){
            posiciones=[];
        }
        document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
        //$('#plano{{ $pl->id_plano }}').data('posiciones',posiciones);
    </script>
@else
    <div id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        No hay plano de la planta
    </div>
@endif