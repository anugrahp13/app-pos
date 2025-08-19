<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\Widgets\ProductOverview;
use App\Filament\Resources\ProductResource\Widgets\ProductStats;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput, Select, FileUpload, Textarea};
use Filament\Tables\Columns\{TextColumn, ImageColumn, BadgeColumn};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Products';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 10;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->label('Gambar')
                    ->image()
                    ->directory('products')
                    ->imageEditor()
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('barcode')
                    ->unique(ignoreRecord: true)
                    ->label('Kode Barcode'),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->label('Kategori'),

                TextInput::make('purchase_price')
                    ->label('Harga Beli')
                    ->type('text')
                    ->prefix('Rp')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(['.', ','], '', $state))
                    ->extraInputAttributes(['inputmode' => 'numeric'])
                    ->rules(['numeric'])
                    ->required(),

                TextInput::make('sell_price')
                    ->label('Harga Jual')
                    ->type('text')
                    ->prefix('Rp')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(['.', ','], '', $state))
                    ->extraInputAttributes(['inputmode' => 'numeric'])
                    ->rules(['numeric'])
                    ->required(),

                TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->required(),

                Select::make('unit')
                    ->label('Satuan')
                    ->options([
                        'pcs' => 'Pcs',
                        'pack' => 'Pack',
                        'box' => 'Box',
                        'liter' => 'Liter',
                        'renceng' => 'Renceng',
                    ])
                    ->required(),

                Select::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->circular()
                    ->height(50)
                    ->getStateUsing(function ($record) {
                        return $record->image ?: 'products/default-image.png';
                    }),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('sell_price')
                    ->label('Harga Jual')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('purchase_price')
                    ->label('Harga Beli')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('stock')->label('Stok'),
                TextColumn::make('unit')->label('Satuan'),
                BadgeColumn::make('status')
                    ->searchable()
                    ->colors([
                    'active' => 'success',
                    'inactive' => 'danger',
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary'; // opsional: bisa juga 'success', 'warning', dsb
    }
}
