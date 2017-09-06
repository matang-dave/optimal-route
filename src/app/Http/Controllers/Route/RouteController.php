<?php
namespace App\Http\Controllers\Route;
use App\Validator\RouteValidator;
use App\Service\Route\IRouteService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Contracts\Queue\EntityNotFoundException;
use App\Constants\Route\RouteExceptionMessage;

/**
 * Route controller
 * @author matang
 *
 */
class RouteController extends Controller {

	/**
	 * 
	 * @var IRouteService
	 */
	private $routeService;
	
	/**
	 * RouteController depends on @link IRouteService
	 * 
	 * @param IRouteService $routeService
	 */
	public function __construct(IRouteService $routeService) {
		$this->routeService = $routeService;
	}
	
	/**
	 * Add new route to calculate optimal path
	 * 
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function createRoute(Request $request){
		try {
			
			$requestData = $request->json()->all();
			RouteValidator::validateAddNewRoute($requestData);
			
			$originCoordinates= $requestData[0];
			$destinationCoordinates= array_slice($requestData, 1) ;
			
			$response['token']= $this->routeService->addNewRoute($originCoordinates, $destinationCoordinates);
			$staus = Response::HTTP_OK;
		}
		catch (\InvalidArgumentException $e) {
			// invalid arguemts in the request
			$response['error'] = $e->getMessage();
			$staus = Response::HTTP_OK;
		}
		catch (\Exception $e) {
			// unkown error
			$response['error'] = RouteExceptionMessage::UNKNOEN_ERROR;
			$staus = Response::HTTP_INTERNAL_SERVER_ERROR;
		}
		return new JsonResponse($response,$staus);
		
	}
	
	/**
	 * find route information from the token 
	 * 
	 * @param string $token
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getRoute(string $token){
		try {
			$response = [];
			$routeDto = $this->routeService->findRoute($token);
			$response['status'] = $routeDto->getStatus();
			if(!empty($routeDto->getPath())) {
				$response['path']= $routeDto->getPath();
				$response['total_distance'] = $routeDto->getTotalDistance();
				$response['total_time'] = $routeDto->getTotalTravelTime();
			}
			$staus = Response::HTTP_OK;
		}
		catch (EntityNotFoundException $e) {
			// Route not found
			$response['error'] = RouteExceptionMessage::ROUTE_NOT_FOUND.$token;
			$staus = Response::HTTP_NOT_FOUND;
			
		}
		catch (\Exception $e) {
			// unkown error
			$response['error'] = RouteExceptionMessage::UNKNOEN_ERROR;
			$staus = Response::HTTP_INTERNAL_SERVER_ERROR;
			
		}
		return new JsonResponse($response,$staus);
		
	}
	
}