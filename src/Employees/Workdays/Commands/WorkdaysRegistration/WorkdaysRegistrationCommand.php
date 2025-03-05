<?php

declare(strict_types=1);

namespace Src\Employees\Workdays\Commands\WorkdaysRegistration;

use App\User;
use App\ValueObjects\ForeignDevice\Alcometer;
use App\ValueObjects\ForeignDevice\Pulse;
use App\ValueObjects\ForeignDevice\Temperature;
use App\ValueObjects\ForeignDevice\Tonometer;
use DateTime;
use Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum;

final class WorkdaysRegistrationCommand
{
    private $terminal = null;

    private $date = null;

    private $employeeId = null;

    private $probaAlko = null;

    private $peopleThermometer = null;

    private $tonometer = null;

    private $typeAnketa = null;

    private $pulse = null;

    private $alcometer = null;

    private $testNarko = null;

    private $photo = null;

    private $video = null;

    /**
     * @return User|null
     */
    public function getTerminal(): ?User
    {
        return $this->terminal;
    }

    /**
     * @param User|null $terminal
     * @return self
     * @throws \Exception
     */
    public function setTerminal($terminal): self
    {
        if ($terminal instanceof User || is_null($terminal)) {
            $this->terminal = $terminal;
        } else {
            throw new \Exception('Неверный тип терминала');
        }

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return WorkdaysRegistrationCommand
     * @throws \DateMalformedStringException
     */
    public function setDate($date)
    {
        if (is_null($date)) {
            $this->date = null;
        } elseif ($date instanceof DateTime) {
            $this->date = $date;
        } else {
            $this->date = new DateTime($date);
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    /**
     * @param mixed $employeeId
     * @return WorkdaysRegistrationCommand
     */
    public function setEmployeeId($employeeId): self
    {
        if (! is_null($employeeId)) {
            $employeeId = (int) $employeeId;
        }
        $this->employeeId = (int) $employeeId;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getProbaAlko(): ?bool
    {
        return $this->probaAlko;
    }

    /**
     * @param mixed $probaAlko
     * @return WorkdaysRegistrationCommand
     */
    public function setProbaAlko($probaAlko): self
    {
        if (is_null($probaAlko)) {
            $this->probaAlko = null;
        } elseif (is_bool($probaAlko) || is_numeric($probaAlko)) {
            $this->probaAlko = (bool) $probaAlko;
        } else {
            $this->probaAlko = $probaAlko !== 'Положительно';
        }

        return $this;
    }

    /**
     * @return Temperature|null
     */
    public function getPeopleThermometer(): ?Temperature
    {
        return $this->peopleThermometer;
    }

    /**
     * @param Temperature|null $peopleThermometer
     * @return $this
     */
    public function setPeopleThermometer(?Temperature $peopleThermometer): self
    {
        $this->peopleThermometer = $peopleThermometer;

        return $this;
    }

    /**
     * @return Tonometer|null
     */
    public function getTonometer(): ?Tonometer
    {
        return $this->tonometer;
    }

    /**
     * @param Tonometer|null $tonometer
     * @return $this
     */
    public function setTonometer(?Tonometer $tonometer): self
    {
        $this->tonometer = $tonometer;

        return $this;
    }

    /**
     * @return WorkdayEventTypeEnum
     * @throws \Exception
     */
    public function getTypeAnketa(): WorkdayEventTypeEnum
    {
        if (is_null($this->typeAnketa)) {
            throw new \Exception('Null value in typeAnketa');
        }

        return $this->typeAnketa;
    }

    /**
     * @param WorkdayEventTypeEnum $typeAnketa
     * @return WorkdaysRegistrationCommand
     */
    public function setTypeAnketa(WorkdayEventTypeEnum $typeAnketa): self
    {
        $this->typeAnketa = $typeAnketa;

        return $this;
    }

    /**
     * @return Pulse|null
     */
    public function getPulse(): ?Pulse
    {
        return $this->pulse;
    }

    /**
     * @param Pulse|null $pulse
     * @return $this
     */
    public function setPulse(?Pulse $pulse): self
    {
        $this->pulse = $pulse;

        return $this;
    }

    /**
     * @return Alcometer|null
     */
    public function getAlcometer(): ?Alcometer
    {
        return $this->alcometer;
    }

    /**
     * @param Alcometer|null $alcometer
     * @return $this
     */
    public function setAlcometer(?Alcometer $alcometer): self
    {
        $this->alcometer = $alcometer;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getTestNarko(): ?bool
    {
        return $this->testNarko;
    }

    /**
     * @param mixed $testNarko
     * @return WorkdaysRegistrationCommand
     */
    public function setTestNarko($testNarko): self
    {
        if (is_null($testNarko)) {
            $this->testNarko = null;
        } elseif (is_bool($testNarko) || is_numeric($testNarko)) {
            $this->testNarko = (bool) $testNarko;
        } else {
            $this->testNarko = $testNarko !== 'Положительно';
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     * @return WorkdaysRegistrationCommand
     */
    public function setPhoto($photo): self
    {
        if (! is_null($photo)) {
            $photo = (string) $photo;
        }
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVideo(): ?string
    {
        return $this->video;
    }

    /**
     * @param string|null $video
     * @return WorkdaysRegistrationCommand
     */
    public function setVideo($video): self
    {
        if (! is_null($video)) {
            $video = (string) $video;
        }
        $this->video = $video;

        return $this;
    }
}
