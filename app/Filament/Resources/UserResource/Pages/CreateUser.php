<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->shouldRedirectToIndex ? UserResource::getUrl('index') : null;
    }

    protected bool $shouldRedirectToIndex = true;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Tambahkan logika hash password jika perlu
        $data['password'] = bcrypt($data['password']);
        return $data;
    }
}
