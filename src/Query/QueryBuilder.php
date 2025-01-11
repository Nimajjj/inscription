<?php

namespace App\Query;
use App\Query\Query;
use App\Query\QueryAction;
use App\Query\QueryCondition;
use App\Query\QueryLogicalOperator;


final class QueryBuilder
{
    private Query $query;


    public function __construct()
    {
        $this->query = new Query();
    }

    public function buildAction(QueryAction $action): QueryBuilder
    {
        $this->query->action = $action;
        return $this;
    }
    
    public function buildTable(string $table): QueryBuilder
    {
        $this->query->table = $table;
        return $this;
    }
    
    public function buildColumns(array $columns): QueryBuilder
    {
        $this->query->columns = $columns;
        return $this;
    }

    public function buildAllColumns(): QueryBuilder
    {
        $this->query->columns = ["*"];
        return $this;
    }
    
    public function buildValues(array $values): QueryBuilder
    {
        $this->query->values = $values;
        return $this;
    }

    public function buildCondition(
        string $subject, 
        QueryCondition $condition, 
        string $value, 
        ?QueryLogicalOperator $operator = null
    ): QueryBuilder
    {
        $this->query->condition[] = [
            "subject" => $subject, 
            "condition" => $condition, 
            "value" => $value, 
            "operator" => $operator
        ];
        return $this;
    }

    public function resetConditions(): QueryBuilder
    {
        $this->query->condition = [];
        return $this;
    }


    public function build(): Query
    {
        assert($this->query->isValid(), "Query is not valid.");
        return $this->query;
    }
}