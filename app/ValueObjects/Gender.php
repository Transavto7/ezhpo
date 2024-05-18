<?php
declare(strict_types=1);

namespace App\ValueObjects;

use App\Exceptions\GenderParseFailedException;

final class Gender
{
    const MALE = 'Мужской';
    const FEMALE = 'Женский';

    /** @var string */
    private $value;

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Gender
     * @throws GenderParseFailedException
     */
    public static function parse(string $value): Gender
    {
        if (self::isMale($value)) {
            return self::male();
        }

        if (self::isFemale($value)) {
            return self::female();
        }

        throw new GenderParseFailedException("Gender '$value' is not a valid gender.");
    }

    /**
     * @param string $value
     * @return Gender
     * @throws GenderParseFailedException
     */
    public static function from(string $value): Gender
    {
        switch ($value) {
            case self::MALE:
                return self::male();
            case self::FEMALE:
                return self::female();
            default:
                throw new GenderParseFailedException("Gender '$value' is not a valid gender.");
        }
    }

    /**
     * @return Gender
     */
    public static function male(): Gender
    {
        return new self(self::MALE);
    }

    /**
     * @return Gender
     */
    public static function female(): Gender
    {
        return new self(self::FEMALE);
    }

    /**
     * @param string $value
     * @return bool
     */
    private static function isMale(string $value): bool
    {
        $trimmed = mb_strtolower(trim($value));

        return (mb_strlen($trimmed) === 1 && $trimmed === 'м') || (str_contains($trimmed,'муж'));
    }

    /**
     * @param string $value
     * @return bool
     */
    private static function isFemale(string $value): bool
    {
        $trimmed = mb_strtolower(trim($value));

        return (mb_strlen($trimmed) === 1 && $trimmed === 'ж') || (str_contains($trimmed,'жен'));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
