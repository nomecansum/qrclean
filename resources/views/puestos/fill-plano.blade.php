
@php
    // dd($pl);   
@endphp
@if(isset($pl->img_plano))
{{--  {!! json_encode($pl->posiciones) !!}  --}}
    <div class="row container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        <img src="{{ url('img/plantas/'.$pl->img_plano) }}" style="width: 100%" id="img_fondo{{ $pl->id_planta }}">
        @php
            $left=0;
            $top=0;
            $puestos= DB::Table('puestos')
                ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
                ->where('id_planta',$pl->id_planta)
                ->get();
        @endphp
        @foreach($puestos as $puesto)
            @php
                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
            @endphp
            <div class="text-center font-bold rounded add-tooltip bg-{{ $puesto->val_color }} align-middle flpuesto draggable" id="puesto{{ $puesto->id_puesto }}" title="{{ $puesto->des_puesto }}@if(isset($reserva)) Reservado por {{ $reserva->name }} @endif" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" style="top: {{ $top }}px; left: {{ $left }}px">
                <span class="h-100 align-middle text-center" style="font-size: 10px;">{{ $puesto->cod_puesto }}@if(isset($reserva))<br><span class="font-bold" style="font-size: 18px; color: #ff0">R</span>@endif</span>
                
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
        
        try{
            posiciones={!! json_encode($pl->posiciones)??'[]' !!};
            //posiciones=JSON.parse(posiciones); 
        } catch($err){
            posiciones=[];
        }
        document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
        //$('#plano{{ $pl->id_plano }}').data('posiciones',posiciones);
    </script>
@endif