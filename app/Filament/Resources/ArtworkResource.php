<?php

namespace App\Filament\Resources;


use App\Filament\Resources\ArtworkResource\Pages;
use App\Filament\Resources\ArtworkResource\RelationManagers;
use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Plate;
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
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\BadgeColumn;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Date;
use Filament\Tables\Filters\TernaryFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\NumberFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\TextFilter;
use Filament\Forms\Components\Grid;
use Konnco\FilamentImport\Actions\ImportAction;
use Konnco\FilamentImport\ImportField;
use App\Filament\Resources\ArtworkResource\Widgets\StatsOverview;



class ArtworkResource extends Resource
{
    protected static ?string $model = Artwork::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Artworks';
    protected static ?string $recordTitleAttribute = 'description';


    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }


    public static function form(Form $form): Form
    {


        return $form


            ->schema([
                TextInput::make('description')->required(),

                Select::make('artworks_order_id')
                    ->label('Order')
                    ->options(Order::all()->pluck('orderno', 'id'))
                    ->searchable()
                    ->required()
                    ->hint("[Go to Order](" . url("/orders/") . ")"),
                TextInput::make('requiredqty')->required(),
                // TextInput::make('jobrun'),
                // TextInput::make('labelrepeat'),
                TextInput::make('printedqty'),
                TextInput::make('remark'),

                Select::make('artworks_plate_id')
                    ->label('Plate ID')
                    ->options(Plate::all()->pluck('plateno', 'id'))
                    ->searchable(),


                Select::make('type')
                    ->options([
                        'sheetform' => 'Sheetform',
                        'rollform' => 'Rollform',

                    ]),




                Select::make('awstatus')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'printed' => 'Printed',
                        'platesent' => 'Plate Sent',
                        'sentforapproval' => 'Sent for Approval',
                        'noartworkfile' => 'No Artwork File',
                    ]),


                Select::make('priority')
                    ->options([
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',

                    ]),



                Toggle::make('prepressstage')->label('Prepress Done'),
                // TextInput::make('artworks_media_id'),
                // $table->id();
                // $table->string('description');
                // $table->bigInteger('artworks_order_id');
                // $table->integer('requiredqty');
                // $table->integer('jobrun');
                // $table->integer('labelrepeat');
                // $table->integer('printedqty');
                // $table->bigInteger('artworks_media_id');
                // $table->timestamps();
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->label('Description'),
                TextColumn::make('requiredqty')->default('0')
                    ->searchable(),
                // TextColumn::make('jobrun'),
                // TextColumn::make('labelrepeat'),
                TextColumn::make('printedqty'),
                TextColumn::make('Balance')
                    ->getStateUsing(function (Artwork $record) {
                        // return whatever you need to show
                        return $record->printedqty - $record->requiredqty;
                    }),


                TextColumn::make('order.orderno'),



                BadgeColumn::make('awstatus')
                    ->colors([
                        'warning' => 'Pending',
                        'warning' => 'sentforapproval',
                        'success' => 'Approved',
                        'success' => 'Printed',
                        'success' => 'Plate Sent',
                        'warning' => 'noartworkfile',
                    ]),
                BooleanColumn::make('prepressstage')->label('Prepress Done')->sortable(),
                TextColumn::make('remark')->searchable(),
                TextColumn::make('updated_at')->sortable(),
                TextColumn::make('created_at')->sortable(),


            ])->defaultSort('created_at', 'desc')

            ->filters([
                SelectFilter::make('artworks_order_id')->relationship('order', 'orderno'),
                SelectFilter::make('awstatus')
                    ->options([
                        'pending' => 'Pending',
                        'sentforapproval' => 'Sent for Approval',
                        'Approved' => 'Approved',
                        'Printed' => 'Printed',
                        'Plate Sent' => 'Plate Sent',
                        'noartworkfile' => 'No Artwork File',
                    ]),

                TernaryFilter::make('prepressstage'),
                NumberFilter::make('requiredqty'),
                SelectFilter::make('priority')
                    ->options([
                        'high' => 'high',
                    ]),





            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                FilamentExportBulkAction::make('export')



            ]);
    }

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArtworks::route('/'),
            'create' => Pages\CreateArtwork::route('/create'),
            'edit' => Pages\EditArtwork::route('/{record}/edit'),
        ];
    }
}
