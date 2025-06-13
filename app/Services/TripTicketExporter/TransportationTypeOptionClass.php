<?php

namespace App\Services\TripTicketExporter;

use App\Enums\TripTicket\TransportationTypeEnum;
use App\Enums\TripTicket\TripTicketTemplateEnum;

final class TransportationTypeOptionClass
{
    public static function getClassName(string $type): string
    {
        $className = '';

       if (in_array($type, TransportationTypeEnum::forTemplate4C())) {
           $className .= ' pl-'. TripTicketTemplateEnum::S4;
       }
       if (in_array($type, TransportationTypeEnum::forTemplate3())) {
           $className .= ' pl-'. TripTicketTemplateEnum::_3;
       }
       if (in_array($type, TransportationTypeEnum::forTemplate4P())) {
           $className .= ' pl-'. TripTicketTemplateEnum::_4P;
       }
       if (in_array($type, TransportationTypeEnum::forTemplatePG1())) {
           $className .= ' pl-'. TripTicketTemplateEnum::PG1;
       }
       if (in_array($type, TransportationTypeEnum::forTemplate6C())) {
           $className .= ' pl-'. TripTicketTemplateEnum::_6C;
       }
       if (in_array($type, TransportationTypeEnum::forTemplate3C())) {
           $className .= ' pl-'. TripTicketTemplateEnum::_3C;
       }
       if (in_array($type, TransportationTypeEnum::forTemplate4O())) {
           $className .= ' pl-'. TripTicketTemplateEnum::_4O;
       }
       if (in_array($type, TransportationTypeEnum::forTemplateECM2())) {
           $className .= ' pl-'. TripTicketTemplateEnum::ECM2;
       }

        return $className;
    }
}
