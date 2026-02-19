<?php

namespace Modules\Requests\Filament\Resources\Documents;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Requests\Filament\Resources\Documents\Pages\CreateDocument;
use Modules\Requests\Filament\Resources\Documents\Pages\EditDocument;
use Modules\Requests\Filament\Resources\Documents\Pages\ListDocuments;
use Modules\Requests\Filament\Resources\Documents\Schemas\DocumentForm;
use Modules\Requests\Filament\Resources\Documents\Tables\DocumentsTable;
use Modules\Requests\Models\Document;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Document';

    public static function form(Schema $schema): Schema
    {
        return DocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
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
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
