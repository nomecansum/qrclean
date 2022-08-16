@php
    $dias=['LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO'];
    $dias_semana=json_decode($dato->dias_semana);
@endphp
<div class="panel editor">
    <div class="panel-heading">
        <div class="panel-control">
            <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
        </div>
        <h3 class="panel-title">Datos del turno</h3>
    </div>
    <div class="panel-body collapse show">

        <form  action="{{url('turnos/save')}}" method="POST" class="form-ajax formturno">
            <div class="row">
                <input type="hidden" name="id" value="{{$dato->id_turno??0}}">
                {{csrf_field()}}
                <div class="form-group col-md-4">
                    <label for="">Nombre</label>
                    <input required type="text" name="des_turno" class="form-control" required value="{{isset($dato) ? $dato->des_turno : ''}}">
                </div>
                <div class="form-group col-md-2">
                    <label for="val_color">Color</label><br>
                    <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($tipo->val_color)?$tipo->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                </div>
                <div class="form-group col-md-3">
                    <label for="">Fecha de efectividad (sin año)</label>
                    <div class="input-group">
                        <input type="text" class="form-control pull-left" id="fechas" name="fechas" style="  width: 200px" value="{{ Carbon\Carbon::parse($dato->fec_inicio)->format('d/m/Y').' - '.Carbon\Carbon::parse($dato->fec_fin)->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </div>

                <div class="form-group col-md-3">
                    <label for="">Cliente</label>
                    <select required name="cod_cliente" class="form-control" id="cod_cliente">
                        {{-- <option value="" selected></option> --}}
                        @foreach ($clientes as $cl)
                            <option  {{ (isset($dato) && $dato->id_cliente == $cl->id_cliente) || ($cl->id_cliente==session('id_cliente')) ? 'selected' : ''}}  value="{{$cl->id_cliente}}">{{$cl->nom_cliente}}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group col-md-2 p-t-30 mt-1" >
                    <input type="checkbox" class="form-control  magic-checkbox" name="mca_nacional"  id="mca_nacional" value="S" {{isset($fes) ? ($fes->mca_nacional == 'S' ? 'checked' : '') : ''}}> 
                    <label class="custom-control-label"   for="mca_nacional">Festivo nacional</label>
                </div> --}}

            </div>
            <div class="row d-flex flex-wrap">
                @for ($i = 1; $i < 8; $i++)
                    <div class="form-group rounded b-all text-center p-10 " style="width: 14%; margin-right: 2px" >
                        <input type="checkbox" class="form-control  magic-checkbox chkdia" name="dia[]" id="dia{{$i}}" value="{{$i}}" {{ in_array($i,$dias_semana->dia)?'checked':'' }}> 
                        <label class="custom-control-label"   for="dia{{$i}}"><b>{{$dias[$i-1]}}</b></label><br>
                        <div class="form-group">
                            <label for="">Inicio</label>
                            <input type="time" name="hora_inicio[]" id="hora_inicio{{ $i }}" {{ in_array($i,$dias_semana->dia)?'':'disabled' }} class="form-control control{{ $i }}" value="{{ in_array($i,$dias_semana->dia)?$dias_semana->hora_inicio[array_search($i,$dias_semana->dia)]:'' }}">
                        </div>
                        <div class="form-group">
                            <label for="">Fin</label>
                            <input type="time" name="hora_fin[]" id="hora_fin{{ $i }}" {{ in_array($i,$dias_semana->dia)?'':'disabled' }} class="form-control control{{ $i }}" value="{{ in_array($i,$dias_semana->dia)?$dias_semana->hora_fin[array_search($i,$dias_semana->dia)]:'' }}">
                        </div>
                        <div class="form-group">
                            <label for="">Semanas</label>
                            <select name="mod_semana[]" id="mod_semana" id="mod_semana{{ $i }}" {{ in_array($i,$dias_semana->dia)?'':'disabled' }} class="form-control control{{ $i }}">
                                <option value="-1" {{ in_array($i,$dias_semana->dia)&&$dias_semana->mod_semana[array_search($i,$dias_semana->dia)]==-1?'selected':'' }}>Todas</option>
                                <option value="0" {{ in_array($i,$dias_semana->dia)&&$dias_semana->mod_semana[array_search($i,$dias_semana->dia)]==0?'selected':'' }}>Pares</option>
                                <option value="1" {{ in_array($i,$dias_semana->dia)&&$dias_semana->mod_semana[array_search($i,$dias_semana->dia)]==1?'selected':'' }}>Impares</option>
                            </select>

                        </div>
                    </div>
                    
                @endfor
            </div>
            
            <div class="row">
                <div class="col-md-12 text-right" style="margin-top:32px">
                    @if(checkPermissions(['Festivos'],["C"]))<button type="submit" class="btn btn-primary btnguardar">{{trans('strings.submit')}}</button>@endif
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    
    $('.chkdia').click(function(){
        if($(this).is(':checked')){
            $('.control'+$(this).val()).prop( "disabled", false);
            $('.control'+$(this).val()).attr('required', true);
            if($('#hora_inicio'+$(this).val()).val()==""){
                $('#hora_inicio'+$(this).val()).val('00:00');
            }
            if($('#hora_fin'+$(this).val()).val()==""){
                $('#hora_fin'+$(this).val()).val('23:59');
            }
        } else {
            $('.control'+$(this).val()).prop( "disabled", true );
            $('.control'+$(this).val()).attr('required', false);
        }

    })

    $('.form-ajax').submit(form_ajax_submit);

    //Date range picker
    $('#fechas').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: '{{trans("general.date_format")}}',
            applyLabel: "OK",
            cancelLabel: "Cancelar",
            daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
            monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
            firstDay: {{trans("general.firstDayofWeek")}}
        },
        opens: 'right',
    });
    
    $(".select2").select2();

    $('.minicolors').minicolors({
          control: $(this).attr('data-control') || 'hue',
          defaultValue: $(this).attr('data-defaultValue') || '',
          format: $(this).attr('data-format') || 'hex',
          keywords: $(this).attr('data-keywords') || '',
          inline: $(this).attr('data-inline') === 'true',
          letterCase: $(this).attr('data-letterCase') || 'lowercase',
          opacity: $(this).attr('data-opacity'),
          position: $(this).attr('data-position') || 'bottom',
          swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
          change: function(value, opacity) {
            if( !value ) return;
            if( opacity ) value += ', ' + opacity;
          },
          theme: 'bootstrap'
        });

    $('.demo-psi-cross').click(function(){
            $('.editor').hide();
        });
</script>
