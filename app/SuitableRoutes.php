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

    private $placesCodes;

    /** @var Collection */
    private $routes;

    private $name;

    public function __construct()
    {
        $this->carriers = new Collection();
        $this->places = new Collection();
        $this->placesCodes = new Collection();
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
                ],
                'referralUrl' => $self->createReferralLink($outbound['OriginId'], $outbound['DestinationId'], $outbound['DepartureDate'], $inbound['DepartureDate'])
            ];
        })->each(function($quote){
            $this->getRoutes()->push($quote);
        });

        return $this;
    }

    public function createReferralLink($origin, $destination, $from, $to)
    {
        $url = 'http://partners.api.skyscanner.net/apiservices/referral/v1.0/GB/EUR/en-GB/%s/%s/%s/%s?apiKey=%s';

        return sprintf(
            $url,
            $this->getPlacesCodes()->get($origin),
            $this->getPlacesCodes()->get($destination),
            (new Carbon($from))->format('Y-m-d'),
            (new Carbon($to))->format('Y-m-d'),
            env('SKYSCANNER_SHORT_KEY')
        );
    }

    public function getPlacesCodes()
    {
        return $this->placesCodes;
    }

    /**
     * @param $data
     * @return $this
     */
    protected function parsePlaces($data)
    {
        collect($data)->groupBy('PlaceId')->map(function($place){
            return collect(collect($place)->first());
        })->each(function($place, $key) {
            $this->getPlaces()->put($key, $place->get('Name'));
            $this->placesCodes->put($key, $place->get('SkyscannerCode'));
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

    public function serialize()
    {
        return [
            'name' => $this->name,
            'carriers' => $this->getCarriers()->all(),
            'routes' => $this->getRoutes()->sortBy('price')->all(),
            'priceRange' => [
                'min' => $this->getRoutes()->pluck('price')->min(),
                'max' => $this->getRoutes()->pluck('price')->max()
            ]
        ];
    }
}
