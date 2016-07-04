<?php
namespace App\Services;

use App\SuitableItineraries;
use Carbon\Carbon;
use GuzzleHttp\Client;

class RoutesService
{
    private $baseUri;
    private $apiKey;
    private $suitableItineraries;

    public function __construct($key = '')
    {
        $this->apiKey = $key;
        $this->baseUri = 'http://partners.api.skyscanner.net/apiservices/browseroutes/v1.0/GB/GBP/en-GB';
        $this->suitableItineraries = new SuitableItineraries(1);
    }

    public static function create($key)
    {
        return new RoutesService($key);
    }

    public function findSuitableRoutes($origin, $destination, Carbon $from, Carbon $to)
    {
        $client = new Client();
        $url = sprintf('%s/%s/%s/%s/%s?apiKey=%s', $this->baseUri, $origin, $destination, $from->format('Y-m-d'), $to->format('Y-m-d'), $this->apiKey);
        $response = $client->request('GET', $url);
        $json_results = json_decode((string) $response->getBody(), true);
        $quotes = collect($json_results['Quotes'])->sortBy('MinPrice');
        dd($quotes->all(), $json_results);
    }
}
