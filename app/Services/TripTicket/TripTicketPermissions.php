<?php

namespace App\Services\TripTicket;

use App\Enums\TripTicket\TripTicketStatus;
use App\Enums\TripTicket\TripTicketType;

final class TripTicketPermissions
{
    public static function canPrint(string $type, string $status): bool
    {
        $allowedStatuses = [TripTicketStatus::APPROVED, TripTicketStatus::ACTIVATED, TripTicketStatus::PRINTED];

        switch (true) {
            case $type === TripTicketType::GENERATED && in_array($status, $allowedStatuses):
            case $type === TripTicketType::IN_ADVANCE:
            case $type === TripTicketType::COMMON && in_array($status, $allowedStatuses):
                return true;
            default:
                return false;
        }
    }

    public static function canApprove(string $type, string $status): bool
    {
        switch (true) {
            case $type === TripTicketType::GENERATED && $status === TripTicketStatus::CREATED:
            case $type === TripTicketType::IN_ADVANCE && $status === TripTicketStatus::ACTIVATED:
            case $type === TripTicketType::COMMON && $status === TripTicketStatus::CREATED:
                return true;
            default:
                return false;
        }
    }

    public static function canCancelApprove(string $type, string $status): bool
    {
        switch (true) {
            case $type === TripTicketType::GENERATED && $status === TripTicketStatus::APPROVED:
            case $type === TripTicketType::IN_ADVANCE && $status === TripTicketStatus::APPROVED:
            case $type === TripTicketType::COMMON && $status === TripTicketStatus::APPROVED:
                return true;
            default:
                return false;
        }
    }

    public static function canEdit(string $type, string $status): bool
    {
        switch (true) {
            case $type === TripTicketType::GENERATED && $status === TripTicketStatus::CREATED:
            case $type === TripTicketType::IN_ADVANCE && $status !== TripTicketStatus::APPROVED:
            case $type === TripTicketType::COMMON && $status === TripTicketStatus::CREATED:
                return true;
            default:
                return false;
        }
    }

    public static function canDelete(string $type, string $status): bool
    {
        $allowedStatuses = [TripTicketStatus::CREATED, TripTicketStatus::PRINTED];

        switch (true) {
            case $type === TripTicketType::GENERATED && $status !== TripTicketStatus::APPROVED:
            case $type === TripTicketType::IN_ADVANCE && in_array($status, $allowedStatuses):
            case $type === TripTicketType::COMMON && $status === TripTicketStatus::CREATED:
                return true;
            default:
                return false;
        }
    }
}
