<script>
    let id_fila=0;
    let token_fila=0;
function hoverdiv(obj,e,divid,id,txt,token){
    event.stopPropagation();
    if(id==id_fila){
        $('#'+divid).hide();
        id_fila=0;
        return;
    }
    id_fila=id;
    token_fila=token;
    console.log(obj.position());
    
    console.log(e);
    var left  =obj.position().left-left_toolbar;
    var top  = obj.position().top+top_toolbar;


    $('#nombrepuesto').html(txt);
    $('#txt_borrar').html(txt);
    $('#toolbutton').data('id',id);
    $('#toolbutton').data('token',token);
    $('#link_borrar').attr('href','{{url('/puestos/delete')}}/'+id)
    $('#'+divid).css('left',left);
    $('#'+divid).css('top',top);
    console.log(left+','+top);
    $('#'+divid).show();
    animateCSS('#'+divid,'fadeIn');
    return false;
}

function editar(){
    $('#editorCAM').load("{{ url('/puestos/edit/') }}"+"/"+id_fila, function(){
        animateCSS('#editorCAM','bounceInRight');
        $('#toolbutton').hide();
    });
}

function scan(){
    window.open("{{ url('/puesto') }}"+"/"+token_fila, 'scan');
}

function estado(est){
    $.get("{{ url('/puesto/estado/') }}/"+token_fila+"/"+est, function(data){
        toast_ok('Cambio de estado',data.mensaje);
        //console.log('#estado_'+$(this).data('id'));
        $('#estado_'+data.id).removeClass();
        $('#estado_'+data.id).addClass('bg-'+data.color);
        $('#estado_'+data.id).html(data.label);
        $('#toolbutton').hide();
        animateCSS('#estado_'+data.id,'rubberBand');
    }) 
    .fail(function(err){
        console.log(err);
        toast_error('Error',err);
    });
}

function cancelar(){
    $.get("{{ url('/reservas/cancelar_puesto/') }}/"+token_fila, function(data){
        toast_ok('Cancelar reserva',data.mensaje);
        $('#nreserva_'+data.id).html();
        $('#freserva_'+data.id).html();
        //console.log('#estado_'+$(this).data('id'));    
    }) 
    .fail(function(err){
        console.log(err);
        toast_error('Error',err);
    });
}

</script>