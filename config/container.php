<?php

declare(strict_types=1);

use App\Auth\AuthService;
use App\Controller\AuthController;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Http\Router;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;
use Illuminate\Database\Capsule\Manager as Capsule;
use function DI\autowire;
use function DI\factory;

return [
	SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
	ReservationRepositoryInterface::class => autowire(EloquentReservationRepository::class),
	Capsule::class => factory(static function (): Capsule {
		return require dirname(__DIR__) . '/config/database.php';
	}),
	ViewRenderer::class => factory(static function (): ViewRenderer {
		return new ViewRenderer(dirname(__DIR__) . '/templates');
	}),
	Router::class => factory(static function (\Psr\Container\ContainerInterface $container): Router {
	  return new Router(
	    $container,
	    $container->get(ViewRenderer::class),
	    dirname(__DIR__) . '/routes/web.php'
	  );
	}),
	SalleController::class => autowire(),
AuthController::class => autowire(),
AuthService::class => autowire(),
	ReservationController::class => autowire(),
	CreerReservationService::class => autowire(),
	AnnulerReservationService::class => autowire(),
	SalleValidator::class => autowire(),
	ReservationValidator::class => autowire(),
];
