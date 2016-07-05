<?php
namespace App\Services;

use App\SuitableRoutes;
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
        $this->baseUri = 'http://partners.api.skyscanner.net/apiservices/browseroutes/v1.0/GB/EUR/en-GB';
        $this->suitableRoutes = new SuitableRoutes();
    }

    public static function create($key)
    {
        return new RoutesService($key);
    }

    public function findSuitableRoutes($origin, $destination, $startDate, $nights, $dayOfWeek, $weeksFlexibility, $ip)
    {
        $isStartingDay = sprintf('is%s', $dayOfWeek);
        $start = (new Carbon($startDate));
        if(!$start->$isStartingDay()) $start = $start->modify('next ' . $dayOfWeek);
        $end = $start->copy()->addWeeks($weeksFlexibility);
        $dates = new \DatePeriod($start, new \DateInterval( 'P1W'), $end);
        foreach($dates as $date) {
            $currentStart = Carbon::instance($date);
            $currentEnd = $currentStart->copy()->addDays($nights);
            $this->findRoutesForDates($origin, $destination, $currentStart, $currentEnd, $ip);
        }

        $name = "Available {$nights} nights trip from {$origin} to {$destination}, starting on {$startDate}, for the following {$weeksFlexibility} {$dayOfWeek}s";

        return $this->suitableRoutes->setName($name);
    }

    private function findRoutesForDates($origin, $destination, Carbon $from, Carbon $to, $ip)
    {
        $client = new Client();
        $url = sprintf('%s/%s/%s/%s/%s?apiKey=%s', $this->baseUri, $origin, $destination, $from->format('Y-m-d'), $to->format('Y-m-d'), $this->apiKey);
        $response = $client->request('GET', $url, ['headers' => ['Accept' => 'application/json', 'X-Forwarded-For' => $ip]]);
        $jsonResults = json_decode((string) $response->getBody(), true);

        $this->suitableRoutes->parseAndAdd($jsonResults);
    }
}
