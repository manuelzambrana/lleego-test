<?php

namespace App\Infrastructure\HttpClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AvailabilityClient
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    //Metodo para realizar la peticion al XML
    public function fetch(string $origin, string $destination, string $date): array
    {
        // URL para la API
        $url = "https://testapi.lleego.com/prueba-tecnica/availability-price?origin={$origin}&destination={$destination}&date={$date}";

        // Hacer la solicitud GET
        $response = $this->httpClient->request('GET', $url);

        // Obtener el contenido de la respuesta
        $responseContent = $response->getContent();

        // Cargar el contenido del SOAP
        libxml_use_internal_errors(true);  // Habilitar manejo de errores internos
        $soapContent = simplexml_load_string($responseContent);

        // Si no se pudo cargar el XML del SOAP, mostramos los errores
        if ($soapContent === false) {
            throw new \Exception("Error al cargar el contenido SOAP.");
        }

        // Obtener los namespaces para acceder a las etiquetas correctamente
        $namespaces = $soapContent->getNamespaces(true);

        // Acceder al cuerpo del mensaje SOAP y luego al contenido XML dentro de <AirShoppingRS>
        $soapBody = $soapContent->children($namespaces['soap'])->Body;
        $airShoppingRS = $soapBody->children($namespaces[''])->AirShoppingRS->DataLists->FlightSegmentList;

        // Si no se encontraron vuelos devolvemos mensaje
        if (empty($airShoppingRS)) {
            return ['message' => 'No se encontraron vuelos.'];
        }

        // Si tenemos vuelos lo pasamos al metodo parseFlights
        return $this->parseFlights($airShoppingRS);
    }


    //Metodo para devolver los datos de los vuelos que necesitamos
    private function parseFlights($xml): array
    {
        $flights = [];

        foreach ($xml->FlightSegment as $segment) {
            $flights[] = [
                'originCode' => (string)$segment->Departure->AirportCode,
                'originName' => (string)$segment->Departure->AirportName,
                'destinationCode' => (string)$segment->Arrival->AirportCode,
                'destinationName' => (string)$segment->Arrival->AirportName,
                'start' => (string)$segment->Departure->Date . ' ' . (string)$segment->Departure->Time,
                'end' => (string)$segment->Arrival->Date . ' ' . (string)$segment->Arrival->Time,
                'transportNumber' => (string)$segment->MarketingCarrier->FlightNumber,
                'companyCode' => (string)$segment->MarketingCarrier->AirlineID,
                'companyName' => (string)$segment->MarketingCarrier->Name,
            ];
        }

        return $flights;
    }

}
