<?php

namespace Src\Users\Management\Queries\GetUserItem;

use Carbon\Carbon;
use JsonSerializable;

final class UserItemViewModel implements JsonSerializable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $blocked;

    /**
     * @var string|null
     */
    private $apiToken;

    /**
     * @var string|null
     */
    private $entityType;

    /**
     * @var string|null
     */
    private $entityTypeLabel;

    /**
     * @var EntityViewModel|null
     */
    private $entity;

    /**
     * @var CompanyViewModel|null
     */
    private $company;

    /**
     * @var string[]
     */
    private $roles;

    /**
     * @var Carbon|null
     */
    private $deletedAt;

    /**
     * @param int $id
     * @param string $login
     * @param string $email
     * @param bool $blocked
     * @param string|null $apiToken
     * @param string|null $entityType
     * @param string|null $entityTypeLabel
     * @param EntityViewModel|null $entity
     * @param CompanyViewModel|null $company
     * @param string[] $roles
     * @param Carbon|null $deletedAt
     */
    public function __construct(
        int $id,
        string $login,
        string $email,
        bool $blocked,
        ?string $apiToken,
        ?string $entityType,
        ?string $entityTypeLabel,
        ?EntityViewModel $entity,
        ?CompanyViewModel $company,
        array $roles,
        ?Carbon $deletedAt
    ) {
        $this->id = $id;
        $this->login = $login;
        $this->email = $email;
        $this->blocked = $blocked;
        $this->apiToken = $apiToken;
        $this->entityType = $entityType;
        $this->entityTypeLabel = $entityTypeLabel;
        $this->entity = $entity;
        $this->company = $company;
        $this->roles = $roles;
        $this->deletedAt = $deletedAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'blocked' => $this->blocked,
            'apiToken' => $this->apiToken,
            'entityType' => $this->entityType,
            'entityTypeLabel' => $this->entityTypeLabel,
            'entity' => $this->entity,
            'company' => $this->company,
            'roles' => $this->roles,
            'deletedAt' => $this->deletedAt ? $this->deletedAt->format('d.m.Y H:i:s') : null,
        ];
    }
}
