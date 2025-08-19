<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReportDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Laporan';
    protected static ?string $navigationLabel = 'Semua Laporan';
    protected static string $view = 'filament.pages.report-dashboard';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 31;
}
