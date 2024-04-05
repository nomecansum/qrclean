
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
            <form method="POST" action="{{ url('/bloqueo/save') }}" id="edit_bloqueo_form" name="edit_bloqueo_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row mt-2">
                    <input type="hidden" name="id_bloqueo" value="{{ $id }}">
                    <div class="form-group col-md-12 {{ $errors->has('des_motivo') ? 'has-error' : '' }}">
                        <label for="des_motivo" class="control-label">Motivo</label>
                        <input class="form-control" required name="des_motivo" type="text" id="des_motivo" value="{{ old('des_motivo', optional($tipo)->des_motivo) }}" maxlength="200" placeholder="Enter nombre here...">
                        {!! $errors->first('des_motivo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                        <label for="id_cliente" class="control-label">Cliente</label>
                        <select class="form-control" required id="multi-cliente" name="cliente">
                            @foreach ($Clientes as $key => $Cliente)
                                <option value="{{ $key }}" {{ old('id_cliente', optional($tipo)->id_cliente) == $key||$id==0 && $key==session('CL')['id_cliente'] ? 'selected' : '' }}>
                                    {{ $Cliente }}
                                </option>
                            @endforeach
                        </select>
                            
                        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_turno">Turno</label>
                        <select name="id_turno" id="id_turno"  class="form-control">
                            <option value="0">Cualquiera</option>
                            @foreach($turnos as $dato)
                            <option value="{{ $dato->id_turno}}" {{$tipo->id_turno==$dato->id_turno?'selected':''}}>{{ $dato->des_turno }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4" >
                        <label>Fechas </label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control pull-left rangepicker" id="fechas" name="fechas" value="{{  Carbon\Carbon::parse($tipo->fec_inicio)->format('d/m/Y').' - '.Carbon\Carbon::parse($tipo->fec_fin)->format('d/m/Y') }}">
                            <span class="btn input-group-text btn-secondary btn_fechas" ><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                        </div>
                    </div>
                    <div class="row">
                        @include('resources.combos_filtro',[$hide=['cli'=>1,'head'=>1,'btn'=>1,'usu'=>1,'est_inc'=>1,'tip_mark'=>1, 'tip_inc'=>1]])
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

    <script>
    //Ponemos los valores seleccionados que tengan los select2
    $(document).ready(function() {
       //console.log($("#multi-planta"));
         $('#multi-cliente').select2().val({{ old('id_cliente', optional($tipo)->id_cliente) }});
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
        string="{{ $tipo->list_edificios }}"
        var arr = string.split(',');
        $('#multi-edificio').select2().val(arr);

        string="{{ $tipo->list_plantas }}"
        var arr = string.split(',');
        $('#multi-planta').select2().val(arr);

        string="{{ $tipo->list_puestos }}"
        var arr = string.split(',');
        $('#multi-puesto').select2().val(arr);

        string="{{ $tipo->list_tags }}"
        var arr = string.split(',');
        $('#multi-tag').select2().val(arr);

        string="{{ $tipo->list_tipos }}"
        var arr = string.split(',');
        $('#multi-tipo').select2().val(arr);

        string="{{ $tipo->list_estados}}"
        var arr = string.split(',');
        $('#multi-estado').select2().val(arr);

        $('#multi-edificio').select2().val();
        $('#multi-planta').select2().val();
        $('#multi-puesto').select2().val();
        $('#multi-tag').select2().val();
        $('#multi-tipo').select2().val();
        $('#multi-estado').select2().val();

        
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
        icon:'{{isset($tipo) ? ($tipo->val_icono) : ''}}'
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
