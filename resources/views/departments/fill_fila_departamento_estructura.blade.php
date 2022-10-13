<li class="clickable" style="font-size:16px; font-weight:bold">
    <label style="font-size: 20px;">
        <i class="mdi mdi-minus-box"></i>
        <i class="mdi mdi-sitemap icon-box" style="color:darkgreen"></i>
        <input type="checkbox" class="chkdep @foreach(departamentos_padres($dep->cod_departamento,'simple') as $dp)dpto{{ $dp }} @endforeach cen{{ $dep->id_edificio??0 }}" data-name="cod_departamento[]" data-departamento="{{$dep->cod_departamento}}" data-centro="{{$dep->id_edificio??0}}" value="{{$dep->cod_departamento}}"> {{$dep->nom_departamento}}
    </label>
    @include('departments.fill_empleados_departamento_estructura')
</li>
@php
    $hay_hijos=Collect(departamentos_centro_hijos($dep->cod_departamento,$dep->id_edificio??0,2,'collect'));
@endphp
@if(!$hay_hijos->isempty())
    <ul>@each('departments.fill_fila_departamento_estructura', $hay_hijos, 'dep','departments.fill_fila_departamento_final')</ul>
@endif
