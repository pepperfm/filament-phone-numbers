<?php

declare(strict_types=1);

namespace PepperFM\FilamentPhoneNumbers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PepperFM\FilamentPhoneNumbers\FilamentPhoneNumbers
 */
class FilamentPhoneNumbers extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \PepperFM\FilamentPhoneNumbers\FilamentPhoneNumbers::class;
    }
}
