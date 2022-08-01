@extends('layout')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>
    </ol>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">

    <link href="{{ asset('/plugins/fullcalendar/lib/main.css') }}" rel="stylesheet">
    
    {{--  <link href="{{ asset('/plugins/fullcalendar/nifty-skin/fullcalendar-nifty.min.css') }}" rel="stylesheet">  --}}
    
    {{--  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.5.0/main.min.css' rel='stylesheet' />  --}}
@endsection

@section('content')



    <div class="panel">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</h3>
        </div>
        <div class="panel-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('users.users.update', $users->id) }}" id="edit_users_form" name="edit_users_form" accept-charset="UTF-8" class="form-horizontal mt-4 form-ajax" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{-- <input name="_method" type="hidden" value="POST"> --}}
            @include ('users.form', [
                                        'users' => $users,
                                      ])

                <div class="form-group">
                    <div class="col-md-10">
                        <input class="btn btn-primary btn-lg" type="submit" value="Actualizar">
                    </div>
                </div>
            </form>

        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Actividad de {{ $users->name }}</h3>
        </div>
        <div class="panel-body">
            <div class="row mt-2 ">
                <div class="col-md-1"></div>
                <div class="fluid col-md-10">
                    <div id='demo-calendar'></div>
                    <div id="events-popover-head" class="hide">Events</div>
                    <div id="events-popover-content" class="hide">Test</div>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Plantas en las que puede reservar</h3>
        </div>
        <div class="panel-body" id="plantas_usuario">

        </div>
    </div>
    @if(isSupervisor($users->id))
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Puestos que puede gestionar como supervisor</h3>
        </div>
        <div class="panel-body" id="puestos_usuario">

        </div>
    </div>
    @endif
@endsection

@section('scripts')
<script src="{{ asset('plugins/fullcalendar/lib/main.min.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/lib/locales/es.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/tooltip.min.js') }}"></script>
    <script>
        $(function(){
            $('#plantas_usuario').load("{{ url('users/plantas/'.$users->id) }}/1")
        });

        $('.configuracion').addClass('active active-sub');
	    $('.usuarios').addClass('active-link');
        @if(isSupervisor($users->id))
            $(function(){
                $('#puestos_usuario').load("{{ url('users/puestos_supervisor/'.$users->id) }}")
            });
        @endif
        


        // Initialize the calendar
        // -----------------------------------------------------------------
        var calendarEl = document.getElementById('demo-calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                eventDidMount: function(info) {
                    //console.log(info);
                    var tooltip = new Tooltip(info.el, {
                    title: info.event.extendedProps.description,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                    });
                },
                eventLimit: 4,

                eventLimitClick: function (cellInfo, jsEvent) {
            
                    $(cellInfo.dayEl).popover({
                        html: true,
                        placement: 'bottom',
                        container: 'body',
                        title: function () {
                            return $("#events-popover-head").html();
                        },
                        content: function () {
                            return $("#events-popover-content").html();
                        }
                    });

                    $(cellInfo.dayEl).popover('show');
                },
                dayClick: function (cellInfo, jsEvent) {
                    $(this).popover({
                        html: true,
                        placement: 'bottom',
                        container: 'body',
                        title: function () {
                            return $("#events-popover-head").html();
                        },
                        content: function () {
                            return $("#events-popover-content").html();
                        }
                    });

                    $(this).popover('show');
                },
                editable: false,
                droppable: false, // this allows things to be dropped onto the calendar
                eventLimit: true, // allow "more" link when too many events
                locale: 'es',
                firstDay: 1,
                themeSystem: 'bootstrap',
                moreLinkClick: "popover",
                dayMaxEventRows: 4,
                events: {!! $eventos !!}
            });
            calendar.render();

        // $('.fc-dayGridMonth-button').html('Mes');
        // $('.fc-timeGridWeek-button').html('Semana');
        // $('.fc-listGridWeek-button').html('Lista');
        $('.fc-event-title').css('font-size','10px');
        $('.fc-event-title').css('font-weight','normal');
        
    </script>

    
@endsection
@include('layouts.scripts_panel')