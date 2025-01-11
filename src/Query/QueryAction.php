<?php

namespace App\Query;

enum QueryAction 
{
    case SELECT;
    case INSERT;
    case UPDATE;
    case DELETE;
};
