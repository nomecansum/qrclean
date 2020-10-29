
    <div class="panel">

        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title" id="titulo">
               Detalle de la incidencia
            </h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-10"><h4>{{ $incidencia->des_incidencia }}</h4></div>
                <div class="col-md-2"><h5>{!! beauty_fecha($incidencia->fec_apertura) !!}</h5></div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <span class="font-bold">{{ $incidencia->des_tipo_incidencia }} </span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">Edificio: </span><span>{{ $incidencia->des_edificio }}</span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">Planta: </span><span>{{ $incidencia->des_planta }}</span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">Puesto: </span><span>{{ $incidencia->des_puesto }}</span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    {{ $incidencia->txt_incidencia }}
                </div>
            </div>
            @if(isset($incidencia->fec_cierre))
            <div class="row  mt-3 bg-gray">
                <div class="col-md-3">
                    <span class="font-bold">Resuelta por: </span><span>{{ App\Models\users::find($incidencia->id_usuario_cierre)->name }}</span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">Fecha: </span><span>{!! beauty_fecha($incidencia->fec_cierre) !!}</span>
                </div>
                <div class="col-md-3">
                    <span class="font-bold">Causa cierre: </span><span>{{ App\Models\causas_cierre::find($incidencia->id_causa_cierre)->des_causa }}</span>
                </div>
                <div class="col-md-12 mt-3">
                    {{ $incidencia->comentario_cierre }}
                </div>
            </div>
            @endif
            <div class="row mt-3">
                <div class="col-md-6">
                    @if($incidencia->img_attach1)
                        <img src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$incidencia->img_attach1) }}" style="width: 100%">
                    @endif
                </div>
                <div class="col-md-6">
                    @if($incidencia->img_attach2)
                        <img src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$incidencia->img_attach2) }}" style="width: 100%">
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>

    </script>
    @include('layouts.scripts_panel')