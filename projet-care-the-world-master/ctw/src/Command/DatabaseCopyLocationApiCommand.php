<?php

namespace App\Command;

// a garder temporairement car sans ca PHP n'a pas assez de memoire allouée pour executer la commande qui est très lourde..
// Mais il ne faut pas rester comme ca #mauvaisePratique

ini_set('memory_limit', '1024M');

use App\Entity\Location;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;

class DatabaseCopyLocationApiCommand extends Command
{
    protected static $defaultName = 'app:database:copy-location-api';
    protected static $defaultDescription = 'Récupère la liste de toutes les communes francaises dpuis l\'API du gouvernement pour les y stocker dans notre BDD locale';
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;

    }

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        for($i = 1; $i < 96; $i++){
            if($i < 10){
                $apiRequestUrl = "https://geo.api.gouv.fr/communes?codeDepartement=0".$i."&fields=nom,code,codesPostaux,centre,codeDepartement,departement,codeRegion,region&format=json&geometry=centre";
  
            } else {
                $apiRequestUrl = "https://geo.api.gouv.fr/communes?codeDepartement=".$i."&fields=nom,code,codesPostaux,centre,codeDepartement,departement,codeRegion,region&format=json&geometry=centre";

            }
            $apiResponse = file_get_contents($apiRequestUrl);

            foreach (json_decode($apiResponse) as $value) {
                $location = new Location();

                $location->setNameCity($value->nom);
                $location->setCodeCity($value->code);

                $location->setZipCode(intval($value->codesPostaux[0]));

                $location->setNameRegion($value->region->nom);
                $location->setCodeRegion($value->region->code);

                $location->setNameDepartment($value->departement->nom);
                $location->setCodeDepartment($value->departement->code);

                $location->setLatitude($value->centre->coordinates[1]);
                $location->setLongitude($value->centre->coordinates[0]);


                $this->em->persist($location);
                $this->em->flush();
                $io->success($location->getCodeDepartment().' '.$location->getNameCity());
            }
        }
        $io->success('SUCCESS !');

        return Command::SUCCESS;
    }
}
