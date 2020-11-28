


    <div class="panel">

        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss" data-dismiss="panel"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Editar encuesta</h3>
        </div>

        <div class="panel-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ url('/encuestas/update') }}" id="edit_encuesta_form" name="edit_encuesta_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
            {{ csrf_field() }}
            <input name="_method" type="hidden" value="PUT">
            
                <div class="row">
                    <div class="form-group col-md-12 {{ $errors->has('titulo') ? 'has-error' : '' }}">
                        <label for="des_planta" class="control-label">Titulo</label>
                            <input class="form-control" required name="titulo" type="text" id="titulo" value="{{ old('titulo', optional($encuesta)->des_planta) }}" maxlength="50" placeholder="Enter titulo here...">
                            {!! $errors->first('titulo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="id_cliente" class="control-label">Cliente</label>
                        <select class="form-control" required id="id_cliente" name="id_cliente">
                            @foreach ($clientes as $key => $cliente)
                                <option value="{{ $key }}" {{ old('id_cliente', optional($encuesta)->id_cliente) == $key ? 'selected' : '' }}>
                                    {{ $cliente }}
                                </option>
                            @endforeach
                        </select>
                        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                        
                        {{--  <div class="form-group col-md-1">
                            <label for="des_planta" class="control-label">Orden</label>
                            <select class="form-control" required id="num_orden" name="num_orden">
                                @for ($n=1; $n<26;$n++ )
                                    <option value="{{ $n }}" {{ old('num_orden', optional($encuesta)->num_orden) == $n ? 'selected' : '' }}>
                                        {{ $n }}
                                    </option>
                                @endfor
                            </select>
                        </div>  --}}
                        {{--  <div class="row">
                            <div class="col-md-12">
                                <label for="id_edificio" class="control-label">Edificio</label>
                                <select class="form-control" required id="id_edificio" name="id_edificio">
                                        <option value="" style="display: none;" {{ old('id_edificio', optional($encuesta)->id_edificio ?: '') == '' ? 'selected' : '' }} disabled selected>Enter id edificio here...</option>
                                    @foreach ($Edificios as $key => $Edificio)
                                        <option value="{{ $key }}" {{ old('id_edificio', optional($encuesta)->id_edificio) == $key ? 'selected' : '' }}>
                                            {{ $Edificio }}
                                        </option>
                                    @endforeach
                                </select>
                                {!! $errors->first('id_edificio', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>  --}}
                    </div>
                
                </div>

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <input class="btn btn-primary" type="submit" value="Guardar">
                    </div>
                </div>
            </form>

        </div>
    </div>

<script>
    $('.form-ajax').submit(form_ajax_submit);
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
</script>
@include('layouts.scripts_panel')