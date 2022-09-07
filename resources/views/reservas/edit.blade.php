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

<div class="card" id="editor">
    <div class="card">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Reserva de puesto</h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(count($misreservas)>0)
                <div class="row b-all rounded mb-2">
                    <div class="col-md-12">
                        <label for="fechas">Reservas activas para el día {{ $f1->format('d/m/Y')}}</label>
                        <div class="table-responsive">
                            <table class="table table-striped table-responsive table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Periodo</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Puesto</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach($misreservas as $res)
                                    <tr class="{{ $res->mca_anulada=='S'?'bg-gray':'' }}">
                                        <td>{{ $res->id_reserva }}</td>
                                        <td>{{ Carbon\Carbon::parse($res->fec_reserva)->format('H:i') }} @if($res->fec_fin_reserva!=null)<i class="fas fa-arrow-right"></i> {{ Carbon\Carbon::parse($res->fec_fin_reserva)->format('H:i') }}@endif {{ $res->mca_anulada=='S'?'[ANULADA]':'' }}</td>
                                        <td style="color: {{ $res->val_color }}"><i class="{{ $res->val_icono }}"></i>{{ $res->des_tipo_puesto }}</td>
                                        <td class="text-center">{{ $res->cod_puesto }}</td>
                                        <td style="position:relative">
                                            @if($res->mca_anulada=='N')
                                            <td style="position: relative">
                                                <div class="pull-right floating-like-gmail" style="width: 400px;">
                                                    <div class="btn-group btn-group pull-right" role="group">
                                                        <a href="javascript:void(0)" class="btn btn-info btn-xs btn_edit  add-tooltip" title="Modificar reserva" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-des_puesto="{{ $res->cod_puesto }}"><i class="fad fa-pencil-alt"></i> Modificar</a>
                                                        <a href="#planta{{ $res->id_planta }}" class="btn btn-secondary btn-xs btn_ver  add-tooltip" title="Ver puesto en plano/mapa" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-puesto="{{ $res->id_puesto }}"><i class="fad fa-search-location"></i> Ver puesto</a>
                                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs btn_del  add-tooltip" title="Cancelar reserva" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-des_puesto="{{ $res->cod_puesto }}"><i class="fad fa-trash-alt"></i> Borrar</a> 
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
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
                    <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ isset($reserva->fec_reserva)?Carbon\Carbon::parse($reserva->fec_reserva)->format('H:i'):'00:00' }}">
                    <input type="hidden" name="hora_fin" id="hora_fin" value="{{ isset($reserva->fec_fin_reserva)?Carbon\Carbon::parse($reserva->fec_fin_reserva)->format('H:i'):'23:59' }}">
                    {{csrf_field()}}
                    <div class="form-group col-md-4">
                        <label for="fechas">Fecha</label>
                        {{--  <div class="input-group">
                            <input type="text" class="form-control pull-left singledate" id="fechas" name="fechas" style="width: 180px" value="{{ $f1->format('d/m/Y')}}">
                            <span class="btn input-group-text btn-secondary datepickerbutton" disabled  style="height: 33px"><i class="fas fa-calendar mt-1"></i></span>
                        </div>  --}}
                        <div class="input-group">
                            <input type="text" class="form-control pull-left" id="fechas" autocomplete="off" name="fechas" style="" value="{{ $f1->format('d/m/Y').' - '.$f1->format('d/m/Y') }}">
                            <span class="btn input-group-text btn-secondary btn_calendario"   style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                        </div>

                    </div>
                    <div class="form-group col-md-3">
                        <label for="id_edificio"><i class="fad fa-building"></i> Edificio</label>
                        <select name="id_edificio" id="id_edificio" class="form-control">
                            @foreach(DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $edificio)
                                <option value="{{ $edificio->id_edificio}}" {{ $reserva->id_edificio==$edificio->id_edificio?'selected':'' }}>{{ $edificio->des_edificio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="planta"><i class="fad fa-layer-group"></i> Planta</label>
                        <select name="id_planta" id="id_planta" class="form-control">
                            <option value="0">Cualquiera</option>
                            @foreach($plantas_usuario as $p)
                                <option value="{{ $p->id_planta}}" {{ $reserva->id_planta==$p->id_planta?'selected':'' }}>{{ $p->des_planta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="id_usuario">Tipo de puesto</label>
                        <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                            @foreach($tipos as $t)
                                <option value="{{ $t->id_tipo_puesto}}" {{ isset($reserva->id_tipo_puesto)&&$reserva->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }} data-observaciones="{{ $t->observaciones }}" data-slots="{{ $t->slots_reserva }}">{{ $t->des_tipo_puesto }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    
                   
                    
                </div>
                <div class="row mt-2">
                    @if(session('CL')['mca_reserva_horas']=='S')
                    <div class="form-group col-md-7" id="slider" style="padding-left: 30px; padding-right: 30px">
                        <label for="hora-range-drg"><i class="fad fa-clock"></i> Horas [<span id="horas_rango"></span>] <span id="obs" class="text-info"></span></label>
                        <div id="hora-range-drg" style="margin-top: 40px"></div><span id="hora-range-val" style="display: none"></span>
                    </div>
                    @endif
                    <div class="form-group col-md-7"  id="slots" style="display: none">
                    
                    </div>
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
                    <div class="d-flex">
                        
                        <div class="form-check form-switch">
                            <input id="_dm-dbInvisibleMode" class="form-check-input" type="checkbox"  name="mca_ical"  id="mca_ical" value="S">
                        </div>
                        <label class="form-check-label h6 mt-1" for="_dm-dbInvisibleMode">Añadir a mi calendario</label>
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

    $('.btn_calendario').click(function(){
        picker.show();
    })

    $('#id_tipo_puesto').change(function(){
        $('#obs').html();
        var selected = $(this).find('option:selected');
        $('#obs').html("<i class='fa fa-info-circle'></i> "+selected.data('observaciones'));
        if(selected.data('slots')!=""){
            $('#slots').load('{{ url('reservas/slots') }}/'+$(this).val()+"/{{ $reserva->id_reserva??0 }}", function(){
                console.log("cambio a slots")
                $('#slots').show();
                $('#slider').hide();
            });
        } else {
            $('#slider').show();
            $('#slots').hide();
            console.log("cambio a slider")
        }
        
        console.log(selected.data('slots'));
    })

    $('#id_edificio').change(function(){
        $('#id_planta').load("{{ url('/combos/plantas/') }}/"+$(this).val(), function(){
            $('#id_planta').prepend("<option value='0'>Cualquiera</option>")
            $('#id_planta').val({{ $reserva->id_planta }});
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


    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    $('#val_icono').iconpicker({
        icon:'{{isset($t) ? ($t->val_icono) : ''}}'
    });

    function comprobar_puestos(){
        //console.log($('#fechas').val());
        $.post('{{url('/reservas/comprobar')}}', {_token: '{{csrf_token()}}',fechas: $('#fechas').val(),edificio:$('#id_edificio').val(),tipo: $('#tipo_vista').val(), hora_inicio: $('#hora_inicio').val(),hora_fin: $('#hora_fin').val(), tipo_puesto: $('#id_tipo_puesto').val(),id_planta:$('#id_planta').val(),tags:$('#multi-tag').val(),andor:$('#andor').is(':checked'),'id_reserva':{{ $edit??0 }}}, function(data, textStatus, xhr) {
            $('#detalles_reserva').html(data);
        });
    }

    $('#id_edificio, #id_tipo_puesto, #id_planta, #multi-tag').change(function(){
      comprobar_puestos();
    })

    var picker = new Litepicker({
        element: document.getElementById( "fechas" ),
        singleMode: false,
        @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
        @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
        autoApply: true,
        format: 'DD/MM/YYYY',
        lang: "es-ES",
        lockDays: [{!! $festivos_usuario !!}],
        maxDays: {{ config_cliente('max_dias_reserva',Auth::user()->id_cliente) }},
        tooltipText: {
            one: "day",
            other: "days"
        },
        tooltipNumber: (totalDays) => {
            return totalDays - 1;
        },
        lockDaysFilter: (day) => {
            const d = day.getDay();
            return [-1{{ $perfil_usuario->mca_reservar_sabados=='N'?'':',6' }}{{ $perfil_usuario->mca_reservar_domingos=='N'?'':',0' }}].includes(d);
        },
        setup: (picker) => {
            picker.on('selected', (date1, date2) => {
                comprobar_puestos();
            });
        }
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
    $('.btn_edit').click(function(){
        $('#editorCAM').load("{{ url('/reservas/edit/') }}/"+$(this).data('id'), function(){
            $('#id_tipo_puesto').trigger('change');
        });
        //animateCSS('#editor','fadeOut',$('#editor').html(''));
    })

    $('.btn_ver').click(function(){
        $('.flpuesto').removeClass('glow');
        $('#puesto'+$(this).data('puesto')).addClass('glow');
    })

    @if(isset($edit))
        $(function(){
            r_def.noUiSlider.set(["{{ isset($reserva->fec_reserva)?time_to_dec(Carbon\Carbon::parse($reserva->fec_reserva)->format('H:i:s'))/60:config_cliente('min_hora_reservas') }}", "{{ isset($reserva->fec_fin_reserva)&&$reserva->fec_fin_reserva!=''?time_to_dec(Carbon\Carbon::parse($reserva->fec_fin_reserva)->format('H:i:s'))/60:config_cliente('min_hora_reservas') }}"])
        })
    @endif


 </script>
