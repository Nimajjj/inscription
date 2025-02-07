<?php

namespace App\Query;
use App\Query\QueryAction;
use App\Utils\EnumsUtils;
use App\VO\Uid;

final class Query
{
    public QueryAction $action;
    public array $condition;
    public string $table;
    public array $columns;
    public array $values;

    public function __construct()
    {
    }

    public function isValid(): bool
    {
        return $this->isActionValid()
            && $this->isColumnsValid()
            && $this->isConditionValid()
            && $this->isTableValid()
            && $this->isValueValid();
    }

    public function toRawSql(): string
    {
        $action = EnumsUtils::QueryActionToString($this->action);

        return match ($this->action) 
        {
            QueryAction::SELECT => $this->buildSelectQuery(),
            QueryAction::INSERT => $this->buildInsertQuery(),
            QueryAction::UPDATE => $this->buildUpdateQuery(),
            QueryAction::DELETE => $this->buildDeleteQuery(),
            default => throw new \InvalidArgumentException("Invalid Query Action"),
        };
    }
    
    private function buildSelectQuery(): string
    {
        $columns = empty($this->columns) ? '*' : implode(', ', $this->columns);
        $query = "SELECT $columns FROM {$this->table}";
    
        if (!empty($this->condition)) 
        {
            $query .= " WHERE " . $this->buildConditions();
        }
    
        return $query . ';';
    }
    
    private function buildInsertQuery(): string
    {
        $columns = implode(', ', $this->columns);
        $values = implode(', ', array_map(
            function($value): string
            {
                if ($value instanceof \DateTimeImmutable)
                {
                    $v = $value->format('Y-m-d H:i:s');
                    return "'$v'";
                }

                return "'$value'";
            },
             $this->values));
        return "INSERT INTO {$this->table} ($columns) VALUES ($values);";
    }
    
    private function buildUpdateQuery(): string
    {
        $setClauses = $this->buildSetClauses();
        $query = "UPDATE {$this->table} SET $setClauses";
    
        if (!empty($this->condition)) 
        {
            $query .= " WHERE " . $this->buildConditions();
        }
    
        return $query . ';';
    }
    
    private function buildDeleteQuery(): string
    {
        $query = "DELETE FROM {$this->table}";
    
        if (!empty($this->condition)) 
        {
            $query .= " WHERE " . $this->buildConditions();
        }
    
        return $query . ';';
    }
    
    private function buildSetClauses(): string
    {
        $setClauses = [];
        foreach ($this->columns as $index => $column) 
        {
            $value = $this->values[$index] ?? '';
            if ($value instanceof \DateTimeImmutable)
            {
                $v = $value->format('Y-m-d H:i:s');
                $setClauses[] = "$column = '$v'";
                continue;
            }
            $setClauses[] = "$column = '$value'";
        }
        return implode(', ', $setClauses);
    }
    
    private function buildConditions(): string
    {
        return implode(
            ' ', 
            array_map(function (array $condition): string 
            {
                $subject = $condition['subject'];
                $operator = isset($condition['operator'])
                ? EnumsUtils::QueryLogicalOperatorToString($condition['operator'])
                : '';
                $conditionType = EnumsUtils::QueryConditionToString($condition['condition']);
                $value = $condition['value'];

                return "$subject $conditionType '$value'" . ($operator ? " $operator" : '');
            }, 
            $this->condition)
        );
    }

    private function isActionValid(): bool
    {
        return isset($this->action) 
            && $this->action instanceof QueryAction;
    }

    private function isConditionValid(): bool
    {
        // If conditions are empty, they're valid (not required for all queries)
        if (empty($this->condition)) 
        {
            return true;
        }

        // Check if each condition array has the required keys and valid values
        foreach ($this->condition as $condition) 
        {
            if (!isset($condition['subject'], $condition['condition'], $condition['value'])) 
            {
                return false;
            }

            if (!$condition['condition'] instanceof QueryCondition) 
            {
                return false;
            }

            if (
                isset($condition['operator']) 
                && !$condition['operator'] instanceof QueryLogicalOperator
            ) 
            {
                return false;
            }
        }

        return true;
    }

    private function isTableValid(): bool
    {
        return !empty($this->table) 
            && is_string($this->table);
    }

    private function isColumnsValid(): bool
    {
        // For SELECT, INSERT and UPDATE actions, columns are required
        if (in_array(
            $this->action, 
            [QueryAction::INSERT, QueryAction::UPDATE, QueryAction::SELECT],
            true)
        ) 
        {
            return !empty($this->columns) 
                && is_array($this->columns);
        }

        // For DELETE, columns are not applicable
        return true;
    }

    private function isValueValid(): bool
    {
        // For INSERT and UPDATE actions, values must match columns
        if (in_array(
            $this->action,
             [QueryAction::INSERT,
              QueryAction::UPDATE], true)
        ) 
        {
            return !empty($this->values) 
                && count($this->columns) === count($this->values);
        }
        return true;
    }
}