<?php

namespace App\Factory;

interface IDataModelFactory
{
    public function create(array $data): object;
}