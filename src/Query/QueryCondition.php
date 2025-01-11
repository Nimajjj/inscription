<?php

namespace App\Query;

enum QueryCondition 
{
    case IS_EQUAL;
    case IS_DIFFERENT;
    case IS_GREATER;
    case IS_LESS;
};