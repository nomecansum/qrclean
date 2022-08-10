

@php
if(count(session('clientes'))<100 && !fullAccess()){
    $clientes=DB::table('usuarios_clientes')
        ->join('clientes','clientes.id_cliente','usuarios_clientes.id_cliente')
        ->where('id_usuario',Auth::user()->id_usuario)
        ->get();
}
else if(isset($reglas)&&strlen($reglas->clientes)>0){
    $clientes=DB::table('usuarios_clientes')
        ->join('clientes','clientes.id_cliente','usuarios_clientes.id_cliente')
        ->where('id_usuario',Auth::user()->id_usuario)
        ->wherein('clientes.id_cliente',explode(",",$reglas->clientes))
        ->get();
    $clientes=collect($clientes);
}
else {
    $clientes=[];
    $clientes=collect($clientes);
}
@endphp

<div class="row">
    <div class="form-group  pr-5 col-md-12" style="{{ ((!fullAccess() && count(clientes())==1) || (isset($hide['cli']) && $hide['cli']===1)) ? 'display: none' : ''}}">
        <label>{{trans('general.clientes')}}
            @include('resources.spin_puntitos',['id_spin'=>'spin_cli','clase'=>'spin_cli'])
        </label>
        <select class="mb-2 col-md-11  form-control" multiple="multiple" name="clientes[]" id="multi-clientes" lang="{{ config('app.lang', 'es') }}">
            @foreach ($clientes as $c)
                <option value="{{$c->id_cliente}}" @if(isset($reglas)&&in_array($c->id_cliente,explode(",",$reglas->clientes))) selected @endif>{{$c->nombre_cliente}}</option>
            @endforeach
        </select>
        @if(count(session('clientes'))<100 && Auth::user()->mca_acceso_todos_clientes!=1)
            <button class="btn btn-info float-right mt-0 position-absolute select-all"data-select="multi-clientes" style="height: 47px"  @if(Auth::user()->mca_acceso_todos_clientes==1 || count(session('clientes'))>100) disabled @endif type="button"><i class="fad fa-check-double"></i></button>
        @endif
        @if(count(clientes())==1)
                <input type="hidden" value="{{$clientes->first()->id_cliente}}" name="clientes[]" >
        @endif
    </div>

</div>
@if(isset($parametros))
@php
    //dd($parametros);
@endphp
    @include('resources.form_parametros')
@endif



<script>
    $('#nom_queue').val("{{ $scope??'events' }}");
    $("#multi-clientes").select2(
    {
        placeholder: "Todos",
        allowClear: true,
        width: '90%',
        minimumInputLength: 3,
        ajax: { url: "{{ asset('/combos/clientes_search') }}", type: "post", dataType: 'json', delay: 500,data: function (params) {
        $('#spin_cli').show();
        return {
            searchTerm: params.term,// search term
            _token:'{{csrf_token()}}'
            };

        },
        processResults: function (response) {
            $('#spin_cli').hide();
            return {
                results: response
            };
        },
        cache: true
        }
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


    $('#multi-clientes').change(function(event) {
        $('.multi2').empty();
        $('.spin_tag').show();
        $('.spin_disp').show();
        $.post('{{url(config('app.carpeta_asset').'/filters/loadtags')}}', {_token:'{{csrf_token()}}',clientes:$(this).val()}, function(data, textStatus, xhr) {
            console.log("hemos leido");
            $('.multitags').empty();
            $('.multitags').val(null).trigger('change');
            $('.multidispositivos').empty();
            $('.multidispositivos').val(null).trigger('change');
            cliente="";
            $.each(data.tags, function(index, val) {
                if(cliente!=val.nombre_cliente){
                    $('.multitags').append('<optgroup label="'+val.nombre_cliente+'"></optgroup>');
                    cliente=val.nombre_cliente;
                }
                $('.multitags').append('<option value="'+val.id_tag+'">'+val.nombre_tag+'</option>');
            });
            setTimeout(() => {
                $('.spin_tag').hide();
            }, 1000);
            

            cliente="";
            $.each(data.dispositivos, function(index, val) {
                if(cliente!=val.nombre_cliente){
                    $('.multidispositivos').append('<optgroup label="'+val.nombre_cliente+'"></optgroup>');
                    cliente=val.nombre_cliente;
                }
                $('.multidispositivos').append('<option value="'+val.id_dispositivo+'">'+val.nombre+'</option>');
            });
            setTimeout(() => {
                $('.spin_disp').hide();
            }, 1000);

        //Ahora tenemos que seleccionar los tags o dispositivos que estuvieran marcados
        @if(isset($parametros))
            @foreach($parametros as $p)
                @if(($p->tipo=="tags" || $p->tipo=="disp") && isset($p->value))
                    $("#{{ $p->tipo }}-{{ $p->name }}").val({!! js_array($p->value,'num') !!});
                    $("#{{ $p->tipo }}-{{ $p->name }}").trigger('change');
                @endif
            @endforeach
        @endif

        });
    });

    $('.select2-multiple').on('change', function(){
        $(this).find('option').css('bg-primary');
        $(this).find('option:selected').addClass('bg-primary');
    });

    @if(isset($reglas)&&strlen($reglas->clientes)>0)
        $('#multi-clientes').trigger('change');
    @endif
</script>
