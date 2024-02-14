@php
    $edificio_ahora=0;
    $planta_ahora=0;
    $id_puesto_edit=App\Models\reservas::find($r->id_reserva??0)->id_puesto??null;
@endphp
<style>
   .glow {
        background-color: #1c87c9;
        border: 6px dashed yellow;
        color: #eeeeee;
        cursor: pointer;
        display: inline-block;
        font-family: sans-serif;
        font-size: 20px;
        padding: 10px 10px;
        text-align: center;
        text-decoration: none;
        opacity: 1 !important;
      }
      @keyframes glowing {
        0% {
          background-color: #2ba805;
          box-shadow: 0 0 5px #2ba805;
        }
        50% {
          background-color: #f0be1a;
          box-shadow: 0 0 20px #49e819;
        }
        100% {
          background-color: #ffee02;
          box-shadow: 0 0 5px #2ba805;
        }
      }
      .glow {
        animation: glowing 1300ms infinite;
      }
</style>
<input type="hidden" name="tipo_vista" id="tipo_vista" value="{{ $tipo_vista??'comprobar' }}">
<div class="row botones_accion">
    <div class="col-md-8">
        <span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="col-md-2 text-end">
        <a href="#modal-leyenda " class="link-primary" data-bs-toggle="modal" data-bs-target="#modal-leyenda"><img src="{{ url("img/img_leyenda.png") }}"> LEYENDA</a>
    </div>
    <div class="col-md-2 text-end">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked="">
            <label class="btn btn-outline-primary btn-xs boton_modo" data-href="comprobar" for="btnradio1"><i class="fad fa-th"></i> Mosaico</label>
            
            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
            <label class="btn btn-outline-primary btn-xs boton_modo" data-href="comprobar_plano" for="btnradio2"><i class="fad fa-map-marked-alt"></i> Plano</label>
        </div>
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
<div class="card">
    <div class="card-body">
        @php
        
            $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('id_planta')->unique()->toArray();
            $plantas=DB::table('plantas')->wherein('id_planta',$plantas)->get();
        @endphp
        @foreach($plantas as $pl)
       
            @if($pl->tipo_vista=='M' || $pl->tipo_vista==null)
                <a id="planta{{ $pl->id_planta }}">
                <h3 class=" w-100 bg-gray rounded">PLANTA {{ $pl->des_planta }}</h3>
                @include('reservas.fill-mosaico')
                </a>
            @endif
            @if($pl->tipo_vista=='P')
                <a id="planta{{ $pl->id_planta }}">
                    <div class="card border-dark mb-3">
                        <div class="card-header bg-gray">
                            <h3 >{{ $pl->des_planta }}</h3>
                        </div>
                        <div class="card-body  card_plano">
                            @include('reservas.fill-plano')
                        </div>
                    </div>
                    
                </a>
            @endif
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

    $('.flpuesto').mouseover(function(){
        $(this).css('opacity',1);
    })

    $('.flpuesto').mouseout(function(){
        $(this).css('opacity',$(this).data('opacity'));
    })

    var tooltip = $('.add-tooltip');
    if (tooltip.length)tooltip.tooltip();

    $('#tipo_vista').val('comprobar');

    @if(isset($r->id_reserva)&&$r->id_reserva!=0)
    $(function(){
        $('#puesto{{ $id_puesto_edit }}').addClass('glow');
    })
   @endif
</script>