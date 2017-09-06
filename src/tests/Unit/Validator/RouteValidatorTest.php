<?php
use App\Validator\RouteValidator;
use App\Model\Route;
use Tests\TestCase;

class RouteValidatorTest extends TestCase {
	
	public function testValidCoordinates() {
		try {
			$result = 0;
			RouteValidator::validateAddNewRoute([[2.32,4.56],[1.2,2.89]]);
		}
		catch(\InvalidArgumentException $e) {
			$result = 1;
		}
		$this->assertEquals(0, $result);
	}
	
	public function testNoPostData() {
		
		try {
			$result = 0;
			RouteValidator::validateAddNewRoute([]);
		}
		catch(\InvalidArgumentException $e) {
			$result = 1;
		}
		$this->assertEquals(1, $result);
	}
	
	public function testNoDestination() {
		
		try {
			$result = 0;
			RouteValidator::validateAddNewRoute([[1.236,2.89]]);
		}
		catch(\InvalidArgumentException $e) {
			$result = 1;
		}
		$this->assertEquals(1, $result);
	}
	
	
	public function testInvalidCoordinatesCount() {
		try {
			$result = 0;
			RouteValidator::validateAddNewRoute([[2.32,4.56,6.32],[1,2.89]]);
		}
		catch(\InvalidArgumentException $e) {
			$result = 1;
		}
		$this->assertEquals(1, $result);
	}
	
	public function testCoordinatesThreasholdReach() {
		
		$destinations = [];
		for($i=0;$i<=Route::MAX_DETINATION_COUNT_ALLOWED+1;$i++) {
			$destinations[] = [mt_rand(1,25)/17,mt_rand(2,50)/19];
		}
		try {
			$result = 0;
			RouteValidator::validateAddNewRoute($destinations);
		}
		catch(\InvalidArgumentException $e) {
			$result = 1;
		}
		$this->assertEquals(1, $result);
	}
	
	
}