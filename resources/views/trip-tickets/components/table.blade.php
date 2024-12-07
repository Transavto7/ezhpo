<table id="trip-tickets-table" class="trip-tickets-table table table-striped table-sm">
    <thead>
    <tr>
        <th>#</th>

        @foreach($fieldPrompts as $field)
            <th
                data-field-key="{{ $field->field }}"
                @isset($blockedToExportFields[$field->field])
                    class="not-export"
                @endisset>
                <span class="user-select-none"
                      @if ($field->content)
                          data-toggle="tooltip"
                      data-html="true"
                      data-trigger="click hover"
                      title="{{ $field->content }}"
                      @endif>
                    {{ $field->name }}
                </span>
                <a class="not-export"
                   href="?orderBy={{ $orderBy === 'DESC' ? 'ASC' : 'DESC' }}&orderKey={{ $field->field }}&{{ $queryString }}">
                    <i class="fa fa-sort"></i>
                </a>
            </th>
        @endforeach

        @if(request()->get('trash'))
            <th width="60">Удаливший</th>
            <th width="60">Время удаления</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($tripTickets as $tripTicketKey => $tripTicket)
        <tr data-field="{{ $tripTicketKey }}">
            <td>
                <input
                    type="checkbox"
                    data-id="{{ $tripTicket->id }}"
                    class="hv-checkbox-mass-deletion">
            </td>

            @foreach($fieldPrompts as $field)
                <td data-field-key="{{ $field->field }}"
                    @isset($blockedToExportFields[$field->field])
                        class="not-export"
                    @endisset>
                    @if(in_array($field->field, ['start_date', 'created_at']) && $tripTicket[$field->field])
                        {{ date('d.m.Y', strtotime($tripTicket[$field->field])) }}
                    @elseif($field->field === 'driver_fio' && user()->access('drivers_read'))
                        <a href="{{ route('renderElements', ['model' => 'Driver', 'filter' => 1, 'fio' => $tripTicket[$field->field] ]) }}">
                            {{ $tripTicket[$field->field] }}
                        </a>
                    @elseif($field->field === 'car_gos_number' && user()->access('cars_read'))
                        <a href="{{ route('renderElements', ['model' => 'Car', 'filter' => 1, 'gos_number' => $tripTicket[$field->field] ]) }}">
                            {{ $tripTicket[$field->field] }}
                        </a>
                    @elseif($field->field === 'logistics_method')
                        {{ \App\Enums\LogisticsMethodEnum::getLabel($tripTicket[$field->field]) }}
                    @elseif($field->field === 'transportation_type')
                        {{ \App\Enums\TransportationTypeEnum::getLabel($tripTicket[$field->field]) }}
                    @elseif($field->field === 'template_code')
                        {{ \App\Enums\TripTicketTemplateEnum::getLabel($tripTicket[$field->field]) }}
                    @elseif(in_array($field->field, ['medic_form_id', 'tech_form_id']))
                        @component('trip-tickets.common.uuid-cell', ['uuid' => $tripTicket[$field->field]])
                        @endcomponent
                    @else
                        {{ $tripTicket[$field->field] }}
                    @endif
                </td>
            @endforeach

            @if($permissionToDelete && request()->get('trash'))
                <td class="td-option">
                    {{ ($tripTicket->deleted_user_name) }}
                </td>
                <td class="td-option">
                    {{ ($tripTicket->deleted_at) }}
                </td>
            @endif

            <td class="td-option not-export d-flex justify-content-end">
                @if($permissionToUpdate)
                    <a href="{{ route('trip-tickets.edit', $tripTicket->id) }}"
                       class="btn btn-info btn-sm mr-1"><i class="fa fa-edit"></i></a>
                @endif

                @if($permissionToDelete)
                    <a
                        href="{{ route('trip-tickets.trash', ['id' => $tripTicket->id, 'action' => request()->get('trash') ? 0 : 1]) }}"
                        class="btn btn-warning btn-sm hv-btn-trash mr-1"
                        data-id="{{ $tripTicket->id }}">
                        @if(request()->get('trash', 0))
                            <i class="fa fa-undo"></i>
                        @else
                            <i class="fa fa-trash"></i>
                        @endisset
                    </a>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
