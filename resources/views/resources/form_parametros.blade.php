<link rel="stylesheet" href="{{ url('/plugins/html5-editor/bootstrap-wysihtml5.css') }}" />
@php $padre=""; @endphp
<div class="row form_param">
    @foreach($parametros as $p)
        @if(isset($p->id_campo_padre) && $p->id_campo_padre!=$padre)
            @php $padre=$p->id_campo_padre; @endphp
            <div class="w-100"></div>
        @endif
        @php
            if(isset($p->num_nivel)){
                $margin=50*$p->num_nivel;
                $margin='style="margin-left: '.$margin.' px;"';
            } else {
                $margin="";
            }
           // dd($p);
        @endphp

        {{-- Etiqueta --}}
        @if($p->tipo=="lbl")
            <div class="w-100"></div>
            <div class="col-md-3 pt-3">
                <div class="form-group">
                    <h3 class="font-weight-bold">{{ ucfirst(str_replace("_"," ",$p->label)) }}</h3>
                </div>
            </div>

        @endif


        {{-- Boolean Checkbox --}}
        @if($p->tipo=="bool" || $p->tipo=="bol")
            <div class="col-md-3 pt-3 mb-4 mt-2">
                <div class="custom-control custom-switch" {{ $margin }}>
                    <input type="checkbox" name="{{ $p->name }}" class="custom-control-input" id="{{ $p->name }}" value="{{ isset($p->value)&&!is_array($p->value)? 'checked':'' }}">
                    <label class="custom-control-label pt-1" for="{{ $p->name }}">{{ $p->label }}</label>
                </div>
            </div>
        @endif

        {{-- Campo de texto --}}
        @if($p->tipo=="txt")
            <div class="col-md-6"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="text" name="{{ $p->name }}" id="{{ $p->name }}" class="form-control" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

        {{-- Campo de texto --}}
        @if($p->tipo=="img")
            <div class="col-md-6"  {{ $margin }}>
                <div class="form-group">
                    <label for="{{ $p->name }}">{{ $p->label }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="{{ $p->name }}" id="{{ $p->name }}" lang="es" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : ''}}" {{ $p->required==true?'required':'' }}>
                        <label class="custom-file-label" for="{{ $p->name }}">{{ isset($p->value)? $p->value : $p->label}}</label>
                      </div>
                </div>
            </div>
        @endif

        {{-- Campo de texto enriquecido --}}
        @if($p->tipo=="html5" || $p->tipo=="htm")
            <div class="col-md-12"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <textarea  class="textarea_editor form-control" name="{{ $p->name }}" id="{{ $p->name }}" rows="6" style="height: 200px" placeholder="Enter text ..." {{ $p->required==true?'required':'' }}>{!! isset($p->value)&&!is_array($p->value)? $p->value : $p->def !!}</textarea>
                </div>
            </div>
        @endif


        {{-- Campo numerico --}}
        @if($p->tipo=="num")
            <div class="col-md-3"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="number" name="{{ $p->name }}" id="{{ $p->name }}" class="form-control col-10" min="{{ $p->min??'' }}" max="{{ $p->max??'' }}" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

        {{-- Campo fecha --}}
        @if($p->tipo=="fec")
            <div class="col-md-5"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="date" name="{{ $p->name }}" id="{{ $p->name }}" class="form-control col-10 " value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif 

        {{-- Combo proveniente de consulta de BDD --}}
        @if($p->tipo=="list_db")
            @php
                $qr=DB::select( DB::raw($p->sql));
                $qr=collect($qr);
                try{
                    if (!fullAccess()){
                        $qr=$qr->orWhereIn('id_cliente',clientes());
                    }
                } catch(\Exception $e){

                }
            @endphp

            <div @if($p->multiple) class="col-md-12" @else class="col-md-4" @endif  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <select @if($p->multiple) class="select2 mb-2 col-md-11 select2-multiple form-control" style="width: 100%" multiple="multiple" name="{{ $p->name }}[]"  @else class="form-control {{ isset($p->buscar)&&$p->buscar==true?'select2':'' }}"  name="{{ $p->name }}"  @endif id="multi-{{ $p->name }}" {{ $p->required==true?'required':'' }}>
                        @foreach($qr as $item)

                            @if($p->multiple)
                                <option value="{{$item->id}}" {{ isset($p->value) && is_array ($p->value) && in_array($item->id,$p->value)===true?'selected':'' }}>{{$item->nombre}}</option>
                            @else
                                <option value="{{$item->id}}" {{ isset($p->value) && $item->id==$p->value?'selected':'' }}>{{$item->nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

        @endif

        @if($p->tipo=="tags")
            @php
                $qr=[];
            @endphp

            <div class="col-md-12" {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}
                        @include('resources.spin_puntitos',['id_spin'=>'spin_tag{{ $p->name }}','clase'=>'spin_tag'])
                    </label><br>
                    <select class="select2 mb-2 col-md-11 select2-multiple form-control multi2 multitags" style="width: 100%" multiple="multiple" name="{{ $p->name }}[]" id="tags-{{ $p->name }}" {{ $p->required==true?'required':'' }}>
                        @foreach($qr as $item)
                        <option value="{{$item->id_tag}}" {{ isset($p->value) && is_array ($p->value) && in_array($item->id_tag,$p->value)===true?'selected':'' }}>{{$item->nombre_tag}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        @endif

        @if($p->tipo=="disp")
            @php
                $qr=[];
            @endphp

            <div class="col-md-12" {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}
                        @include('resources.spin_puntitos',['id_spin'=>'spin_disp{{ $p->name }}','clase'=>'spin_disp'])
                    </label><br>
                    <select class="select2 mb-2 col-md-11 select2-multiple form-control multi2 multidispositivos" style="width: 100%" multiple="multiple" name="{{ $p->name }}[]" id="disp-{{ $p->name }}" {{ $p->required==true?'required':'' }}>
                        @foreach($qr as $item)
                        <option value="{{$item->id_dispositivo}}" {{ isset($p->value) && is_array ($p->value) && in_array($item->id_dispositivo,$p->value)===true?'selected':'' }}>{{$item->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        @endif

        {{-- Color picker --}}
        @if($p->tipo=="color" || $p->tipo=="col")
            <div class="col-md-2 mr-4"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="text" autocomplete="off"  name="{{ $p->name }}" id="{{ $p->name }}" class="colorpicker form-control" value="{{ isset($p->value)? $p->value : App\Classes\RandomColor::one(['luminosity' => 'bright']) }}" {{ $p->required==true?'required':'' }} />
                </div>
            </div>
        @endif

        {{-- Lista de elementos con pares lista/valor --}}
        @if($p->tipo=="list" || $p->tipo=="lis")
            <div @if($p->multiple) class="col-md-12" @else class="col-md-4" @endif  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <select @if($p->multiple) class="mb-2 col-md-11 select2-multiple form-control" multiple="multiple" style="width: 100%" name="{{ $p->name }}[]"  @else class="form-control {{ isset($p->buscar)&&$p->buscar==true?'select2':'' }}"  name="{{ $p->name }}"  @endif id="multi-{{ $p->name }}" {{ $p->required==true?'required':'' }}>
                        @php
                            $lista=explode("|",$p->list);
                            $valores=explode("|",$p->values);
                            $index=0;
                        @endphp
                        @foreach($lista as $item)
                            <option value="{{ $valores[$index]}}" {{ isset($p->value) && $valores[$index]==$p->value?'selected':'' }}>{{ $item}}</option>
                            @php $index++; @endphp
                        @endforeach
                    </select>

                </div>
            </div>
        @endif

        {{-- Esto es solo para hacer el campo de cliente mandatory si se ha puesto el parametro correspondiente --}}
        @if($p->tipo=="cli")
            @if($p->required==true)
                <script>
                    $('#multi-clientes').attr("required", "true");
                    $('#multi-clientes').attr("data-placeholder", "Debe seleccionar al menos un cliente");
                    //$('#multi-clientes').select2({placeholder:"Debe seleccionar al menos un cliente"});
                </script>
            @endif
        @endif

    @endforeach

</div>

<script src="{{ url('/plugins/html5-editor/wysihtml5-0.3.0.js') }}"></script>
<script src="{{ url('/plugins/html5-editor/bootstrap-wysihtml5.js') }}"></script>
<script>
    $(".select2-multiple").select2({
            allowClear: true
        });

    $("#comando").select2({
        allowClear: false
    });

    $('.colorpicker').minicolors({
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

    $('.singledate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput : true,
        autoApply: true,
        locale: {
            format: '{{trans("general.date_format")}}',
            applyLabel: "OK",
            cancelLabel: "Cancelar",
            daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
            monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
            firstDay: {{trans("general.firstDayofWeek")}}
        },
    });

    $('.textarea_editor').wysihtml5();

    $('.custom-file-input').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })

    
</script>
