@php
    $max_orden=App\Models\acciones::where('cod_regla',$accion->cod_regla)->where('val_iteracion',$accion->val_iteracion)->max('num_orden');
    if(isset($max_orden)&&is_numeric($max_orden)){
        $max_orden++;
    } else {
        $max_orden=1;
    }
@endphp
<style>
    .select-all{
           max-height: 46px !important;
       }
</style>
<input type="hidden" name="cod_regla" value="{{ $accion->cod_regla }}">
<input type="hidden" name="cod_accion" value="{{ $accion->cod_accion }}">
<input type="hidden" name="val_iteracion" value="{{ $accion->val_iteracion }}">

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="">Accion</label>
            @php
                $files = File::allFiles(resource_path('views/events/acciones'));
            @endphp
            <select name="accion" id="accion"class="form-control select2" required style="width: 100%" placeholder="Seleccione una accion">
                <option value=""></option>
                @foreach ($files as $file)
                    <option style="text-transform: uppercase" {{ isset($accion->nom_accion)&&$accion->nom_accion==basename($file)?'selected':'' }}  value="{{ basename($file) }}">{{ str_replace(".php","",str_replace("_"," ",basename($file))) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label  for="val_iteracion">{{ __('eventos.iteracion') }}</label><br>
            <select name="val_iteracion" id="val_iteracion" class="form-control">
                @for($n=1; $n<6; $n++)
                    <option value="{{ $n }}" {{ $accion->val_iteracion==$n? 'selected': '' }}>{{ $n }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label  for="num_orden">Orden</label><br>
            <select name="num_orden" id="num_orden" class="form-control">
                @for($n=1; $n<=$max_orden; $n++)
                    <option value="{{ $n }}" {{ $accion->num_orden==$n? 'selected': '' }}>{{ $n }}</option>
                @endfor
            </select>
        </div>
    </div>
    <div class="col-md-4 mt-4 text-muted" id="txt-desc" style="font-weight: 400;">
        @isset($descripcion){{ $descripcion }}@endisset
    </div>
</div>

@if($accion->nom_accion!=null)
    @include('resources.form_parametros')
@endif

<div class="row">
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary btn_accion float-right">{{trans('general.submit')}}</button>
    </div>
</div>
@if($accion->nom_accion!=null && $campos_notificaciones)
<br><br>
<div class="row campos_notificaciones">
    <div class="col-md-12">
        <div class="panel" style="background-color: #fff3cd">
            <h4 class="mt-2 ml-2"><i class="fas fa-information"></i> {{ __('eventos.campos_para_notificaciones') }}:</h4>
            <div class="panel-body">
                <ul class="text-muted" style="columns: 2;-webkit-columns: 2;-moz-columns: 2;">
                    @if($campos!="")
                        @foreach($campos as $campo)
                            <li><b>{{ $campo->label }}: </b>{{ $campo->desc }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>

        </div>
    </div>
</div>
@endif
<script>
    $('#accion').change(function(){
        $('#param_accion').html();
        $.post('{{url(config('app.carpeta_asset')."/cambiar_accion")}}', {_token: '{{csrf_token()}}', 'accion': $('#accion').val(), 'cod_regla': '{{ $regla }}' ,'id_accion': '{{ $accion->cod_accion }}'}, function(data, textStatus, xhr) {
        })
        .done(function(data){
            $('#param_accion').html(data);
            $('#param_accion').show();
            animateCSS('#div_detalle_accion','bounceInRight');
        });
    });

    $("#formaccion").bind('ajax:complete', function() {
        console.log('form_accion_complete');
    });

    $(function(){
      
    });

</script>