<?php

namespace App\Utils;
use App\Query\QueryAction;
use App\Query\QueryCondition;
use App\Query\QueryLogicalOperator;

final class EnumsUtils
{

    static function QueryActionToString(QueryAction $queryAction): string
    {
        return match ($queryAction) 
        {
            QueryAction::SELECT => 'SELECT',
            QueryAction::INSERT => 'INSERT',
            QueryAction::UPDATE => 'UPDATE',
            QueryAction::DELETE => 'DELETE',
        };
    }
    
    static function QueryConditionToString(QueryCondition $queryCondition): string
    {
        return match ($queryCondition) 
        {
            QueryCondition::IS_EQUAL => '=',
            QueryCondition::IS_DIFFERENT => '!=',
            QueryCondition::IS_GREATER => '>',
            QueryCondition::IS_LESS => '<',
        };
    }
    
    static function QueryLogicalOperatorToString(QueryLogicalOperator $queryLogicalOperator): string
    {
        return match ($queryLogicalOperator) 
        {
            QueryLogicalOperator::AND => 'AND',
            QueryLogicalOperator::OR => 'OR',
        };
    }
}