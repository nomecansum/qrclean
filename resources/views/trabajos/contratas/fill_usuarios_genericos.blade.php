<div class="row mb-4">
    <div class="form-group col-md-2">
        <label for="name" class="control-label">Crear usuarios</label>
        <input class="form-control" name="num_usuarios" type="number" id="num_usuarios" value="1"  min="1" max="1000" >
    </div>
    <div class="form-group col-md-1">
        <label for="name" class="control-label">Inicio</label>
        <input class="form-control" name="num_inicio" type="number" id="num_inicio" value="1"  min="1" max="1000" >
    </div>
    <div class="form-group col-md-4">
        <label for="des_prefijo" class="control-label">Prefijo</label>
        <input class="form-control" name="des_prefijo" type="text" id="des_prefijo"  maxlength="200" placeholder="Ej: OPERARIO_">
    </div>
    <div class="form-group col-md-1" >
        <label for="val_color">Color</label><br>
        <input type="color" autocomplete="off" name="val_color_gen" id="val_color_gen"  class="form-control" value="{{isset($dato->val_color)?$dato->val_color:''}}" />
    </div>
    <div class="col-md-1 text-end">
       <input class="btn btn-primary mt-3 btn_crear" type="button" value="Crear">
    </div>
    <div class="col-md-1">

    </div>
    <div class="col-md-2 text-end">
        <button class="btn btn-warning mt-3 btn_borrar" type="button" value="Borrar"><i class="fa-solid fa-trash"></i> Borrar</button>
     </div>
</div>

@foreach($usuarios as $u)
    <div class="col-md-4 mb-2" style="color: {{ $u->val_color }}; font-weight: 400" id="operario_{{ $u->id_operario }}">
        <input type="checkbox" class="chk_generico" name="usuint_{{ $u->id_operario }}" data-id="{{ $u->id_operario }}" style="display: none" checked> <i class="fa-solid fa-person-simple"></i> {{ $u->nom_operario }}
    </div>
@endforeach

<script>
    $('.btn_crear').click(function(){
        if($('#des_prefijo').val()==''){
            toast_warning('Crear usuarios','Debe ingresar un prefijo');
            return;
        }
        $.post('{{url('/trabajos/contratas/crear_usuarios')}}', {_token: '{{csrf_token()}}',num_usuarios:$('#num_usuarios').val(),num_inicio:$('#num_inicio').val(),des_prefijo:$('#des_prefijo').val(),val_color:$('#val_color_gen').val(),id_contrata:{{ $id }}}, function(data, textStatus, xhr) {
                console.log(data);
                if(data.error){
                    toast_error(data.title,data.error);
                } else if(data.alert){
                    toast_warning(data.title,data.alert);
                } else{
                    toast_ok(data.title,data.message);
                    $('#lista_genericos').load('{{url('/trabajos/contratas/usuarios_genericos',$id)}}');
                }
            })
            .fail(function(err){
                toast_error('Error',err.responseText);
            });
    })
    $('.btn_borrar').click(function(){
        $('.chk_generico').show();
    })

    $('.chk_generico').click(function(){
        $.get('{{url("/trabajos/contratas/set_usuarios_contrata/D",$id)}}/'+$(this).data('id'), function(data, textStatus, xhr) {
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.alert){
                toast_warning(data.title,data.alert);
            } else{
                $('#operario_'+data.id).remove();
                toast_ok(data.title,data.message);
                
            }
        })
    })
</script>