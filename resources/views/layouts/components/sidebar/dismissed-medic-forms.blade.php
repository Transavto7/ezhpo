@php
    use App\Enums\FormTypeEnum;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Auth;
    use App\User;

    /** @var User|null $user */
    $user = Auth::user();

    $dismissedMedicFormsUrl = route('home', FormTypeEnum::MEDIC) . "?" . Arr::query([
        'filter' => 1,
        'date' => now()->subMonth()->startOfMonth()->format('Y-m-d'),
        'TO_date' => now()->subMonth()->endOfMonth()->format('Y-m-d'),
        'admitted' => ['Не допущен', 'Не идентифицирован'],
        'company_id' => ($user && $user->isCompany() && $user->relatedCompany) ? $user->relatedCompany->hash_id : null
    ]);
@endphp
<li>
    <a href="{{ $dismissedMedicFormsUrl }}" class="bg-success text-white">
        <i class="fa fa-book"></i>Отстранения водителей
    </a>
</li>
