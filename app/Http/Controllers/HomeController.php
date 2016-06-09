<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function welcome()
    {
        $cities = $this->getAvailableCities();
        $days = $this->getDays();

        return view('welcome', ['cities' => $cities, 'days' => $days]);
    }

    public function findFlights(Request $request)
    {
        // use chace to store results

        return view('results');
    }

    private function getDays()
    {
      return [
        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
      ];
    }

    private function getAvailableCities()
    {
        return [
          'BCN' => 'Barcelona',
          'MAD' => 'Madrid',
          'PAR' => 'Paris',
          'LON' => 'London',
          'DUB' => 'Dublin',
          'VLC' => 'Valencia',
          'ATH' => 'Athens',
          'ROM' => 'Rome',
          'MLA' => 'Malta (Luqa)',
          'MIL' => 'Milan',
          'CTA' => 'Catania'
        ];
    }
}
