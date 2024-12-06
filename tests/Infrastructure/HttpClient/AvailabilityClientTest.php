<?php
namespace App\Tests\Infrastructure\HttpClient;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;
use App\Infrastructure\HttpClient\AvailabilityClient;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityClientTest extends TestCase
{
    public function testFetchReturnsFlightsFromXML()
    {
        // Ruta al archivo XML de prueba
        $xmlFilePath = __DIR__ . '/mock_response.xml'; // Asegúrate de que el archivo esté en la ruta correcta

        // Leer el contenido del archivo XML
        $xmlContent = file_get_contents($xmlFilePath);

        // Crear un mock de la respuesta HTTP
        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')->willReturn($xmlContent);
        $responseMock->method('getStatusCode')->willReturn(200);

        // Crear un mock del cliente HTTP
        $httpClientMock = $this->createMock(MockHttpClient::class);
        $httpClientMock->method('request')->willReturn($responseMock);

        // Instanciar el AvailabilityClient con el mock
        $availabilityClient = new AvailabilityClient($httpClientMock);

        // Llamar al método fetch
        $flights = $availabilityClient->fetch('MAD', 'BIO', '2022-06-01');

        // Aserciones para verificar los resultados
        $this->assertIsArray($flights);
        $this->assertNotEmpty($flights);
        $this->assertCount(5, $flights);
        $this->assertEquals('MAD', $flights[0]['originCode']);
        $this->assertEquals('BIO', $flights[0]['destinationCode']);
    }
}
