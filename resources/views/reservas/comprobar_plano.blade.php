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
    <div class="col-md-2 text-right">
        <a href="#modal-leyenda" data-toggle="modal" data-target="#modal-leyenda"><img src="{{ url("img/img_leyenda.png") }}"> LEYENDA</a>
    </div>
    <div class="col-md-2 text-right">
        <a href="javascript:void(0)" class="mr-2 boton_modo" data-href="comprobar" ><i class="fad fa-th"></i> Mosaico</a>
        <a href="javascript:void(0)" class="mr-2 boton_modo" data-href="comprobar_plano" style="color: #1e90ff"><i class="fad fa-map-marked-alt"></i> Plano</a>
    </div>
</div>
@if($edificios->isempty())
    <div class="row">
        <div class="col-md-12  alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> El usuario no tiene asignada ninguna planta en la que pueda reservar, debe asignarle plantas en los detalles de usuario o utilizando la acción de "Asignar planta"
        </div>
    </div>
@endif
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
            $plantas=plantas::where('id_edificio',$e->id_edificio)
            ->where(function($q) use($plantas_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('id_planta',$plantas_usuario??[]);
                }
            })
            ->where(function($q) use($id_planta){
                if($id_planta && $id_planta!=0){
                    $q->where('plantas.id_planta',$id_planta);
                }
            })
            ->get();
        @endphp
        @foreach($plantas as $pl)
            <h3 class="pad-all w-100 bg-gray rounded">PLANTA {{ $pl->des_planta }}</h3>
            @include('reservas.fill-plano')
        @endforeach
    </div>
</div>
@endforeach
@include('resources.leyenda_reservas')
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
        //animateCSS('#des_puesto','zoomIn');
        $('#frm_contador').submit();
    })

    $('.boton_modo').click(boton_modo_click);

   setTimeout(recolocar_puestos, 800);

   $('#tipo_vista').val('comprobar_plano');
    
</script>