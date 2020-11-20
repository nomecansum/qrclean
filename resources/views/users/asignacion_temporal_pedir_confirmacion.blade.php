@foreach($respuesta as $resp)
<div class="row">
    <div class="col-md-12">
        <i class="fad fa-info-circle"></i> {{ $resp }}
    </div>
</div>
@endforeach
<div class="row mt-3">
    <div class="col-md-2"></div>
    <div class="col-md-1"><button type="button" data-dismiss="modal" class="btn btn-danger">Cancelar</button></div>
    <div class="col-md-6"></div>
    <div class="col-md-1"><button type="button" class="btn btn-success" id="btn_aceptar_asignacion">Aceptar</button></div>
    <div class="col-md-2"></div>
</div>
<script>
    $('#btn_aceptar_asignacion').click(function(){
        $.post('{{url('/users/asignar_temporal')}}', {_token: '{{csrf_token()}}',puesto:{{ $r->puesto }},rango: "{!! $r->rango !!}",id_usuario: {!! $r->id_usuario[0] !!},accion: 'C'}, function(data, textStatus, xhr) {
            
        }) 
    })
</script>