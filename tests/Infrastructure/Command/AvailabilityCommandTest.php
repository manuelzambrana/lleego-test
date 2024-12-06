<?php
namespace App\Tests\Infrastructure\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Infrastructure\Command\AvailabilityCommand;
use App\Application\UseCase\FetchAvailabilityUseCase;

class AvailabilityCommandTest extends TestCase
{
    public function testCommandOutputsFlights(): void
    {
        // Crear un mock del caso de uso
        $fetchAvailabilityUseCaseMock = $this->createMock(FetchAvailabilityUseCase::class);
        $fetchAvailabilityUseCaseMock->method('execute')->willReturn([
            [
                'originCode' => 'MAD',
                'originName' => 'Madrid Barajas',
                'destinationCode' => 'BIO',
                'destinationName' => 'Bilbao',
                'start' => '2022-06-01T10:00:00',
                'end' => '2022-06-01T11:30:00',
                'transportNumber' => 'IB1234',
                'companyCode' => 'IB',
                'companyName' => 'Iberia',
            ]
        ]);

        // Configurar el comando
        $command = new AvailabilityCommand($fetchAvailabilityUseCaseMock);
        $application = new Application();
        $application->add($command);
        $commandTester = new CommandTester($command);

        // Ejecutar el comando
        $commandTester->execute([
            'origin' => 'MAD',
            'destination' => 'BIO',
            'date' => '2022-06-01',
        ]);

        // Verificar la salida
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Madrid Barajas', $output);
        $this->assertStringContainsString('Bilbao', $output);
    }
}
