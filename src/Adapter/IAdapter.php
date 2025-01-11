<?php

namespace App\Adapter;
use App\Query\Query;

interface IAdapter
{
    public function executeQuery(Query $query, array &$outResult): bool;
}