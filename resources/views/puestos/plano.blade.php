@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Mapa de puestos (plano)</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
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
            font-size: 9px;
            width: 40px;
            height: 40px;
            overflow: hidden;
        }
        .glow {
            background-color: #1c87c9;
            border: none;
            color: #eeeeee;
            cursor: pointer;
            display: inline-block;
            font-family: sans-serif;
            font-size: 20px;
            padding: 13px 10px;
            text-align: center;
            text-decoration: none;
            opacity: 1;
        }
        @keyframes glowing {
            0% {
            background-color: #2ba805;
            box-shadow: 0 0 5px #2ba805;
            }
            50% {
            background-color: #49e819;
            box-shadow: 0 0 20px #49e819;
            }
            100% {
            background-color: #2ba805;
            box-shadow: 0 0 5px #2ba805;
            }
        }
        .glow {
            animation: glowing 1300ms infinite;
        }
        
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item"><a href="{{url('/puestos')}}">Puestos</a></li>
        <li class="breadcrumb-item active">Mapa de puestos</li>
    </ol>
@endsection

@php
    $edificio_ahora=0;
    $planta_ahora=0;
    use App\Models\plantas;
@endphp

@section('content')
        <div class="row botones_accion">
            <div class="col-md-7">
                
            </div>
            <div class="col-md-2 text-right">
                <a href="#modal-leyenda" data-toggle="modal" data-target="#modal-leyenda"><img src="{{ url("img/img_leyenda.png") }}"> LEYENDA</a>
            </div>
            <div class="col-md-3 text-right">
                <a href="{{ url('puestos/lista') }}" class="mr-2" ><i class="fad fa-list"></i> Lista</a>
                <a href="{{ url('puestos/mapa') }}" class="mr-2" ><i class="fad fa-th"></i> Mosaico</a>
                <a href="{{ url('puestos/plano') }}" class="mr-2" style="color: #1e1ed3; font-weight: bold"><i class="fad fa-map-marked-alt"></i> Plano</a>
            </div>
        </div>
   
        @foreach ($edificios as $e)
        <div class="panel">
            <div class="panel-heading bg-gray-dark">
                <div class="row">
                    <div class="col-md-3">
                        <span class="text-2x ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}</span>
                    </div>
                    <div class="col-md-7"></div>
                    <div class="col-md-2 text-right">
                        <h4>
                            <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                            <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $e->puestos }}</span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @php
                    $plantas=plantas::where('id_edificio',$e->id_edificio)->get();
                    if(isset($cualpuesto)){ //Esto es para el caso de que queramos filtrar para mostrar la ubicacion de un puesto
                        $plantas=$plantas->where('id_planta',$cualpuesto->id_planta);
                    }
                @endphp
                @foreach($plantas as $pl)
                    <h3 class="pad-all w-100 bg-gray rounded">PLANTA {{ $pl->des_planta }}</h3>
                    @include('puestos.fill-plano')
                @endforeach
            </div>
        </div>
        @endforeach
        @include('resources.leyenda_puestos')

@endsection


@section('scripts')
    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.mapa').addClass('active-link');
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
