<?php

namespace App\Events\UserActions;

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
            case 'medic':
                return UserActionTypesEnum::MEDICAL_CHECKUP_LOG_REQUEST;
            case 'tech':
                return UserActionTypesEnum::TECHNICAL_INSPECTION_LOG_REQUEST;
            case 'bdd':
                return UserActionTypesEnum::BRIEFING_LOG_REQUEST;
            case 'report_cart':
                return UserActionTypesEnum::REPORT_CARD_LOG_REQUEST;
            case 'pechat_pl':
                return UserActionTypesEnum::TRIP_TICKET_PRINTING_LOG_REQUEST;
            case 'pak':
                return UserActionTypesEnum::ERROR_REGISTER_LOG_REQUEST;
            case 'pak_queue':
                return UserActionTypesEnum::WAITING_LIST_REQUEST;
            default:
                throw new \DomainException("Undefined form type {$this->formType}");
        }
    }
}
