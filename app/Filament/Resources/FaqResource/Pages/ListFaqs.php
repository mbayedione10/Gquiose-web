<?php

use Filament\Resources\Pages\ListRecords;
<?php

namespace App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource;
use Filament\Pages\Actions;
class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label("Nouvelle Faq"),
        ];
    }
}
