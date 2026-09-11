<?php

declare(strict_types=1);

use App\Auth\AuthService;
use App\Controller\AuthController;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use FastRoute\RouteCollector;

return static function (RouteCollector $routes): void {
$routes->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
$routes->addRoute('POST', '/login', [AuthController::class, 'login']);
$routes->addRoute('POST', '/logout', [AuthController::class, 'logout']);
$routes->addRoute('GET', '/', [SalleController::class, 'index']);
	$routes->addRoute('GET', '/salles', [SalleController::class, 'index']);
	$routes->addRoute('GET', '/salles/create', [SalleController::class, 'create']);
	$routes->addRoute('POST', '/salles', [SalleController::class, 'store']);
	$routes->addRoute('GET', '/salles/{id:\d+}', [SalleController::class, 'show']);
	$routes->addRoute('GET', '/salles/{id:\d+}/edit', [SalleController::class, 'edit']);
	$routes->addRoute('POST', '/salles/{id:\d+}/edit', [SalleController::class, 'update']);
	$routes->addRoute('GET', '/reservations', [ReservationController::class, 'index']);
	$routes->addRoute('GET', '/reservations/create', [ReservationController::class, 'create']);
	$routes->addRoute('POST', '/reservations', [ReservationController::class, 'store']);
	$routes->addRoute('GET', '/reservations/{id:\d+}', [ReservationController::class, 'show']);
	$routes->addRoute('POST', '/reservations/{id:\d+}/cancel', [ReservationController::class, 'cancel']);
};
