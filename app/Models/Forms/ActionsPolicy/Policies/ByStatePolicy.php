<?php

namespace App\Models\Forms\ActionsPolicy\Policies;

use App\Models\Forms\Form;
use App\User;
use App\Models\Forms\ActionsPolicy\Contracts\PolicyInterface;

class ByStatePolicy implements PolicyInterface
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var array
     */
    private $disabledAttributesMap;

    /**
     * @var array
     */
    private $hiddenAttributesMap;

    public function __construct(Form $form, ?User $user)
    {
        $this->form = $form;
        $this->user = $user;

        $this->flush();
    }

    private function flush()
    {
        $this->flushDisabledAttributes();
        $this->flushHiddenAttributes();
    }

    public function getDisabledAttributesMap(): array
    {
        return array_keys($this->disabledAttributesMap);
    }

    public function getHiddenAttributesMap(): array
    {
        return array_keys($this->hiddenAttributesMap);
    }

    public function isAttributeDisabled(string $attribute): bool
    {
        return isset($this->disabledAttributesMap[$attribute]);
    }

    public function isAttributeHidden(string $attribute): bool
    {
        return isset($this->hiddenAttributesMap[$attribute]);
    }

    private function flushDisabledAttributes()
    {
        $this->disabledAttributesMap = [];

        $disabledIfFilled = [
            'driver_id',
            'company_id',
            'car_id',
            'type_view',
            'period_pl',
            'car_type_auto'
        ];

        $details = $this->form->details;
        if (!$details->is_dop || ($details->result_dop !== null)) {
            $disabledIfFilled[] = 'point_id';
            $disabledIfFilled[] = 'date';
        }

        foreach ($disabledIfFilled as $attribute) {
            if ($this->form->getAttribute($attribute) !== null) {
                $this->disabledAttributesMap[$attribute] = true;

                continue;
            }

            if ($details->getAttribute($attribute) !== null) {
                $this->disabledAttributesMap[$attribute] = true;
            }
        }
    }

    private function flushHiddenAttributes()
    {
        $this->hiddenAttributesMap = [];
    }
}
