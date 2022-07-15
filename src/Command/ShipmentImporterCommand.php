<?php

declare(strict_types=1);

namespace App\Command;


use ApiPlatform\Core\Exception\DeserializationException;
use App\Entity\Shipment;
use App\Service\ShipmentPriceResolver;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Contracts\Cache\CacheInterface;


#[AsCommand(
    name: 'Shipment Importer',
    description: 'using this command you can pass json file for import shipments to system',
    aliases: ['shipment:import'],
    hidden: false
)]
class ShipmentImporterCommand extends Command
{
    private const IMPORT_BATCH_SIZE = 100;
    protected static $defaultName = 'shipment:import';

    private string                 $shipmentFile;
    private ?int                   $toIndex;
    private ?int                   $fromIndex;
    private SerializerInterface    $serializer;
    private EntityManagerInterface $entityManager;
    private ShipmentPriceResolver  $priceResolver;
    private CacheInterface         $cacheService;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface    $serializer,
        ShipmentPriceResolver  $priceResolver,
        CacheInterface         $cacheService
    ) {
        $this->serializer    = $serializer;
        $this->entityManager = $entityManager;
        $this->priceResolver = $priceResolver;

        parent::__construct(self::$defaultName);
        $this->cacheService = $cacheService;
    }

    protected function configure(): void
    {
        $this->addArgument(
            'shipment_file',
            InputArgument::REQUIRED,
            'please provide the jso file which you wanna import shipments from it'
        )->addOption(
            'from',
            'f',
            InputOption::VALUE_OPTIONAL,
            "this option indicate which index of provided shipments is the first one which you wanna import to database"
            . " this options in combination with to option provide a facility for you to import range of data"
            . " if you have a huge amount of data for import"
            . " for example you can write command like this shipment:import -f 1 -t 20 xxx.json"
            . " which means you wanna import first 20 items of data to db."
        )->addOption(
            'to',
            't',
            InputOption::VALUE_OPTIONAL,
            'this option indicate which index of provided shipments is the last one that you wanna import to database'
            . " this options in combination with from option provide a facility for you to import range of data"
            . " if you have a huge amount of data for import"
            . " for example you can write command like this shipment:import -f 1 -t 20 xxx.json"
            . " which means you wanna import first 20 items of data to db."
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io                 = new SymfonyStyle($input, $output);
        $this->shipmentFile = $input->getArgument('shipment_file');
        $this->fromIndex    = $input->getOption('from');
        $this->toIndex      = $input->getOption('to');
        if (!file_exists($this->shipmentFile)) {
            $io->note(sprintf('the file you passed as a shipments file: %s not found', $this->shipmentFile));
            return Command::INVALID;
        }
        $io->info(
            'start to import shipments data into the database,it may take times based on your data amount, so please be patient!'
        );
        try {
            $shipments = $this->cacheService->get(
                'shipments_data' . md5(pathinfo($this->shipmentFile, PATHINFO_FILENAME)),
                function () {
                    return $this->serializer->deserialize(
                        file_get_contents($this->shipmentFile),
                        'App\Entity\Shipment[]',
                        JsonEncoder::FORMAT
                    );
                }
            );
            $this->importShipments($shipments, $output);
        } catch (Exception $e) {
            $io->Error(sprintf('please provide a valid json to import shipments:[%s]', $e->getMessage()));
            throw new BadRequestException(
                sprintf('please provide a valid json to import shipments:[%s]', $e->getMessage())
            );
        }
        $io->newLine();
        $io->success(
            sprintf(
                'importing shipments data from %d to %d items in list successfully finished.',
                $this->fromIndex,
                $this->toIndex
            )
        );

        return Command::SUCCESS;
    }

    /**
     * @param Shipment[] $shipments
     */
    private function importShipments(
        array           $shipments,
        OutputInterface $output,
    ): void {
        $batchIndex  = 1;
        $from        = $this->fromIndex ?? 0;
        $to          = $this->toIndex ?? count($shipments) - 1;
        $progressBar = new ProgressBar($output, $to - $from);
        for ($i = $from; $i <= $to; $i++) {
            $shipment = $shipments[$i];
            $this->priceResolver->resolveShipmentPrice($shipment);
            $this->entityManager->persist($shipment);
            ++$batchIndex;
            if (($batchIndex % self::IMPORT_BATCH_SIZE === 0)) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
            $this->entityManager->flush();
            $progressBar->advance();
        }
    }
}
