<?php
namespace App\Service\Route;
use App\DTO\RouteDTO;

/**
 * Route service  
 * @author matang
 *
 */
interface IRouteService {
	
	/**
	 * find route from unique id
	 * 
	 * @param string $uniqueId
	 * @return RouteDTO
	 */
	public function findRoute(string $uniqueId):RouteDTO;
	
	/**
	 * find optimal travel plan for a route
	 */
	public function findOptmialTravelPlan();
	
	/**
	 * add new route to calculate optimal travel plan
	 * 
	 * @param array $originCoordinates
	 * @param array $destinationCoordinates
	 * @return string
	 */
	public function addNewRoute(array $originCoordinates, array $destinationCoordinates):string;
	
	/***
	 * Delete route data for which optimal route is generated 
	 * long ago or error received. 
	 */
	public function deleteOldRoute();
}