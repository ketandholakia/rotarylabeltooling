<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlateResource\Pages;
use App\Filament\Resources\PlateResource\RelationManagers;
use App\Models\Plate;
use App\Models\Media;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;



class PlateResource extends Resource
{
    protected static ?string $model = Plate::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Artworks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('plateno')->required(),
                TextInput::make('run')->required(),

                Select::make('plates_media_id')
                    ->label('Media')
                    ->options(Media::all()->pluck('mediatype', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('remark'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plateno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('run'),
                TextColumn::make('media.mediatype'),
                TextColumn::make('remark')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])->defaultSort('plateno', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ArtworksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlates::route('/'),
            'create' => Pages\CreatePlate::route('/create'),
            'edit' => Pages\EditPlate::route('/{record}/edit'),
        ];
    }
}
