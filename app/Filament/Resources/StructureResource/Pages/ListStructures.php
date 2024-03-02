<?php

namespace App\Filament\Resources\StructureResource\Pages;

use App\Models\Structure;
use App\Models\Ville;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Traits\HasDescendingOrder;
use App\Filament\Resources\StructureResource;
use Livewire\Features\Placeholder;
use Rap2hpoutre\FastExcel\Facades\FastExcel;

class ListStructures extends ListRecords
{
    use HasDescendingOrder;

    protected static ?string $title = "Liste des structures";

    protected static string $resource = StructureResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()
                ->label("Nouvelle structure")
                ->icon('heroicon-o-plus-circle'),

            Action::make('import')
                ->label("Importer un fichier excel")
                ->color("success")
                ->icon('heroicon-o-arrow-circle-down')
                ->requiresConfirmation()
                ->form([

                    FileUpload::make('file')
                        ->label("Fichier excel")
                        ->required()
                        ->maxSize(1024)
                ])
                ->action(function (array $data){
                    $path = public_path('storage/' . $data['file']);
                    $this->import($path);
                })
        ];
    }

    public function import($path)
    {

        $COLUMN_STRUCTURE = "Structure";
        $COLUMN_TELEPHONE = "Téléphone";
        $COLUMN_VILLE = "Ville";
        $COLUMN_ADRESSE = "Adresse";
        $COLUMN_SERVICE = "Services";
        $COLUMN_OFFRE = "Offres";
        $COLUMN_LATITUDE = "Latitude";
        $COLUMN_LONGITUDE = "Longitude";


        $compteur = 0;
        $fileIsCorrect = false;

        FastExcel::import($path, function ($data) use (
            $COLUMN_STRUCTURE,
            $COLUMN_TELEPHONE, $COLUMN_VILLE, $COLUMN_ADRESSE, $COLUMN_SERVICE, $COLUMN_OFFRE,
            $COLUMN_LATITUDE, $COLUMN_LONGITUDE,
            $compteur, $fileIsCorrect
        ) {
            $compteur++;


            if ($compteur == 1) {

                if (!$this->notNull($COLUMN_STRUCTURE, $data) || !$this->notNull($COLUMN_TELEPHONE, $data)
                    || !$this->notNull($COLUMN_VILLE, $data) || !$this->notNull($COLUMN_ADRESSE, $data) || !$this->notNull($COLUMN_SERVICE, $data)
                    || !$this->notNull($COLUMN_OFFRE, $data) || !$this->notNull($COLUMN_LATITUDE, $data) || !$this->notNull($COLUMN_LONGITUDE, $data)
                ) {
                    Notification::make('upload_error')
                        ->title('Attention')
                        ->danger()
                        ->body('Le libélé des colonnes est incorrect, télécharger le fichier d\'exemple en cliquant sur le bouton **télécharger le fichier**')
                        ->persistent()
                        ->send();
                } else
                    $fileIsCorrect = true;


            }

            if ($fileIsCorrect) {

                $structure = Structure::where('phone', $data[$COLUMN_TELEPHONE])->first();

                if ($structure == null)
                {
                    $ville = Ville::where("name", $data[$COLUMN_VILLE])->first();

                    if ($ville == null)
                    {
                        $ville = new Ville();
                        $ville->name = $data[$COLUMN_VILLE];
                        $ville->status = true;
                        $ville->save();
                    }

                    $structure = new Structure();
                    $structure->name = $data[$COLUMN_STRUCTURE];
                    $structure->description = $data[$COLUMN_OFFRE];
                    $structure->phone = $data[$COLUMN_TELEPHONE];
                    $structure->offre = $data[$COLUMN_SERVICE];
                    $structure->ville_id = $ville->id;
                    $structure->latitude = $data[$COLUMN_LATITUDE];
                    $structure->longitude = $data[$COLUMN_LONGITUDE];
                    $structure->adresse = $data[$COLUMN_ADRESSE];
                    $structure->status = true;
                    $structure->save();
                }
                else
                {
                    error_log("Error: " .$data[$COLUMN_STRUCTURE]);
                }

            }
        });
    }

    private function notNull($column, $data)
    {
        return isset($data[$column]);
    }
}
