# optimal-route

Objective : Find optimal route for given input coordinates using google directions api

Tech stack :
* PHP 7
* Nginx
* Mongo db
* Docker

Frameworks :
* Laravel
* PHP Unit

## Exposed endpoints

Method:  
 - `POST`

URL path:  
 - `/route`

Input body:  

```json
[
	["ROUTE_START_LATITUDE", "ROUTE_START_LONGITUDE"],
	["DROPOFF_LATITUDE_#1", "DROPOFF_LONGITUDE_#1"],
	...
]
```

Response body:  
 - `HTTP code 200`  

```json
{ "token": "TOKEN" }
```

or

```json
{ "error": "ERROR_DESCRIPTION" }
```

---

Input body example:

```json
[
	["22.372081", "114.107877"],
	["22.284419", "114.159510"],
	["22.326442", "114.167811"]
]
```

Response example:

```json
{ "token": "9d3503e0-7236-4e47-a62f-8b01b5646c16" }
```

### Get shortest driving route
Get shortest driving route for submitted locations (sequence of `[lat, lon]` values starting from start location resulting in shortest path)

Method:  
- `GET`

URL path:  
- `/route/<TOKEN>`

Response body:  
- HTTP 200  

```json
{
	"status": "success",
	"path": [
		["ROUTE_START_LATITUDE", "ROUTE_START_LONGITUDE"],
		["DROPOFF_LATITUDE_#1", "DROPOFF_LONGITUDE_#1"],
		...
	],
	"total_distance": DRIVING_DISTANCE_IN_METERS,
	"total_time": ESTIMATED_DRIVING_TIME_IN_SECONDS
}
```  
or  

```json
{
	"status": "in progress"
}
```  
or  

```json
{
	"status": "failure",
	"error": "ERROR_DESCRIPTION"
}
```

---

URL example:  
 - `/route/9d3503e0-7236-4e47-a62f-8b01b5646c16`

Response example:  
```json
{
	"status": "success",
	"path": [
		["22.372081", "114.107877"],
		["22.326442", "114.167811"],
		["22.284419", "114.159510"]
	],
	"total_distance": 20000,
	"total_time": 1800
}
```

## Setup environment :

1.Run composer

  optimal-route/src/composer install

2.Run dockers from the dockerfile under optimal-route folder

    docker-compose build 
    docker-compose up -d

    Docker exposed port required to be free:
    1) 80 - http
    2) 443 - https
    3) 27017 - mongodb
  
3. chmod 755 -R  optimal-route/src/storage/
 
