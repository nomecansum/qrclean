@php
    $edificio_ahora=0;
    $planta_ahora=0;
@endphp
<div class="row botones_accion">
    <div class="col-md-8">
        <span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="col-md-4 text-right">
        <a href="javascript:void(0)" class="mr-2 boton_modo" data-href="comprobar" style="color: #1e90ff"><i class="fad fa-th"></i> Mosaico</a>
        <a href="javascript:void(0)" class="mr-2 boton_modo" data-href="comprobar_plano"><i class="fad fa-map-marked-alt"></i> Plano</a>
    </div>
</div>
@foreach ($edificios as $e)
<div class="panel">
    <div class="panel-body">
        @php
            $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
        @endphp
        @foreach($plantas as $key=>$value)
            <h3 class=" w-100 bg-gray rounded">PLANTA {{ $value }}</h3>
            @php
                $puestos_planta=$puestos->where('id_planta',$key);
            @endphp
            <div class="d-flex flex-wrap">
                @foreach($puestos_planta as $p)
                @php
                    if(in_array($p->id_puesto,$reservas->pluck('reservas.id_puesto')->toArray())){
                        $color="LightCoral";
                        $clase_disp="";
                    }   else {
                        $color="#dff9d2";
                        $clase_disp="disponible";
                    }
                @endphp
                    <div class="text-center rounded mr-2 mb-2 align-middle sitio {{ $clase_disp }}" data-id="{{ $p->id_puesto }}" data-bgcolor="{{ $color }}" data-desc="{{ $p->des_puesto }}" style="width:60px; height: 60px; overflow: hidden; background-color: {{ $color }}; border: 1px solid #999; cursor: pointer">
                        <span class="h-100 align-middle" style="font-size: 12px">{{ $p->cod_puesto }}</span>
                    </div>
                    
                @endforeach
            </div>
        @endforeach
    </div>
</div>
@endforeach
<script>

    $('.sitio').click(function(){
        $('#des_puesto').html('');
        $('#des_puesto_form').html('');
        
        $('#id_puesto').val(null);
        $('.disponible').removeClass('bg-info');
        $('.disponible').each(function(){
            $(this).css('background-color',$(this).data('bgcolor'));
        });
        
    });

    $('.disponible').click(function(){
        $('#des_puesto').html($(this).data('desc'));
        $('#des_puesto_form').val($(this).data('desc'));
        $('#id_puesto').val($(this).data('id'));
        $('.disponible').removeClass('bg-info');
        $('.disponible').each(function(){
            $(this).css('background-color',$(this).data('bgcolor'));
        });
        $(this).css('background-color','');
        $(this).addClass('bg-info');
        animateCSS('#des_puesto','zoomIn');
        $('#frm_contador').submit();
    })

    $('.boton_modo').click(function(){
        $('#loadfilter').show();
        $.post('{{url('/reservas/comprobar')}}', {_token: '{{csrf_token()}}',fecha: $('#fechas').val(),edificio:$('#id_edificio').val(),tipo: $(this).data('href')}, function(data, textStatus, xhr) {
            $('#detalles_reserva').html(data);
        });
    })
</script>