<?php

declare(strict_types=1);

namespace PepperFM\FilamentPhoneNumbers\Tests\Fixtures;

use PepperFM\FilamentPhoneNumbers\Infolists\Components\PhoneNumberEntry;
use PepperFM\FilamentPhoneNumbers\Tests\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BaseInfolist extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public User $user;

    public function mount($id): void
    {
        $this->user = User::find($id);
    }

    public function testInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->user)
            ->schema($this->getInfolistSchema());
    }

    public function getInfolistSchema(): array
    {
        return [
            PhoneNumberEntry::make('phone'),
        ];
    }

    public function render(): View
    {
        return view('infolists.fixtures.infolist');
    }
}
