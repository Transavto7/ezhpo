<?php

namespace App\Events\UserActions;

use App\Enums\UserActionTypesEnum;
use App\User;
use Illuminate\Queue\SerializesModels;

class ClientReportRequest implements UserActionEventInterface
{
    use SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $requestType;

    /**
     * @param User $user
     * @param string $requestType
     */
    public function __construct(User $user, string $requestType)
    {
        $this->user = $user;
        $this->requestType = $requestType;
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
        switch ($this->requestType) {
            case 'graph_pv':
                return UserActionTypesEnum::WORK_SCHEDULE_REQUEST;
            case 'service_report_request':
                return UserActionTypesEnum::SERVICE_REPORT_REQUEST;
            case 'dynamic_medic':
                return UserActionTypesEnum::MEDICAL_INSPECTIONS_NUMBER_REPORT_REQUEST;
            case 'dynamic_tech':
                return UserActionTypesEnum::TECHNICAL_INSPECTIONS_NUMBER_REPORT_REQUEST;
            case 'dynamic_all':
                return UserActionTypesEnum::ALL_INSPECTIONS_NUMBER_REPORT_REQUEST;
            default:
                throw new \DomainException("Undefined request type $this->requestType");
        }
    }
}
