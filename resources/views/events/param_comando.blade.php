
<style>
    .select-all{
           max-height: 46px !important;
       }
</style>
@php
$clientes=DB::table('clientes')
        ->wherein('id_cliente',clientes())
        ->get();
@endphp

<div class="row">
    <div class="form-group  pr-5 col-md-12" style="{{ (isset($hide['cli']) && $hide['cli']===1) ? 'display: none' : ''}}">
        <label>{{trans('general.clientes')}}
            @include('resources.spin_puntitos',['id_spin'=>'spin_cli','clase'=>'spin_cli'])
        </label><br>
        <select class="mb-2 col-md-11  form-control" multiple="multiple" name="clientes[]" id="multi-clientes" lang="{{ config('app.lang', 'es') }}">
            @foreach ($clientes as $c)
                <option value="{{$c->id_cliente}}" @if(isset($reglas)&&in_array($c->id_cliente,explode(",",$reglas->clientes))) selected @endif>{{$c->nom_cliente}}</option>
            @endforeach
        </select>
        <button class="btn btn-primary float-right mt-0 position-absolute select-all"data-select="multi-clientes" style="height: 47px"  type="button"><i class="fad fa-check-double"></i> Todos</button>
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
        width: '90%'
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
