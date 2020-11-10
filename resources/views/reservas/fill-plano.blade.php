
@php
// dd($pl);   
@endphp
@if(isset($pl->img_plano))
{{--  {!! json_encode($pl->posiciones) !!}  --}}
<div class="row container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
    <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$pl->img_plano) }}" style="width: 100%" id="img_fondo{{ $pl->id_planta }}">
    @php
        $left=0;
        $top=0;
        $puestos= DB::Table('puestos')
            ->select('puestos.*','plantas.*','estados_puestos.val_color as color_estado')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->where('puestos.id_planta',$pl->id_planta)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                }
            })
            ->where(function($q){
                if(!checkPermissions(['Mostrar puestos no reservables'],['R'])){
                    $q->where('puestos.mca_reservar','S');
                }
            })
            ->get();
    @endphp
    @foreach($puestos as $puesto)
        @php
            $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
            $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
            $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
            $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
            $title=$puesto->des_puesto;
            $borde="";
            if(isset($reserva)){
                $color="LightCoral";
                $font_color="#fff";
                $clase_disp="";
                $title="Reservado por ".$reserva->name." para hoy";
            } else if(isset($asignado_usuario)){
                $color="LightCoral";
                $font_color="#fff";
                $clase_disp="";
                $title="Puesto permanentemente asignado a ".$asignado_usuario->name;
                $borde="border: 3px solid #ff9f1a; border-radius: 16px";
            } else if(isset($asignado_otroperfil)){
                $color="LightCoral";
                $font_color="#fff";
                $clase_disp="";
                $title="Puesto reservado para  ".$asignado_otroperfil->des_nivel_acceso;
            } else if(isset($asignado_miperfil)){
                $color="#dff9d2";
                $font_color="##05688f";
                $clase_disp="disponible";
                $title="Puesto reservado para  ".$asignado_miperfil->des_nivel_acceso;
                $borde="border: 3px solid #05688f; border-radius: 10px";
            }   else {
                $color="#dff9d2";
                $font_color="#aaa";
                $clase_disp="disponible";
                $borde="border: 5px solid ".$puesto->val_color??"#fff".";";
            } 
        @endphp
        <div class="text-center font-bold rounded add-tooltip align-middle flpuesto draggable {{ $clase_disp }}" id="puesto{{ $puesto->id_puesto }}" title="{{ $title }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw;top: {{ $top }}px; left: {{ $left }}px; background-color: {{ $color }}; color: {{ $font_color }}; {{ $borde }}">
            <span class="h-100 align-middle text-center" style="font-size: {{ $puesto->factor_letra }}vw;">{{ $puesto->cod_puesto }}</span>
            @if(isset($reserva))<br>
                <span class="font-bold" style="font-size: {{ $puesto->factor_letra }}vw; color: #ff0">R</span>
            @endif
            @if(isset($asignado_usuario))<br>
                <span class="font-bold" style="font-size: {{ $puesto->factor_letra }}vw; color: #f4d35d">{{ iniciales($asignado_usuario->name,3) }}</span>
            @endif
            @if(isset($asignado_miperfil))<br>
                <span class="font-bold" style="font-size: {{ $puesto->factor_letra }}vw; color: #05688f"><i class="fad fa-users" style="color: #f4a462"></i></span>
            @endif
            @if(isset($asignado_otroperfil))<br>
                <span class="font-bold" style="font-size: {{ $puesto->factor_letra }}vw;"><i class="fad fa-users" style="color: #fff"></i></span>
            @endif
        </div>
    @php
        $left+=50;
        if($left==500){
            $left=0;
            $top+=50;
        }
     @endphp
    @endforeach
</div>
<script>
    var tooltip = $('.add-tooltip');
    if (tooltip.length)tooltip.tooltip();
    
    try{
        posiciones={!! json_encode($pl->posiciones)??'[]' !!};
        //posiciones=JSON.parse(posiciones); 
    } catch($err){
        posiciones=[];
    }
    document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
    //$('#plano{{ $pl->id_plano }}').data('posiciones',posiciones);
</script>
@else
    <div id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        No hay plano de la planta
    </div>
@endif