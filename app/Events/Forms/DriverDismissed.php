<?php

namespace App\Events\Forms;

use App\Models\Forms\Form;
use Illuminate\Queue\SerializesModels;

class DriverDismissed
{
    use SerializesModels;

    /**
     * @var Form
     */
    private $form;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }
}
