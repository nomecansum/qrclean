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
            font-weight: bold;
            font-size: 9px;
            width: 40px;
            height: 40px;
            overflow: hidden;
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
            <div class="col-md-8">

            </div>
            <div class="col-md-4 text-right">
                <a href="{{ url('puestos/mapa') }}" class="mr-2" ><i class="fad fa-th"></i> Mosaico</a>
                <a href="{{ url('puestos/plano') }}" class="mr-2" style="color:#fff"><i class="fad fa-map-marked-alt"></i> Plano</a>
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
                @endphp
                @foreach($plantas as $pl)
                    <h3 class="pad-all w-100 bg-gray rounded">PLANTA {{ $pl->des_planta }}</h3>
                    @include('puestos.fill-plano')
                @endforeach
            </div>
        </div>
        @endforeach


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