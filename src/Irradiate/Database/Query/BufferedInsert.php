<?php

namespace Irradiate\Database\Query;

/**
 * Devises a mechanism for inserting records to the database in a bulk fashion,
 * by buffering data and flushing it to the database when a designated limit
 * (the buffer size) is reached.
 * 
 * Usage:
 * <code>
 * $bufferedInsert = \DB::table('articles')->insertBuffered(5);
 * $bufferedInsert->add(['John']);
 * $bufferedInsert->add(['Michael']);
 * $bufferedInsert->add(['Jessica']);
 * $bufferedInsert->add(['Albie']);
 * $bufferedInsert->add(['Paul']); // Buffered entries are now persisted
 * $bufferedInsert->add(['Sherlock']);
 * 
 * // Manually persist any remaining entries
 * $bufferedInsert->flush(); // "Sherlock" is persisted as well
 * 
 * // Set a new buffer size
 * $bufferedInsert->size(10);
 * 
 * // Clear buffered entries (without persisting them)
 * $bufferedInsert->clear();
 * </code>
 * 
 * @author    Bogdan Ghervan <bogdan.ghervan@gmail.com>
 * @package   Irradiate\Database\Query
 */
class BufferedInsert
{
    /**
	 * The database query builder instance.
	 *
	 * @var \Irradiate\Database\Query\Builder
	 */
	protected $builder;
    
    /**
     * Buffer size.
     * 
     * @var int
     */
    protected $bufferSize = 2500;
    
    /**
     * Entries buffer.
     * 
     * @var array
     */
    protected $entriesBuffer = [];
    
    /**
	 * Creates a new buffered insert instance.
     * 
     * @param  \Irradiate\Database\Query\Builder $builder
     * @return void
     */
    public function __construct(\Irradiate\Database\Query\Builder $builder)
    {
        $this->builder = $builder;
    }
    
    /**
     * Sets the buffer size.
     * 
     * @param  int $bufferSize
     * @return BufferedInsert
     * @throws \InvalidArgumentException
     */
    public function size($bufferSize)
    {
        if ($bufferSize < 1) {
            throw new \InvalidArgumentException('Size for buffered insert is too small');
        }
        
        $this->bufferSize = $bufferSize;
        
        return $this;
    }
    
    /**
     * Buffers record to be inserted to the database.
     * When the buffer size is reached, records are automatically flushed to the database,
     * and the buffer emptied.
     * 
     * @param  array $values
     * @return \Irradiate\Database\Query\BufferedInsert
     */
    public function add(array $values)
    {
        $this->entriesBuffer[] = $values;
        
        if ($this->bufferIsFull()) {
            $this->flush();
        }
        
        return $this;
    }
    
    /**
     * Checks whether configured buffer size has been reached.
     * 
     * @return boolean
     */
    protected function bufferIsFull()
    {
        return count($this->entriesBuffer) >= $this->bufferSize;
    }
    
    /**
     * Flushes buffered items to database, while emptying the buffer.
     * 
     * @return \Irradiate\Database\Query\BufferedInsert
     */
    public function flush()
    {
        $this->builder->insert($this->entriesBuffer);
        $this->clear();
        
        return $this;
    }
    
    /**
     * Clears insert buffer of buffered items.
     * 
     * @return BufferedInsert
     */
    public function clear()
    {
        $this->entriesBuffer = [];
        
        return $this;
    }
}
