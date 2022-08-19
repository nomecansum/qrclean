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

        .letras_imagen {
            position: absolute;
            top: 40%;
            left: 30%;
            color: #fff; 
            -webkit-text-stroke: 1px #2e0edf;
            font-weight: bold;
            z-index: 1000;
        }
        
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item"><a href="{{url('/puestos/mapa')}}">Mi oficina</a></li>
        <li class="breadcrumb-item active">Ubicacion de mis compañeros</li>
    </ol>
@endsection

@php
    $edificio_ahora=0;
    $planta_ahora=0;
    use App\Models\plantas;
@endphp

@section('content')
        <div class="row botones_accion mb-2">
            <div class="col-md-4">
                <form action="{{ url('puestos/plano') }}" name="form_mapa" id="form_mapa" method="POST">
                    {{ csrf_field() }}
                    <div class="input-group float-right" id="div_fechas">
                        <input type="text" class="form-control pull-left" id="fecha" name="fecha" style="width: 100px" value="{{isset($r->fecha)?$r->fecha:Carbon\Carbon::now()->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                
            </div>
            
        </div>
   
        @foreach ($edificios as $e)
        <div class="panel">
            <div class="panel-heading bg-gray-dark">
                <div class="row">
                    <div class="col-md-5">
                        <span class="text-2x ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}</span>
                    </div>
                    <div class="col-md-5"></div>
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
                    @include('puestos.fill_usuarios_puestos')
                @endforeach
            </div>
        </div>
        @endforeach
@endsection


@section('scripts')
    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.compas').addClass('active-link');

        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        function recolocar_puestos(posiciones){
            $('.container').each(function(){
                plano=$(this);
                //console.log(plano.data('posiciones'));
                
                $.each(plano.data('posiciones'), function(i, item) {//console.log(item);
                    puesto=$('#puesto'+item.id);
                    console.log('#puesto'+item.id);
                    puesto.css('top',plano.height()*item.offsettop/100);
                    puesto.css('left',plano.width()*item.offsetleft/100);
                });

            }) 
        }

        
        $(function(){
            $(window).resize(function(){
                recolocar_puestos();
            })

            $('.mainnav-toggle').click(function(){
                recolocar_puestos();
            })
        })
        

        $('#fecha').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput : true,
            //autoApply: true,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            }
        });

        $('#fecha').change(function(){
               $('#form_mapa').submit();
        });
    </script>
@endsection
