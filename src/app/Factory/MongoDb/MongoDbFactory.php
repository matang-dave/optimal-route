<?php
namespace App\Factory\MongoDb;
use \MongoDB\Driver\Manager;
use \MongoDB\Driver\Query;
use \MongoDB\Driver\BulkWrite;

/**
 * IMongoDbFactory implimentation
 * 
 * @author matang
 *
 */
class MongoDbFactory implements  IMongoDbFactory 
{
	/**
	 * mongodb connection manager
	 * 
	 * @var Manager
	 */
	private $connection;
	
	/**
	 * singleton implimentation for connection object
	 * 
	 * {@inheritDoc}
	 * @see \App\Factory\MongoDb\IMongoDbFactory::getConnection()
	 */
	public function getConnection() {
		if($this->connection) {
			return $this->connection;
		}
		else {
			$app = app('config');
			$uri  = $app['database']['connections']['mongodb']['uri'];
			$uriOptions = array();
			$driverOptions = array();
			$this->connection = new Manager($uri,$uriOptions,$driverOptions);
			return $this->connection;
		}
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Factory\MongoDb\IMongoDbFactory::getQueryObject()
	 */
	public function getQueryObject(array $filters=[],array $options=[]) {
		return new Query($filters, $options);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Factory\MongoDb\IMongoDbFactory::getWriteObject()
	 */
	public function getWriteObject(array $options=[]) {
		return new BulkWrite($options);
	}
	
}