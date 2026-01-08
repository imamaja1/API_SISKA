<?php

namespace App\Filament\Resources\ApiDocs\Schemas;

use App\Models\Category;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ApiDocForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->options(function () {
                        return Category::all()->pluck('name', 'id')->toArray();
                    })
                    ->required(),
                TextInput::make('judul')
                    ->required(),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                MarkdownEditor::make('endpoint')
                    ->columnSpanFull()
                    ->required(),
                MarkdownEditor::make('response')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}
