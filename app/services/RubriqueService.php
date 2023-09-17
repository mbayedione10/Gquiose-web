<?php


namespace App\services;


use App\Models\Rubrique;

class RubriqueService
{
    public function all()
    {
        return Rubrique::has('articles')
            ->select('id', 'name')
            ->get();
    }

}
