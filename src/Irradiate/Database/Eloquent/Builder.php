<?php

namespace Irradiate\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use Irradiate\Database\Query\Builder as QueryBuilder;

/**
 * Extended Eloquent builder.
 *
 * @author    Bogdan Ghervan <bogdan.ghervan@gmail.com>
 * @package   Irradiate\Database\Query
 */
class Builder extends BaseBuilder
{
	/**
	 * Creates a new Eloquent query builder instance.
	 *
	 * @param  \Irradiate\Database\Query\Builder $query
	 * @return void
	 */
	public function __construct(QueryBuilder $query)
	{
		parent::__construct($query);
	}
    
	/**
	 * Chunk the results of the query, but take no more than $limit.
     * 
     * This method operates in a similar fashion to
     * {@link \Illuminate\Database\Eloquent\Builder::chunk}, but additionally
     * takes the $limit parameter to cap the total number of items
     * processed.
     * 
     * The processing is handled in $callback.
	 *
	 * @param  int $chunkSize
	 * @param  int $limit
	 * @param  callable $callback
	 * @return void
	 */
    public function chunkWithLimit($chunkSize, $limit, callable $callback)
    {
        $chunkSize = min($chunkSize, $limit);
        $perPage   = $chunkSize;
        $page      = 1;
        
        $results = $this->take($chunkSize)->get();
        
        while (count($results)) {
			// On each chunk result set, we will pass them to the callback and then let the
			// developer take care of everything within the callback, which allows us to
			// keep the memory low for spinning through large result sets for working.
			if (call_user_func($callback, $results) === false) {
				break;
			}
            
            $page++;
            $limit -= $chunkSize;
            
            $chunkSize = min($chunkSize, $limit);
            if (!$chunkSize) {
                break;
            }
            
            $results = $this->skip(($page - 1) * $perPage)->take($chunkSize)->get();
        }
    }
}
