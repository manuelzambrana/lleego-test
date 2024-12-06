<?php
namespace App\Tests\Application\UseCase;

use PHPUnit\Framework\TestCase;
use App\Application\UseCase\FetchAvailabilityUseCase;
use App\Infrastructure\HttpClient\AvailabilityClient;

class FetchAvailabilityUseCaseTest extends TestCase
{
    public function testExecuteReturnsFlights(): void
    {
        // Crear un mock del cliente HTTP
        $availabilityClientMock = $this->createMock(AvailabilityClient::class);
        $availabilityClientMock->method('fetch')->willReturn([
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

        // iniciamos el caso de uso con datos mocks
        $useCase = new FetchAvailabilityUseCase($availabilityClientMock);

        // Ejecutamos el proceso
        $flights = $useCase->execute('MAD', 'BIO', '2022-06-01');
        // Verificamos los datos
        $this->assertIsArray($flights);
        $this->assertCount(1, $flights);
        $this->assertEquals('MAD', $flights[0]['originCode']);
    }
}
