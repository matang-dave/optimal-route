<?php
namespace App\Repository\Route;
use App\Model\Route;
use Illuminate\Contracts\Queue\EntityNotFoundException;
use App\Constants\Route\RouteStatus;
use App\Model\Coordinate;
use App\Factory\MongoDb\IMongoDbFactory;
use Illuminate\Support\Facades\App;

/**
 * MongoDb implimentation for @link IRouterRepository
 * 
 * @author matang
 *
 */
class RouterRepository implements IRouterRepository 
{
	/**
	 * Factory to create mongodb instances
	 * 
	 * @var IMongoDbFactory
	 */
	private $mongoDbFactory;
	
	/**
	 * collection name for Route entities
	 * 
	 * @var string
	 */
	private $collection;
	
	/**
	 * construcuter for @link RouterRepository depends on @link IMongoDbFactory and collection
	 * 
	 * @param IMongoDbFactory $mongoDbFactory
	 * @param string $collection
	 */
	public function __construct(IMongoDbFactory $mongoDbFactory,string $collection){
		$this->mongoDbFactory = $mongoDbFactory;
		$this->collection = $collection;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Repository\Route\IRouterRepository::saveRoute()
	 */
	public function saveRoute(Route $route){
		$routeArray = $this->convertToArray($route);
		$bulk = $this->mongoDbFactory->getWriteObject();
		$bulk->insert($routeArray);
		$this->mongoDbFactory->getConnection()->executeBulkWrite($this->collection,$bulk);
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Repository\Route\IRouterRepository::findRoute()
	 */
	public function findRoute(string $uniqueId):Route {
		
		$query = $this->mongoDbFactory->getQueryObject(['uniqueId'=>$uniqueId]);
		$rows = $this->mongoDbFactory->getConnection()->executeQuery($this->collection,$query)->toArray();
 		if(empty($rows)) {
 			throw new EntityNotFoundException(Route::class, $uniqueId);
 		}
		foreach ($rows as $routeArray) {
			return $this->convertToObject($routeArray);
		}
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Repository\Route\IRouterRepository::deleteOldRoute()
	 */
	public function deleteOldRoute(int $beforeSeconds) {
	
		$filter = [
				'lastModified'=>array('$lt'=>time()-$beforeSeconds),
				'status'=>array('$in'=>[RouteStatus::API_CALCULATED,RouteStatus::API_ERROR]),
		];
		$query = $this->mongoDbFactory->getQueryObject($filter);
		$bulk = $this->mongoDbFactory->getWriteObject();
		$rows = $this->mongoDbFactory->getConnection()->executeQuery($this->collection,$query)->toArray();
		
		foreach ($rows as $routedb) {
			$bulk->delete(['uniqueId'=>$routedb->uniqueId]);
		}
		
		if(!empty($rows)) {
			$this->mongoDbFactory->getConnection()->executeBulkWrite($this->collection,$bulk);
		}
		
	}
	
	/**
	 * 
	 * @throws \EntityNotFoundException
	 * @return Route
	 */
	public function findRouteForOptimalTavlePlan():Route {
		
		$query = $this->mongoDbFactory->getQueryObject(['status'=>RouteStatus::API_NEW],['limit'=>1]);
		$rows = $this->mongoDbFactory->getConnection()->executeQuery($this->collection,$query)->toArray();
		if(empty($rows)) {
			throw new EntityNotFoundException(Route::class,null);
		}
		foreach ($rows as $routeArray) {
			return $this->convertToObject($routeArray);
		}
	}
	
	

	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Repository\Route\IRouterRepository::updateRouteTravelPlan()
	 */
	public function update(Route $route) {
		
		$routeArray = $this->convertToArray($route);
		$bulk = $this->mongoDbFactory->getWriteObject();
		$bulk->update(
				['uniqueId'=>$route->getUniqueId()],
				[
					'$set'=> [
							'travelPlan'=>$route->getTravelPlan(),
							'travelDistance'=>$route->getTotalTravelDistance(),
							'travelTime'=>$route->getTotalTravelTime(),
							'errMsg'=>$route->getErrorMesage(),
							'status'=>$route->getStatus(),
							'lastModified'=>time()
							
					]
					
				]
		);
		$this->mongoDbFactory->getConnection()->executeBulkWrite($this->collection,$bulk);
	}
	
	/**
	 * prepare array to store @link Route object
	 * 
	 * @param Route $route
	 * @return array
	 */
	private function convertToArray(Route $route):array {
		
		$routeArray = [];
		$routeArray['uniqueId'] = $route->getUniqueId();
		
		$routeArray['origin'] = [];
		$routeArray['origin']['latitude'] = $route->getOrigin()->getLatitude();
		$routeArray['origin']['longitude'] = $route->getOrigin()->getLongitude();
		
		$routeArray['destinations'] = [];
		
		$destinationCoordinations = $route->getDestinations();
		foreach($destinationCoordinations as $destinationCoordination) {
			$coordinate['latitude'] = $destinationCoordination->getLatitude();
			$coordinate['longitude'] = $destinationCoordination->getLongitude();
			
			$routeArray['destinations'][] = $coordinate;
		}
		
		$routeArray['status'] = $route->getStatus();
		$routeArray['travelDistance'] = $route->getTotalTravelDistance();
		$routeArray['travelTime'] = $route->getTotalTravelTime();
		$routeArray['travelPlan'] = $route->getTravelPlan();
		$routeArray['errMsg'] = $route->getErrorMesage();
		
		return $routeArray;
	}
	
	/**
	 * convert db result to @link Route object 
	 * @param \stdClass $routeDb
	 * @return Route
	 */
	private function convertToObject(\stdClass $routeDb):Route {
		
		$route = App::make(Route::class);
		$route->setUniqueId($routeDb->uniqueId);
		$route->setStatus($routeDb->status);
		$route->setTotalTravelDistance($routeDb->travelDistance);
		$route->setTotalTravelTime($routeDb->travelTime);
		$route->setTravelPlan($routeDb->travelPlan);
		$route->setOrigin(new Coordinate($routeDb->origin->latitude, $routeDb->origin->longitude));
		$route->setErrorMessage($routeDb->errMsg);
		
		$destinationCoordinates = [];
		foreach ($routeDb->destinations as $destination) {
			$destinationCoordinates[] = new Coordinate($destination->latitude, $destination->longitude);
		}
		
		$route->setDestinations($destinationCoordinates);
		
		return $route;
		
	}
}