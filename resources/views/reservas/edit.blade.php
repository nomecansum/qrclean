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
    .litepicker .container__days .day-item.is-locked{
        background-color: #f2dede !important;
    }

    

</style>
@if(count($misreservas)>0)
    <div class="card mb-3">
        <div class="card-body">
            <div class="row b-all rounded mb-2">
                <div class="col-md-12">
                    <label for="fechas">Reservas activas para el día {{ $f1->format('d/m/Y')}}</label>
                    <div class="table-responsive">
                        @mobile
                        <table id="tablares" class="table table-hover"
                        data-toggle="table"
                        data-mobile-responsive="true"
                        data-locale="es-ES">
                        @elsemobile
                        <table id="tablares" class="table table-hover">
                        @endmobile
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
                                <tr class="p-2 {{ $res->mca_anulada=='S'?'bg-gray':'' }}">
                                    <td>{{ $res->id_reserva }}</td>
                                    <td>{{ Carbon\Carbon::parse($res->fec_reserva)->format('H:i') }} @if($res->fec_fin_reserva!=null)<i class="fas fa-arrow-right"></i> {{ Carbon\Carbon::parse($res->fec_fin_reserva)->format('H:i') }}@endif {{ $res->mca_anulada=='S'?'[ANULADA]':'' }}</td>
                                    <td style="color: {{ $res->val_color }}"><i class="{{ $res->val_icono }}"></i>{{ $res->des_tipo_puesto }}</td>
                                    <td class="text-center">{{ $res->cod_puesto }}</td>

                                    @if($res->mca_anulada=='N')
                                    <td class="text-end">
                                        <div class="pull-right" style="bottom: 0px">
                                            <div class="btn-group btn-group pull-right" role="group">
                                                <a href="#" class="btn btn-info btn-xs btn_edit  add-tooltip" onclick="editar_reserva({{ $res->id_reserva }})" title="Modificar reserva" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-des_puesto="{{ $res->cod_puesto }}"><i class="fad fa-pencil-alt"></i> Editar</a>
                                                <a href="#planta{{ $res->id_planta }}" class="btn btn-secondary btn-xs btn_ver  add-tooltip" title="Ver puesto en plano/mapa" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-puesto="{{ $res->id_puesto }}"><i class="fad fa-search-location"></i> Ver</a>
                                                <a href="#" class="btn btn-danger btn-xs btn_del  add-tooltip" onclick="borrar_reserva({{ $res->id_reserva }},$(this))" title="Cancelar reserva" data-id="{{ $res->id_reserva }}" data-fecha="{{ Carbon\Carbon::parse($res->fec_reserva)->format('d/m/Y') }}" data-des_puesto="{{ $res->cod_puesto }}"><i class="fad fa-trash-alt"></i> Cancelar</a>
                                            </div>
                                        </div>
                                    </td>
                                    @else
                                    <td></td>
                                    @endif

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endif
 
