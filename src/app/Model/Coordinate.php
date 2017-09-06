<?php
namespace App\Model;
class Coordinate {
	
	/**
	 * latitude
	 * @var float
	 */
	private $latitude;
	
	/**
	 * longitude
	 * @var float
	 */
	private$longitude;
	
	/**
	 * constructor for Coordinate
	 * 
	 * @param float $latitude
	 * @param float $longitude
	 */
	public function __construct(float $latitude,float $longitude) {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}
	
	/**
	 * st value for latitude
	 * @param float $latitude
	 */
	public function setLatitude(float $latitude) {
		$this->latitude = $latitude;
	}
	
	/**
	 * get value for latitude
	 * @return float
	 */
	public function getLatitude():float{
		return $this->latitude;
	}
	
	/**
	 * set value for longitude
	 * @param float $longitude
	 */
	public function setLongitude(float $longitude) {
		$this->longitude = $longitude;
	}
	
	/**
	 * get value for longitude
	 * @return float
	 */
	public function getLongitude():float {
		return $this->longitude;
	}
}