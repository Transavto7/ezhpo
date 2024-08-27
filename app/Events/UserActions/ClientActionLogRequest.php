<?php

namespace App\Events\UserActions;

use App\Enums\FormTypeEnum;
use App\Enums\UserActionTypesEnum;
use App\User;
use Illuminate\Queue\SerializesModels;

class ClientActionLogRequest implements UserActionEventInterface
{
    use SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $formType;

    /**
     * @param User $user
     * @param string $formType
     */
    public function __construct(User $user, string $formType)
    {
        $this->user = $user;
        $this->formType = $formType;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): string
    {
        switch ($this->formType) {
            case FormTypeEnum::MEDIC:
                return UserActionTypesEnum::MEDICAL_CHECKUP_LOG_REQUEST;
            case FormTypeEnum::TECH:
                return UserActionTypesEnum::TECHNICAL_INSPECTION_LOG_REQUEST;
            case FormTypeEnum::BDD:
                return UserActionTypesEnum::BRIEFING_LOG_REQUEST;
            case FormTypeEnum::REPORT_CARD:
                return UserActionTypesEnum::REPORT_CARD_LOG_REQUEST;
            case FormTypeEnum::PRINT_PL:
                return UserActionTypesEnum::TRIP_TICKET_PRINTING_LOG_REQUEST;
            case FormTypeEnum::PAK:
                return UserActionTypesEnum::ERROR_REGISTER_LOG_REQUEST;
            case FormTypeEnum::PAK_QUEUE:
                return UserActionTypesEnum::WAITING_LIST_REQUEST;
            default:
                throw new \DomainException("Undefined form type $this->formType");
        }
    }
}
