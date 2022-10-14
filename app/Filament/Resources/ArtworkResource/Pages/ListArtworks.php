<?php

namespace App\Filament\Resources\ArtworkResource\Pages;

use App\Filament\Resources\ArtworkResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Konnco\FilamentImport\Actions\ImportAction;
use Konnco\FilamentImport\ImportField;

class ListArtworks extends ListRecords
{
    protected static string $resource = ArtworkResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),

        ];
    }

    protected function getHeaderWidgets(): array
    {
        return ArtworkResource::getWidgets();
    }
}
