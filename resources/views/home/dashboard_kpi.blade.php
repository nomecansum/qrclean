@php
 //Datos de KPI
    use App\Models\puestos;
    use App\Models\edificios;
    use App\Models\plantas;
    $puestos=DB::table('puestos')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('id_cliente',Auth::user()->id_cliente);
            }
        })
        ->get();
    
    
    $puestos_si=puestos::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        }
    })
    ->where('id_estado',1);
    
    $edificios=edificios::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        }
    });
    
    $plantas=plantas::where(function($q){
        if (!isAdmin()) {
            $q->where('id_cliente',Auth::user()->id_cliente);
        }
    });

    

    try{
        $pct_completado=(100*$puestos_si->count()/$puestos->count());
    } catch(\Exception $e){
        $pct_completado=0;
    }

    //Datos de donut chart   

@endphp

@include('home.accesos_directos')

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-purple panel-colorful media middle pad-all">
            <div class="media-left">
                <div class="pad-hor">
                    <i class="fad fa-building fa-2x"></i>
                </div>
            </div>
            <div class="media-body">
                <p class="text-2x mar-no text-semibold">{{ $edificios->count() }}</p>
                <p class="mar-no">Edificios</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-info panel-colorful media middle pad-all">
            <div class="media-left">
                <div class="pad-hor">
                    <i class="fad fa-layer-group fa-2x"></i>
                </div>
            </div>
            <div class="media-body">
                <p class="text-2x mar-no text-semibold">{{ $plantas->count() }}</p>
                <p class="mar-no">Plantas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-mint panel-colorful media middle pad-all">
            <div class="media-left">
                <div class="pad-hor">
                    <i class="fad fa-desktop-alt fa-2x"></i>
                </div>
            </div>
            <div class="media-body">
                <p class="text-2x mar-no text-semibold">{{ $puestos->count() }}</p>
                <p class="mar-no">Puestos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-{{ color_porcentaje($pct_completado) }} panel-colorful media middle pad-all">
            <div class="media-left">
                <div class="pad-hor">
                    <i class="fad fa-check"></i>
                </div>
            </div>
            <div class="media-body">
                <p class="text-2x mar-no text-semibold">{{ round($pct_completado) }}%   </p>
                <p class="mar-no">Operativos</p>
            </div>
        </div>
    </div>
</div>



<div class="row">
    <div class="col-md-6">
        @include('home.kpi_grafico_puestos')
        @include('home.incidencias_abiertas')
    </div>
    <div class="col-md-6">
        @include('home.calendario')
    </div>
</div>

@include('home.rondas_pendientes')