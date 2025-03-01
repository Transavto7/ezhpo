<?php

namespace App\Events\Forms;

use App\Models\Forms\Form;
use App\User;
use Illuminate\Queue\SerializesModels;

class FormAction
{
    use SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Form
     */
    private $form;

    /**
     * @var string
     */
    private $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Form $form, string $type)
    {
        $this->user = $user;
        $this->form = $form;
        $this->type = $type;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
