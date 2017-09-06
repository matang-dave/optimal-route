<?php
namespace App\Model;

use App\Constants\Route\RouteStatus;

abstract class Route {

	/**
	 * unique id for route
	 * @var string
	 */
	protected $uniqueId;
	
	/**
	 *  origin coordinates
	 * @var Coordinate
	 */
	protected $origin;
	
	/**
	 * destination coordinates
	 * 
	 * @var array
	 */
	protected $destinations = [];
	
	/**
	 * travel plan
	 * @var array
	 */
	protected $travelPlan = [];
	
	/**
	 * Distance calculator for travel plan
	 * @var DistanceCalculator
	 */
	protected $distanceCalculator;
	
	/**
	 * total traveling time
	 * @var int
	 */
	protected $totalTravelTime;
	
	/**
	 * total traveling distance
	 * @var int
	 */
	protected $totalTravelDistance;
	
	/**
	 * status for traveling plan calculation
	 * @var string
	 */
	protected $status;
	
	/**
	 * error message
	 * @var string
	 */
	protected $errorMessage;
	
	/**
	 * maximum destinations allowed for the travel plan
	 * 
	 * @var integer
	 */
	CONST MAX_DETINATION_COUNT_ALLOWED = 5;
	
	/**
	 * Time limit for storing route travel plan after computation
	 * 
	 * @var integer
	 */
	CONST TIME_TO_LIVE_SECONDS =  86400;
	
	/**
	 * Route depends on @link DistanceCalculator for optimal travel plan 
	 * calculation
	 * 
	 * @param DistanceCalculator $distanceCalculator
	 */
	public function __construct(DistanceCalculator $distanceCalculator) {
		$this->distanceCalculator = $distanceCalculator;
		$this->totalTravelDistance = -1;
		$this->totalTravelTime = -1;
	}
	
	/**
	 * set unique id
	 * @param string $uniqueId
	 */
	public function setUniqueId(string $uniqueId) {
		$this->uniqueId = $uniqueId;
	}
	
	/**
	 * get unique id
	 * @return string
	 */
	public function getUniqueId():string {
		return $this->uniqueId;
	}
	
	/**
	 * set origin coordinates
	 * @param Coordinate $origin
	 */
	public function setOrigin(Coordinate $origin) {
		$this->origin = $origin;
	}
	
	/**
	 * get origin coordinates
	 * @return Coordinate
	 */
	public function getOrigin():Coordinate {
		return $this->origin;
	}
	
	/**
	 * set desinations coordinates
	 * @param array $destinations
	 */
	public function setDestinations(array $destinations) {
		$this->destinations = $destinations;
	}
	
	/**
	 * get destination coordinates
	 * @return array
	 */
	public function getDestinations():array {
		return $this->destinations;
	}
	
	/**
	 * Add new route to collection for calculating optimal route
	 * 
	 * @param Coordinate $origin
	 * @param array $destinations
	 */
	public function addNewRoute(Coordinate $origin , array $destinations) {
		$this->origin = $origin;
		$this->destinations = $destinations;
		$this->status = RouteStatus::API_NEW;
		$this->uniqueId = $this->generateUniqueId();
	}
	
	/**
	 * Generate unique id for the Route
	 * @return string
	 */
	private function generateUniqueId() {
		return uniqid(null,true).'-'.uniqid(null,true);
	}
	
	/**
	 * Set optimal travel  plan
	 * @param array $travelPlan
	 */
	public function setTravelPlan(array $travelPlan) {
		$this->travelPlan = $travelPlan;
	}
	
	/**
	 * get travel plan for the route
	 * @return array
	 */
	public function getTravelPlan():array {
		return $this->travelPlan;
	}
	
	/**
	 * set total traveling plan for the route
	 * @param int $totalTravelTime
	 */
	public function setTotalTravelTime(int $totalTravelTime) {
		$this->totalTravelTime = $totalTravelTime;
	}
	
	/**
	 * get total traveling plan for the route
	 * @return int
	 */
	public function getTotalTravelTime():int {
		return $this->totalTravelTime; 
	}
	
	/**
	 * set total traveling distance for the route
	 * @param int $totalTravelDistance
	 */
	public function setTotalTravelDistance(int $totalTravelDistance) {
		$this->totalTravelDistance = $totalTravelDistance;
	}
	
	/**
	 * get total traveling distance for the route
	 * @return int
	 */
	public function getTotalTravelDistance():int{
		return $this->totalTravelDistance;
	}
	
	/**
	 * set route status
	 * @param string $status
	 */
	public function setStatus(string $status) {
		 $this->status = $status;
	}
	
	/**
	 * get sattus for Route
	 * @return string
	 */
	public function getStatus():string {
		return $this->status;
	}
	
	public function getErrorMesage():string {
		return $this->errorMessage?$this->errorMessage:"";
	}
	
	public function setErrorMessage(string $errMsg){
		$this->errorMessage = $errMsg;
	}
	
	
	/**
	 * calculate optimal travel plan for the route
	 */
	abstract function generateOptimalTravelPlan();
	
}