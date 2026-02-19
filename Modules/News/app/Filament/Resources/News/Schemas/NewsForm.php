<?php

namespace Modules\News\Filament\Resources\News\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->rows(5),
                TextInput::make('photo')
                    ->url()
                    ->maxLength(255),
            ]);
    }
}
