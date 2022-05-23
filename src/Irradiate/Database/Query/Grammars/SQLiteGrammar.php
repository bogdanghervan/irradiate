<?php

namespace Irradiate\Database\Query\Grammars;

use Illuminate\Database\Query\Grammars\SQLiteGrammar as BaseSQLiteGrammar;
use Irradiate\Database\Query\Builder;

/**
 * Enhanced SQLite grammar.
 *
 * @author    Bogdan Ghervan <bogdan.ghervan@gmail.com>
 * @package   Irradiate\Database\Query\Grammars
 */
class SQLiteGrammar extends BaseSQLiteGrammar
{
    /**
     * Compiles an INSERT INTO... ON CONFLICT DO UPDATE... statement into SQL.
     * Loosely based on {@link \Illuminate\Database\Query\Grammars\Grammar::compileInsert}.
     * 
     * The upsert syntax was added to SQLite with version 3.24.0 (2018-06-04),
     * so it will not work for versions of PDO including an older libsqlite.
     *
     * @param  \Irradiate\Database\Query\Builder $query
     * @param  array $values
     * @param  array $updateables
     * @param  array $conflictColumns
     * @return string
     */
    public function compileInsertOrUpdate(Builder $query, array $values, array $updateables, array $conflictColumns = [])
    {
        // Essentially we will force every insert to be treated as a batch insert which
        // simply makes creating the SQL easier for us since we can utilize the same
        // basic routine regardless of an amount of records given to us to insert.
        $table = $this->wrapTable($query->from);
        
        if (!is_array(reset($values))) {
            $values = array($values);
        }
        
        $columns = $this->columnize(array_keys(reset($values)));
        
        // We need to build a list of parameter place-holders of values that are bound
        // to the query. Each insert should have the exact same amount of parameter
        // bindings so we can just go off the first list of values in this array.
        $parameters = $this->parameterize(reset($values));
        
        $value = array_fill(0, count($values), "($parameters)");
        
        $parameters = implode(', ', $value);
        
        $updateables = $this->updateables($updateables);
        $onConflict = $this->compileOnConflict($conflictColumns);
        
        return "insert into $table ($columns) values $parameters $onConflict do update set $updateables";
    }
    
    /**
     * Builds the list of columns to be updated in a INSERT INTO... ON CONFLICT DO UPDATE...
     * statement.
     *
     * @param  array $updateables
     * @return string
     */
    protected function updateables(array $updateables)
    {
        $updateablesList = [];
        foreach ($updateables as $updateable) {
            $updateablesList[] = sprintf('%1$s = excluded.%1$s', $updateable);
        }
        
        return implode(', ', $updateablesList);
    }
    
    /**
     * Compiles the ON CONFLICT clause of an INSERT statement.
     *
     * @param array $conflictColumns
     * @return string
     */
    protected function compileOnConflict(array $conflictColumns)
    {
        return 'on conflict' . (
            count($conflictColumns) ?
                ('(' . $this->columnize($conflictColumns) . ')') :
                ''
            );
    }
}
