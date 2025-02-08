<?php

namespace App\EntityManager;

use App\Model\DataModel;
use App\VO\UID;
use App\Event\EventManager;

interface IEntityManager
{
    public function __construct(EventManager $eventManager);

    public function getById(UID $id): DataModel;
    public function create(DataModel $dataModel): DataModel;
    public function update(DataModel $dataModel): DataModel;
    public function delete(DataModel $dataModel): void;
}