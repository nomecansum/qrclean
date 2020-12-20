
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
        $puestos_reservados=$reservas->where('id_estado',1)->count();
        $ocupados=$puestos_activos-$puestos_libres-$puestos_reservados;
        $disponibles=$puestos_libres-$puestos_reservados;
        $pct_aforo=round(100*$ocupados/$puestos_activos);
        $pl=$plantas;
    @endphp
    <div class="row">
        <div class="col-md-2 text-center">
            @if(isset($cliente))
            <img src="{{ !empty($cliente->img_logo) && file_exists(public_path().'/img/clientes/images/'.$cliente->img_logo) ? url('/img/clientes/images/'.$cliente->img_logo) : url('/img/logo.png') }}" style="width: 120px"><br>
            
            @endif
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6"><span style="font-size: 60px">{{ $edificio->des_edificio }}</span></div>
                <div class="col-md-6"><span style="font-size: 60px">{{ $plantas->des_planta }}</span></div>
            </div>
            <div class="row">
                <div class="col-md-4 text-center text-2x">
                    Aforo<br>
                    <span id="activos" style="font-size: 60px; font-weight: bolder;">{{ $puestos_activos }}</span>
                </div>
                <div class="col-md-4 text-center text-2x text-success">
                    Disponibles<br>
                    <span id="disponibles" style="font-size: 60px; font-weight: bolder;">{{ $disponibles }}</span>
                </div>
                <div class="col-md-4 text-center text-2x" style="color: rgb(241, 241, 14); text-shadow: 0 0 2px rgb(34, 41, 83); font-weight: bolder;">
                    Reservados<br>
                    <span id="reservados" style="font-size: 60px; font-weight: bolder;">{{ $puestos_reservados }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="text-center text-2x" style="padding-top: 30px">
               
                <span id="ocupacion" class="mt-3 text-{{ color_porcentaje_inv($pct_aforo) }}" style="font-size: 110px;font-weight: bolder;">{{ $pct_aforo }}%</span>
                Ocupacion
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        
        
    </div>
    <div class="panel">
        <div class="panel-body">
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
                {{--  <script>

                    try{
                        posiciones={!! json_encode($pl->posiciones)??'[]' !!};
                        console.log(posiciones);
                        //posiciones=JSON.parse(posiciones); 
                    } catch($err){
                        posiciones=[];
                    }
                    document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
                    //$('#plano{{ $pl->id_plano }}').data('posiciones',posiciones);
                </script>  --}}
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
            console.log(posiciones);
        } catch($err){
            posiciones=[];
        }
       
        function recolocar_puestos(posiciones){
            $('.container').each(function(){
                plano=$(this);
                //console.log(plano.data('posiciones'));
                
                $.each(plano.data('posiciones'), function(i, item) {//console.log(item);
                    puesto=$('#puesto'+item.id);
                    puesto.css('top',plano.height()*item.offsettop/100);
                    puesto.css('left',plano.width()*item.offsetleft/100);
                });

            }) 
        }

        

        $(window).resize(function(){
            recolocar_puestos();
        })

        $('.mainnav-toggle').click(function(){
            recolocar_puestos();
        })
        
    </script>
@endsection

{{--  //console.log(posiciones);
function recolocar_puestos(){
    $.each(posiciones, function(i, item) {//console.log(item);
        puesto=$('#puesto'+item.id);
        puesto.css('top',$('#plano').height()*item.offsettop/100);
        puesto.css('left',$('#plano').width()*item.offsetleft/100);
    });
}

function refrescar_datos(){
    $.get("{{ url('MKD/datos_plano/'.$plantas->id_planta.'/'.urlencode(Auth::user()->token_acceso)) }}",function(data){
        cuenta=0;
        disponibles=0;
        reservados=0;
        posiciones=$.parseJSON(data);
        $.each(posiciones, function(i, item) {
            puesto=$('#puesto'+item.id_puesto);
            puesto.removeClass('bg-info');
            puesto.removeClass('bg-success');
            puesto.removeClass('bg-warning');
            puesto.removeClass('bg-danger');
            puesto.removeClass('bg-gray');
            puesto.addClass('bg-'+item.val_color);
            $('#reserva'+item.id_puesto).html('');
            if(item.id_estado==1 && item.fec_reserva!=null){
                $('#reserva'+item.id_puesto).html('R');
                reservados++;
            }
            if(item.id_estado<4){
                cuenta++;
            }
            if(item.id_estado==1 && item.fec_reserva==null){
                disponibles++;
            }
        });
        $('#activos').html(cuenta);
        $('#disponibles').html(disponibles);
        $('#reservados').html(reservados);
        ocupados=cuenta-disponibles-reservados;
        pct_aforo=Math.round(100*ocupados/cuenta);
        //console.log(pct_aforo);
        $('#ocupacion').html(pct_aforo+'%');
    })
}

$(function() {
    recolocar_puestos();
    ir=setInterval(refrescar_datos,3000);
});



$(window).resize(function(){
    recolocar_puestos();
})

$('.mainnav-toggle').click(function(){
    recolocar_puestos();
})  --}}