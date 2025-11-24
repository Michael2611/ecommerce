<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Group::make()->schema([
                    Section::make('Product Information')->schema([
                        TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $operation, $state, Set $set){
                            if($operation !== 'create'){
                                return;
                            }
                            $set('slug', Str::slug($state));
                        }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory('products'),
                    ])->columns(2),

                    Section::make('Images')->schema([
                        FileUpload::make('images')
                            ->multiple()
                            ->directory('products')
                            ->maxFiles(5)
                            ->reorderable()
                        ])
                    ])->columnSpan(2),

                Section::make('Price')->schema([
                    TextInput::make('price')
                        ->numeric()
                        ->required()
                        ->prefix('INR'),


                Section::make('Associations')->schema([
                    Select::make('category_id')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->relationship('category','name'),

                    Select::make('brand_id')
                        ->searchable()
                        ->required()
                        ->preload()
                        ->relationship('brand','name'),
                ]),

                Section::make('Status')->schema([
                    Toggle::make('in_stock')
                        ->required()
                        ->default(true),

                    Toggle::make('is_active')
                        ->required()
                        ->default(true),

                    Toggle::make('is_featured')
                        ->required(),

                    Toggle::make('on_sale')
                        ->required()
                ])

            ])->columnSpan(1)

            ])->columns(3);
    }
}
