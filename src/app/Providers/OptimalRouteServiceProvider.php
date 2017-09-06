<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\Route\IRouterRepository;
use App\Repository\Route\RouterRepository;
use App\Service\Route\IRouteService;
use App\Service\Route\RouteService;
use App\Model\Route;
use App\Model\OptimalRoute;
use App\Factory\MongoDb\MongoDbFactory;
use App\Model\DistanceCalculator;
use App\Model\GoogleDistanceCalculator;

class OptimalRouteServiceProvider extends ServiceProvider
{
	public function register() {
		
		$app = $this->app;

		$app->bind(Route::class,OptimalRoute::class);
		$app->bind(DistanceCalculator::class,GoogleDistanceCalculator::class);
		
		$app->bind(IRouterRepository::class,RouterRepository::class);
		$app->singleton(RouterRepository::class,function(){
			return new RouterRepository(new MongoDbFactory(),"lalamove.routes");
		});
		
		$app->bind(IRouteService::class,RouteService::class);
		$app->bind(RouteService::class,function($app){
			return new RouteService($app->make(RouterRepository::class));
		});
	}
}