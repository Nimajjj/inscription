<?php

namespace App\Model;
use App\VO\UID;

interface DataModel
{
    public function getId(): UID;
    public function setId(UID $id) : DataModel;
}