<?php
namespace App;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class SuitableRoutes implements \IteratorAggregate
{
    /** @var  Collection */
    private $carriers;

    /** @var Collection */
    private $places;

    /** @var Collection */
    private $routes;

    private $name;

    public function __construct()
    {
        $this->carriers = new Collection();
        $this->places = new Collection();
        $this->routes = new Collection();
        $this->name = '';
    }

    public function __toString()
    {
        return $this->name;
    }

    public static function parse($data)
    {
        return (new SuitableRoutes())->parseAndAdd($data);
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function parseAndAdd($data)
    {
        return $this
            ->parsePlaces($data['Places'])
            ->parseCarriers($data['Carriers'])
            ->parseQuotes($data['Quotes'])
        ;
    }

    /**
     * @param $data
     * @return $this
     */
    protected function parseQuotes($data)
    {
        $quotes = collect($data)->sortBy('MinPrice');
        $self = $this;
        $quotes->map(function($quote) use($self){
            $outbound = $quote['OutboundLeg'];
            $inbound = $quote['InboundLeg'];
            return [
                'price' => $quote['MinPrice'],
                'direct' => $quote['Direct'],
                'daysQuoted' => (new Carbon($quote['QuoteDateTime']))->diffForHumans(Carbon::now()),
                'outbound' => [
                    'carrier' => $self->getCarriers()->get(collect($outbound['CarrierIds'])->first()),
                    'origin' => $self->getPlaces()->get($outbound['OriginId']),
                    'destination' => $self->getPlaces()->get($outbound['DestinationId']),
                    'departureDate' => (new Carbon($outbound['DepartureDate']))->format('d/m/Y')
                ],
                'inbound' => [
                    'carrier' => $self->getCarriers()->get(collect($inbound['CarrierIds'])->first()),
                    'origin' => $self->getPlaces()->get($inbound['OriginId']),
                    'destination' => $self->getPlaces()->get($inbound['DestinationId']),
                    'departureDate' => (new Carbon($inbound['DepartureDate']))->format('d/m/Y')
                ]
            ];
        })->each(function($quote){
            $this->getRoutes()->push($quote);
        });

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    protected function parsePlaces($data)
    {
        collect($data)->groupBy('PlaceId')->map(function($places){
            return collect($places)->pluck('Name')->first();
        })->each(function($place, $key) {
            $this->getPlaces()->put($key, $place);
        });

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    protected function parseCarriers($data)
    {
        collect($data)->groupBy('CarrierId')->map(function($carriers){
            return collect($carriers)->pluck('Name')->first();
        })->each(function($carrier, $key) {
            $this->getCarriers()->put($key, $carrier);
        });

        return $this;
    }

    public function getIterator() {
        return $this->routes->getIterator();
    }

//    public function addIfBetter(Route $itinerary)
//    {
//        if(count($this->itineraries) < $this->maxResults) {
//            $this->itineraries[] = $itinerary;
//            $this->sort();
//            return $this;
//        }
//        // we check if it's better than last one
//        $lastOne = $this->itineraries[$this->maxResults - 1];
//        if($itinerary->nicest($lastOne)) {
//            $this->itineraries[$this->maxResults - 1] = $itinerary;
//            $this->sort();
//        }
//
//        return $this;
//    }

    private function sort()
    {
//        usort($this->itineraries, function($a, $b){
//            return $a->agony() > $b->agony();
//        });
    }

    /**
     * @return Collection
     */
    public function getCarriers()
    {
        return $this->carriers;
    }

    /**
     * @param Collection $carriers
     * @return $this
     */
    public function setCarriers($carriers)
    {
        $this->carriers = $carriers;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param Collection $places
     * @return $this
     */
    public function setPlaces($places)
    {
        $this->places = $places;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param Collection $routes
     * @return $this
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
        return $this;
    }
}
