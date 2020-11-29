@php
    //$hide=['cli'=>0,'cen'=>0,'dis'=>0,'col'=>0,'dep'=>0,'emp'=>0,'fec'=>0];
    use App\Models\estados;
    use App\Models\tags;
@endphp
@if(!(isset($hide['head']) || (isset($hide['head']) && ($hide['head']!==1))))
<div class="panel " style="padding-right: 10px" >
    <div class="panel-heading cursor-pointer" style="padding-top: 2px" id="headfiltro" >
        {{--  <span class="mt-3 ml-2 font-18"></span>  --}}
        <div id="expand_campos" data-div="divfiltro"  class="expandir ml-2  font-18 p-t-10"><i class="fad fa-filter"></i> Filtro <a href=javascript:void(0); class="expand"><i class="fas fa-caret-right text-mint"></i></a></div>
        <span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
        {{-- <div id="loadfilter" class="load8"><div class="loader"></div></div> --}}
    </div>
    <div class="panel-body" id="divfiltro" style="display: none" >
@endif
        <div class="form-group col-md-12" style="{{ ((!fullAccess() && count(clientes())==1) || (isset($hide['cli']) && $hide['cli']===1)) ? 'display: none' : ''}}">
            <label>Cliente</label>
            <div class="input-group select2-bootstrap-append">
                <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="cliente[]" id="multi-cliente">
                    @foreach (lista_clientes() as $c)
                        <option value="{{$c->id_cliente}}">{{$c->nom_cliente}}</option>
                    @endforeach
                </select>
                <div class="input-group-btn">
                    <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                </div>
            </div>
        </div>
        <div class="form-group  col-md-12" style="{{ (isset($hide['edi']) && $hide['edi']==1) ? 'display: none' : ''  }}">
            <label>Edificio</label>
            <div class="input-group select2-bootstrap-append">
                <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="edificio[]" id="multi-edificio"></select>
                <div class="input-group-btn">
                    <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                </div>
            </div>
        </div>
        
        <div class="form-group  col-md-12" style="{{ (isset($hide['pla']) && $hide['pla']==1) ? 'display: none' : ''  }}">
            <label>Planta</label>
            <div class="input-group select2-bootstrap-append">
                <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="planta[]" id="multi-planta" ></select>
                <div class="input-group-btn">
                    <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                </div>
            </div>
        </div>
        <div class="form-group  col-md-12" style="{{ (isset($hide['tag']) && $hide['tag']==1) ? 'display: none' : ''  }}">
            <label>Tag</label>
            <div class="input-group select2-bootstrap-append">
                <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="tags[]" id="multi-tag" ></select>
                <div class="input-group-btn">
                    <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                </div>
            </div>
        </div>
        <div class="form-group  col-md-12" style="{{ (isset($hide['pue']) && $hide['pue']==1) ? 'display: none' : ''  }}">
            <label>Puesto</label>
            <div class="input-group select2-bootstrap-append">
                <select class="select2 select2-filtro mb-2 select2-multiple form-control multi2" multiple="multiple" name="puesto[]" id="multi-puesto" ></select>
                <div class="input-group-btn">
                    <button class="btn btn-primary select-all" data-select="multi-dispositivos"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                </div>
            </div>
        </div>
        <div class="form-group  col-md-12" style="{{ (isset($hide['est']) && $hide['est']==1) ? 'display: none' : ''  }}">
            <label>Estado</label>
            <div class="input-group select2-bootstrap-append">
                <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="estado[]" id="multi-estado" >
                    @foreach(estados::all() as $estado)
                        <option {{ $estado->id_estado==0?'selected':'' }} value="{{ $estado->id_estado }}">{{ $estado->des_estado }}</option>
                    @endforeach
                        <option value="A">Anonimo</option>
                        <option value="R">Reserva</option>
                        <option value="P">Asignado a perfil</option>
                        <option value="U">Asignado a usuario</option>
                </select>
                <div class="input-group-btn">
                    <button class="btn btn-primary select-all" data-select="multi-estado"  type="button" style="margin-left:-10px"><i class="fad fa-check-double"></i> todos</button>
                </div>
            </div>
        </div>
        
        <div class="row" style="{{ (isset($hide['btn']) && $hide['btn']==1) ? 'display: none' : ''  }}">
            <div class="col-md-12 text-right mb-3">
                <button id="btn_submit" class="btn btn-primary btn-lg float-right"><i class="fa fa-search"></i> {{ $etiqueta_boton??'Ver' }}</button>
            </div>
        </div>
@if(!(isset($hide['head']) || (isset($hide['head']) && ($hide['head']!==1))))
    </div>
</div>
@endif





