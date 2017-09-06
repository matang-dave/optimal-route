<?php
namespace App\Repository\Route;
use App\Model\Route;

/**
 * Interface for Route repository
 * @author matang
 *
 */
interface IRouterRepository
{
	/**
	 * Save route entity in the db
	 * @param Route $route
	 */
	public function saveRoute(Route $route);
	
	/**
	 *
	 * @throws \EntityNotFoundException
	 * @return Route
	 */
	public function findRoute(string $uniqueId):Route;
	
	/**
	 * Find route for which we need to calculate optimal travel plan
	 * @return Route
	 */
	public function findRouteForOptimalTavlePlan():Route;  
	
	/**
	 * Update travel plan for the route
	 * 
	 * @param Route $route
	 */
	public function update(Route $route);
	
	/**
	 * 
	 * 
	 * @param int $beforeMicroSeconds
	 */
	public function deleteOldRoute(int  $updatedBefore);
	
	
}