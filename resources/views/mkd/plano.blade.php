
@extends('layout_mkd')

@section('styles')

<style type="text/css">
    .container {
        border: 1px solid #DDDDDD;
        width: 100%;
        position: relative;
        padding: 0px;
    }
    .flpuesto {
        float: left;
        position: absolute;
        z-index: 1000;
        color: #FFFFFF;
        font-weight: bold;
        font-size: 9px;
        width: 60px;
        height: 60px;
        overflow: hidden;
    }
    
    </style>
@endsection
@section('content')
    @php
        $puestos_activos=$puestos->wherein('id_estado',[1,2,3])->count();
        $puestos_libres=$puestos->where('id_estado',1)->count();
        $puestos_asignados=$asignados_usuarios->count();
        $puestos_reservados=$reservas->where('id_estado',1)->count()+$puestos_asignados;
        $disponibles=$puestos_libres-$puestos_reservados;
        $ocupados=$puestos_activos-$disponibles;
        
        $pct_aforo=round(100*$ocupados/$puestos_activos);
        $pl=$plantas;
    @endphp
    <div class="row">
        <div class="col-md-2 text-center">
            @if(isset($cliente))
            <img src="{{ !empty($cliente->img_logo)  ?Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')): url('/img/logo.png') }}" style="width: 120px; margin-top: 40px">
            
            @endif
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6"><span style="font-size: 40px">{{ $edificio->des_edificio }}</span></div>
                <div class="col-md-6"><span style="font-size: 40px">{{ $plantas->des_planta }}</span></div>
            </div>
            <div class="row">
                <div class="col-md-4 text-center fs-2">
                    Aforo<br>
                    <span id="activos" style="font-size: 40px; font-weight: bolder;">{{ $puestos_activos }}</span>
                </div>
                <div class="col-md-4 text-center fs-2 text-success">
                    Disponibles<br>
                    <span id="disponibles" style="font-size: 40px; font-weight: bolder;">{{ $disponibles }}</span>
                </div>
                <div class="col-md-4 text-center fs-2" style="color: rgb(241, 241, 14); text-shadow: 0 0 2px rgb(34, 41, 83); font-weight: bolder;">
                    Reservados<br>
                    <span id="reservados" style="font-size: 40px; font-weight: bolder;">{{ $puestos_reservados }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="text-center fs-2" style="padding-top: 30px">
               
                <span id="ocupacion" class="mt-3 text-{{ color_porcentaje_inv($pct_aforo) }}" style="font-size: 60px;font-weight: bolder;">{{ $pct_aforo }}%</span>
                Ocupacion
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        
        
    </div>
    <div class="card">
        <div class="card-body">
            @if(isset($pl->img_plano))
            {{--  {!! json_encode($pl->posiciones) !!}  --}}

                <div class="row container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
                    <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$pl->img_plano) }}" style="width: 100%" id="img_fondo{{ $pl->id_planta }}">
                    @php
                        $left=0;
                        $top=0;
                        $agent = new \Jenssegers\Agent\Agent;

                    @endphp
                    <div id="puestos">
                        @foreach($puestos as $puesto)
                            @php
                                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
                                $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  

                                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto);

                            @endphp
                            
                                <div class="text-center rounded add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto draggable puesto_parent" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!!  nombrepuesto($puesto)." \r\n ".$cuadradito['title'] !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw;top: {{ $top }}px; left: {{ $left }}px; {{ $cuadradito['borde'] }}">
                                    <span class="h-100 align-middle text-center puesto_child style="font-size: {{ $puesto->factor_letra }}vw; ">
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
                </div>
            @endif
        </div>
    </div>
@endsection
@section('scripts')    
    <script>
        $('.form-ajax').submit(form_ajax_submit);
        try{
            posiciones={!! json_encode($pl->posiciones)??'[]' !!};
            posiciones=$.parseJSON(posiciones); 
        } catch($err){
            posiciones=[];
        }
        document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
        
        $(window).resize(function(){
            recolocar_puestos(posiciones);
        })

        $('.nav-toggler').click(function(){
            recolocar_puestos(posiciones);
        })

        $(function(){
            recolocar_puestos(posiciones);
        })
        function refrescar(){
            // console.log('Refrescar');
            // $('.flpuesto').remove();
            // $.get("{{ url('/MKD/plano/'.$planta.'/'.$token.'/refresh') }}", function(data){
            //     $('#puestos').html(data);
            // });  
            //recolocar_puestos(posiciones);
            //$('.flpuesto').remove();

        }

        setInterval(refrescar, 5000);
       
        
        
    </script>
@endsection