{{--  <div class="modal fade" id="save-favorite">
    <div class="modal-dialog modal-sm">
        <div class="modal-content"><div><img src="/images/onthespot_20.png" class="float-right"></div>
            <div class="modal-header"   style="justify-content: left"><i class="mdi mdi-export text-primary mdi-48px"></i><h4 style="margin-top: 20px"><label>{{ __('general.guardar_filtro_favorito') }}</label></h4></div>
            <div class="modal-body">
                <input type="text" class="form-control" placeholder="Descripción del filtro" id="des_filtro">
            </div>
            <div class="modal-footer">
                <button type="button" id="save-favorite-button" class="btn btn-info">{{trans('strings.save')}}</button>
                <button type="button" data-dismiss="modal" class="btn btn-warning">{{trans('strings.cancel')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="favorites-table">
    <div class="modal-dialog">
        <div class="modal-content"><div><img src="/images/onthespot_20.png" class="float-right"></div>
            <div class="modal-header"   style="justify-content: left">
                <i class="mdi mdi-import text-primary mdi-48px"></i>
                <h3 style="margin-top: 15px"><label>{{ __('general.cargar_filtro_favorito') }}</label></h3>
            </div>
            <div class="modal-body">
                <table id="tablafavoritos" class="table table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{trans('general.descripcion')}}</th>
                            <th>{{trans('general.opciones')}}</th>
                        </tr>
                    </thead>
                    <tbody id="all-favorites">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>  --}}

<br>


@section('scripts2')
<script>

    $(function(){
        $('#multi-cliente').change();
    }) 

    $('.select-all').click(function(event) {
        $(this).parent().parent().find('select option').prop('selected', true)
        $(this).parent().parent().find('select').select2();
        $(this).parent().parent().find('select').change();
    });



    $('#headfiltro').click(function(){
        $('#divfiltro').toggle();
    })  

    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        width: "99.2%",
    });

    $('#multi-cliente').change(function(event) {
        $('#loadfilter').show();
        $('.multi2').empty();
        $.post('{{url('/filters/loadedificios')}}', {_token:'{{csrf_token()}}',cliente:$(this).val()}, function(data, textStatus, xhr) {
            console.log(data);
            cliente="";
            $.each(data.edificios, function(index, val) {
                if(cliente!=val.id_cliente){
                    $('#multi-edificio').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente=val.id_cliente;
                }
                $('#multi-edificio').append('<option value="'+val.id_edificio+'">'+val.des_edificio+'</option>');
            });

            cliente_c="";
            edificio_c="";
            $.each(data.plantas, function(index, val) {
                if(cliente_c!=val.id_cliente){
                    $('#multi-planta').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_c=val.id_cliente;
                }
                if(edificio_c!=val.id_edificio){
                    $('#multi-planta').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_c=val.id_edificio;
                }
                $('#multi-planta').append('<option value="'+val.id_planta+'">'+val.des_planta+'</option>');
            });

            cliente_c="";
            edificio_c="";
            planta_c="";
            $.each(data.puestos, function(index, val) {
                if(cliente_c!=val.id_cliente){
                    $('#multi-puesto').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_c=val.id_cliente;
                }
                if(edificio_c!=val.id_edificio){
                    $('#multi-puesto').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_c=val.id_edificio;
                }
                if(planta_c!=val.id_planta){
                    $('#multi-puesto').append('<optgroup label="'+val.des_planta+'"></optgroup>');
                    planta_c=val.id_planta;
                }
                $('#multi-puesto').append('<option value="'+val.id_puesto+'">'+val.cod_puesto+'</option>');
            });
            $('#loadfilter').hide();

            tag_c="";
            $.each(data.tags, function(index, val) {
                if(tag_c!=val.id_cliente){
                    $('#multi-tag').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    tag_c=val.id_cliente;
                }
                $('#multi-tag').append('<option value="'+val.id_tag+'">'+val.nom_tag+'</option>');
            });
            $('#loadfilter').hide();
        });
        try{ end_update_filtros() } catch(excp){ } //Funcion para actualizar cosas despues ed que se hayan cargado
        
    });

    $('#multi-edificio').change(function(event) {
        $('#loadfilter').show();
        $('#multi-planta').empty();
        $('#multi-puesto').empty();
       $.post('{{url('/filters/loadplantas')}}', {_token:'{{csrf_token()}}',centros:$(this).val(),cliente:$('#multi-cliente').val(),edificio:$('#multi-edificio').val()}, function(data, textStatus, xhr) {
            cliente_e="";
            edificio_e="";
            $.each(data.plantas, function(index, val) {
                if(cliente_e!=val.id_cliente){
                    $('#multi-planta').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_e=val.id_cliente;
                }
                if(edificio_e!=val.id_edificio){
                    $('#multi-planta').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_e=val.id_edificio;
                }
                $('#multi-planta').append('<option value="'+val.id_planta+'">'+val.des_planta+'</option>');
            });

            cliente_e="";
            edificio_e="";
            planta_e="";
            $.each(data.puestos, function(index, val) {
                if(cliente_e!=val.id_cliente){
                    $('#multi-puesto').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_e=val.id_cliente;
                }
                if(edificio_e!=val.id_edificio){
                    $('#multi-puesto').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_e=val.id_edificio;
                }
                if(planta_e!=val.id_planta){
                    $('#multi-puesto').append('<optgroup label="'+val.des_planta+'"></optgroup>');
                    planta_e=val.id_planta;
                }
                $('#multi-puesto').append('<option value="'+val.id_puesto+'">'+val.cod_puesto+'</option>');
            });
            $('#loadfilter').hide();
        });
        //try{ end_update_filtros() } catch(excp){ } //Funcion para actualizar cosas despues ed que se hayan cargado
    });

    $('#multi-planta').change(function(event) {
        $('#loadfilter').show();
        $('#multi-puesto').empty();
        $.post('{{url('/filters/loadpuestos')}}', {_token:'{{csrf_token()}}',centros:$(this).val(),cliente:$('#multi-cliente').val(),edificio:$('#multi-edificio').val(),planta:$('#multi-planta').val()}, function(data, textStatus, xhr) {
            cliente_p="";
            edificio_p="";
            planta_p="";
            $.each(data.puestos, function(index, val) {
                if(cliente_p!=val.id_cliente){
                    $('#multi-puesto').append('<optgroup label="'+val.nom_cliente+'"></optgroup>');
                    cliente_p=val.id_cliente;
                }
                if(edificio_p!=val.id_edificio){
                    $('#multi-puesto').append('<optgroup label="'+val.des_edificio+'"></optgroup>');
                    edificio_p=val.id_edificio;
                }
                if(planta_p!=val.id_planta){
                    $('#multi-puesto').append('<optgroup label="'+val.des_planta+'"></optgroup>');
                    planta_p=val.id_planta;
                }
                $('#multi-puesto').append('<option value="'+val.id_puesto+'">'+val.cod_puesto+'</option>');
            });
            $('#loadfilter').hide();
        });
        //try{ end_update_filtros() } catch(excp){ } //Funcion para actualizar cosas despues ed que se hayan cargado
    });

    $('#save-favorite-button').click(function(event) {
		let name = $('#des_filtro').val();
		if (name.trim() == "") {
			Swal.fire({title: "{{ __('general.espere') }}",
						text: "{{ __('general.indique_nombre_filtro') }}",
						type: 'warning',
						footer: '<div><img src="/images/spotdyna.png"></div>',
                        timer: 3000});
            return false;
		}
		let data = $('.ajax-filter').serialize();
        data+='&name='+name;
        data+='&_token={{csrf_token()}}';

		$.post('{{url('getForm')}}', data, function(data, textStatus, xhr) {
			$('#save-favorite').modal('hide');
			$.toast({
                heading: '{{trans('strings.save_favorite')}}',
                text: name+': '+'{{trans('strings.favorite_success')}}',
                position: 'top-left',
                showHideTransition: 'slide',
                loaderBg: '#ff8000',
                icon: 'success',
                hideAfter: 6000,
                stack: 6,
				bgColor : '#d4edda',
				textColor : '#155724',
            });
            $('#des_filtro').val('');
        });


	});

	$('#load-favorites').click(function(event) {
        $.post('{{url('getFavorites')}}', {_token:'{{csrf_token()}}'}, function(data, textStatus, xhr) {
			$('#all-favorites').html(data);
			//$('#tablafavoritos').dataTable();
            $('#favorites-table').modal('show');
		});
	});

	function loadFilter(json)
	{
        $('#spin_filtros').show();
        let filter = JSON.parse(json);
		$('#multi-rango').val(filter['rango']);
		$('#multi-cliente').val(filter['clientes']).select2({placeholder: "todos"});
        $('.multi2').empty();

        $.post('{{url('filtros/loadtags')}}', {_token:'{{csrf_token()}}',clientes:$('#multi-cliente').val()}, function(data, textStatus, xhr) {

            $.each(data.edificios, function(index, val) {
                $('#multi-edificio').append('<option value="'+val.id_edificio+'">'+val.des_edificio+'</option>');
            });

            $.each(data.plantas, function(index, val) {
                $('#multi-planta').append('<option value="'+val.id_planta+'">'+val.des_planta+'</option>');
            });

            $.each(data.puestos, function(index, val) {
                $('#multi-puesto').append('<option value="'+val.id_puesto+'">'+val.des_puesto+'</option>');
            });

        });
        $('#favorites-table').modal('hide');
        $('#spin_filtros').hide();
	}

    $('#multi-cliente').on('select2:clearing', function(e){
        // $('#multi-cliente').off('change');
        e.preventDefault();
        $('#multi-cliente').empty();
        $('#multi-cliente').val(null).trigger('change');
        $('#multi-cliente').load("{{ url('/combos/clientes') }}", function(){
            $('#multi_clientes').trigger("change");
        });

        console.log('clearing');
    });

    $('.select-all').css('height',46);
        //$(':checkbox').on('change', handleCheckboxClick);

    $('.expand').click(function(){
        $(this).find('i').toggleClass('fas fa-caret-right fas fa-caret-down');
    });
</script>
@stop
