<Chart
    :pv_id="{{ json_encode(request()->get('pv_id')) }}"
    :date_from="'{{ request()->get('date_from') }}'"
    :date_to="'{{ request()->get('date_to') }}'"
    :type_anketa="'{{ request()->get('type_anketa') }}'"
></Chart>
