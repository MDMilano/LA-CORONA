<?php

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;

class AccountWidget extends BaseAccountWidget
{
    protected string $view = 'filament-panels::widgets.account-widget';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
