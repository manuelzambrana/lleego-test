<?php

namespace App\Infrastructure\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use App\Application\UseCase\FetchAvailabilityUseCase;

class AvailabilityCommand extends Command
{
    //declaramos el comando
    protected static $defaultName = 'lleego:avail';

    private FetchAvailabilityUseCase $fetchAvailability;

    public function __construct(FetchAvailabilityUseCase $fetchAvailability)
    {
        parent::__construct();
        $this->fetchAvailability = $fetchAvailability;
    }

    //declaracion de parametros a pasar al comando
    protected function configure(): void
    {
        $this
            ->setDescription('Fetch flight availability')
            ->addArgument('origin', InputArgument::REQUIRED, 'Origin airport code')
            ->addArgument('destination', InputArgument::REQUIRED, 'Destination airport code')
            ->addArgument('date', InputArgument::REQUIRED, 'Travel date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //obtenemos los argumentos del comando
        $origin = $input->getArgument('origin');
        $destination = $input->getArgument('destination');
        $date = $input->getArgument('date');

        // Obtener los vuelos desde el caso de uso
        $flights = $this->fetchAvailability->execute($origin, $destination, $date);

        // Verificar si se obtuvieron vuelos
        if (empty($flights)) {
            $output->writeln('No se encontraron vuelos.');
            return Command::SUCCESS;
        }

        // Crear la tabla para mostrar los vuelos
        $table = new Table($output);
        $table
            ->setHeaders([
                'Origin Code', 'Origin Name', 'Destination Code', 'Destination Name',
                'Start', 'End', 'Transport Number', 'Company Code', 'Company Name'
            ])
            ->setRows(array_map(fn($flight) => [
                $flight['originCode'],       // Código del aeropuerto de origen
                $flight['originName'],       // Nombre del aeropuerto de origen
                $flight['destinationCode'],  // Código del aeropuerto de destino
                $flight['destinationName'],  // Nombre del aeropuerto de destino
                $flight['start'],            // Hora de salida
                $flight['end'],              // Hora de llegada
                $flight['transportNumber'],  // Número de vuelo
                $flight['companyCode'],      // Código de la aerolínea
                $flight['companyName'],      // Nombre de la aerolínea
            ], $flights));

        // Mostrar la tabla
        $table->render();

        return Command::SUCCESS;
    }
}
