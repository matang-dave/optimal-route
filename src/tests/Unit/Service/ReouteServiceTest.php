<?php
namespace Tests\Unit\Service;
use Tests\TestCase;
use App\Service\Route\RouteService;
use App\Repository\Route\RouterRepository;
use App\Constants\Route\RouteStatus;
use App\Model\Route;
use App\Model\OptimalRoute;
use App\Exceptions\Route\RouteException;

class RouteServiceTest extends TestCase {
	
	public function testFindRouteWithNewStatus() {
		$app = $this->createApplication();
		
		$route = $this->createMock(OptimalRoute::class);
		$route->expects($this->once())->method('generateOptimalTravelPlan')->willReturn(true);
		$route->expects($this->once())->method('getStatus')->willReturn(RouteStatus::API_NEW);
		$routeRepositrory = $this->createMock(RouterRepository::class);
		$routeRepositrory->expects($this->once())->method('update')->willReturn(true);
		$routeRepositrory->method('findRoute')->willReturn($route);
		
		$routeService = new RouteService($routeRepositrory);
		$routeDto = $routeService->findRoute('abcd');
		
		$this->assertEquals(RouteStatus::UI_SUCCESS, $routeDto->getStatus());
	}
	
	public function testFindRouteWithNewStatusThrowsRouteException () {
		$app = $this->createApplication();
		
		$route = $this->createMock(OptimalRoute::class);
		$route->expects($this->once())->method('generateOptimalTravelPlan')->willThrowException(new RouteException("message"));
		$route->expects($this->once())->method('getStatus')->willReturn(RouteStatus::API_NEW);
		$routeRepositrory = $this->createMock(RouterRepository::class);
		$routeRepositrory->expects($this->once())->method('update')->willReturn(true);
		$routeRepositrory->method('findRoute')->willReturn($route);
		
		$routeService = new RouteService($routeRepositrory);
		$routeDto = $routeService->findRoute('abcd');
		
		$this->assertEquals(RouteStatus::UI_ERROR, $routeDto->getStatus());
	}
	
	
	public function testFindRouteWithNewStatusThrowsException () {
		$app = $this->createApplication();
		
		$route = $this->createMock(OptimalRoute::class);
		$route->expects($this->once())->method('generateOptimalTravelPlan')->willThrowException(new \Exception("message"));
		$route->expects($this->once())->method('getStatus')->willReturn(RouteStatus::API_NEW);
		$routeRepositrory = $this->createMock(RouterRepository::class);
		$routeRepositrory->expects($this->once())->method('update')->willReturn(true);
		$routeRepositrory->method('findRoute')->willReturn($route);
		
		$routeService = new RouteService($routeRepositrory);
		$routeDto = $routeService->findRoute('abcd');
		
		$this->assertEquals(RouteStatus::UI_ERROR, $routeDto->getStatus());
	}
	
	
	public function testFindRouteWithCalculatedStatus() {
		$app = $this->createApplication();
		
		$route = $app->make(Route::class);
		$route->setTravelPlan([1]);
		$route->setStatus(RouteStatus::API_CALCULATED);
		$routeRepositrory = $this->createMock(RouterRepository::class);
		$routeRepositrory->expects($this->once())->method('findRoute')->willReturn($route);
		
		$routeService = new RouteService($routeRepositrory);
		$routeDto = $routeService->findRoute('abcd');
		
		$this->assertNotEmpty($routeDto->getPath());
	}
	
	public function testSaveRoute() {
		
		$app = $this->createApplication();
		
		$routeRepositrory = $this->createMock(RouterRepository::class);
		$routeRepositrory->expects($this->once())->method('saveRoute')->willReturn(true);
		$routeService = new RouteService($routeRepositrory);
		
		$routeService = new RouteService($routeRepositrory);
		$originCoordinates = ['12.128845','52.125845'];
		$destinationCoordinates = [['58.36949','8.87522']];
		
		$uniqueId = $routeService->addNewRoute($originCoordinates,$destinationCoordinates);
		
		$this->assertNotEmpty($uniqueId);
		
	}
	
	public function testFindOptimalTravelPlan() {
		
		$app = $this->createApplication();
		
		$route = $this->createMock(OptimalRoute::class);
		$route->expects($this->once())->method('generateOptimalTravelPlan')->willReturn(true);
		
		$routeRepositrory = $this->createMock(RouterRepository::class);
		$routeRepositrory->expects($this->once())->method('findRouteForOptimalTavlePlan')->willReturn($route);
		$routeRepositrory->expects($this->once())->method('update')->willReturn($route);
		
		$routeService = new RouteService($routeRepositrory);
		$routeService->findOptmialTravelPlan();
		
		$this->assertNotFalse(true);
		
	}
}