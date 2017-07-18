<?php

namespace Irradiate\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Irradiate\Database\Query\Builder as QueryBuilder;
use Irradiate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Base class for Eloquent models.
 * Models should extend this class to be able to
 * benefit from Irradiate wizardry.
 *
 * @author    Bogdan Ghervan <bogdan.ghervan@gmail.com>
 * @package   App
 */
abstract class Model extends BaseModel
{
	/**
	 * Create a new Eloquent query builder for the model.
	 *
	 * @param  \Irradiate\Database\Query\Builder $query
	 * @return \Irradiate\Database\Eloquent\Builder|static
	 */
	public function newEloquentBuilder($query)
	{
		return new EloquentBuilder($query);
	}
    
	/**
	 * Get a new query builder instance for the connection.
	 *
	 * @return \Irradiate\Database\Query\Builder
	 */
	protected function newBaseQueryBuilder()
	{
		$conn = $this->getConnection();

		$grammar = $conn->getQueryGrammar();

		return new QueryBuilder($conn, $grammar, $conn->getPostProcessor());
	}
}
