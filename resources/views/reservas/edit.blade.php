<style type="text-css">
    .noUi-pips-horizonta{
        padding: 0px;
        margin-top: 0px;
        line-height: 0px
    }

</style>
<div class="panel" id="editor">
    <div class="panel">
        {{-- <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title" id="titulo">Reserva de puesto</h3>
            <span style="font-size: 30px; font-weight: bolder; color: #888; margin-top:60px" id="des_puesto"></span>
        </div> --}}
        <div class="panel-body">
            @if(count($misreservas)>0)
                <div class="row b-all rounded mb-2">
                    <div class="col-md-12">
                        <label for="fechas">Reservas activas para el día {{ $f1->format('d/m/Y')}}</label>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Periodo</th>
                                        <th>Tipo</th>
                                        <th>Puesto</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach($misreservas as $res)
                                    <tr>
                                        <td>{{ $res->id_reserva }}</td>
                                        <td>{{ Carbon\Carbon::parse($res->fec_reserva)->format('H:i') }} @if($res->fec_fin_reserva!=null)<i class="fas fa-arrow-right"></i> {{ Carbon\Carbon::parse($res->fec_fin_reserva)->format('H:i') }}@endif</td>
                                        <td style="color: {{ $res->val_color }}"><i class="{{ $res->val_icono }}"></i>{{ $res->des_tipo_puesto }}</td>
                                        <td>{{ $res->cod_puesto }}</td>
                                        <td><a href="javascript:void(0)" class="btn_del text-danger add-tooltip" title="Cancelar reserva" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-des_puesto="{{ $res->cod_puesto }}"><i class="fad fa-trash-alt"></i></a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                    
                </div>
            @endif
            <form  action="{{url('reservas/save')}}" method="POST" name="frm_contador" id="frm_contador" class="form-ajax">
                <div class="row">
                    <input type="hidden" name="id_reserva" value="{{ $reserva->id_reserva }}">
                    <input type="hidden" name="id_cliente" value="{{ $reserva->id_cliente }}">
                    <input type="hidden" id="id_puesto" name="id_puesto" value="">
                    <input type="hidden" id="des_puesto_form" name="des_puesto" value="">
                    <input type="hidden" name="tipo_vista" id="tipo_vista" value="comprobar">
                    <input type="hidden" name="hora_inicio" id="hora_inicio" value="00:00">
                    <input type="hidden" name="hora_fin" id="hora_fin" value="23:59">
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
                    <div class="form-group col-md-3">
                        <label for="id_usuario">Tipo de puesto</label>
                        <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                            @foreach($tipos as $t)
                                <option value="{{ $t->id_tipo_puesto}}" {{ isset($puesto->id_tipo_puesto) && $puesto->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }}>{{ $t->des_tipo_puesto }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                   
                    
                </div>
                <div class="row mt-2">
                    @if(session('CL')['mca_reserva_horas']=='S')
                    <div class="form-group col-md-7">
                        <label for="hora-range-drg"><i class="fad fa-clock"></i> Horas</label>
                        <div id="hora-range-drg"></div><span id="hora-range-val" style="display: none"></span>
                    </div>
                    @endif
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
    comprobar_puestos();

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
        $.post('{{url('/reservas/comprobar')}}', {_token: '{{csrf_token()}}',fecha: $('#fechas').val(),edificio:$('#id_edificio').val(),tipo: $('#tipo_vista').val(), hora_inicio: $('#hora_inicio').val(),hora_fin: $('#hora_fin').val(), tipo_puesto: $('#id_tipo_puesto').val()}, function(data, textStatus, xhr) {
            $('#detalles_reserva').html(data);
        });
    }

    $('#id_edificio, #id_tipo_puesto').change(function(){
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

    @if(session('CL')['mca_reserva_horas']=='S')

        var aproximateHour = function (mins)
        {
        //http://greweb.me/2013/01/be-careful-with-js-numbers/
        var minutes = Math.round(mins % 60);
        if (minutes == 60 || minutes == 0)
        {
            return mins / 60;
        }
        return Math.trunc (mins / 60) + minutes / 100;
        }


        function filter_hour(value, type) {
        return (value % 60 == 0) ? 1 : 0;
        }


        var r_def = document.getElementById('hora-range-drg');
        var r_def_value = document.getElementById('hora-range-val');


        noUiSlider.create(r_def,{
            start : [480, 1080],
            connect: true, 
            behaviour: 'tap-drag', 
            step: 15,
            tooltips: true,
            range : {'min': 0, 'max': 1439},
            format:  wNumb({
                    decimals: 2,
                mark: ":",
                    encoder: function(a){
                return aproximateHour(a);
                }
                }),
        });
        r_def.noUiSlider.on('change', function( values, handle ) {
            console.log(values);
            $('#hora_inicio').val(values[0]);
            $('#hora_fin').val(values[1]);
            comprobar_puestos();
            // r_def_value.innerHTML = values[handle];
            // $('.texto_puesto').css('font-size',values[handle]+'vw');
            // $('#factor_letra').val(values[handle]);
        });

        
        values=r_def.noUiSlider.get();
        $('#hora_inicio').val(values[0]);
        $('#hora_fin').val(values[1]);
    @endif

    $('.btn_del').click(function(){
        $.post('{{url('/reservas/cancelar')}}', {_token: '{{csrf_token()}}',fecha: $(this).data('fecha'),id: $(this).data('id'), des_puesto: $(this).data('des_puesto')}, function(data, textStatus, xhr) {
            
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
                $('#editorCAM').load("{{ url('/reservas/create/'.$f1->format('Y-m-d')) }}");
                //animateCSS('#editor','fadeOut',$('#editor').html(''));
            }
            
        })
        .fail(function(err) {
            mensaje_error_respuesta(err);
        })
    })

    
 </script>
