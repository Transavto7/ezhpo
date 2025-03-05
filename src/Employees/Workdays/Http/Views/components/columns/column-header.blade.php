@php
    /**
     * @var \App\FieldPrompt $field
     */
@endphp

<th data-field-key="{{ $field->field }}">
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
