

@include('home.accesos_directos')

@include('home.4_kpi')
<div class="card">
    <div class="card-body">
        <h1 class="text-danger"><i class="fa-solid fa-bell-exclamation"></i> Importante</h1>
        <h3>
            Semana del 11 al 15 de Marzo semana inaugural de gimnasios. La reserva de clases desde Spotlinker comenzará a funcionar el <b>lunes 17 de Marzo.</b> 
        </h3>
    </div>
</div>

@include('home.puesto_asignado')

<div class="row">
    <div class="col-md-6">
        @include('home.kpi_grafico_puestos')
        @if(checkPermissions(['Incidencias'],['R']))
            @include('home.incidencias_abiertas')
        @endif
    </div>
    <div class="col-md-6">
        @if(checkPermissions(['Reservas'],['R']))
            @include('home.calendario')
        @endif
    </div>
</div>
@if(checkPermissions(['Incidencias'],['R']))
    @include('home.tabla_incidencias')
@endif

@include('home.rondas_pendientes')
