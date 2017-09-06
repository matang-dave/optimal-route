<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\Service\Route\IRouteService;
class DeleteOldRoute extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'delete-old-route';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete old route information';
	
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$routeService = App::make(IRouteService::class);
		$routeService->deleteOldRoute();
	}
}