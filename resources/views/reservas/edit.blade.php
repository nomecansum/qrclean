<style type="text-css">
    .noUi-pips-horizonta{
        padding: 0px;
        margin-top: 0px;
        line-height: 0px
    }

    .select2-choices {
        min-height: 150px;
        max-height: 150px;
        overflow-y: auto;
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
                                        <td><a href="javascript:void(0)" class="btn_del text-danger add-tooltip" title="Cancelar reserva" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-des_puesto="{{ $res->cod_puesto }}"><i class="fad fa-trash-alt"></i></a>
                                            <a href="#planta{{ $res->id_planta }}" class="btn_ver text-info add-tooltip" title="Ver puesto en plano/mapa" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-puesto="{{ $res->id_puesto }}"><i class="fad fa-search-location"></i></a></td>
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
                    <input type="hidden" name="tipo_vista" id="tipo_vista" value="{{ Auth::user()->val_vista_puestos??'comprobar' }}">
                    <input type="hidden" name="hora_inicio" id="hora_inicio" value="00:00">
                    <input type="hidden" name="hora_fin" id="hora_fin" value="23:59">
                    {{csrf_field()}}
                    <div class="form-group col-md-3">
                        <label for="fechas">Fecha</label>
                        {{--  <div class="input-group">
                            <input type="text" class="form-control pull-left singledate" id="fechas" name="fechas" style="width: 180px" value="{{ $f1->format('d/m/Y')}}">
                            <span class="btn input-group-text btn-mint datepickerbutton" disabled  style="height: 33px"><i class="fas fa-calendar mt-1"></i></span>
                        </div>  --}}
                        <div class="input-group">
                            <input type="text" class="form-control pull-left" id="fechas" name="fechas" style="height: 33px; width: 200px" value="{{ $f1->format('d/m/Y').' - '.$f1->format('d/m/Y') }}">
                            <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                        </div>

                    </div>
                    <div class="form-group col-md-3">
                        <label for="id_edificio"><i class="fad fa-building"></i> Edificio</label>
                        <select name="id_edificio" id="id_edificio" class="form-control">
                            @foreach(DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $edificio)
                                <option value="{{ $edificio->id_edificio}}" {{ isset($reserva) && $reserva->id_edificio==$edificio->id_edificio?'selected':'' }}>{{ $edificio->des_edificio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="planta"><i class="fad fa-layer-group"></i> Planta</label>
                        <select name="id_planta" id="id_planta" class="form-control">
                            <option value="0">Cualquiera</option>
                            @foreach($plantas_usuario as $p)
                                <option value="{{ $p->id_planta}}" {{ isset($reserva->id_tipo_puesto) && $reserva->id_planta==$p->id_planta?'selected':'' }}>{{ $p->des_planta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="id_usuario">Tipo de puesto</label>
                        <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                            @foreach($tipos as $t)
                                <option value="{{ $t->id_tipo_puesto}}" {{ isset($reserva->id_tipo_puesto) && $reserva->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }}>{{ $t->des_tipo_puesto }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    
                   
                    
                </div>
                <div class="row mt-2">
                    @if(session('CL')['mca_reserva_horas']=='S')
                    <div class="form-group col-md-7">
                        <label for="hora-range-drg"><i class="fad fa-clock"></i> Horas [<span id="horas_rango"></span>]</label>
                        <div id="hora-range-drg" style="margin-top: 40px"></div><span id="hora-range-val" style="display: none"></span>
                    </div>
                    @endif
                    <div class="form-group col-md-5">
                        <label>Tags
                            <input id="andor" name="andor" type="checkbox">
                            <span id="andor-field" class="label label-info">OR</span>
                        </label>
                        <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="tags[]" id="multi-tag" >
                            @foreach(DB::table('tags')->where('id_cliente',Auth::user()->id_cliente)->get() as $tag)
                                <option value="{{ $tag->id_tag}}">{{ $tag->nom_tag }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_ical"  id="mca_ical" value="S"> 
						<label class="custom-control-label" for="mca_ical">Añadir a mi calendario</label>
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
    comprobar_puestos();

    var changeCheckbox = document.getElementById('andor'), changeField = document.getElementById('andor-field');
    new Switchery(changeCheckbox,{ size: 'small',color:'#489eed' })
    changeCheckbox.onchange = function() {
        if(changeCheckbox.checked){
            changeField.innerHTML='AND';
        } else {
            changeField.innerHTML='OR';
        }
        //$('#andor').val(changeField.innerHTML);
        //comprobar_puestos();
    };
    changeCheckbox.onclick = function() {
        comprobar_puestos();
    };

    $('#id_edificio').change(function(){
        $('#id_planta').load("{{ url('/combos/plantas/') }}/"+$(this).val(), function(){
            $('#id_planta').prepend("<option value='0'>Cualquiera</option>")
        });
    })
    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        width: "99.2%",
        height: "20px"
    });

    $(function(){
        $('#id_planta').load("{{ url('/combos/plantas/') }}/"+$('#id_edificio').val(), function(){
            $('#id_planta').val({{ $reserva->id_planta }});
            $('#id_planta option[value={{ $reserva->id_planta }}]').attr('selected','selected');
            $('#id_planta').prepend("<option value='0'>Cualquiera</option>")
        });
        
    })

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
                animateCSS('#editor','fadeOut',$('#editor').html(''));
                toast_ok(data.title,data.mensaje);
                loadMonth();
                animateCSS('#TD'+data.fecha,'flip');
                
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
        console.log($('#fechas').val());
        $.post('{{url('/reservas/comprobar')}}', {_token: '{{csrf_token()}}',fecha: $('#fechas').val(),edificio:$('#id_edificio').val(),tipo: $('#tipo_vista').val(), hora_inicio: $('#hora_inicio').val(),hora_fin: $('#hora_fin').val(), tipo_puesto: $('#id_tipo_puesto').val(),id_planta:$('#id_planta').val(),tags:$('#multi-tag').val(),andor:$('#andor').is(':checked')}, function(data, textStatus, xhr) {
            $('#detalles_reserva').html(data);
        });
    }

    $('#id_edificio, #id_tipo_puesto, #id_planta, #multi-tag').change(function(){
      comprobar_puestos();
    })


    // $('.singledate').daterangepicker({
    //     singleDatePicker: true,
    //     showDropdowns: true,
    //     //autoUpdateInput : false,
    //     autoApply: true,
    //     locale: {
    //         format: '{{trans("general.date_format")}}',
    //         applyLabel: "OK",
    //         cancelLabel: "Cancelar",
    //         daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
    //         monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
    //         firstDay: {{trans("general.firstDayofWeek")}}
    //     }
       
    // },
    // function(date) {
    //     $('#fechas').val(moment(date).format('D/M/Y'));
    //     $('#fechas').data('fecha',moment(date).format('Y-MM-DD'));
    //     comprobar_puestos();
    // });
    $('#fechas').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            },
            "maxSpan": {"days": {{ config_cliente('max_dias_reserva',Auth::user()->id_cliente) }}},
            opens: 'right',
        }, function(start_date, end_date) {
            $('#fechas').val(start_date.format('DD/MM/YYYY')+' - '+end_date.format('DD/MM/YYYY'));
            $('#fechas').data('fecha_inicio',moment(start_date).format('Y-MM-DD'));
            $('#fechas').data('fecha_fin',moment(start_date).format('Y-MM-DD'));
            //window.location.href = '{{ url('/rondas/index/') }}/'+start_date.format('YYYY-MM-DD')+'/'+end_date.format('YYYY-MM-DD');
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
            start : [{{ config_cliente('min_hora_reservas') }}, {{ config_cliente('max_hora_reservas') }}],
            connect: true, 
            behaviour: 'tap-drag', 
            step: 10,
            tooltips: true,
            range : {'min': {{ config_cliente('min_hora_reservas') }}, 'max': {{ config_cliente('max_hora_reservas') }} },
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
            $('#horas_rango').html(values[0]+' - '+values[1]);
            comprobar_puestos();
            // r_def_value.innerHTML = values[handle];
            // $('.texto_puesto').css('font-size',values[handle]+'vw');
            // $('#factor_letra').val(values[handle]);
        });

        
        values=r_def.noUiSlider.get();
        $('#hora_inicio').val(values[0]);
        $('#hora_fin').val(values[1]);
        $('#horas_rango').html(values[0]+' - '+values[1]);
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

    $('.btn_ver').click(function(){
        $('.flpuesto').removeClass('glow');
        $('#puesto'+$(this).data('puesto')).addClass('glow');
    })


 </script>
