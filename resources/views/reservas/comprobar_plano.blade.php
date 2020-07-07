@php
    $edificio_ahora=0;
    $planta_ahora=0;
    use App\Models\plantas;
@endphp

<!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
<link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
<style type="text/css">
    .container {
        border: 1px solid #DDDDDD;
        width: 100%;
        position: relative;
        padding: 0px;
    }
    .flpuesto {
        float: left;
        position: absolute;
        z-index: 1000;
        color: #aaa;
        font-weight: bold;
        font-size: 9px;
        width: 40px;
        height: 40px;
        overflow: hidden;
    }
    
</style>

<div class="row botones_accion">
    <div class="col-md-8">
        <span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="col-md-4 text-right">
        <a href="javascript:void(0)" class="mr-2 boton_modo" data-href="comprobar" ><i class="fad fa-th"></i> Mosaico</a>
        <a href="javascript:void(0)" class="mr-2 boton_modo" data-href="comprobar_plano" style="color: #1e90ff"><i class="fad fa-map-marked-alt"></i> Plano</a>
    </div>
</div>
@foreach ($edificios as $e)
<div class="panel">
    <div class="panel-heading bg-gray-dark">
        <div class="row">
            <div class="col-md-3">
                <span class="text-2x ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}</span>
            </div>
            <div class="col-md-7"></div>
            <div class="col-md-2 text-right">
                <h4>
                    <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                    <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $e->puestos }}</span>
                </h4>
            </div>
        </div>
    </div>
    <div class="panel-body">
        @php
            $plantas=plantas::where('id_edificio',$e->id_edificio)->get();
        @endphp
        @foreach($plantas as $pl)
            <h3 class="pad-all w-100 bg-gray rounded">PLANTA {{ $pl->des_planta }}</h3>
            @include('reservas.fill-plano')
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

    function recolocar_puestos(posiciones){
        $('.container').each(function(){
            plano=$(this);
            //console.log(plano.data('posiciones'));
            
            $.each(plano.data('posiciones'), function(i, item) {//console.log(item);
                
                puesto=$('#puesto'+item.id);
                console.log(item);
                puesto.css('top',plano.height()*item.offsettop/100);
                puesto.css('left',plano.width()*item.offsetleft/100);
            });

        }) 
    }

        

    $(window).resize(function(){
        recolocar_puestos();
    })

    $('.mainnav-toggle').click(function(){
        recolocar_puestos();
    })
    recolocar_puestos();
    
</script>