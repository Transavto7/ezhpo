<?php

namespace App\ValueObjects;

use App\Driver;
use App\Models\Forms\MedicForm;

class NotAdmittedReasons
{
    /**
     * @var array
     */
    private $data;

    protected function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromForm(MedicForm $form): self
    {
        return new self($form->only([
            'alcometer_result',
            'proba_alko',
            'test_narko',
            'med_view',
            't_people',
            'driver_id',
            'pulse',
            'tonometer'
        ]) + ['driver_id' => $form->form->driver_id]);
    }

    public function getReasons(): array
    {
        $reasons = [];

        if (doubleval($this->data['alcometer_result'] ?? 0) > 0) {
            $reasons[] = 'alcometer_result';
        }

        if (($this->data['proba_alko'] ?? '') === 'Положительно') {
            $reasons[] = 'proba_alko';
        }

        if (($this->data['test_narko'] ?? '') === 'Положительно') {
            $reasons[] = 'test_narko';
        }

        if (($this->data['med_view'] ?? '') === 'Отстранение') {
            $reasons[] = 'med_view';
        }

        if (doubleval($this->data['t_people']) >= 37) {
            $reasons[] = 't_people';
        }

        $driver = Driver::query()->where('hash_id', $this->data['driver_id'] ?? 0)->first();

        /** @var Driver $driver */
        if ($driver) {
            $pulse = intval($this->data['pulse'] ?? 60);
            if ($pulse <= $driver->getPulseLower() || $pulse >= $driver->getPulseUpper())
            {
                $reasons[] = ['pulse'];
            }

            $pressure = explode('/', $this->data['tonometer'] ?? '120/60');
            $systolic = intval($pressure[0]);
            $diastolic = intval($pressure[1]);

            if ($diastolic >= $driver->getPressureDiastolic() || $systolic >= $driver->getPressureSystolic()) {
                $reasons[] = 'tonometer';
            }
        }

        return $reasons;
    }
}
