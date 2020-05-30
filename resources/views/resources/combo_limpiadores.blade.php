
@foreach($clientes as $key=>$value)
    @php $u=$usuarios->where('id_cliente',$key) @endphp
    <span class="font-bold">{{ $value }}</span>
    @foreach($usuarios as $u)
        <div class="ml-3">
            <input type="checkbox" class="form-control chkuser magic-checkbox" name="lista_user[]" data-id="{{ $u->id }}" id="chk{{ $u->id }}" value="{{ $u->id }}">
            <label class="custom-control-label"   for="chk{{ $u->id }}">{{ $u->name }}</label>
        </div>

    @endforeach
@endforeach