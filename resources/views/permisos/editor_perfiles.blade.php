<div class="box-header with-border">
    <h3 class="box-title">@isset ($s)Editar perfil @else Crear perfil @endisset</h3>
</div>
<div class="panel-body">
    <form action="{{url('profiles/update')}}" method="POST" class="form-ajax" id="formperfil">
        <input type="hidden" name="id" id="id" value="{{ isset($n) ? $n->cod_nivel : 0}}">
        {{csrf_field()}}
        <div class="row">
            <div class="form-group col-md-10">
                <label for="">Descripcion</label>
                <input type="text" name="des_nivel_acceso" id="des_nivel_acceso" class="form-control" required value="{{isset($n) ? $n->des_nivel_acceso : ''}}">
            </div>

            <div class="form-group col-md-2">
                <label for="">Nivel</label>
                <input type="number" name="num_nivel_acceso" id="num_nivel_acceso" min="0" max="{{ Auth::user()->nivel_acceso }}" class="form-control" required value="{{isset($n) ? $n->val_nivel_acceso : ''}}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-3">
                <label>Hereda de</label>
                <select  name="hereda_de" class="form-control" id="nn">
                    <option value="" selected=""></option>
                    @foreach ($niveles as $nivel)
                        <option value="{{$nivel->cod_nivel}}">{{$nivel->des_nivel_acceso}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Homepage</label>
                <select  name="home_page" class="form-control" id="hh">
                    <option value="" selected=""></option>
                    @foreach ($homepages as $h)
                        <option value="{{$h}}" {{ isset($n) && old('id_cliente', optional($n)->home_page) == $h? 'selected' : '' }}>{{$h}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                <label for="id_cliente" class="control-label">Cliente</label>
                <select class="form-control" required id="id_cliente" name="id_cliente">
                    @foreach (lista_clientes() as $cl)
                        <option value="{{ $cl->id_cliente }}" {{ isset($n) && old('id_cliente', optional($n)->id_cliente) == $cl->id_cliente ? 'selected' : '' }}>
                            {{ $cl->nom_cliente }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="row b-all rounded ml-1 mr-1">
            <h5 class="ml-1">Opciones</h5>
            @if(isAdmin())
            <div class="col-md-2 p-b-20">
                <input type="checkbox" class="form-control  magic-checkbox" name="mca_fijo"  id="mca_fijo" value="S" {{ isset($n) && $n->mca_fijo=='S'?'checked':'' }}> 
                <label class="custom-control-label"   for="mca_fijo">Fijo</label>
            </div>
            @endif
            <div class="col-md-2 p-b-20">
                <input type="checkbox" class="form-control  magic-checkbox" name="mca_reserva_multiple"  id="mca_reserva_multiple" value="S"  {{ isset($n) && $n->mca_reserva_multiple=='S'?'checked':'' }}> 
                <label class="custom-control-label"   for="mca_reserva_multiple">Reserva multiple</label>
            </div>
            <div class="col-md-2 p-b-20">
                <input type="checkbox" class="form-control  magic-checkbox" name="mca_liberar_auto"  id="mca_liberar_auto" value="S" {{ isset($n) && $n->mca_liberar_auto=='S'?'checked':'' }}> 
                <label class="custom-control-label"   for="mca_liberar_auto">Liberar reservas AUTO</label>
            </div>
            <div class="col-md-2 p-b-20">
                <input type="checkbox" class="form-control  magic-checkbox" name="mca_reservar_sabados"  id="mca_reservar_sabados" value="S" {{ isset($n) && $n->mca_reservar_sabados=='S'?'checked':'' }}> 
                <label class="custom-control-label"   for="mca_reservar_sabados">Reservar sabados</label>
            </div>
            <div class="col-md-2 p-b-20">
                <input type="checkbox" class="form-control  magic-checkbox" name="mca_reservar_domingos"  id="mca_reservar_domingos" value="S" {{ isset($n) && $n->mca_reservar_domingos=='S'?'checked':'' }}> 
                <label class="custom-control-label"   for="mca_reservar_domingos">Reservar domingos</label>
            </div>
            <div class="col-md-2 p-b-20">
                <input type="checkbox" class="form-control  magic-checkbox" name="mca_reservar_festivos"  id="mca_reservar_festivos" value="S" {{ isset($n) && $n->mca_reservar_festivos=='S'?'checked':'' }}> 
                <label class="custom-control-label"   for="mca_reservar_festivos">Reservar festivos</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-primary float-right    " style="margin-top: 25px">Guardar</button>
            </div>
        </div>
    </form>
</div>
<div class="row alert alert-warning not-dismissable" id="warning_level" style="display: none">
    <h3 class="text-warning col-md-12"><i class="fa fa-exclamation-triangle"></i> Atencion!!</h3> Si selecciona la opcion de heredar de, se borrarán todos los permisos que tuviera este perfil.
</div>
<script>
    $('#formperfil').submit(form_ajax_submit);
</script>