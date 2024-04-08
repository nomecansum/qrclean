
<style type="text/css">
    .popover {
        z-index: 100000;
    }
</style>

    <div class="card editor mb-5">

        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nuevo bloqueo programado de puestos
                    @else
                        Editar bloqueo programado de puestos
                    @endif
                </h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <form method="POST" action="{{ url('/avisos/save') }}" id="edit_bloqueo_form" name="edit_bloqueo_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row mt-2">
                    <input type="hidden" name="id_aviso" value="{{ $id }}">
                    <div class="form-group col-md-10 {{ $errors->has('val_titulo') ? 'has-error' : '' }}">
                        <label for="val_titulo" class="control-label">Nombre</label>
                        <input class="form-control" required name="val_titulo" type="text" id="val_titulo" value="{{ old('val_titulo', optional($aviso)->val_titulo) }}" maxlength="200" placeholder="Enter nombre here...">
                        {!! $errors->first('val_titulo', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-12 {{ $errors->has('txt_aviso') ? 'has-error' : '' }}">
                        <label for="txt_aviso" class="control-label">Texto</label>
                        <textarea  class="textarea_editor form-control" name="txt_aviso" id="txt_aviso" rows="6" style="height: 200px;">
                            {{ old('des_motivo', optional($aviso)->txt_aviso) }}
                        </textarea>
                        {!! $errors->first('txt_aviso', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                        <label for="id_cliente" class="control-label">Cliente</label>
                        <select class="form-control" required id="multi-cliente" name="cliente">
                            @foreach ($Clientes as $key => $Cliente)
                                <option value="{{ $key }}" {{ old('id_cliente', optional($aviso)->id_cliente) == $key||$id==0 && $key==session('CL')['id_cliente'] ? 'selected' : '' }}>
                                    {{ $Cliente }}
                                </option>
                            @endforeach
                        </select>
                            
                        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-4" >
                        <label>Fechas </label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control pull-left rangepicker" id="fechas" name="fechas" value="{{  Carbon\Carbon::parse($aviso->fec_inicio)->format('d/m/Y').' - '.Carbon\Carbon::parse($aviso->fec_fin)->format('d/m/Y') }}">
                            <span class="btn input-group-text btn-secondary btn_fechas" ><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                        </div>
                    </div>
                    <div class="form-group col-md-1">
                        <label for="val_color">Color</label><br>
                        <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($aviso->val_color)?$aviso->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    
                    <div class="form-group col-md-1" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" required name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-align="right" data-placement="inline" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    <div class="col-md-1 p-t-30 ">
                        <div class="form-check">
                            <input name="mca_activo"  id="mca_activo" value="S" {{ $aviso->mca_activo=='S'?'checked':'' }}  class="form-check-input" type="checkbox">
                            <label for="mca_activo" class="form-check-label">Activo</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @include('resources.combos_filtro',[$hide=['cli'=>1,'head'=>1,'btn'=>1,'usu'=>1,'est_inc'=>1,'tip_mark'=>1, 'tip_inc'=>1,'tag'=>1,'pue'=>1,'est'=>1],$show=['perfil'=>1,'tur'=>1]])
                </div>
                <div class="form-group mt-3">
                    <div class="col-md-12 text-end">
                        @if(checkPermissions(['Bloqueo de puestos'],['W'])) <input class="btn btn-primary" type="submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
                    </div>
                </div>
            </form>

        </div>
    </div>

    @yield('scripts2')
    <script src="{{ asset('/plugins/html5-editor/wysihtml5-0.3.0.js') }}"></script>
    <script src="{{ asset('/plugins/html5-editor/bootstrap-wysihtml5.js') }}"></script>
    <script>
    $('.textarea_editor').wysihtml5({
        events: {
            load: function () {
                $('.textarea_editor').addClass('textnothide');
            }
        }
    });

    $('#val_icono').iconpicker({
        icon:'{{isset($aviso) ? ($aviso->val_icono) : ''}}'
    });

    //Ponemos los valores seleccionados que tengan los select2
    $(document).ready(function() {
       //console.log($("#multi-planta"));
         $('#multi-cliente').select2().val({{ old('id_cliente', optional($aviso)->id_cliente) }});
       $('#multi-cliente').change();
       $('.scroll-top').click();
       s=setTimeout('end_update_filtros()',2000);
      
    });

    // function end_update_filtros(entidad){
    //     console.log('end update '+entidad)
    //     $('#multi-planta').select2().val(61,);
    //    //$('#multi-planta').trigger('change');
    //    console.log($('#multi-planta').select2().val());
    // }
    function end_update_filtros(entidad){
        //window.scrollTo(0,0);
        console.log('end_update');
        string="{{ $aviso->val_edificios }}"
        var arr = string.split(',');
        $('#multi-edificio').select2().val(arr);

        string="{{ $aviso->val_plantas }}"
        var arr = string.split(',');
        $('#multi-planta').select2().val(arr);

        string="{{ $aviso->val_perfiles }}"
        var arr = string.split(',');
        $('#multi-perfiles').select2().val(arr);

        string="{{ $aviso->val_turnos }}"
        var arr = string.split(',');
        $('#multi-turnos').select2().val(arr);

        string="{{ $aviso->val_tipo_puesto }}"
        var arr = string.split(',');
        $('#multi-tipo').select2().val(arr);

        $('#multi-edificio').select2().val();
        $('#multi-planta').select2().val();
        $('#multi-perfiles').select2().val();
        $('#multi-turnos').select2().val();
        $('#multi-tipo').select2().val();

        
    }

    $(".select2").select2({
        placeholder: "Seleccione",
        allowClear: true,
        width: "90%",
    });

    $('.btn_fechas').click(function(){
        rangepicker.show();
    })

    $('.form-ajax').submit(form_ajax_submit);

    //$('#frm_contador').on('submit',form_ajax_submit);
    $('#frm_contador').submit(form_ajax_submit);

    $('#val_icono').iconpicker({
        icon:'{{isset($aviso) ? ($aviso->val_icono) : ''}}'
    });

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    $('.add_nuevo').click(function(){
        $($('#editor_nuevo').html()).insertBefore("#div_nuevo");
        //$('#editor_nuevo').show();
    });

    $('#mca_slots').click(function(){
        if($(this).is(':checked')){
            $('#body_slots').show();
        }else{
            $('#body_slots').hide();
        }
    });
    
    $('.fin').change(function(){
        var hora_inicio=moment($(this).parents(':eq(1)').find('.hora_inicio').val(), ['h:m a', 'H:m']);
        var hora_fin=moment($(this).val(), ['h:m a', 'H:m']);
        console.log(hora_inicio+' '+hora_fin);
        if(hora_inicio>hora_fin){
            toast_warning('Hora incorrecta','La hora de inicio no puede ser mayor a la hora de fin');
            $(this).val(hora_inicio.add(1,'hours').format('H:m'));
        }
    });

    var rangepicker = new Litepicker({
        element: document.getElementById( "fechas" ),
        singleMode: false,
        @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
        @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
        autoApply: true,
        format: 'DD/MM/YYYY',
        lang: "es-ES",
        tooltipText: {
            one: "day",
            other: "days"
        },
        tooltipNumber: (totalDays) => {
            return totalDays - 1;
        },
        setup: (rangepicker) => {
            rangepicker.on('selected', (date1, date2) => {
                //comprobar_puestos();
            });
        }
    });

    
    </script>
