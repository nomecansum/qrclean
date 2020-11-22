<div class="row">
    @if(checkPermissions(['Scan acceso'],['R']))
    <div class="col-md-6 text-center mb-2">
        <a class="btn btn-lg btn-primary text-2x rounded" href="{{ url('/scan_usuario/') }} "><i class="fad fa-qrcode "></i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Scan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
    </div>
    @endif
    @if(checkPermissions(['Reservas'],['R']))
    <div class="col-md-6 text-center mb-2 ">
        <a class="btn btn-lg btn-info text-2x rounded" href="{{ url('/reservas/') }} "><i class="fad fa-calendar-alt "></i> Mis reservas</a>
    </div>
    @endif
</div>