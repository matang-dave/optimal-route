<?php
namespace App\Service\Route;
use App\Repository\Route\IRouterRepository;
use App\DTO\RouteDTO;
use App\Constants\Route\RouteStatus;
use App\Model\Coordinate;
use Illuminate\Support\Facades\App;
use App\Model\Route;
use App\Exceptions\Route\RouteException;
use App\Constants\Route\RouteExceptionMessage;
use \Illuminate\Contracts\Queue\EntityNotFoundException;

/**
 * Route service
 * 
 * @author matang
 *
 */
class RouteService implements IRouteService
{
	/**
	 * 
	 * @var IRouterRepository
	 */
	private $routeRepository;
	
	/**
	 *  RouteService dependson the @link IRouterRepository
	 *  
	 * @param IRouterRepository $routeRepository
	 */
	public function __construct(IRouterRepository $routeRepository) {
		$this->routeRepository = $routeRepository;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Service\Route\IRouteService::findRoute()
	 */
	public function findRoute(string $uniqueId):RouteDTO {
		
		$route = $this->routeRepository->findRoute($uniqueId);
		
		$routeDTO  = new RouteDTO();
		
		if($route->getStatus() == RouteStatus::API_NEW || $route->getStatus() == RouteStatus::API_INPROGRESS) {
			$routeDTO->setStatus(RouteStatus::UI_INPROGRESS);
		}
		else if ($route->getStatus() == RouteStatus::API_ERROR){
			$routeDTO->setStatus(RouteStatus::UI_ERROR);
		}
		else {
			$routeDTO->setStatus(RouteStatus::UI_SUCCESS);
			$routeDTO->setPath($route->getTravelPlan());
			$routeDTO->setTotalDistance($route->getTotalTravelDistance());
			$routeDTO->setTotalTravelTime($route->getTotalTravelTime());
		}
		return $routeDTO;
	}

	
	public function deleteOldRoute() {
		$result = [];
		try {
			$this->routeRepository->deleteOldRoute(Route::TIME_TO_LIVE_SECONDS);
			$result['success'] = 1;
		}
		catch(\Exception $e){
			$result['success'] = 0;
		}
		return $result;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Service\Route\IRouteService::findOptmialTravelPlan()
	 */
	public function findOptmialTravelPlan() {
		try {
			$route = $this->routeRepository->findRouteForOptimalTavlePlan();
			$route->generateOptimalTravelPlan();
			$route->setStatus(RouteStatus::API_CALCULATED);
			$this->routeRepository->update($route);
		}
		catch (EntityNotFoundException $e) {
			//do nothing
		}
		catch(RouteException $e) {
			$route->setStatus(RouteStatus::API_ERROR);
			$route->setErrorMessage($e->getMessage());
			$this->routeRepository->update($route);
		}
		catch (\Exception $e) {
			$route->setStatus(RouteStatus::API_ERROR);
			$route->setErrorMessage(RouteExceptionMessage::UNKNOEN_ERROR);
			$this->routeRepository->update($route);
		}
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Service\Route\IRouteService::addNewRoute()
	 */
	public function addNewRoute(array $originCoordinates, array $destinationCoordinates):string {
		
		$origin = new Coordinate(trim($originCoordinates[0]),trim($originCoordinates[1]));
		$destinations = [];
		
		foreach($destinationCoordinates as $destinationCoordinate) {
			
			$routeCoordinate = new Coordinate($destinationCoordinate[0],$destinationCoordinate[1]);
			$destinations[] = $routeCoordinate;
		}
		
		$route = App::make(Route::class);
		$route->addNewRoute($origin, $destinations);
		
		$this->routeRepository->saveRoute($route);
		
		return $route->getUniqueId();
	}
}