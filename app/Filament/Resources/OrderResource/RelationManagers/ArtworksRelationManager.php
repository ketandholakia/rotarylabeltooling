<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Models\Artwork;


class ArtworksRelationManager extends RelationManager
{
    protected static string $relationship = 'artworks';

    protected static ?string $recordTitleAttribute = 'orderno';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('description')->required(),

                Forms\Components\TextInput::make('requiredqty')->default(0)->required(),
                Forms\Components\TextInput::make('jobrun'),
                Forms\Components\TextInput::make('printedqty'),
                Forms\Components\TextInput::make('labelrepeat'),

                // Forms\Components\TextInput::make('artworks_media_id'),
                Forms\Components\Select::make('awstatus')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'printed' => 'Printed',
                        'platesent' => 'Plate Sent',
                        'sentforapproval' => 'Sent for Approval',
                        'noartworkfile' => 'No Artwork File',
                    ])->required()->default('Pending'),


                Forms\Components\Select::make('priority')
                    ->options([
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',

                    ])->required()->default('medium'),


                Forms\Components\TextInput::make('remark'),
                Forms\Components\Toggle::make('prepressstage')->label('Prepress Done'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('requiredqty')->label('R. Qty'),
                Tables\Columns\TextColumn::make('printedqty')->label('Prt Qty'),
                Tables\Columns\TextColumn::make('Balance')->label('Bal Qty')
                    ->getStateUsing(function (Artwork $record) {
                        // return whatever you need to show
                        return $record->printedqty - $record->requiredqty;
                    }),
                Tables\Columns\TextColumn::make('remark')->label('Ref'),
                Tables\Columns\BooleanColumn::make('prepressstage')->label('Prepress')->sortable(),

                Tables\Columns\BadgeColumn::make('awstatus')->label('Status')
                    ->colors([
                        'warning' => 'Pending',
                        'warning' => 'sentforapproval',
                        'success' => 'Approved',
                        'success' => 'Printed',
                        'success' => 'Plate Sent',
                        'warning' => 'noartworkfile',
                    ])->sortable(),



            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
                FilamentExportBulkAction::make('export')

            ]);
    }
}
