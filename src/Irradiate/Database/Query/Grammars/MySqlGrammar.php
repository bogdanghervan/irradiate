<?php

namespace Irradiate\Database\Query\Grammars;

use Irradiate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar as BaseMySqlGrammar;

/**
 * Enhanced MySQL grammar.
 * 
 * @author    Bogdan Ghervan <bogdan.ghervan@gmail.com>
 * @package   Irradiate\Database\Query\Grammars
 */
class MySqlGrammar extends BaseMySqlGrammar
{
	/**
	 * Compiles an INSERT INTO... ON DUPLICATE KEY UPDATE... statement into SQL.
     * Loosely based on {@link \Illuminate\Database\Query\Grammars\Grammar::compileInsert}.
	 *
	 * @param  \Irradiate\Database\Query\Builder $query
	 * @param  array $values
	 * @param  array $updateables
	 * @return string
	 */
    public function compileInsertOrUpdate(Builder $query, array $values, array $updateables)
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
        
        return "insert into $table ($columns) values $parameters on duplicate key update $updateables";
    }
    
    /**
     * Builds the list of columns to be updated in a INSERT INTO... ON DUPLICATE KEY UPDATE...
     * statement.
     * 
     * @param  array $updateables
     * @return string
     */
    protected function updateables(array $updateables)
    {
        $updateablesList = [];
        foreach ($updateables as $updateable) {
            $updateablesList[] = sprintf('%1$s = VALUES(%1$s)', $updateable);
        }
        
        return implode(', ', $updateablesList);
    }
}
