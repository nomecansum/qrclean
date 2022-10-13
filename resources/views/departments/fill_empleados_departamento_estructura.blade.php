<ul class="sortable">
    @foreach (DB::table('users')->where('id_departamento',$dep->cod_departamento)
        ->select('users.id','users.name','users.id_departamento','users.img_usuario as img_empleado','users.id_edificio')
        ->where('users.id_edificio',$dep->id_edificio??0)
        ->wherenull('deleted_at')
        ->orderBy('name','asc')
        ->get() as $_emp)
        <li data-emp="{{$_emp->id}}" style="font-size:14px">
            <label style="font-size: 18px;">
                {{-- <i class="mdi mdi-account icon-box"  style="color:darkorange"></i> --}}
                @if(isset($_emp->img_empleado))<img class="mb-0" src="{{ url('/uploads/employees/images/'.$_emp->img_empleado) }}" style="width: 20px;">@else {!! icono_nombre($_emp->name,20,10,'ic_planner') !!} @endif
                <input type="checkbox" name="cod_empleados[]" class="checkboxRep @foreach(departamentos_padres($_emp->id_departamento,'simple') as $dp)dpto{{ $dp }} @endforeach cen{{ $_emp->id_edificio }}" value="{{$_emp->id}}" style="font-size:14px"  >  {{$_emp->name}} 
            </label>
        </li>
    @endforeach
</ul>
{{-- {{ checkSupervisor2($_emp->id)?'checked':'' }} --}}