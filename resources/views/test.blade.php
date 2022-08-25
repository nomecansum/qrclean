@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Pagina de prueba</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Titulo</h3>
    </div>
    <div class="card-body">
       {{-- @php
           $icons=[];
           $json=file_get_contents(public_path('/plugins/fontawesome6/metadata/categories.json'));
           $json=json_decode($json);
           foreach($json as $cat){
                foreach($cat->icons as $icon){
                    $icons[]='fa-solid fa-'.$icon;
                    $icons[]='fa-regular fa-'.$icon;
                    $icons[]='fa-dutone fa-'.$icon;
                }
           }
           $icons=array_unique($icons);
           dd(json_encode(array_values($icons)));
       @endphp --}}

       {{-- @php
          OneSignal::sendNotificationToExternalUser(
            "Some Message Some Message Some Message Some Message Some Message Some Message Some Message Some Message Some Message Some Message Some Message Some Message Some Message Some Message ",
            ["14"],
            $url = "http://qrclean/reservas",
            $data = json_decode('{"datos": [1,2,3,4]}'),
            $buttons = null,
            $schedule = null
        );
       @endphp --}}
       {{-- @php
           notificar_usuario(App\Models\Users::find(14),"prueba",null,"prueba",[3],1,[],null);
       @endphp --}}

       @php
           $datos='{
    "result": {
        "promoted_by": "",
        "parent": "",
        "caused_by": "",
        "watch_list": "",
        "upon_reject": "cancel",
        "sys_updated_on": "2022-08-25 09:06:36",
        "approval_history": "",
        "number": "INC0021737",
        "proposed_by": "",
        "lessons_learned": "",
        "u_num_workspace": "001",
        "u_inc_type": "workspace",
        "state": "1",
        "sys_created_by": "spotlinker.integration",
        "knowledge": "false",
        "order": "",
        "cmdb_ci": "",
        "contract": "",
        "impact": "4",
        "active": "true",
        "work_notes_list": "",
        "priority": "4",
        "sys_domain_path": "/",
        "business_duration": "",
        "group_list": "",
        "approval_set": "",
        "major_incident_state": "",
        "universal_request": "",
        "short_description": "Título de la incidencia",
        "correlation_display": "",
        "work_start": "",
        "additional_assignee_list": "",
        "notify": "1",
        "service_offering": "",
        "sys_class_name": "incident",
        "closed_by": "",
        "follow_up": "",
        "parent_incident": "",
        "reopened_by": "",
        "u_servicenow_url": "https://now4v1.service-now.com/nav_to.do?uri=incident.do?sys_id=9348515d876999905b90a7573cbb35e1",
        "reassignment_count": "0",
        "assigned_to": "",
        "sla_due": "",
        "comments_and_work_notes": "",
        "u_outside_time_worked": "false",
        "u_description_html": "Descripción de la incidencia",
        "escalation": "0",
        "upon_approval": "proceed",
        "u_rpt_link": "INC0021737",
        "correlation_id": "",
        "timeline": "",
        "u_thrid_party_url": "",
        "made_sla": "true",
        "promoted_on": "",
        "u_third_party_id": "1186",
        "u_ramo": "",
        "child_incidents": "0",
        "hold_reason": "",
        "task_effective_number": "INC0021737",
        "u_building": "PED",
        "resolved_by": "",
        "sys_updated_by": "spotlinker.integration",
        "opened_by": {
            "link": "https://now4v1.service-now.com/api/now/table/sys_user/473519298701ddd05b90a7573cbb35f3",
            "value": "473519298701ddd05b90a7573cbb35f3"
        },
        "u_workspace": "F",
        "user_input": "",
        "sys_created_on": "2022-08-25 09:06:36",
        "sys_domain": {
            "link": "https://now4v1.service-now.com/api/now/table/sys_user_group/global",
            "value": "global"
        },
        "proposed_on": "",
        "actions_taken": "",
        "route_reason": "",
        "calendar_stc": "",
        "closed_at": "",
        "business_service": {
            "link": "https://now4v1.service-now.com/api/now/table/cmdb_ci_service/df709d88db6fa810b318917bd3961928",
            "value": "df709d88db6fa810b318917bd3961928"
        },
        "business_impact": "",
        "rfc": "",
        "time_worked": "",
        "expected_start": "",
        "opened_at": "2022-08-25 09:06:36",
        "u_cod_workspace": "PED-01-F-001",
        "u_cat_user": "",
        "work_end": "",
        "caller_id": {
            "link": "https://now4v1.service-now.com/api/now/table/sys_user/db4f8e541b65519088d8f7c4464bcbfe",
            "value": "db4f8e541b65519088d8f7c4464bcbfe"
        },
        "reopened_time": "",
        "resolved_at": "",
        "subcategory": "",
        "work_notes": "",
        "close_code": "",
        "assignment_group": {
            "link": "https://now4v1.service-now.com/api/now/table/sys_user_group/a24f43a3db9c8d902f8e92b8f49619cd",
            "value": "a24f43a3db9c8d902f8e92b8f49619cd"
        },
        "business_stc": "",
        "cause": "",
        "description": "",
        "calendar_duration": "",
        "close_notes": "",
        "sys_id": "9348515d876999905b90a7573cbb35e1",
        "contact_type": "spotlinker",
        "incident_state": "1",
        "urgency": "4",
        "problem_id": "",
        "company": "",
        "u_estimated_time_worked": "",
        "activity_due": "",
        "severity": "3",
        "overview": "",
        "comments": "",
        "approval": "not requested",
        "u_floor": "01",
        "due_date": "",
        "sys_mod_count": "0",
        "reopen_count": "0",
        "sys_tags": "",
        "location": "",
        "category": ""
    }
}';

        function recursiveFind(array $haystack, $needle)
        {
            $iterator  = new RecursiveArrayIterator($haystack);
            $recursive = new RecursiveIteratorIterator(
                $iterator,
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($recursive as $key => $value) {
                if ($key === $needle) {
                    return $value;
                }
            }
        }

        

        if(isjson($datos)){
            $datos=json_decode($datos,true);
            dd(find_in_ArrayRecursive($datos,'result|assignment_group|value'));
        }
       @endphp
       
    </div>
</div>

@endsection


@section('scripts')
    <script>
        

        $('.SECCION_MENU').addClass('active active-sub');
        $('.ITEM_MENU').addClass('active-link');
    </script>
@endsection
