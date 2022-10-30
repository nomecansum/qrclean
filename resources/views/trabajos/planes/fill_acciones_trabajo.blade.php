<div class="row mb-3">
    <label class="text-danger font-bold"><i class="fa-solid fa-trash"></i> Borrar configuracion</label>
    <div class="col-md-4" id="boton_borrar_1">
        <a class="btn btn-lg btn-danger btn_borrar_detalle" data-id="{{ $detalle->key_id }}"><i class="fa-solid fa-trash"></i> Borrar</a>
    </div>
    <div class="col-md-8" id="boton_borrar_2" style="display: none">
        <label>¿Seguro que quiere borrar este detalle? Esta accion no puede deshacerse</label>
        <a class="btn btn-lg btn-danger btn_borrar_detalleOK mr-3" data-id="{{ $detalle->key_id }}"><i class="fa-solid fa-trash"></i>Si, Borrar</a>
        <a class="btn btn-lg btn-warning btn_borrar_detalleCANCEL" data-id="{{ $detalle->key_id }}">Cancelar</a>
    </div>
    
</div>

<div class="row">
    <label><i class="fa-solid fa-copy"></i> Copiar configuracion a otros trabajos</label>
    <h5>PLANTAS</h5>
    @foreach($lista_plantas as $planta)
        <div class="col-md-4 mb-2">
            <input type="checkbox" class="chk_planta" name="plantas_copiar[]" data-id="{{ $planta->id_planta }}"  value="{{ $planta->id_planta }}">  {!! $planta->des_planta !!}
        </div>
    @endforeach
    <h5>ZONAS</h5>
    @foreach($lista_zonas as $zona)
        <div class="col-md-4 mb-2">
            <input type="checkbox" class="chk_planta" name="zonas_copiar[]" data-id="{{ $zona->id_zona }}"  value="{{ $zona->id_zona }}"> <span class="text-info">[{{ $zona->des_planta }}]</span>  {!! $zona->des_zona !!}
        </div>
    @endforeach
    <h5>OTROS TRABAJOS DEL PLAN</h5>
    @foreach($plan as $p)

        <div class="col-md-6 mb-2">
            <input type="checkbox" class="chk_planta" name="trabajos_copiar[]" data-id="{{ $p->id_trabajo }}" value="{{ $p->id_grupo.'_'.$p->id_trabajo }}"> <span class="text-info">[{{ $p->des_grupo }}]</span>  {!! $p->des_trabajo !!}
        </div>
    @endforeach
</div>

<div class="modal fade" id="borrar-detalle" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
               <h3 class="modal-title text-nowrap">Borrar detalle</h3>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body" id="detalle_periodo">
                ¿Borrar detalle de trabajo?
            </div>
            <div class="modal-footer">
                <a class="btn btn-info" id="btn_si_borrar" href="javascript:void(0)">Si</a>
                <button type="button" id="btn_no_borrar" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.btn_borrar_detalle').click(function(){
        $('#boton_borrar_1').hide();
        $('#boton_borrar_2').show();
        animateCSS('#boton_borrar_2', 'bounceInRight');
    })

    $('.btn_borrar_detalleCANCEL').click(function(){
        $('#boton_borrar_1').show();
        $('#boton_borrar_2').hide();
        animateCSS('#boton_borrar_2', 'bounceInRight');
    })

    $('.btn_borrar_detalleOK').click(function(){
        $.get("{{ url('trabajos/planes/delete_detalle') }}/"+$(this).data('id'), function(data){
            if (data.error) {
                toast_error(data.title,data.error);
            } else {
                toast_ok(data.title,data.message);
                $('.modal').modal('hide');
                $('.select2-multiple').trigger('change');
            }
        })
    })
</script>