

<div class="row">
    <div class="form-group col-md-12">
        <div class="form-group" style="{{ ((!fullAccess() && count(clientes())==1) || (isset($hide['cli']) && $hide['cli']===1)) ? 'display: none' : ''}}">
            <label>Cliente</label>
            <div class="input-group select2-bootstrap-append">
                <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="cliente[]" id="multi-cliente">
                    @foreach (lista_clientes() as $c)
                        <option value="{{$c->id_cliente}}">{{$c->nom_cliente}}</option>
                    @endforeach
                </select>
                <div class="input-group-btn">
                    <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button"><i class="fad fa-check-double"></i> todos</button>
                </div>
            </div>
        </div>
    </div>
</div>
@if(isset($parametros))
    @include('resources.form_parametros')
@endif



<script>
    $(".select2-multiple").select2({
        placeholder: "Todos",
        allowClear: true,
        width: "99.2%",
    });
    $('.select-all').click(function(event) {
        $(this).parent().parent().find('select option').prop('selected', true)
        $(this).parent().parent().find('select').select2();
        $(this).parent().parent().find('select').change();
    });

    $('.select-all').dblclick(function(event) {
        $(this).parent().parent().find('select option').prop('selected', false)
        $(this).parent().parent().find('select').select2();
        $(this).parent().parent().find('select').change();
    });

    // $('.btn_form').click(function(){
    //     $('#formcomando').submit();
    // })
    $('#txt-desc').html("{!! $descripcion !!}");

</script>