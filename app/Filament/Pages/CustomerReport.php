<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CustomerReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.customer-report';
}
