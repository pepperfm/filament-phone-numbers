<?php

declare(strict_types=1);

namespace PepperFM\FilamentPhoneNumbers\Support;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;

class PhoneHelper
{
    public static function normalizePhoneNumber(?string $number, bool $strict = false, int $format = PhoneNumberFormat::E164, string $region = 'US'): ?string
    {
        $phone = null;

        if (filled($number)) {
            try {
                $phone = PhoneNumber::parse($number, $region);
                $valid = $strict ? $phone->isValidNumber() : $phone->isPossibleNumber();

                if (!$valid) {
                    $phone = null;
                }
            } catch (PhoneNumberParseException $e) {
                $phone = null;
            }
        }

        return $phone?->format(PhoneNumberFormat::E164);
    }

    public static function isValidPhoneNumber(?string $number, $strict = false, bool $allowEmpty = true, string $region = 'US'): bool
    {
        if (blank($number) && $allowEmpty) {
            return true;
        }

        try {
            $phone = PhoneNumber::parse($number, $region);

            return $strict ? $phone->isValidNumber() : $phone->isPossibleNumber();
        } catch (PhoneNumberParseException $e) {
            return false;
        }
    }

    public static function formatPhoneNumber(?string $number, bool $strict = false, int $format = PhoneNumberFormat::NATIONAL, string $region = 'US'): ?string
    {
        if (!filled($number)) {
            return null;
        }

        try {
            if ($strict) {
                return self::normalizePhoneNumber($number, true, $format);
            }
            return PhoneNumber::parse($number, $region)->format($format);
        } catch (PhoneNumberParseException $e) {
            return null;
        }
    }
}
