@php
    $clientes=\DB::table('clientes')
        ->where(function($q){
            if (!isAdmin()){
                $q->WhereIn('clientes.id_cliente',clientes());
            }
        })
        ->orderby('nombre')
        ->get();
@endphp

@if (fullAccess() || count(clientes()) > 1)
    <a href="javascript:void(0)" onclick="cambioCliente(0)" @if (empty(session('id_cliente'))) style="background-color: floralwhite;" @endif>
        <div class="btn btn-success btn-circle">
                <i class="fas fa-users"></i>
        </div>
        <div class="mail-contnet">
            <h5>[{{trans('general.all')}}]</h5>
        </div>
    </a>
    @foreach ($clientes as $c_)
        <!--li {{session('id_cliente') == $c_->id_cliente ? 'selected' : ''}} value="{{$c_->id_cliente}}">{{$c_->nom_cliente}}</li-->

        <a href="javascript:void(0)" onclick="cambioCliente({{$c_->id_cliente}})" style="{{session('id_cliente') == $c_->id_cliente ? 'background-color: floralwhite;' : ''}}">
            @php
            if(!empty($c_->img_logo) && file_exists(public_path().'/uploads/customers/images/' . $c_->img_logo))
                echo '<div class="user-img" style="margin: 5px;"> <img src="' . url('uploads/customers/images', $c_->img_logo) . '" alt="cliente" class="img-circle"></div>';
            else echo '<div class="btn btn-success btn-circle"><i class="fas fa-briefcase"></i></div>';
            @endphp
            <div class="mail-contnet">
                <h5>{{$c_->nombre}}</h5>
            </div>
        </a>
    @endforeach

@endif
