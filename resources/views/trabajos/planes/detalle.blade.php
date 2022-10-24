@php
    use Carbon\Carbon;
@endphp

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2" colspan="2" scope="col" class="text-center">Trabajos</th>
                <th  rowspan="2"  scope="col" >Fechas</th>
                <th  rowspan="2"  scope="col" >Periodicidad</th>
                <th colspan="{{ $plantas->count() }}" class="text-center bg-light"  scope="col" >PLANTAS</th>
                @if($zonas->count()>0)<th colspan="{{ $zonas->count() }}" class="text-center bg-light"  scope="col" >ZONAS</th>@endif
            </tr>
            <tr>
                @foreach($plantas as $planta)
                    <th class="text-center"  scope="col" ><span class="vertical">{{$planta->des_planta}}</span></th>
                @endforeach
                @foreach($zonas as $zona)
                    <th class="text-center"  scope="col" ><span class="vertical">{{ $zona->des_planta }}<br>{{$zona->des_zona}}</span></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($grupos as $grupo)
                @php
                    $trabajos_grupo=$trabajos->where('id_grupo',$grupo->id_grupo);
                    //dump($trabajos_grupo);
                @endphp
                @foreach($trabajos_grupo as $trabajo)
                    <tr>
                        @if($loop->index==0)
                            <td rowspan="{{ $trabajos_grupo->count() }}" class="text-center align-middle" style="vertical-align: middle; padding: 10px 0px 10px 0px; background-color: {{ $grupo->val_color }}"><span class="vertical text-center ml-2 {{ txt_blanco($grupo->val_color) }}"> {{ $grupo->des_grupo }}</span></td>
                        @endif
                        <td scope="col"><div style="margin-left: {{ 30*$trabajo->num_nivel }}px"><i class="{{ $trabajo->val_icono }}"></i> {{ $trabajo->des_trabajo }}</td></div>
                        <td class="text-center"  scope="col" >{{ Carbon::parse($trabajo->fec_inicio)->format('d/M') }}<br>{{ Carbon::parse($trabajo->fec_fin)->format('d/M') }}</td>
                        <td scope="col" class="text-center"  ></td>
                        @foreach($plantas as $planta)
                            <td scope="col" >
                                
                            </td>
                        @endforeach
                        @foreach($zonas as $zona)
                            <td scope="col" >
                                
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>