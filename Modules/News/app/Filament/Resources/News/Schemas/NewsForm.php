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
                    ->label('Заголовок RU')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Описание RU')
                    ->required()
                    ->rows(5),
                TextInput::make('translations.kk.title')
                    ->label('Заголовок KK')
                    ->required()
                    ->maxLength(255),
                Textarea::make('translations.kk.description')
                    ->label('Описание KK')
                    ->required()
                    ->rows(5),
                TextInput::make('translations.en.title')
                    ->label('Заголовок EN')
                    ->required()
                    ->maxLength(255),
                Textarea::make('translations.en.description')
                    ->label('Описание EN')
                    ->required()
                    ->rows(5),
                TextInput::make('photo')
                    ->label('Фото URL')
                    ->url()
                    ->maxLength(255),
            ]);
    }
}
