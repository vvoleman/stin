<?php

namespace App\Service\Currency;

use App\Exception\Currency\CurrencyRequestException;
use App\Exception\InvalidDatetimeException;
use App\IFileService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use App\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CurrencyLoader
{
    public const PATH = __DIR__ . "/../../../storage/currency";
    private const API_BASE = "https://data.kurzy.cz/json/meny/";
    private const BANK = 6;

    //private Filesystem $fs;
    //private IFileService $fileService;
    private Client $client;
    private int $bankNumber = self::BANK;

    public function __construct()
    {
        //$this->fs = new Filesystem();
    }

    /**
     * Retrieves all JSON files with valid datetime name, sorted by name ASC
     *
     * @return array<string,SplFileInfo>
     */
    public function getAllFiles(): array
    {
        $finder = new Finder();
        // Get *.json in specific folder, sort by name
        $finder->name("*.json")->files()->in(self::PATH)->sortByName();

        $files = [];
        foreach ($finder as $file) {
            $relativeName = $file->getBasename();
            $parts = explode(".", $relativeName);

            // Is date string valid?
            try {
                $date = new \DateTime($parts[0]);
            } catch (\Exception $e) {
                continue;
            }
            $files[$date->format("Y-m-d")] = $file;
        }

        return $files;
    }

    /**
     * @param string $dateString
     * @param bool $canUseOlder If request fails, it tries to load latest list
     * @return array
     * @throws CurrencyRequestException Unable to retrieve data from API
     * @throws IOException File issues
     * @throws InvalidDatetimeException Invalid parameter
     */
    public function loadCurrencyListForDate(string $dateString = "today",bool $canUseOlder = true): array
    {
        // Is dateString valid?
        try {
            $date = new \DateTime($dateString);
        } catch (\Exception $e) {
            throw new InvalidDatetimeException($e);
        }

        // Path to a file
        $name = sprintf("%s/%s.json",self::PATH,$date->format("Y-m-d"));
        // There is already a file for this day
        if(file_exists($name)){
            return $this->loadFile($name);
        }

        // If it doesnt exists, request to API
        try {
            $response = $this->getClient()->get(
                sprintf("b[%d]den[%s]", $this->bankNumber, $date->format("Ymd"))
            );

        } catch (GuzzleException $e) {
            // If enabled, it will attempt to load last list from local storage
            if($canUseOlder){
                $lastFile = $this->getLastList();
                if(!!$lastFile){
                    return $lastFile;
                }
                $msg = "Unable to retrieve new list, unable to load older";
            }else{
                $msg = "Unable to retrieve new list";
            }

            throw new CurrencyRequestException($msg);
        }

        // Save to file
        $json = json_decode(($response->getBody()),true);

        $file = fopen($name,"w+");
        $status = fwrite($file,json_encode($json));
        fclose($file);

        // Unable to write?
        if(!$status){
            throw new IOException("Unable to write to file $name");
        }

        // Return retrieved data
        return $json;
    }

    /**
     * Returns last list from local storage
     *
     * @return array|null
     */
    public function getLastList(): ?array{
        $files = $this->getAllFiles();
        if(sizeof($files) == 0) return null;

        return json_decode($files[array_key_last($files)]->getContents(), true);
    }

    /**
     * Returns Client
     *
     * @return Client
     */
    private function getClient(): Client
    {
        if (!isset($this->client)) {
            $this->client = new Client(["base_uri" => self::API_BASE]);
        }
        return $this->client;
    }

    /**
     * Load data from specific file
     *
     * @param string $path
     * @return array
     */
    private function loadFile(string $path): array{
        $file = fopen($path,"r");
        $data = fread($file,filesize($path));
        fclose($file);
        return json_decode($data,true);
    }

}