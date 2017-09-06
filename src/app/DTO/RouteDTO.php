<?php
namespace App\DTO;
class RouteDTO {
	
	private $status;
	private $path=[];
	private $total_distance;
	private $total_time;
	private $token;
	
	public function setToken(string $token) {
		$this->token = $token;
	}
	
	public function getToken():string {
		return $this->token;
	}
	
	public function setStatus(string $status) {
		$this->status = $status;
	}
	
	public function getStatus():string {
		return $this->status;
	}
	
	public function getPath():array {
		return $this->path;
	}
	
	public function setPath(array $path) {
		$this->path = $path;
	}
	
	public function setTotalDistance(int $totalDistance){
		$this->total_distance = $totalDistance;
	}
	
	public function getTotalDistance():int {
		return $this->total_distance;
	}
	
	public function setTotalTravelTime(int $totalTravelTime) {
		$this->total_time = $totalTravelTime;
	}
	
	public function getTotalTravelTime():int {
		return $this->total_time;
	}
	
	
	
}