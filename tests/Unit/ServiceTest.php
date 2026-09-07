<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTOBuilder;
use App\Exception\ReservationInvalideException;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use DateTimeImmutable;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;

final class ServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $capsule = new Capsule();
        $capsule->addConnection(['driver' => 'mysql', 'database' => 'unused']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function testCreatesValidReservation(): void
    {
        $reservations = new InMemoryReservationRepository();
        $service = new CreerReservationService(new InMemorySalleRepository(), $reservations);

        $reservation = $service->execute($this->dto('+1 day', '+1 day 2 hours'));

        self::assertSame('confirmee', $reservation->statut);
        self::assertCount(1, $reservations->items);
    }

    public function testRejectsMissingRoom(): void
    {
        $this->expectException(SalleIndisponibleException::class);
        (new CreerReservationService(new InMemorySalleRepository(false), new InMemoryReservationRepository()))
            ->execute($this->dto('+1 day', '+1 day 2 hours'));
    }

    public function testRejectsInactiveRoom(): void
    {
        $this->expectException(SalleIndisponibleException::class);
        (new CreerReservationService(new InMemorySalleRepository(true, false), new InMemoryReservationRepository()))
            ->execute($this->dto('+1 day', '+1 day 2 hours'));
    }

    public function testRejectsEndBeforeStart(): void
    {
        $this->expectException(ReservationInvalideException::class);
        (new CreerReservationService(new InMemorySalleRepository(), new InMemoryReservationRepository()))
            ->execute($this->dto('+1 day 2 hours', '+1 day'));
    }

    public function testRejectsDurationOverFourHours(): void
    {
        $this->expectException(ReservationInvalideException::class);
        (new CreerReservationService(new InMemorySalleRepository(), new InMemoryReservationRepository()))
            ->execute($this->dto('+1 day', '+1 day 5 hours'));
    }

    public function testRejectsPastDate(): void
    {
        $this->expectException(ReservationInvalideException::class);
        (new CreerReservationService(new InMemorySalleRepository(), new InMemoryReservationRepository()))
            ->execute($this->dto('-1 hour', '+1 hour'));
    }

    public function testRejectsConflict(): void
    {
        $reservations = new InMemoryReservationRepository();
        $reservations->conflict = new Reservation(['statut' => 'confirmee']);

        $this->expectException(SalleIndisponibleException::class);
        (new CreerReservationService(new InMemorySalleRepository(), $reservations))
            ->execute($this->dto('+1 day', '+1 day 2 hours'));
    }

    public function testAcceptsAdjacentReservation(): void
    {
        $reservations = new InMemoryReservationRepository();
        $reservations->adjacent = true;

        $reservation = (new CreerReservationService(new InMemorySalleRepository(), $reservations))
            ->execute($this->dto('+1 day 2 hours', '+1 day 4 hours'));

        self::assertSame('confirmee', $reservation->statut);
    }

    public function testCancelsReservation(): void
    {
        $reservations = new InMemoryReservationRepository();
        $reservations->stored = new Reservation(['id' => 7, 'statut' => 'confirmee']);

        $result = (new AnnulerReservationService($reservations))->execute(7);

        self::assertSame('annulee', $result->statut);
    }

    public function testRejectsUnknownReservationOnCancellation(): void
    {
        $this->expectException(ReservationIntrouvableException::class);
        (new AnnulerReservationService(new InMemoryReservationRepository()))->execute(99);
    }

    private function dto(string $start, string $end): \App\DTO\CreerReservationDTO
    {
        $now = new DateTimeImmutable();

        return (new CreerReservationDTOBuilder())
            ->salleId(1)
            ->responsable('Awa Ndiaye')
            ->email('awa@example.com')
            ->motif('Cours de PHP')
            ->dateDebut($now->modify($start))
            ->dateFin($now->modify($end))
            ->build();
    }
}

final class InMemorySalleRepository implements SalleRepositoryInterface
{
    public function __construct(
        private readonly bool $exists = true,
        private readonly bool $active = true,
    ) {
    }

    public function all(): array { return []; }

    public function findById(int $id): ?Salle
    {
        return $this->exists ? new Salle(['id' => $id, 'active' => $this->active]) : null;
    }

    public function save(Salle $salle): Salle { return $salle; }
}

final class InMemoryReservationRepository implements ReservationRepositoryInterface
{
    /** @var list<Reservation> */
    public array $items = [];
    public ?Reservation $conflict = null;
    public ?Reservation $stored = null;
    public bool $adjacent = false;

    public function all(?int $salleId = null): array { return $this->items; }

    public function findById(int $id): ?Reservation { return $this->stored; }

    public function findConflict(int $salleId, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): ?Reservation
    {
        return $this->conflict;
    }

    public function save(Reservation $reservation): Reservation
    {
        $reservation->id = count($this->items) + 1;
        $this->items[] = $reservation;
        return $reservation;
    }

    public function cancel(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulee';
        return $reservation;
    }
}