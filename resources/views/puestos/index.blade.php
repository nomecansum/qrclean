@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de puestos</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">puestos</li>
        {{--  <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection

@section('content')

    <div class="row botones_accion">
        <div class="col-md-8">

        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group btn-group-xs pull-right" role="group">
                <div class="btn-group mr-3">
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false" title="Acciones sobre la seleccion de puestos">
                            <i class="fad fa-poll-people pt-2" style="font-size: 20px" aria-hidden="true"></i> Acciones <i class="dropdown-caret"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="">
                            <li class="dropdown-header">Cambiar estado</li>
                            <li><a href="#" data-estado="1" class="btn_estado_check"><i class="fas fa-square text-success"></i> Disponible</a></li>
                            <li><a href="#" data-estado="2" class="btn_estado_check"><i class="fas fa-square text-danger"></i> Usado</a></li>
                            <li><a href="#" data-estado="3" class="btn_estado_check"><i class="fas fa-square text-info"></i> Limpieza</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header">Acciones</li>
                            <li><a href="#" class="btn_qr"><i class="fad fa-qrcode"></i> Imprimir QR</a></li>
                            <li><a href="#" class="btn_asignar" ><i class="fad fa-broom"></i>Ruta de limpieza</a></li>
                        </ul>
                    </div>
                </div>
                <div class="btn">
                    <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo puesto">
                        <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                        <span>Nuevo</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @php $etiqueta_boton="Ver puestos" @endphp
    <form method="post" name="form_puestos" id="formbuscador" action="{{ url('puestos/') }}">
        @csrf
        <input type="hidden" name="document" value="pantalla">
        @include('resources.combos_filtro')
    </form>
    <div id="editorCAM" class="mt-2">

    </div>
    <script>
        let id_fila=0;
        let token_fila=0;

        function hoverdiv(obj,e,divid,id,txt,token){
            if(id==id_fila){
                $('#'+divid).hide();
                id_fila=0;
                return;
            }
            id_fila=id;
            token_fila=token;
            console.log(obj.position());
            
            console.log(e);
            var left  =obj.position().left-280;
            var top  = obj.position().top+16;


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
            $
        }
        function editar(){
            $('#editorCAM').load("{{ url('/puestos/edit/') }}"+"/"+id_fila, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
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
       
    </script>
    <div id="myFilter">
        @if(!isset($r))
            @include('puestos.fill-tabla')
        @endif
    </div>
    
    <div class="modal fade" id="eliminar-puesto" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <h4 class="modal-title">¿Borrar puesto <span id="txt_borrar"></span>?</h4>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" href="" id="link_borrar">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
<script>

       

    $('#frmpuestos').submit(form_pdf_submit);
    $('#formbuscador').submit(ajax_filter);

    


	$('#btn_nueva_puesto').click(function(){
       $('#editorCAM').load("{{ url('/puestos/edit/0') }}", function(){
		animateCSS('#editorCAM','bounceInRight');
	   });
	  // window.scrollTo(0, 0);
      //stopPropagation()
	});

	

    $('td').click(function(event){
        editar( $(this).data('id'));
    })


    $("#chktodos").click(function(){
        $('.chkpuesto').not(this).prop('checked', this.checked);
    });



$('.btn_estado_check').click(function(){
    console.log('check');
    var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
      return $(this).val();
    }).get(); // <----
    if(searchIDs.length==0){
        toast_error('Error','Debe seleccionar algún puesto');
        exit();
    }

    $.post('{{url('/puestos/accion_estado')}}', {_token: '{{csrf_token()}}',estado: $(this).data('estado'),lista_id:searchIDs}, function(data, textStatus, xhr) {
        toast_ok('Acciones',data.mensaje);
        //console.log($('.chkpuesto:checkbox:checked'));
        $('.chkpuesto:checkbox:checked').each(function(){
            //console.log('#estado_'+$(this).data('id'));
            $('#estado_'+$(this).data('id')).removeClass();
            $('#estado_'+$(this).data('id')).addClass('bg-'+data.color);
            $('#estado_'+$(this).data('id')).html(data.label);
        });

        

        //console.log('success');
    })
    .fail(function(err){
        toast_error('Error',err.responseJSON.message);
    });
});

$('.btn_qr').click(function(){
    //block_espere();
    var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
      return $(this).val();
    }).get(); // <----
    if(searchIDs.length==0){
        toast_error('Error','Debe seleccionar algún puesto');
        exit();
    }
//
    $('#frmpuestos').attr('action',"{{url('/puestos/print_qr')}}");
    $('#frmpuestos').submit();
    //
});

$('.btn_asignar').click(function(){
    //block_espere();
    var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
      return $(this).val();
    }).get(); // <----
    if(searchIDs.length==0){
        toast_error('Error','Debe seleccionar algún puesto');
        exit();
    }
    //fin_espere();
});


    $('#tablapuestos').on('click-cell.bs.table', function(e, value, row, $element){
        //console.log(e);
    });


</script>
@endsection
