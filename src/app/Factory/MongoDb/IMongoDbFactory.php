<?php
namespace App\Factory\MongoDb;

/**
 * MongoDb facotry to create instance of connection,Read and Write objects
 * of Mongo. 
 * @author matang
 *
 */
interface IMongoDbFactory {
	
	/**
	 * Get mongo db connection
	 */
	public function getConnection();
	
	/**
	 *  Get query object instance with filters and options
	 * 
	 * @param array $filters
	 * @param array $options
	 */
	public function getQueryObject(array $filters,array $options);
	
	/**
	 * Get write object instace with options
	 * 
	 * @param array $options
	 */
	public function getWriteObject(array $options);
	
}