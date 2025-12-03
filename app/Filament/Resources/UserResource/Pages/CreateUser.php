<?php

use Filament\Resources\Pages\CreateRecord;
<?php

namespace App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource;
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
