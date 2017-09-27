<?php
namespace App\Model;

use App\Exceptions\Route\RouteException;
use App\Constants\Route\RouteExceptionMessage;

/**
 * Distance calculator implimentation by Google api
 * @author matang
 *
 */
class GoogleDistanceCalculator extends DistanceCalculator
{
	/**
	 * Authentication key for the accessing google url$route['waypoint_order']
	 * @var string
	 */
	CONST APPLICATION_KEY = 'AIzaSyAxLY20MPt2nhMTiO3GONO6wgOkcAIGk-I';
	
	/**
	 * Google api url to calclualte distance matrix
	 * @var string
	 */
	CONST API_URL = 'https://maps.googleapis.com/maps/api/directions/json?';
	
	
	CONST DISNTANCE_MATRIX_URL = 'https://maps.googleapis.com/maps/api/distancematrix/json?';
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Model\DistanceCalculator::finDistanceMatrix()
	 */
	public function findOptimalTravelRoute(Route $route):array {
		$destinationCoordinates = $route->getDestinations ();
		$destinationCount = count ( $destinationCoordinates );
		
		$minDistance = PHP_INT_MAX;
		$minTravelTime = PHP_INT_MAX;
		$optimalWayPoints [] = [ ];
		
		
		$farestDestinationIndex = $this->findFarestDestinationIndex ( $route );
		$destinationCoordinate = $destinationCoordinates [$farestDestinationIndex];
		// generate url as current coordinate as destinations and other as middle
		$url = $this->generateUrl ( $route, $farestDestinationIndex );
		// fetch response
		$response = $this->queryUrl ( $url );
		
		list ( $minDistance, $minTravelTime, $wayPoints ) = $this->findMinimumDistanceAndTime ( $response );
		// if current route's distance is less than
		// minimum distacne, currnet route is the optimal route
		
		for($i = 0; $i < count ( $wayPoints ); $i ++) {
			if ($wayPoints [$i] >= $farestDestinationIndex) {
				$wayPoints [$i] = $wayPoints [$i] + 1;
			}
		}
		
		$wayPoints [] = $farestDestinationIndex;
		$optimalWayPoints = $wayPoints;
		
		return [ 
				$minDistance,
				$minTravelTime,
				$optimalWayPoints 
		];
	}
	
	/**
	 * find index of the farest destination index from the origin
	 * 
	 * @param Route $route
	 * @return int
	 */
	private function findFarestDestinationIndex(Route $route):int{
		
		$url = self::DISNTANCE_MATRIX_URL.'key='.self::APPLICATION_KEY.'&origins='.
				$route->getOrigin()->getLatitude().",".$route->getOrigin()->getLongitude();
		
		$url.='&destinations=';
		$destinations = $route->getDestinations();
		foreach ($destinations as $destination) {
			$url.=
			$destination->getLatitude().",".$destination->getLongitude().'|';
		}
		
		$url = rtrim($url,'|');
		
		$response = $this->queryUrl($url);
		
		$this->validateResponse($response);
		
		$maxDistnce = 0;
		$maxDistanceIndex = 0;
		
		for($i=0; $i<count($response['rows'][0]['elements']);$i++){
			$element = $response['rows'][0]['elements'][$i];
			if($element['distance']['value']>$maxDistnce) {
				$maxDistnce = $element['distance']['value'];
				$maxDistanceIndex = $i;
			}
		}
		
		return $maxDistanceIndex;
		
	}
	
	/**
	 * 
	 * @param unknown $response
	 * @throws RouteException
	 */
	private function validateResponse($response){
		
		switch ($response['status']) {
			
			case 'INVALID_REQUEST':
				throw new RouteException(RouteExceptionMessage::INVALID_REQUEST);
			case 'MAX_ELEMENTS_EXCEEDED':
				throw new RouteException(RouteExceptionMessage::MAX_ELEMENTS_EXCEEDED);
			case 'OVER_QUERY_LIMIT':
				throw new RouteException(RouteExceptionMessage::MAX_QUERY_LIMIT_EXCEEDED);
			case 'REQUEST_DENIED':
			case 'UNKNOWN_ERROR':
				throw new RouteException(RouteExceptionMessage::MAX_QUERY_LIMIT_EXCEEDED);
		}
	}
	
	/**
	 * Generate url to fetch time and distance matrix for given route
	 * @return string 
	 */
	private function generateUrl(Route $route,$destinationIndex):string {
		
		$url = self::API_URL.'key='.self::APPLICATION_KEY.'&origin='.
				$route->getOrigin()->getLatitude().",".$route->getOrigin()->getLongitude();
		
		 
		$destinations = $route->getDestinations();
		$url.="&destination=".$destinations[$destinationIndex]->getLatitude().",".$destinations[$destinationIndex]->getLongitude();
		$destinationCount = count($destinations);
		$wayPoints= '';
		
		if($destinationCount>1) {
			$url.= "&waypoints=optimize:true|";
			
			for($i = 0; $i < $destinationCount; $i ++) {
				if ($i == $destinationIndex) {
					continue;
				}
				$wayPoints .= $destinations[$i]->getLatitude() . "," . $destinations[$i]->getLongitude() . "|";
			}
			$wayPoints = rtrim($wayPoints,"|");
			$url.=$wayPoints;
		}
		
		return $url;
		
	}
	
	/**
	 * get routing information using GET query to url
	 * 
	 * @param string $url
	 * @throws RouteException
	 * @return array
	 */
	private function queryUrl($url):array {
		
		try {
			$response = file_get_contents($url);
		}
		catch (\Exception $e) {
			throw new RouteException(RouteExceptionMessage::ERROR_FETCHING_DATA,$e->getCode(),$e->getPrevious());
		}
		return json_decode($response,true);
		
	}
	
	/**
	 * 
	 * @param unknown $response
	 * @throws RouteException
	 * @return unknown[][][]
	 */
	private function findMinimumDistanceAndTime($response) {
		
		
		$this->validateResponse($response);
		
		$minDistance = PHP_INT_MAX;
		$minTime = PHP_INT_MAX;
		$optimalWayPoints = [];
		
		//find minimum distance from the different routes
		foreach ($response['routes'] as $route) {
			
			$totalDistance = 0;
			$totalTime = 0;
			$wayPointOrder = [];
			foreach ( $route ['legs'] as $leg ) {
				$totalDistance += $leg ['distance'] ['value'];
				$totalTime += $leg ['duration'] ['value'];
			}
			
			if ($totalDistance < $minDistance) {
				$minDistance = $totalDistance;
				$minTime = $totalTime;
				$optimalWayPoints = $route['waypoint_order'];
			}
		}
		
		return [$minDistance,$minTime,$optimalWayPoints];
	}
}