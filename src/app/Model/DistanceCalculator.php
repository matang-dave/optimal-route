<?php
namespace App\Model;

/**
 * Calculate distance for provided coordinates in the route 
 * @author matang
 *
 */
abstract class DistanceCalculator {
	
	abstract function findOptimalTravelRoute(Route $route):array; 
}