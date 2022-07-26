<html>
    @isset($data['medics'])
        @include('reports.journal.export.medics', ['medics' => $data['medics']])
    @endisset

    @isset($data['techs'])
        @include('reports.journal.export.techs', ['techs' => $data['techs']])
    @endisset

    @php
        $months = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
            "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"
        ];
    @endphp

    @isset($data['medics_other'])
        @include('reports.journal.export.medics-other', ['months' => $months, 'data' => $data['medics_other']])
    @endisset

    @isset($data['techs_other'])
        @include('reports.journal.export.techs-other', ['months' => $months, 'data' => $data['techs_other']])
    @endisset

    @isset($data['other_pl'])
        @include('reports.journal.export.pl', ['months' => $months, 'data' => $data['other_pl']])
    @endisset
</html>
