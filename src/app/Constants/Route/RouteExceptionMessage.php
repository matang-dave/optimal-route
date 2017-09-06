<?php
namespace App\Constants\Route;
final class RouteExceptionMessage {
	CONST INVALID_REQUEST = 'Invalid request';
	CONST MAX_ELEMENTS_EXCEEDED = 'Maximum elements limit in a route exceeded';
	CONST TRAVEL_MATRIX_UNKOWN_ERROR = 'Error in computing optimal route.Please try again after sometimes';
	CONST COORDIATE_NOT_FOUND = "Error in locating coordinate";
	CONST CANNOT_COMPUTE_DISTANCE = 'Error in computing distance between coordinates';
	CONST MAX_ROUTE_LENGTH_EXCEEDED = 'Maximum distance between coordinates exceeded the thresold';
	CONST MAX_QUERY_LIMIT_EXCEEDED = 'Maximum query limits exceeded.Please try again after sometimes';
	CONST ERROR_FETCHING_DATA = 'Error while fetching data of coordinate distance.Please try againt after sometimes';
	CONST NO_ROUTE_COORDINATES_SUPPLIED = 'No coordiantes supplied';
	CONST NO_DESTINATION_COORDINATES_SUPPLIED = 'Pleaese add destination coordinates';
	CONST MAXIMUM_DESTINATION_COUNT_PER_ROUTE_LIMIT_EXCEEDED = 'Maximum destination count per route limit exceeded';
	CONST INVALID_ROUTE_COORDINATES_PROVIDED = 'Invalid route coordinates provided';
	CONST UNKNOEN_ERROR = 'Unknown error.Please try again after sometime';
	CONST ROUTE_NOT_FOUND = 'Route not found for id ';
	
	
	
	
}
	