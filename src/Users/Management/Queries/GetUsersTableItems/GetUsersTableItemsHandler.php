<?php

namespace Src\Users\Management\Queries\GetUsersTableItems;

use App\Enums\UserEntityType;
use App\User;
use Carbon\Carbon;
use Src\Users\Management\Enums\UserStatusEnum;

final class GetUsersTableItemsHandler
{
    public function handle(GetUsersTableItemsQuery $query): TableItems
    {
        $builder = User::query()
            ->with([
                'roles',
            ]);

        if ($query->getSearch()) {
            $pattern = '%'.$query->getSearch().'%';

            $builder->where(function ($query) use ($pattern) {
                $query->where(function ($subQuery) use ($pattern) {
                    $subQuery->where('entity_type', '!=', UserEntityType::TERMINAL)
                        ->where(function ($q) use ($pattern) {
                            $q->where('login', 'like', $pattern)
                                ->orWhere('email', 'like', $pattern);
                        });
                })->orWhere(function ($subQuery) use ($pattern) {
                    $subQuery->where('entity_type', UserEntityType::TERMINAL)
                        ->whereExists(function ($query) use ($pattern) {
                            $query->selectRaw(1)
                                ->from('terminals')
                                ->whereColumn('terminals.related_user_id', 'users.id')
                                ->where('terminals.name', 'like', $pattern);
                        });
                })->orWhere(function ($subQuery) use ($pattern) {
                    $subQuery->whereNull('entity_type')
                        ->where(function ($q) use ($pattern) {
                            $q->where('login', 'like', $pattern)
                                ->orWhere('email', 'like', $pattern);
                        });
                });
            });
        }

        if ($query->getStatus()) {
            $pattern = $query->getStatus()->equal(UserStatusEnum::blocked()) ? 1 : 0;
            $builder->where('blocked', $pattern);
        }

        if ($query->getEntityType()) {
            $builder->where('entity_type', $query->getEntityType()->value());
        }

        if ($query->getSortBy() && $query->getSortOrder()) {
            $builder->orderBy($query->getSortBy(), $query->getSortOrder());
        }

        if (count($query->getUserIds())) {
            $builder->whereIn('id', $query->getUserIds());
        }

        if ($query->isUntyped()) {
            $builder->whereNull('users.entity_type');
        }

        $paginator = $builder->paginate($query->getPerPage());

        $items = $paginator
            ->getCollection()
            ->map(function (User $user) {
                $entityTypeLabel = null;
                if ($user->entity_type) {
                    $entityTypeLabel = UserEntityType::from($user->entity_type)->getLabel();
                }

                $updatedAt = null;
                if ($user->updated_at) {
                    $updatedAt = Carbon::parse($user->updated_at)->format('Y-m-d H:i:s');
                }

                $companyName = null;
                if ($user->isCompany() && $user->relatedCompany) {
                    $companyName = $user->relatedCompany->name;
                }

                if ($user->isDriver() && $user->relatedDriver) {
                    $companyName = $user->relatedDriver->company->name;
                }

                if ($user->isTerminal() && $user->relatedTerminal) {
                    $companyName = $user->relatedTerminal->company->name;
                }

                $roles = $user->roles->pluck('guard_name')->toArray();

                $login = $user->login;
                if ($user->isTerminal()) {
                    $login = $user->relatedTerminal->name;
                }

                return [
                    'id' => $user->id,
                    'show_url' => route('users.management.show-page', $user->id),
                    'entity_type' => $user->entity_type,
                    'entity_type_label' => $entityTypeLabel,
                    'login' => $login,
                    'email' => $user->email,
                    'company' => $companyName,
                    'roles' => $roles,
                    'blocked' => ! ($user->blocked === 0),
                    'api_token' => $user->api_token,
                    'updated_at' => $updatedAt,
                ];
            })
            ->toArray();

        return new TableItems(
            $items,
            $paginator->total()
        );
    }
}