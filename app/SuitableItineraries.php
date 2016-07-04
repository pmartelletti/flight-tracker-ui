<?php
namespace App;

use App\Itinerary;

class SuitableItineraries implements \IteratorAggregate
{
    private $itineraries;
    private $maxResults;

    public function __construct($maxResults = 20)
    {
        $this->itineraries = [];
        $this->maxResults = $maxResults;
    }

    public function getIterator() {
        return new \ArrayIterator($this->itineraries);
    }

    public function addIfBetter(Itinerary $itinerary)
    {
        if(count($this->itineraries) < $this->maxResults) {
            $this->itineraries[] = $itinerary;
            $this->sort();
            return $this;
        }
        // we check if it's better than last one
        $lastOne = $this->itineraries[$this->maxResults - 1];
        if($itinerary->nicest($lastOne)) {
            $this->itineraries[$this->maxResults - 1] = $itinerary;
            $this->sort();
        }

        return $this;
    }

    private function sort()
    {
        usort($this->itineraries, function($a, $b){
            return $a->agony() > $b->agony();
        });
    }
}
