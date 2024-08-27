<?php

namespace App\Events\Forms;

use App\Anketa;
use Illuminate\Queue\SerializesModels;

class DriverDismissed
{
    use SerializesModels;

    /**
     * @var Anketa
     */
    private $form;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Anketa $form)
    {
        $this->form = $form;
    }

    /**
     * @return Anketa
     */
    public function getForm(): Anketa
    {
        return $this->form;
    }
}
