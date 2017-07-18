<?php

namespace Irradiate\Database;

use Illuminate\Database\MySqlConnection as BaseMySqlConnection;
use Irradiate\Database\Query\Grammars\MySqlGrammar;

class MySqlConnection extends BaseMySqlConnection
{
	/**
	 * Get the default query grammar instance.
	 *
	 * @return \Irradiate\Database\Query\Grammars\MySqlGrammar
	 */
	protected function getDefaultQueryGrammar()
	{
		return $this->withTablePrefix(new MySqlGrammar());
	}
    
	/**
	 * Begin a fluent query against a database table and use our own builder.
	 *
	 * @param  string  $table
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function table($table)
	{
		$processor = $this->getPostProcessor();

		$query = new Query\Builder($this, $this->getQueryGrammar(), $processor);

		return $query->from($table);
	}
    
	/**
	 * Starts a new database transaction. Provides a retry mechanism for when
     * a transaction is not started due to the database connection being lost.
	 *
	 * @return void
	 */
    public function beginTransaction()
    {
        $this->reconnectIfMissingConnection();
        
        // Here we will attempt to begin a transaction. If an exception occurs we'll
        // determine if it was caused by a connection that has been lost. If that is
        // the cause, we'll try to re-establish connection and retry the transaction.
        try {
            parent::beginTransaction();
        } catch (\PDOException $e) {
            // Decrease the number of active transactions,
            // since clearly we couldn't start one
            $this->transactions--;
            
            $this->retryTransactionIfCausedByLostConnection($e);
        }
    }
    
	/**
	 * Handles an exception that occurred during transaction initialization.
	 *
	 * @param  \PDOException $e
	 * @return void
	 * @throws \PDOException
	 */
    protected function retryTransactionIfCausedByLostConnection(\PDOException $e)
    {
        $message = $e->getMessage();
        if (str_contains($message, [
			'server has gone away',
			'no connection to the server',
			'Lost connection',
		]))
        {
            $this->reconnect();
            
            parent::beginTransaction();
            return;
        }
        
        throw $e;
    }
}
