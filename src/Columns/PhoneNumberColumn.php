<?php

declare(strict_types=1);

namespace PepperFM\FilamentPhoneNumbers\Columns;

use Brick\PhoneNumber\PhoneNumberFormat;
use PepperFM\FilamentPhoneNumbers\Enums\PhoneFormat;
use PepperFM\FilamentPhoneNumbers\Support\PhoneHelper;
use Filament\Tables\Columns\Concerns\CanBeSearchable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberColumn extends TextColumn
{
    use CanBeSearchable;

    protected null|\Closure|int $displayFormat = null;

    protected bool|\Closure $dial = false;

    protected null|\Closure|string $region = null;

    protected bool|\Closure $useDefaultSearch = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(static function (PhoneNumberColumn $column, $state): ?string {
            if (blank($state)) {
                return null;
            }

            return PhoneHelper::formatPhoneNumber(
                number: $state,
                strict: false,
                format: $column->getDisplayFormat(),
                region: $column->getRegion()
            );
        });

        if ($this->getDial()) {
            $this->url(fn(string $state) => PhoneHelper::formatPhoneNumber(
                number: $state,
                strict: false,
                format: PhoneNumberFormat::RFC3966,
                region: $this->getRegion()
            ));
        }
    }

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
        $this->region = $region;

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
            format: PhoneFormat::RFC3966->value,
            region: $this->getRegion()
        ));

        return $this;
    }

    public function getDial(): string|bool
    {
        return $this->evaluate($this->dial);
    }

    public function useDefaultSearch($defaultSearch = true): static
    {
        $this->useDefaultSearch = $defaultSearch;

        return $this;
    }

    public function getUseDefaultSearch(): bool
    {
        return $this->evaluate($this->useDefaultSearch);
    }

    public function searchable(array|bool|string $condition = true, ?\Closure $query = null, bool $isIndividual = false, bool $isGlobal = true): static
    {
        if (!$this->getUseDefaultSearch() && !$query) {
            parent::searchable(
                condition: $condition,
                query: function (Builder $query, string $search) {
                    if (str_starts_with($search, '(')) {
                        $phoneNumberUtil = PhoneNumberUtil::getInstance();
                        $country = $phoneNumberUtil->getCountryCodeForRegion($this->getRegion());
                        $numbers = '+' . $country . preg_replace('/[^0-9]/', '', $search);
                    } else {
                        $numbers = preg_replace('/[^0-9]/', '', $search);
                    }

                    if (filled($numbers)) {
                        return $query->where($this->name, 'like', '%' . $numbers . '%');
                    }
                    return $query;
                },
                isIndividual: $isIndividual,
                isGlobal: $isGlobal
            );
        } else {
            parent::searchable(condition: $condition, query: $query, isIndividual: $isIndividual, isGlobal: $isGlobal);
        }

        return $this;
    }
}
