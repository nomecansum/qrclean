<div class="panel" id="editor">
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
			</div>
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title" id="titulo">Cancelar reserva de puesto</h3>
        </div>
        <div class="panel-body pb-4">
            <h3>¿Cancelar reserva ID [{{ $reserva->id_reserva }}] del puesto {{ $reserva->des_puesto }} para el {!! beauty_fecha($reserva->fec_reserva) !!}?</h3>
        </div>
        <div class="text-right w-100 pb-3">
            <button type="button" class="btn btn-danger btn_si mb-2"><b>SI</b>, CANCELAR</button>
            <button type="button" class="btn btn-info btn_no ml-4 mb-2 mr-2">NO, aun no</button>
        </div>
    </div>
 </div>

 <script>

    //$('#frm_contador').on('submit',form_ajax_submit);

    $('.demo-psi-cross,.btn_no').click(function(){
        $('#editor').hide();
    })

    $('.btn_si').click(function(){
        animateCSS('#TD{{ Carbon\Carbon::parse($reserva->fec_reserva)->format('Ymd') }}','fadeOutDownBig');
        $.post('{{url('/reservas/cancelar')}}', {_token: '{{csrf_token()}}',fecha: '{{ $reserva->fec_reserva }}',id:'{{ $reserva->id_reserva }}',des_puesto:'{{ $reserva->des_puesto }}'}, function(data, textStatus, xhr) {
            
        })
        .done(function(data) {
            console.log(data);
            if(data.error){
                mensaje_error_controlado(data);

            } else if(data.alert){
                mensaje_warning_controlado(data);
            } else{
                toast_ok(data.title,data.mensaje);
                loadMonth();
                animateCSS('#editor','fadeOut',$('#editor').html(''));
            }
            
        })
        .fail(function(err) {
            mensaje_error_respuesta(err);
        })
    });




 </script>
