<?php

declare(strict_types=1);

namespace PepperFM\FilamentPhoneNumbers\Infolists\Components;

use Brick\PhoneNumber\PhoneNumberFormat;
use PepperFM\FilamentPhoneNumbers\Enums\PhoneFormat;
use PepperFM\FilamentPhoneNumbers\Support\PhoneHelper;
use Filament\Infolists\Components;

class PhoneNumberEntry extends Components\TextEntry
{
    protected null|\Closure|int $displayFormat = null;

    protected null|\Closure|string $region = null;

    protected bool|\Closure $dial = false;

    public function displayFormat(int|PhoneFormat $format = PhoneNumberFormat::NATIONAL): static
    {
        $this->displayFormat = $format instanceof PhoneFormat ? $format->value : $format;

        return $this;
    }

    public function getDisplayFormat(): int
    {
        return $this->displayFormat ? $this->evaluate($this->displayFormat)
            : config('filament-phone-numbers.defaults.display_format');
    }

    public function region(string $region = 'US'): static
    {
        $this->region = strtoupper($region);

        return $this;
    }

    public function getRegion(): string
    {
        return $this->region ? $this->evaluate($this->region)
            : config('filament-phone-numbers.defaults.region');
    }

    public function dial(bool $dial = true): static
    {
        $this->dial = $dial;

        $this->url(fn(?string $state) => PhoneHelper::formatPhoneNumber(
            number: $state,
            strict: false,
            format: PhoneFormat::RFC3966->value,
            region: $this->getRegion()
        ));

        return $this;
    }

    public function getDial(): string
    {
        return $this->evaluate($this->dial);
    }

    public function formatState(mixed $state): mixed
    {
        return PhoneHelper::formatPhoneNumber(
            number: $state,
            strict: false,
            format: $this->getDisplayFormat(),
            region: $this->getRegion()
        );
    }
}
