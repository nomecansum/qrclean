@php

@endphp
<style>
      @keyframes glowing {
        0% {
          color: #2ba805;
        }
        25% {
          color: #f0be1a;
        }
        50% {
          color: #ffee02;
        }
        75% {
          color: #ff3b38;
        }
      }
      .glow {
        animation: glowing 1300ms infinite;
      }
</style>
@if(isset($puesto->img_plano))


    <div class="row container" id="plano{{ $puesto->id_planta }}" data-posiciones="" data-id="{{ $puesto->id_planta }}" style="width: 100%; padding: 0px 0px 0px 0px">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$puesto->img_plano) }}" style="width: 100%" id="img_fondo{{ $puesto->id_planta }}">
        @php
            $left=0;
            $top=0;

        @endphp

            @php
                $title= nombrepuesto($puesto);
                $borde="";

                $color=$puesto->val_color?$puesto->val_color:"#dff9d2";
                $font_color="#fff";
            @endphp
            <i class="fa-solid fa-location-dot fa-3x text-danger glow" style="top: {{ $top }}px; left: {{ $left }}px; position: absolute"></i>
    </div>
    @php
        $posiciones=json_decode($puesto->posiciones);
        $top=0;
        $left=0;
        if(isset($posiciones)){
            foreach($posiciones as $pos){
                if($pos->puesto==$puesto->cod_puesto){
                    $top=$pos->offsettop;
                    $left=$pos->offsetleft;
                }
            }   
        }
       
    @endphp
   

   
    <script>
        
        function posicionar(){
            h_plano=$('#img_fondo{{ $puesto->id_planta }}').outerHeight();
            w_plano=$('#img_fondo{{ $puesto->id_planta }}').outerWidth();

            t_puesto={{ $top }}*h_plano/100;
            l_puesto={{ $left }}*w_plano/100;
            console.log(t_puesto+' '+l_puesto);
            $('#puesto{{ $puesto->id_puesto }}').css({top: t_puesto, left: l_puesto, position: 'absolute'}); 
        }
           
        $(function(){
            setTimeout(posicionar,1500);
        })         

    </script>
@else
    <div id="plano{{ $puesto->id_planta }}" data-posiciones="" data-id="{{ $puesto->id_planta }}">
        No hay plano de la planta
    </div>
@endif