<?php

namespace App\Events\TripTickets;

use App\Models\Forms\Form;
use App\Models\TripTicket;

final class UpdateRelatedItems
{
    /**
     * @var TripTicket|Form
     */
    private $updatedItem;

    /**
     * @param Form|TripTicket $updatedItem
     */
    public function __construct($updatedItem)
    {
        $this->updatedItem = $updatedItem;
    }

    /**
     * @return Form|TripTicket
     */
    public function getUpdatedItem()
    {
        return $this->updatedItem;
    }
}
