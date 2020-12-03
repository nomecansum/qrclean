@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Reserva de puestos</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Reserva de puestos</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion">
    <div class="col-md-4">

    </div>
    <div class="col-md-7">
        <br>
    </div>
    <div class="col-md-1 text-right">
        {{-- <div class="btn-group btn-group-sm pull-right" role="group">
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva reserva">
                <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                <span>Nuevo</span>
            </a>
        </div> --}}
    </div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Mis reservas</h3>
        <span class="float-right" id="spin" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="panel-body">
        <div id="calendario"></div>
    </div>
</div>
@endsection


@section('scripts')
    <script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
    <script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.reservas').addClass('active-link');

        function filter_hour(value, type) {
        return (value % 60 == 0) ? 1 : 0;
        }

        
        function loadMonth(month = null,type = null)
        {
            $('#spinner').show();
            $.post('{{url('reservas/loadMonthSchedule')}}', {_token:'{{csrf_token()}}',month: month,type:type,emp:'{{Auth::user()->id}}'}, function(data, textStatus, xhr) {
                $('#calendario').html(data);
            
                
                $('.changeMonth').click(function(event) {
                    loadMonth($(this).data('month'),$(this).data('action'));
                });
                $('#spinner').hide();
            });
        }

        $(function(){
            loadMonth();
        })

        $('#btn_nueva_puesto').click(function(){
            spshow('spin');
            $('#editorCAM').load("{{ url('/reservas/create/') }}/"+fechacal, function(){
                animateCSS('#editorCAM','bounceInRight');
                sphide('spin');
                $('#titulo').html('Nueva reserva de puesto');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/reservas/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }


        function boton_modo_click(){
            $('#loadfilter').show();
                $.post('{{url('/reservas/comprobar')}}', {_token: '{{csrf_token()}}',fecha: $('#fechas').val(),edificio:$('#id_edificio').val(),tipo: $(this).data('href'), hora_inicio: $('#hora_inicio').val(),hora_fin: $('#hora_fin').val(),id_planta:$('#id_planta').val()}, function(data, textStatus, xhr) {
                    $('#detalles_reserva').html(data);
                    recolocar_puestos();
                });
        }


        function recolocar_puestos(posiciones){
            $('.container').each(function(){
                plano=$(this);
                //console.log(plano.data('posiciones'));
                
                $.each(plano.data('posiciones'), function(i, item) {//console.log(item);
                    
                    puesto=$('#puesto'+item.id);
                    console.log(item);
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
