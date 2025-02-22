<?php

namespace Src\Users\Management\Queries\GetUserItem;

use App\Enums\UserEntityType;
use App\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetUserItemHandler
{
    public function handle(GetUserItemQuery $query): UserItemViewModel
    {
        $user = User::withTrashed()->find($query->getId());

        if (! $user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        $entityViewModel = null;
        $entityTypeLabel = null;
        if ($user->entity_type) {
            $entity = $user->entity;
            $entityTypeLabel = UserEntityType::from($user->entity_type)->getLabel();

            $name = $user->isDriver() ? $entity->fio : $entity->name;

            if ($entity) {
                $url = null;

                if ($user->isTerminal()) {
                    $url = route('terminals.index', ['terminal_id' => [$entity->id]]);
                } elseif ($user->isEmployee()) {
                    $url = route('employees.index', ['employee_id' => [$entity->id]]);
                } elseif ($user->isCompany()) {
                    $url = route('renderElements', ['model' => 'Company', 'filter' => 1, 'id' => $entity->id]);
                } elseif ($user->isDriver()) {
                    $url = route('renderElements', ['model' => 'Driver', 'filter' => 1, 'id' => $entity->id ]);
                }

                $entityViewModel = new EntityViewModel(
                    $entity->hash_id,
                    $name,
                    $entity->deleted_at !== null,
                    $url
                );
            }
        }

        $companyViewModel = null;
        $company = null;

        if ($user->isTerminal() && $user->relatedTerminal) {
            $company = $user->relatedTerminal->company;
        }

        if ($user->isDriver() && $user->relatedDriver) {
            $company = $user->relatedDriver->company;
        }

        if ($user->isCompany() && $user->relatedCompany) {
            $company = $user->relatedCompany;
        }

        if ($company && $company->hash_id && $company->name) {
            $companyViewModel = new CompanyViewModel($company->hash_id, $company->name);
        }

        $roles = $user->roles->pluck('guard_name')->toArray();

        return new UserItemViewModel(
            $user->id,
            $user->login,
            $user->email,
            $user->blocked === 1,
            $user->api_token,
            $user->entity_type,
            $entityTypeLabel,
            $entityViewModel,
            $companyViewModel,
            $roles,
            $user->deleted_at
        );
    }
}
