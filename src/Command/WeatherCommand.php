<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeatherCommand extends Command {

    private const BASEURL = "http://api.weatherapi.com/v1/";
    private const KEY = "cf36d38369d04f11b59112904222102";

    protected function configure(){
        $this
            ->setName('weather:city')
            ->setDescription('Şehrin anlık hava sıcaklığını gösterir')
            ->setHelp('Consola weather:city ve şehir parametresi verilerek çalıştırılır.')
            ->addArgument('city', InputArgument::REQUIRED, 'Sehir adi')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $city = $input->getArgument('city');
        $url = self::BASEURL."current.json?key=".self::KEY."&q=".$city."&aqi=no";

        $body = $this->get($url);

        if(!empty($body["error"])){
            $output->writeln($body["error"]["message"]);
            return 0;
        }

        $output->writeln($city." şu anki sıcaklığı: ".$body["current"]["temp_c"]);
        return 1;
    }

    public function get(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_URL,$url);
        $body=curl_exec($ch);

        return json_decode($body, true);
    }
}
