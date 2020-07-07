<div class="panel" id="editor">
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title" id="titulo">Modificar reserva de puesto</h3>
        </div>
        <div class="panel-body">
            <form  action="{{url('reservas/save')}}" method="POST" name="frm_contador" id="frm_contador" class="form-ajax">
                <div class="row">
                    <input type="hidden" name="id_reserva" value="{{ $reserva->id_reserva }}">
                    <input type="hidden" name="id_cliente" value="{{ $reserva->id_cliente }}">
                    <input type="hidden" id="id_puesto" name="id_puesto" value="">
                    <input type="hidden" id="des_puesto_form" name="des_puesto" value="">
                    {{csrf_field()}}
                    <div class="form-group col-md-3">
                        <label for="fechas">Fecha</label>
                        <div class="input-group">
                            <input type="text" class="form-control pull-left singledate" id="fechas" name="fechas" style="width: 120px" value="{{ $f1->format('d/m/Y')}}">
                            <span class="btn input-group-text btn-mint datepickerbutton" disabled  style="height: 33px"><i class="fas fa-calendar mt-1"></i></span>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="id_edificio"><i class="fad fa-building"></i> Edificio</label>
                        <select name="id_edificio" id="id_edificio" class="form-control">
                            @foreach(DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $edificio)
                                <option value="{{ $edificio->id_edificio}}" {{ isset($puesto) && $puesto->id_edificio==$edificio->id_edificio?'selected':'' }}>{{ $edificio->des_edificio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 pt-4">
                        <span style="font-size: 30px; font-weight: bolder; color: #888; margin-top:60px" id="des_puesto"></span>
                    </div>
                    <div class="md-1 float-right" style="margin-top:22px">
                        @if(checkPermissions(['Reservas'],["W"]))<button type="submit" class="btn btn-primary btn_guardar">GUARDAR</button>@endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="detalles_reserva">

                    </div>
                </div>
                
                
            </form>
        </div>
    </div>
 </div>

 <script>

    //$('#frm_contador').on('submit',form_ajax_submit);
    $('#frm_contador').submit(function(event){
        event.preventDefault();
        let form = $(this);
        let data = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            contentType: false,
            processData: false,
            data: data,
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
                animateCSS('#TD'+data.fecha,'flip');
                animateCSS('#editor','fadeOut',$('#editor').html(''))
            }
            
        })
        .fail(function(err) {
            mensaje_error_respuesta(err);
        })
        .always(function() {
            fin_espere();
            console.log("Reserva complete");
            form.find('[type="submit"]').attr('disabled',false);
        });
    });

    function prueba(){
        console.log('prueba');

    }

    $('.demo-psi-cross').click(function(){
        $('#editor').hide();
    })

    $('#val_icono').iconpicker({
        icon:'{{isset($t) ? ($t->val_icono) : ''}}'
    });

    function comprobar_puestos(){
        $.post('{{url('/reservas/comprobar')}}', {_token: '{{csrf_token()}}',fecha: $('#fechas').val(),edificio:$('#id_edificio').val()}, function(data, textStatus, xhr) {
            $('#detalles_reserva').html(data);
        });
    }

    $('#id_edificio').change(function(){
       comprobar_puestos();
    })


    $('.singledate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        //autoUpdateInput : false,
        autoApply: true,
        locale: {
            format: '{{trans("general.date_format")}}',
            applyLabel: "OK",
            cancelLabel: "Cancelar",
            daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
            monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
            firstDay: {{trans("general.firstDayofWeek")}}
        }
       
    },
    function(date) {
        $('#fechas').val(moment(date).format('D/M/Y'));
        $('#fechas').data('fecha',moment(date).format('Y-MM-DD'));
        comprobar_puestos();
    });

    $('.btn_guardar').click(function(){
        if($('#id_puesto').val()==null || $('#id_puesto').val()==""){
            toast_error('Error', 'Seleccione un puesto para reservar');
            event.preventDefault();
        }
    })

    comprobar_puestos();

 </script>