<div class="card" id="editor">
    
    <div class="card">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Reserva de puesto</h5>
            </div>
            <div class="toolbar-end">

                <!-- Nav tabs -->
                <ul class="nav nav-pills card-header-pills" role="tablist">
                    @if(session('CL')['val_editor_reservas']=='R' || session('CL')['val_editor_reservas']=='T')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#div_rapido" type="button" id="btn_rapida" role="tab" aria-controls="rapido" aria-selected="true">Reserva rápida</button>
                    </li>
                    @endif
                    @if(session('CL')['val_editor_reservas']=='A' || session('CL')['val_editor_reservas']=='T')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#div_avanzado" type="button"  id="btn_avanzada"  role="tab" aria-controls="avanzado" aria-selected="false">Avanzado</button>
                    </li>    
                    @endif            
                </ul>
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>
        <div class="card-body tab-content">
            @if(isset(session('CL')['val_editor_reservas']) && (session('CL')['val_editor_reservas']=='R' || session('CL')['val_editor_reservas']=='T'))
                <div id="div_rapido" class="tab-pane fade active show" role="tabpanel" aria-labelledby="rapido-tab">
                    @include('reservas.fill_editor_reserva_rapido')
                </div>
            @endif
            @if(!isset(session('CL')['val_editor_reservas']) || (isset(session('CL')['val_editor_reservas']) && (session('CL')['val_editor_reservas']=='A' || session('CL')['val_editor_reservas']=='T')))
                <div id="div_avanzado" class="tab-pane fade" role="tabpanel" aria-labelledby="avanzado-tab">
                    @include('reservas.fill_editor_reserva_avanzado')
                </div>
            @endif
            <div class="row mt-2">
                @if(session('CL')['mca_reserva_horas']=='S')
                <div class="form-group col-md-7" id="slider" style="padding-left: 30px; padding-right: 30px"  style="display: none">
                    <label for="hora-range-drg"><i class="fad fa-clock"></i> Horas [<span id="horas_rango"></span>] <span id="obs" class="text-info"></span></label>
                    <div id="hora-range-drg" style="margin-top: 40px"></div><span id="hora-range-val" style="display: none"></span>
                </div>
                @endif
                <div class="form-group col-md-12"  id="slots" style="display: none">
                
                </div>
                <div class="form-group col-md-5" id="div_tags" style="display: none">
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
            <div class="row" id="btn_add_calendario" style="display:none">
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
        </div>
    </div>
 </div>
 <script>

    //$('#frm_contador').on('submit',form_ajax_submit);
    $(function(){
        $('#id_planta').load("{{ url('/combos/plantas_usuario/') }}/"+$('#id_edificio').val(), function(){
            $('#id_planta').val({{ $reserva->id_planta==0?session('planta_pref'):$reserva->id_planta }});
            $('#id_planta option[value={{ $reserva->id_planta }}]').attr('selected','selected');
            $('#id_planta').prepend("<option value='0'>Cualquiera</option>")
        });
        comprobar_puestos();
        var slots=false;
        var tipo_seleccionado=0;
        var edificio_rapido=0;
        var planta_rapida=0;
    })

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
        $('#id_planta').load("{{ url('/combos/plantas_usuario/') }}/"+$(this).val(), function(){
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

    

    $('#frm_contador').submit(function(event){
        event.preventDefault();
        block_espere();
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
            fin_espere();
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

    function comprobar_rapida(){
        //console.log($('#fechas').val());
        $plantas=[];
        $('.btn_planta').each(function(){
            if($(this).is(':checked')){
               
                $plantas.push($(this).val());
                $('#id_planta').val($plantas);
            }
        })
        $('.btn_tipo_puesto').each(function(){
            if($(this).is(':checked')){
                $('#id_tipo_puesto').val($(this).val());
            }
        })

        console.log($('#id_planta').val(),$('#id_tipo_puesto').val());
        $.post('{{url('/reservas/comprobar')}}', {_token: '{{csrf_token()}}',fechas: $('#fechas').val(),edificio:edificio_rapido,tipo: $('#tipo_vista').val(), hora_inicio: $('#hora_inicio').val(),hora_fin: $('#hora_fin').val(), tipo_puesto: $('#id_tipo_puesto').val(),id_planta:planta_rapida,tags:$('#multi-tag').val(),andor:$('#andor').is(':checked'),'id_reserva':{{ $edit??0 }}}, function(data, textStatus, xhr) {
            $('#detalles_reserva').html(data);
        });
    }

    $('#id_edificio, #id_tipo_puesto, #id_planta, #multi-tag').change(function(){
      comprobar_puestos();
    });

    function mostrar_datos_reserva_rapida(){
        
        console.log(slots);
        if(slots==true){
            $('#slots').load('{{ url('reservas/slots') }}/'+tipo_seleccionado+"/{{ $reserva->id_reserva??0 }}", function(){
                $('#slots').show();
                $('#slider').hide();
            });
            console.log("cambio a slots")
        } else {
            $('#slider').show();
            $('#slots').hide();
            console.log("cambio a slider")
        }
        comprobar_rapida();
    }

    $('.btn_tipo_puesto').click(function(){
        //Primero comprobamos si el tipo tiene mas de una planta
        slots=$(this).data('slots')!=0?true:false;
        $('#slots').hide();
        $('#slider').hide();
        tipo_seleccionado=$(this).val();
        $.get('{{ url('reservas/plantas_tipo') }}/'+$(this).val(), function(data) {
            $('.btn_planta').prop('checked',false);
            $('.div_plantas').hide();
            $('#detalles_reserva').empty();
            planta_rapida=0;
            if(data.length>1){
                $('.div_plantas').show();
                $('.btn_planta').prop('checked',false);
                $('.btn_planta').hide();
                $('.lbl_planta').hide();
                //Vamos a mostrar solo las plantas que tocan
                data.forEach(function(item){
                    
                    var array = item.plantas.split(",");
                    array.forEach(function(p){
                        $('#btnplanta'+p).show();
                        $('#btnplanta'+p).next().show();
                    })
                })
               
                edificio_rapido=0;
            } else {
                edificio_rapido=data[0].id_edificio;
                mostrar_datos_reserva_rapida();
            }
        });
    });
    $('.btn_planta').click(function(){
        edificio_rapido=$(this).data('edificio');
        planta_rapida=$(this).data('planta');
        mostrar_datos_reserva_rapida();
    });

    var picker = new Litepicker({
        element: document.getElementById( "fechas" ),
        @if(session('perfil')->mca_reservar_rango_fechas=='N')singleMode: true @else singleMode: false @endif,
        @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
        @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
        autoApply: true,
        format: 'DD/MM/YYYY',
        lang: "es-ES",
        firstDay: 1,
        lockDays: {!! $festivos_usuario??0 !!},
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
            return [-1{{ $perfil_usuario->mca_reservar_sabados=='N'?',6':'' }}{{ $perfil_usuario->mca_reservar_domingos=='N'?',0':'' }}].includes(d);
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

    function borrar_reserva(id,obj){
        $.post('{{url('/reservas/cancelar')}}', {_token: '{{csrf_token()}}',fecha: obj.data('fecha'),id: obj.data('id'), des_puesto: obj.data('des_puesto')}, function(data, textStatus, xhr) {
            
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
    }


    $('.btn_del').click(function(){

        
    })
    function editar_reserva(id){
        $('#editorCAM').load("{{ url('/reservas/edit/') }}/"+id, function(){
            $('#id_tipo_puesto').trigger('change');
        });
    }
    $('.btn_edit').click(function(){
       
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

    @mobile
    $('#tablares').bootstrapTable();
    @endmobile


    $('#btn_rapida').click(function(){
        $('#div_tags').hide();
        $('#btn_add_calendario').hide();
        $('#slots').removeClass('col-md-7');
        $('#slots').addClass('col-md-12');
    })
    $('#btn_avanzada').click(function(){
        $('#div_tags').show();
        $('#btn_add_calendario').show();
        $('#slots').removeClass('col-md-12');
        $('#slots').addClass('col-md-7');
    })

    @if(session('CL')['val_editor_reservas']=='R' || session('CL')['val_editor_reservas']=='T')
        $('#btn_rapida').click();
    @else
        $('#btn_avanzada').click();
    @endif

 </script>
