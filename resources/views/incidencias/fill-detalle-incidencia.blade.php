
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
                <div class="col-md-10 text-nowrap"><b>Abierta por: </b>{{ $incidencia->name }}</div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="rounded {{ txt_blanco($incidencia->val_color) }}" style="background-color: {{ $incidencia->val_color }}; padding: 4px; "><b>Tipo: &nbsp;&nbsp;&nbsp;</b> <i class="{{ $incidencia->val_icono }} {{ txt_blanco($incidencia->val_color) }}" style=""></i> {{ $incidencia->des_tipo_incidencia }} </div>
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
                    <span class="font-bold">Causa cierre: </span><span>{{ App\Models\causas_cierre::find($incidencia->id_causa_cierre)->des_causa??'' }}</span>
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
            <div class="row mt-3">
                <!-- Timeline -->
                <!--===================================================-->
                <div class="timeline">
        
                    <!-- Timeline header -->
                    <div class="timeline-header">
                        <div class="timeline-header-title bg-primary">{!! beauty_fecha($incidencia->fec_apertura) !!}</div>
                    </div>
                    {{--  <div class="timeline-entry">
                        <div class="timeline-stat">
                            <div class="timeline-icon"></div>
                            <div class="timeline-time">3 Hours ago</div>
                        </div>
                        <div class="timeline-label">
                            <p class="mar-no pad-btm"><a href="#" class="btn-link">Lisa D.</a> commented on <a href="#" class="text-semibold"><i>The Article</i></a></p>
                            <blockquote class="bq-sm bq-open mar-no">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt.</blockquote>
                        </div>
                    </div>  --}}
                    @foreach($acciones as $accion)
                    <div class="timeline-entry">
                        <div class="timeline-stat">
                            <div class="timeline-icon"></div>
                            <div class="timeline-time">{!! beauty_fecha($accion->fec_accion) !!}</div>
                        </div>
                        <div class="timeline-label">
                            @if (isset($accion->img_usuario ) && $accion->img_usuario!='')
                                <img class="img-xs img-circle" src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$accion->img_usuario) }}" alt="{{ $accion->name }}">
                            @else
                            {!! icono_nombre($accion->name,32,14) !!}
                            @endif
                            
                            <span class="btn-link">{{ $accion->name }}</span> Like <i>{{ $accion->des_accion }}</i>
                            <br>
                            <div class="float-right">
                                @if(isset($accion->img_attach1)  && $accion->img_attach1!='')<a class="link_imagen" href="#modal_img_accion" data-toggle="modal" data-src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$accion->img_attach1) }}" ><img  src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$accion->img_attach1) }}" style="height: 100px"></a>@endif
                                @if(isset($accion->img_attach2)  && $accion->img_attach2!='')<a class="link_imagen" href="#modal_img_accion" data-toggle="modal" data-src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$accion->img_attach2) }}" ><img  src="{{ Storage::disk(config('app.img_disk'))->url('uploads/incidencias/'.$incidencia->id_cliente.'/'.$accion->img_attach2) }}" style="height: 100px"></a>@endif
                            </div>
                            
                        </div>
                    </div>
                    @endforeach
                </div>
                <!--===================================================-->
                <!-- End Timeline -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_img_accion">
       
            <div class="modal-dialog modal-md">
                <div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <div class="modal-body">
                        <img style="width:100%" id="img_accion">
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-warning">Cerrar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        $('.round').css('line-height','33px');
        $('.link_imagen').click(function(){
            $('#img_accion').attr('src',$(this).data('src'));
        })
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();
    </script>
    @include('layouts.scripts_panel')