<?php

use Filament\Resources\Pages\CreateRecord;
<?php

namespace App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource;
use Filament\Pages\Actions;
class CreateMessage extends CreateRecord
{
    protected static string $resource = MessageResource::class;
}
