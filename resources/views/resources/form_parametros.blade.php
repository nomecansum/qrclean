<link rel="stylesheet" href="{{ asset('/plugins/html5-editor/bootstrap-wysihtml5.css') }}" />
@php $padre=""; @endphp
<div class="row">
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
           if(!isset($p->required)){
                $p->required=false;
            }
        @endphp

        {{-- Etiqueta --}}
        @if($p->tipo=="lbl")
            <div class="w-100"></div>
            <div class="col-md-{{ $p->width??3 }} pt-3">
                <div class="form-group">
                    <h3 class="font-weight-bold">{{ ucfirst(str_replace("_"," ",$p->label)) }}</h3>
                </div>
            </div>

        @endif


        {{-- Boolean Checkbox --}}
        @if($p->tipo=="bool" || $p->tipo=="bol")
            <div class="col-md-{{ $p->width??3 }} pt-3 mb-4 mt-2">
                <div class="custom-control custom-switch" {{ $margin }}>
                    <input type="checkbox" name="{{ $p->name }}" class="custom-control-input" id="{{ $p->name }}" value="1" {{(isset($p->value)&&!is_array($p->value))?'checked':'' }}>
                    <label class="custom-control-label pt-1" for="{{ $p->name }}">{{ $p->label }}</label>
                </div>
            </div>
        @endif

        {{-- Campo de texto --}}
        @if($p->tipo=="txt")
            <div class="col-md-{{ $p->width??6 }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="text" name="{{ $p->name }}" id="{{ $p->name }}" class="form-control" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

        {{-- Campo de texto --}}
        @if($p->tipo=="img")
            <div class="col-md-{{ $p->width??6 }}"  {{ $margin }}>
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
            <div class="col-md-{{ $p->width??12 }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <textarea  class="textarea_editor form-control" name="{{ $p->name }}" id="{{ $p->name }}" rows="6" placeholder="Enter text ..." {{ $p->required==true?'required':'' }}>{!! isset($p->value)&&!is_array($p->value)? $p->value : $p->def !!}</textarea>
                </div>
            </div>
        @endif


        {{-- Campo numerico --}}
        @if($p->tipo=="num")
            <div class="col-md-3"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="number" name="{{ $p->name }}" id="{{ $p->name }}" {{ isset($p->min)?'min='.$p->min:'' }} {{ isset($p->max)?'max='.$p->max:'' }} class="form-control col-10" value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

        {{-- Campo fecha --}}
        @if($p->tipo=="fec")
            <div class="col-md-{{ $p->width??5 }}"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="date" name="{{ $p->name }}" id="{{ $p->name }}" class="form-control col-10 " value="{{ isset($p->value)&&!is_array($p->value)? $p->value : $p->def }}" {{ $p->required==true?'required':'' }}>
                </div>
            </div>
        @endif

         {{-- Campo hora --}}
         @if($p->tipo=="hor")
            <div class="col-md-{{ $p->width??4 }}"  {!! $margin !!}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="time" name="{{ $p->name }}" id="{{ $p->name }}" class="form-control  " value="{{ isset($valor)? $valor : $p->def }}" {{ $enabled}}>
                </div>
            </div> <br>
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

            <div @if($p->multiple) class="col-md-{{ $p->width??12 }}" @else class="col-md-{{ $p->width??4 }}" @endif  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <select @if($p->multiple) class="select2f mb-2 col-md-11 select2f-multiple form-control" multiple="multiple" name="{{ $p->name }}[]"  @else class="form-control {{ isset($p->buscar)&&$p->buscar==true?'select2f':'' }}"  name="{{ $p->name }}"  @endif  style="width: 100%"  id="multi-{{ $p->name }}" {{ $p->required==true?'required':'' }}>
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

        {{-- Color picker --}}
        @if($p->tipo=="color" || $p->tipo=="col")
            <div class="col-md-{{ $p->width??2 }} mr-4"  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <input type="text" autocomplete="off"  name="{{ $p->name }}" id="{{ $p->name }}" class="colorpicker form-control" value="{{ isset($p->value)? $p->value : App\Classes\RandomColor::one(['luminosity' => 'bright']) }}" {{ $p->required==true?'required':'' }} />
                </div>
            </div>
        @endif

        {{-- Lista de elementos con pares lista/valor --}}
        @if($p->tipo=="list" || $p->tipo=="lis")
            <div @if($p->multiple) class="col-md-{{ $p->width??12 }}" @else class="col-md-{{ $p->width??4 }}" @endif  {{ $margin }}>
                <div class="form-group">
                    <label  for="{{ $p->name }}">{{ $p->label }}</label><br>
                    <select @if($p->multiple) class="select2f mb-2 col-md-11 select2f-multiple form-control" multiple="multiple" style="width: 100%" name="{{ $p->name }}[]"  @else class="form-control {{ isset($p->buscar)&&$p->buscar==true?'select2f':'' }}"  name="{{ $p->name }}"  @endif id="multi-{{ $p->name }}" {{ $p->required==true?'required':'' }}>
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
                    //$('#multi-clientes').select2f({placeholder:"Debe seleccionar al menos un cliente"});
                </script>
            @endif
        @endif

        {{-- Salto de linea --}}
        @if(isset($p->br) && $p->br==true)
            <div class="row"><br></div>
        @endif

    @endforeach

</div>

<script src="{{ asset('/plugins/html5-editor/wysihtml5-0.3.0.js') }}"></script>
<script src="{{ asset('/plugins/html5-editor/bootstrap-wysihtml5.js') }}"></script>
<script>
    $(".select2f-multiple,.select2f").select2({
            allowClear: true
        });

    $('.textarea_editor').each(function(){$(this).wysihtml5();});

    $('.custom-file-input').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })


</script>
