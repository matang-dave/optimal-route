<?php
namespace App\Validator;
use App\Model\Route;
use App\Constants\Route\RouteExceptionMessage;

/**
 * Validate request parameters for Route
 * @author matang
 *
 */
class RouteValidator {
	
	/**
	 * validate request parameters to add new route
	 * 
	 * @param array $requestParams
	 * @throws \InvalidArgumentException
	 */
	public static function validateAddNewRoute(array $requestParams) {
		
		// empty request body
		if(!is_array($requestParams) || empty($requestParams)) {
			throw new \InvalidArgumentException(RouteExceptionMessage::NO_ROUTE_COORDINATES_SUPPLIED);
		}
		else if(count($requestParams)==1) {	// no deestination coordinates
			throw new \InvalidArgumentException(RouteExceptionMessage::NO_DESTINATION_COORDINATES_SUPPLIED);
		}
		elseif(count($requestParams)-1>(Route::MAX_DETINATION_COUNT_ALLOWED)){
			// maximum coordinate count reached
			throw new \InvalidArgumentException(RouteExceptionMessage::MAXIMUM_DESTINATION_COUNT_PER_ROUTE_LIMIT_EXCEEDED);
		}
		
		foreach($requestParams as $coordinate) {
			if(!is_array($coordinate) || count($coordinate)!=2) {
				//validate every coordinate has langitue and latitude
				throw new \InvalidArgumentException(RouteExceptionMessage::INVALID_ROUTE_COORDINATES_PROVIDED);
			}
			else if(empty($coordinate[0]) || empty($coordinate[1])) {
				throw new \InvalidArgumentException(RouteExceptionMessage::INVALID_ROUTE_COORDINATES_PROVIDED);
			}
		}
	}
	
}