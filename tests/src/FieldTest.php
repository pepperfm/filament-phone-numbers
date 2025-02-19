<?php

declare(strict_types=1);

use PepperFM\FilamentPhoneNumbers\Forms\Components\PhoneNumber;
use PepperFM\FilamentPhoneNumbers\Support\PhoneHelper;
use PepperFM\FilamentPhoneNumbers\Tests\Fixtures\BaseForm;
use PepperFM\FilamentPhoneNumbers\Tests\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

use function Pest\Livewire\livewire;

it('can render a US format phone number', function () {
    $user = User::factory()->e164()->create();

    $formattedPhone = PhoneHelper::formatPhoneNumber(
        number: $user->phone,
    );

    livewire(TestForm::class, [
        'id' => $user->id,
    ])
        ->assertFormFieldExists('phone', function (PhoneNumber $field): bool {
            return $field->isEnabled();
        })
        ->assertFormSet([
            'phone' => $formattedPhone,
        ]);
});

it('can render a US format phone number with correct mask', function () {
    $user = User::factory()->e164()->create();

    $formattedPhone = PhoneHelper::formatPhoneNumber(
        number: $user->phone,
    );

    livewire(TestFormWithMask::class, [
        'id' => $user->id,
    ])
        ->assertFormFieldExists('phone', function (PhoneNumber $field): bool {
            return $field->getMask() === '(999) 999-9999';
        })
        ->assertFormSet([
            'phone' => $formattedPhone,
        ]);
});

it('can save a US format phone number to e164 format', function () {
    $user = User::factory()->e164()->create();

    $formattedPhone = PhoneHelper::formatPhoneNumber(
        number: $user->phone,
    );

    $newFormattedPhone = PhoneHelper::formatPhoneNumber(
        number: '2345551212',
    );

    livewire(TestFormWithMask::class, [
        'id' => $user->id,
    ])
        ->assertFormFieldExists('phone', function (PhoneNumber $field): bool {
            return $field->getMask() === '(999) 999-9999';
        })
        ->assertFormSet([
            'phone' => $formattedPhone,
        ])
        ->fillForm([
            'phone' => '2345551212',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();

    expect($user->phone)->toBe('+12345551212');
});

class TestForm extends BaseForm
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                PhoneNumber::make('phone'),
            ]);
    }
}

class TestFormWithMask extends BaseForm
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //                TextInput::make('name'),
                //
                //                TextInput::make('email'),

                PhoneNumber::make('phone')
                    ->mask(),
            ]);
    }
}
