<?php
namespace App\Model;
class OptimalRoute extends Route {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \App\Model\Route::generateOptimalTravelPlan()
	 */	
	public function generateOptimalTravelPlan(){
		
		list($minTravelDistance,$minTravelTime,$wayPoints) = 
		$this->distanceCalculator->findOptimalTravelRoute($this);
		$this->totalTravelDistance = $minTravelDistance;
		$this->totalTravelTime = $minTravelTime;
		
		$destintations[] = [(string)$this->getOrigin()->getLatitude(),(string)$this->getOrigin()->getLongitude()];
		for($i=0;$i<count($wayPoints);$i++) {
			$destintations[] = [(string)$this->getDestinations()[$wayPoints[$i]]->getLatitude(),
					(string)$this->getDestinations()[$wayPoints[$i]]->getLongitude()];
		}
		
		$this->setTravelPlan($destintations);
 	}

}