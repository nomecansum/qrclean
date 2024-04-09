@php
    //Sacamos los avisos en funcion de los detos del usuario y la parametrizacion del aviso
    try{

    
    $turnos= DB::table('turnos_usuarios')
        ->select('turnos_usuarios.id_turno')
        ->where('turnos_usuarios.id_usuario',Auth::user()->id)
        ->get();

    $plantas=DB::table('plantas_usuario')
        ->select('plantas_usuario.id_planta')
        ->where('plantas_usuario.id_usuario',Auth::user()->id)
        ->get();

    $edificios=DB::table('plantas_usuario')
        ->join('plantas','plantas_usuario.id_planta','plantas.id_planta')
        ->join('edificios','plantas.id_edificio','edificios.id_edificio')
        ->select('edificios.id_edificio')
        ->where('plantas_usuario.id_usuario',Auth::user()->id)
        ->distinct()
        ->get();

    $tipos=explode(',',Auth::user()->tipos_puesto_admitidos);

    $avisos= DB::table('avisos')
        ->select('avisos.*','clientes.nom_cliente')
        ->join('clientes','avisos.id_cliente','clientes.id_cliente')
        ->where('avisos.mca_activo','S')
        ->where('avisos.fec_inicio','<=',Carbon\Carbon::now())
        ->where('avisos.fec_fin','>=',Carbon\Carbon::now())
        ->where(function($q){
            $q->wherenull('avisos.val_perfiles');
            $q->orwhereraw("FIND_IN_SET(".Auth::user()->cod_nivel.",avisos.val_perfiles)<>0");
        })
        ->where(function($q) use($turnos){
            $q->wherenull('avisos.val_turnos');
            foreach($turnos as $turno)
                $q->orwhereraw("FIND_IN_SET(".$turno->id_turno.",avisos.val_turnos)<>0");
        })
        ->where(function($q) use($edificios){
            $q->wherenull('avisos.val_edificios');
            foreach($edificios as $edificio)
                $q->orwhereraw("FIND_IN_SET(".$edificio->id_edificio.",avisos.val_edificios)<>0");
        })
        ->where(function($q) use($plantas){
            $q->wherenull('avisos.val_plantas');
            foreach($plantas as $planta)
                $q->orwhereraw("FIND_IN_SET(".$planta->id_planta.",avisos.val_plantas)<>0");
        })
        ->where(function($q) use($tipos){
            $q->wherenull('avisos.val_tipo_puesto');
            foreach($tipos as $tipo)
                $q->orwhereraw("FIND_IN_SET(".$tipo.",avisos.val_tipo_puesto)<>0");
        })
        ->where('avisos.mca_activo','S')
        ->orderby('avisos.id_aviso','desc')
        ->get();
    } catch (\Throwable $e) {
        $avisos=[];
    }
@endphp
@if(isset($avisos) && count($avisos)>0)
    @foreach ($avisos as $aviso)
    <div class="card @if(!$loop->last) mb-3 @endif">
        <div class="card-body">
            <h1  style="color: {{ $aviso->val_color }}"><i class="{{ $aviso->val_icono }}"></i> {{ $aviso->val_titulo }}</h1>
            <h3>
            {!! $aviso->txt_aviso !!}
            </h3>
        </div>
    </div>
    @endforeach
@endif
