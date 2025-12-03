<?php

use Filament\Resources\Pages\EditRecord;
<?php

namespace App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource;
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
}
