@include('home.accesos_directos')
<h3>
    Semana del 11 al 15 de Marzo semana inaugural de gimnasios. La reserva de clases desde Spotlinker comenzará a funcionar el lunes 17 de Marzo. 
</h3>
@include('home.puesto_asignado')
@if(checkPermissions(['Reservas puestos'],['R']) || checkPermissions(['Reservas salas'],['R']))
    @include('home.calendario')
@endif


