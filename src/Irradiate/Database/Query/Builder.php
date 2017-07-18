<?php

namespace Irradiate\Database\Query;

use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

/**
 * Extended query builder.
 *
 * @author    Bogdan Ghervan <bogdan.ghervan@gmail.com>
 * @package   Irradiate\Database\Query
 */
class Builder extends BaseBuilder
{
    /**
     * Insert a new record or a set of records into the database,
     * or updates existing record(s).
     *
     * @param  array $values
     * @param  array $updateables
     * @return bool
     */
    public function insertOrUpdate(array $values, array $updateables)
    {
        if (empty($values)) {
            return true;
        }
        
        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient for building these
        // inserts statements by verifying the elements are actually an array.
        if (!is_array(reset($values))) {
            $values = array($values);
        }

        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient for building these
        // inserts statements by verifying the elements are actually an array.
        else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }
        
        // We'll treat every insert like a batch insert so we can easily insert each
        // of the records into the database consistently. This will make it much
        // easier on the grammars to just handle one type of record insertion.
        $bindings = array();

        foreach ($values as $record) {
            foreach ($record as $value) {
                $bindings[] = $value;
            }
        }
        
        $sql = $this->grammar->compileInsertOrUpdate($this, $values, $updateables);
        
        // Once we have compiled the insert statement's SQL we can execute it on the
        // connection and return a result as a boolean success indicator as that
        // is the same type of result returned by the raw connection instance.
        $bindings = $this->cleanBindings($bindings);
        
        return $this->connection->insert($sql, $bindings);
    }
    
    /**
     * Creates a new buffered insert instance and returns it.
     * 
     * @param  int $bufferSize (optional, default is 2500)
     * @return \Irradiate\Database\Query\BufferedInsert
     */
    public function insertBuffered($bufferSize = 2500)
    {
        $bufferedInsert = new BufferedInsert($this);
        
        return $bufferedInsert->size($bufferSize);
    }
    
    /**
     * Adds a "where in" clause to the query (values can also
     * contain a null element which will be properly added to
     * the query).
     * 
     * A better and bolder approach would be to change whereIn
     * itself, but this will have to do for now.
     *
     * @param  string  $column
     * @param  mixed   $values
     * @param  string  $boolean
     * @param  bool    $not
     * @return $this
     */
    public function whereInWithNull($column, $values, $boolean = 'and', $not = false)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }
        if (!is_array($values)) {
            throw new InvalidArgumentException('Values argument should either be an array or an Arrayable');
        }
        
        $foundNullValue = array_search(null, $values, $strict = true);   
        if ($foundNullValue !== false) {
            $nonNullValues = array_except($values, $foundNullValue);
            
            $this->whereNested(function($query) use ($column, $nonNullValues, $not) {
                $query->whereIn($column, $nonNullValues, 'and', $not)
                      ->whereNull($column, $not ? 'and' : 'or', $not);
            }, $boolean);
            
        } else {
            $this->whereIn($column, $values, $boolean, $not);
        }
        
        return $this;
    }
}
