
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
        $ocuoados=$puestos_activos-$puestos_libres-$puestos_reservados;
        $disponibles=$puestos_libres-$puestos_reservados;
        $pct_aforo=round(100*$ocuoados/$puestos_activos);
    @endphp
    <div class="row">
        <div class="col-md-2 text-center">
            @if(isset($cliente))
            <img src="{{ !empty($cliente->img_logo) && file_exists(public_path().'/img/clientes/images/'.$cliente->img_logo) ? url('/img/clientes/images/'.$cliente->img_logo) : url('/img/logo.png') }}" style="width: 120px"><br>
            
            @endif
        </div>
        <div class="col-md-4"><span style="font-size: 60px">{{ $edificio->des_edificio }}</span></div>
        <div class="col-md-4"><span style="font-size: 60px">{{ $plantas->des_planta }}</span></div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2 text-center text-2x">
            Aforo<br>
            <span style="font-size: 60px">{{ $puestos_activos }}</span>
        </div>
        <div class="col-md-2 text-center text-2x">
            Disponibles<br>
            <span style="font-size: 60px">{{ $disponibles }}</span>
        </div>
        <div class="col-md-2 text-center text-2x">
            Reservados<br>
            <span style="font-size: 60px">{{ $puestos_reservados }}</span>
        </div>
        <div class="col-md-4 text-center text-2x">
            Ocupacion<br>
            <span class="text-{{ color_porcentaje_inv($pct_aforo) }}" style="font-size: 80px">{{ $pct_aforo }}%</span>
        </div>
    </div>
    <div class="panel">
        <div class="panel-body">
            @if(isset($plantas->img_plano))
            {{--  style="background-image: url('{{ url('img/plantas/'.$plantas->img_plano) }}'); background-repeat: no-repeat; background-size: contain;"  --}}
                <div class="row container" id="plano" >
                    <img src="{{ url('img/plantas/'.$plantas->img_plano) }}" style="width: 100%" id="img_fondo" class="container">
                    @php
                        $left=0;
                        $top=0;
                    @endphp
                    @foreach($puestos as $puesto)
                        @php
                             $tiene_reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();      
                        @endphp
                        <div class="text-center font-bold rounded add-tooltip bg-{{ $puesto->val_color }} align-middle flpuesto draggable" id="puesto{{ $puesto->id_puesto }}" title="{{ $puesto->des_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" style="top: {{ $top }}px; left: {{ $left }}px">
                            <span class="h-100 align-middle" style="font-size: 10px;">{{ $puesto->cod_puesto }}
                            @if(isset($tiene_reserva))<br><span class="font-bold" style="font-size: 18px; color: #ff0">R</span>@endif
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
            @endif
        </div>
    </div>
@endsection
@section('scripts')    
    <script>
        $('.form-ajax').submit(form_ajax_submit);
    
        try{
            posiciones={!! json_encode($plantas->posiciones)??'[]' !!};
            posiciones=$.parseJSON(posiciones); 
        } catch($err){
            posiciones=[];
        }
        //console.log(posiciones);
        function recolocar_puestos(){
            $.each(posiciones, function(i, item) {//console.log(item);
                puesto=$('#puesto'+item.id);
                puesto.css('top',$('#plano').height()*item.offsettop/100);
                puesto.css('left',$('#plano').width()*item.offsetleft/100);
            });
        }
    
        $( function() {
            recolocar_puestos();
        });
    
        $(window).resize(function(){
            recolocar_puestos();
        })
    
        $('.mainnav-toggle').click(function(){
            recolocar_puestos();
        })
    </script>
@endsection