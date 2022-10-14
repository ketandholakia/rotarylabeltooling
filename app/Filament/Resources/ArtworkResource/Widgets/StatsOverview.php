<?php

namespace App\Filament\Resources\ArtworkResource\Widgets;

use App\Models\Artwork;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Artworks', Artwork::count()),

            Card::make('Pending Prepress', Artwork::whereIn('prepressstage', [0])->count()),
            Card::make('Prepress Completed', Artwork::whereIn('prepressstage', [1])->count()),
            Card::make('Artwork Pending', Artwork::whereIn('awstatus', ['pending'])->count()),
            Card::make('Priority', Artwork::whereIn('priority', ['high'])->count()),

            Card::make('Sent for Approval', Artwork::whereIn('awstatus', ['sentforapproval'])->count())->extraAttributes([
                'class' => 'cursor-pointer',
                'wire:click' => '$emitUp("setStatusFilter", "processed")',
            ]),

        ];
    }
}
