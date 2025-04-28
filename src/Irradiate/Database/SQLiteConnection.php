<?php

namespace Irradiate\Database;

use Illuminate\Database\SQLiteConnection as BaseSQLiteConnection;
use Irradiate\Database\Query\Grammars\SQLiteGrammar;

class SQLiteConnection extends BaseSQLiteConnection
{
    /**
     * Get the default query grammar instance.
     *
     * @return \Irradiate\Database\Query\Grammars\SQLiteGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return new SQLiteGrammar($this);
    }
    
    /**
     * Begin a fluent query against a database table and use our own builder.
     *
     * @param string $table
     * @param string|null $as
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table, $as = null)
    {
        $processor = $this->getPostProcessor();
        
        $query = new Query\Builder($this, $this->getQueryGrammar(), $processor);
        
        return $query->from($table, $as);
    }
}
