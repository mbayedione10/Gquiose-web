<?php

namespace App\Filament\Forms\Components;
use Filament\Forms\Components\Field;
class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';
    public static function make(string $name = 'location'): static
    {
        return parent::make($name);
    }
    public function latitudeField(string $field): static
    {
        $this->latitudeField = $field;
        return $this;
    }
    public function longitudeField(string $field): static
    {
        $this->longitudeField = $field;
        return $this;
    }
    public function getLatitudeField(): string
    {
        return $this->latitudeField ?? 'latitude';
    }
    public function getLongitudeField(): string
    {
        return $this->longitudeField ?? 'longitude';
    }
}
