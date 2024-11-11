<?php

namespace App\Actions\Element;

use App\Company;
use App\Driver;
use App\Enums\UserActionTypesEnum;
use App\Events\Relations\Attached;
use App\Events\UserActions\ClientAddRecord;
use App\Models\Contract;
use App\Services\BriefingService;
use App\Services\UserService;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class CreateDriverHandler extends AbstractCreateElementHandler implements CreateElementHandlerInterface
{
    /**
     * @throws Exception
     */
    public function handle($data)
    {
        $companyId = $data['company_id'];
        /** @var Company|null $company */
        $company = Company::withTrashed()->find($companyId);
        if (!$company) {
            throw new Exception('Компания не найдена');
        }

        $existItem = Driver::withTrashed()
            ->where('company_id', $companyId)
            ->where('fio', trim($data['fio']))
            ->first();
        if ($existItem) {
            throw new Exception('Найден дубликат по ФИО Водителя');
        }

        $validator = function (int $hashId) {
            if (Driver::withTrashed()->where('hash_id', $hashId)->first()) {
                return false;
            }

            if (User::withTrashed()->where('login', $hashId)->first()) {
                return false;
            }

            return true;
        };

        $data['hash_id'] = $this->generateHashId(
            $validator,
            config('app.hash_generator.driver.min'),
            config('app.hash_generator.driver.max'),
            config('app.hash_generator.driver.tries')
        );

        $attributesToSync = ['products_id'];
        foreach ($attributesToSync as $attributeName) {
            $attributeValue = $company->getAttribute($attributeName);

            if (!$attributeValue) {
                continue;
            }

            $data[$attributeName] = $attributeValue;
        }

        /** @var Driver $created */
        $created = $this->createElement($data);

        $user = Auth::user();
        if ($user) {
            event(new ClientAddRecord($user, UserActionTypesEnum::ADD_DRIVER_VIA_FORM));
        }

        UserService::createUserFromDriver($created);

        /** @var Contract $contract */
        $contract = Contract::query()
            ->where('company_id', $companyId)
            ->where('main_for_company', 1)
            ->first();

        if ($contract) {
            $contract->drivers()->attach($created->id);
            event(new Attached($contract, [$created->id], Driver::class));
        }

        if ($company->required_type_briefing) {
            BriefingService::createFirstBriefingForDriver($created, $company);
        }

        return $created;
    }
}
