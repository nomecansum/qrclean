@php
    //$hide=['cli'=>0,'cen'=>0,'dis'=>0,'col'=>0,'dep'=>0,'emp'=>0,'fec'=>0];

@endphp
<div class="form-group  pr-5" style="{{ ((!fullAccess() && count(clientes())==1) || (isset($hide['cli']) && $hide['cli']===1)) ? 'display: none' : ''}}">
    <label>{{trans('general.clientes')}}</label>
    <select class="select2 select2-filtro mb-2 col-md-10 select2-multiple form-control" multiple="multiple" name="clientes[]" id="multi-clientes">
        @foreach (lista_clientes() as $c)
            <option value="{{$c->id_cliente}}">{{$c->nombre_cliente}}</option>
        @endforeach
    </select>
    <button class="btn btn-info float-right mt-0 position-absolute select-all"data-select="multi-clientes"  type="button"><i class="fad fa-check-double"></i> {{ __('general.todos') }}</button>
</div>
<div class="form-group pr-5" style="{{ (isset($hide['tag']) && $hide['tag']==1) ? 'display: none' : ''  }}">
    <label>{{trans('general.tags')}}</label>
    <select class="select2 select2-filtro mb-2 col-md-9 select2-multiple form-control multi2" multiple="multiple" name="tags[]" id="multi-tags">
    </select>
    <button class="btn btn-info float-right mt-0 position-absolute select-all" data-select="multi-tags" type="button"><i class="fad fa-check-double"></i> {{ __('general.todos') }}</button>
</div>
<div class="form-group pr-5" style="{{ (isset($hide['dis']) && $hide['dis']==1) ? 'display: none' : ''  }}">
    <label>{{trans('general.dispositivos')}}</label>
    <select class="select2 select2-filtro mb-2  select2-multiple form-control multi2" multiple="multiple" name="dispositivos[]" id="multi-dispositivos" style="width: 80%">
    </select>
    <button class="btn btn-info float-right mt-0 position-absolute select-all"   data-select="multi-dispositivos"  type="button"><i class="fad fa-check-double"></i> {{ __('general.todos') }}</button>
</div>

<div class="form-group pr-5" style="{{ (isset($hide['con']) && $hide['con']==1) ? 'display: none' : ''  }}">
    <label>{{trans('rss.tipos_de_contenidos')}}</label>
    <select class="select2 select2-filtro mb-2 col-md-11 select2-multiple form-control multi2" multiple="multiple" name="contenidos[]" id="multi-contenidos" >
    </select>
    <button class="btn btn-info float-right mt-0 position-absolute select-all"  data-select="multi-contenidos"  type="button"><i class="fad fa-check-double"></i> {{ __('general.todos') }}</button>
</div>
<div class="row" >
    <div class="col-md-3">
        <div class="form-group" style="{{ (isset($hide['fec']) && $hide['fec']==1) ? 'display: none' : ''  }}">
            <label>{{trans('general.fechas')}}</label>
            <div class="input-group">
                <input type="text" autocomplete="off" class="form-control input-daterange-datepicker" name="rango" id="multi-rango" value="@isset($startdate){{ Carbon\Carbon::parse($startdate)->format(trans("general.short_date_format"))}}@else{{Carbon\Carbon::now()->startOfMonth()->setTimezone(Auth::user()->val_timezone)->format(trans("general.short_date_format"))}}@endisset - @isset($enddate){{ Carbon\Carbon::parse($enddate)->format(trans("general.short_date_format"))}}@else{{Carbon\Carbon::now()->setTimezone(Auth::user()->val_timezone)->format(trans("general.short_date_format"))}}@endisset">
                <div class="input-group-append">
                    <span class="input-group-text btn-info"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 text-right">
        <div class="spinner-border text-primary float-left" role="status" style="margin-right: 10px; display: none" id="spin_filtros"><span class="sr-only">{{trans('strings.espere')}}...</span></div>
    </div>
    <div class="col-md-6 text-right vb">
        @if(!isset($hide['save_filter']))
            <button type="button" style="margin-top:00px"  data-target="#save-favorite" data-toggle="modal" class="btn btn-secondary btn-xs float-right  mr-2">{{ __('general.guardar_filtro') }}</button>
            <button type="button" style="margin-top:00px" id="load-favorites" class="btn btn-primary btn-xs float-right  mr-2">{{ __('general.cargar_filtro') }}</button>
        @endif
    </div>
</div>

<div class="modal fade" id="save-favorite">
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
</div>

<br>


@section('scripts')
<script>
    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        width: '90%'
    });

    $('#multi-clientes').change(function(event) {
        $('.multi2').empty();
        $.post('{{url('filters/loadtags')}}', {_token:'{{csrf_token()}}',clientes:$(this).val()}, function(data, textStatus, xhr) {

            cliente="";
            $.each(data.tags, function(index, val) {
                if(cliente!=val.nombre_cliente){
                    $('#multi-tags').append('<optgroup label="'+val.nombre_cliente+'"></optgroup>');
                    cliente=val.nombre_cliente;
                }
                $('#multi-tags').append('<option value="'+val.id_tag+'">'+val.nombre_tag+'</option>');
            });

            cliente="";
            $.each(data.dispositivos, function(index, val) {
                if(cliente!=val.nombre_cliente){
                    $('#multi-dispositivos').append('<optgroup label="'+val.nombre_cliente+'"></optgroup>');
                    cliente=val.nombre_cliente;
                }
                $('#multi-dispositivos').append('<option value="'+val.id_dispositivo+'">'+val.nombre+'</option>');
            });

            cliente="";
            $.each(data.contenidos, function(index, val) {
                if(cliente!=val.nombre_cliente){
                    $('#multi-contenidos').append('<optgroup label="'+val.nombre_cliente+'"></optgroup>');
                    cliente=val.nombre_cliente;
                }
                $('#multi-contenidos').append('<option value="'+val.id_texto_dinamico+'">'+val.des_texto_dinamico+'</option>');
            });
        });
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
		$('#multi-clientes').val(filter['clientes']).select2({placeholder: "{{ __('general.todos') }}"});
        $('.multi2').empty();

        $.post('{{url('filtros/loadtags')}}', {_token:'{{csrf_token()}}',clientes:$('#multi-clientes').val()}, function(data, textStatus, xhr) {

            $.each(data.tags, function(index, val) {
                $('#multi-tags').append('<option value="'+val.id_tag+'">'+val.nombre_tag+'</option>');
            });

            $.each(data.dispositivos, function(index, val) {
                $('#multi-dispositivos').append('<option value="'+val.id_dispositivo+'">'+val.nombre+'</option>');
            });

            $.each(data.contenidos, function(index, val) {
                $('#multi-colectivos').append('<option value="'+val.id_texto_dinamico+'">'+val.des_texto_dinamico+'</option>');
            });

        });
        $('#favorites-table').modal('hide');
        $('#spin_filtros').hide();
	}

    $('#multi-clientes').on('select2:clearing', function(e){
        // $('#multi-clientes').off('change');
        e.preventDefault();
        $('#multi-clientes').empty();
        $('#multi-clientes').val(null).trigger('change');
        $('#multi-clientes').load("{{ url('/combos/clientes') }}", function(){
            $('#multi_clientes').trigger("change");
        });

        console.log('clearing');
    });

    $('.select-all').css('height',46);
        //$(':checkbox').on('change', handleCheckboxClick);
</script>
@stop
