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
        <div class="card panel-purple panel-colorful media middle pad-all">
            <div class="media-left">
                <div class="pad-hor">
                    <i class="fad fa-building fa-2x"></i>
                </div>
            </div>
            <div class="media-body">
                <p class="text-3x mar-no text-semibold">{{ $edificios->count() }}</p>
                <p class="mar-no  fs-2">Edificios</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card panel-info panel-colorful media middle pad-all">
            <div class="media-left">
                <div class="pad-hor">
                    <i class="fad fa-layer-group fa-2x"></i>
                </div>
            </div>
            <div class="media-body">
                <p class="text-3x mar-no text-semibold">{{ $plantas->count() }}</p>
                <p class="mar-no  fs-2">Plantas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card panel-secondary panel-colorful media middle pad-all">
            <div class="media-left">
                <div class="pad-hor">
                    <i class="fad fa-desktop-alt fa-2x"></i>
                </div>
            </div>
            <div class="media-body">
                <p class="text-3x mar-no text-semibold">{{ $puestos->count() }}</p>
                <p class="mar-no  fs-2">Puestos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card panel-{{ color_porcentaje($pct_completado) }} panel-colorful media middle pad-all">
            <div class="media-left">
                <div class="pad-hor">
                    <i class="fad fa-check fa-2x"></i>
                </div>
            </div>
            <div class="media-body">
                <p class="text-3x mar-no text-semibold">{{ round($pct_completado) }}%   </p>
                <p class="mar-no fs-2">Operativos</p>
            </div>
        </div>
    </div>
</div>

@include('home.puesto_asignado')

<div class="row">
    <div class="col-md-6">
        @include('home.kpi_grafico_puestos')
        @if(checkPermissions(['Incidencias'],['R']))
            @include('home.incidencias_abiertas')
        @endif
    </div>
    <div class="col-md-6">
        @if(checkPermissions(['Reservas'],['R']))
            @include('home.calendario')
        @endif
    </div>
</div>
@if(checkPermissions(['Incidencias'],['R']))
    @include('home.tabla_incidencias')
@endif

@include('home.rondas_pendientes')
