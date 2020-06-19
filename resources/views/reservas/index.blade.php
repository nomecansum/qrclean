@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Reserva de puestos</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Reserva de puestos</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Mis reservas</h3>
    </div>
    <div class="panel-body">
        <div id="calendario"></div>
    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.reservas').addClass('active-link');


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
    </script>
@endsection
