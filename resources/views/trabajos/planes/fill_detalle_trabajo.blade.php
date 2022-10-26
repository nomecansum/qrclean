<form method="POST" action="{{ url('/trabajos/planes/detalle_save') }}" id="edit_plan_detalle" name="edit_plan_detalle" accept-charset="UTF-8" class="form-horizontal form-ajax">
{{ csrf_field() }}
    <div class="row">
        <input type="hidden" name="id_plan" value="{{ $r->id_plan }}">
        <input type="hidden" name="id_grupo" value="{{ $r->grupo }}">
        <input type="hidden" name="tipo" value="{{ $r->tipo }}">
        <input type="hidden" name="id_trabajo" value="{{ $r->trabajo }}">
        @if($r->tipo=='P')
        <input type="hidden" name="id_planta" value="{{ $r->id }}">
        @else
        <input type="hidden" name="id_zona" value="{{ $r->id }}">
        @endif

        <div class="col-md-4">
            <label for="id_contrata" class="control-label">Contrata</label>
            <select class="form-control" required id="id_contrata" name="id_contrata">
                @foreach ($contratas as $dato)
                    <option value="{{ $dato->id_contrata }}" {{ old('id_contrata', optional($detalle)->id_contrata) == $dato->id_contrata ? 'selected' : '' }}>
                        {{ $dato->des_contrata }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="sel_operarios" class="control-label">Operarios</label>
            <select class="form-control" required id="sel_operarios" name="sel_operarios">
                <option value="0">Genericos</option>
                <option value="1" {{ isset($detalle) && old('id_contrata', optional($detalle)->num_operarios) == null ? 'selected' : '' }}>Detallar</option>
            </select>
        </div>
        {{-- <div class="col-md-2">
            <label for="sel_tiempo" class="control-label">Tiempo</label>
            <select class="form-control" required id="sel_tiempo" name="sel_tiempo">
                <option value="0">Generico</option>
                <option value="1" {{ isset($detalle) && old('id_contrata', optional($detalle)->val_tiempo) == null ? 'selected' : '' }}>Calculado</option>
            </select>
        </div> --}}
    </div>
    <div class="row  mt-3" id="detalle_mini">
        @include('trabajos.planes.fill_mini_detalle')
    </div>
</form>

<script>
    $('#sel_operarios').on('change', function() {
        if (this.value == 1) {
            $('#div_operarios_gen').hide();
            $('#div_operarios_esp').show();
            $('#list_operarios').show();
        } else {
            $('#list_operarios').hide();
            $('#div_operarios_gen').show();
            $('#div_operarios_esp').hide();
        }
    });


    $('#id_contrata').change(function(){
        var id_contrata = $(this).val();
        var url = "{{ url('/trabajos/planes/mini_detalle',[$r->id_plan,$r->grupo,$r->trabajo]) }}/"+id_contrata+"/"+$('#sel_operarios').val()+"/"+$('#sel_tiempo').val();
        $('#detalle_mini').load(url);
    });

    
</script>
