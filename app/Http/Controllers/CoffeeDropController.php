<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CoffeeDrop;
use GuzzleHttp\Client;
class CoffeeDropController extends Controller
{

    public function find($postcode){
      //Uses getCoordinates to find latitude and longitude of a postcode
        $inputCoordinates =$this->getCoordinates($postcode);

        if(!is_array($inputCoordinates))
          return $inputCoordinates;
      //Uses updateCoordinates to check if there are any new coffeedrop locations and add their lat and lon to db
        $dbpostcodes =   $this->updateCoordinates();
        $distances = [];
        foreach ($dbpostcodes as $location) {
      //calculate the distances between the input and all db locations
          $distance = $this->getDistanceBetweenTwoPoints($inputCoordinates,array('latitude' => $location->latitude,'longitude' => $location->longitude));
          $distances[$location->postcode] = $distance;
        }
        //compares distances
        $closestCoffeeDrop = min(array_keys($distances, min($distances)));

        $closestCoffeeDropInfo = CoffeeDrop::where('postcode', $closestCoffeeDrop)->first();
        return response()->json(['information' => $closestCoffeeDropInfo]);
    }
    public function calculate(Request $request){
      //calculate toatl pods
      $amountofpods = $this->calculateAmount($request->all());
      $cashback = 0;
      //checks which formula to use
      if($amountofpods <= 50 ){
        foreach ($request->all() as $key => $value) {
          switch ($key) {
            case 'Ristretto':
              $cashback += $value*2;
              break;
            case 'Espresso':
              $cashback += $value*4;
              break;
            case 'Lungo':
              $cashback += $value*6;
              break;
          }
        }
      }
      if($amountofpods > 50 && $amountofpods<=500 ){
        foreach ($request->all() as $key => $value) {
          switch ($key) {
            case 'Ristretto':
              $cashback += $value*3;
              break;
            case 'Espresso':
              $cashback += $value*6;
              break;
            case 'Lungo':
              $cashback += $value*9;
              break;
          }
        }
      }
      if($amountofpods>500 ){
        foreach ($request->all() as $key => $value) {
          switch ($key) {
            case 'Ristretto':
              $cashback += $value*5;
              break;
            case 'Espresso':
              $cashback += $value*10;
              break;
            case 'Lungo':
              $cashback += $value*15;
              break;
          }
        }
      }
      return response()->json(['pounds' => $cashback/100]);
    }
    public function calculateAmount($input){
      $amountofpods = 0;
      foreach ($input as $key => $value) {
        $amountofpods += $value;
      }
        return $amountofpods;
    }
    public function updateCoordinates(){
      // Finds coffeedrops without lat and lon values
      $newCoffeeDrops= CoffeeDrop::select('postcode')->whereNull('latitude')->pluck('postcode');
      foreach ($newCoffeeDrops as $postcode) {
        $coordinates = $this->getCoordinates($postcode);

      //adds lat and lon value based on results from http://postcodes.io/
        CoffeeDrop::where('postcode', $postcode)->update(array('latitude'=>$coordinates['latitude'],'longitude'=>$coordinates['longitude']));

      }

      return CoffeeDrop::select('postcode','latitude','longitude')->get();;
    }
    public function create(Request $request){
      //translates request body to db column names
       $data = [];
        foreach ($request->all() as $key => $value) {
          if ($key=='postcode')
            $data['postcode'] = $value;
          if($key=='opening_times'){
            foreach ($value[0] as $openkey => $openvalue) {
              $newkey = 'open_'.ucfirst($openkey);
              $data[$newkey] = $openvalue;
            }
          }
          if($key=='closing_times'){
            foreach ($value[0] as $closekey => $closevalue) {
              $newkey = 'closed_'.ucfirst($closekey);
              $data[$newkey] = $closevalue;
            }
          }
        }
        //creates new coffeedrop
        $coffeeDrop = CoffeeDrop::create($data);
        return response()->json($coffeeDrop, 201);

    }
    public function getCoordinates($postcode){
      $url = 'https://api.postcodes.io/postcodes/'.$postcode;

      $headers = get_headers($url);
      $statuscode = substr($headers[0], 9, 3);


      if ($statuscode != 200) {
        return "Invalid postcode" . $statuscode;
      }
      else {
        $json = json_decode(file_get_contents($url), true);
        $resultBody = $json['result'];
        $postcodeLat = $resultBody['latitude'];
        $postcodeLon = $resultBody['longitude'];
      }
      $coordinates = array('latitude' => $postcodeLat, 'longitude' => $postcodeLon );
      return $coordinates;
    }
    public function getDistanceBetweenTwoPoints($point1 , $point2){
      // array of lat-long i.e  $point1 = [lat,long]
      $earthRadius = 6371;  // earth radius in km
      $point1Lat = $point1['latitude'];
      $point2Lat =$point2['latitude'];
      $deltaLat = deg2rad($point2Lat - $point1Lat);
      $point1Long =$point1['longitude'];
      $point2Long =$point2['longitude'];
      $deltaLong = deg2rad($point2Long - $point1Long);
      $a = sin($deltaLat/2) * sin($deltaLat/2) + cos(deg2rad($point1Lat)) * cos(deg2rad($point2Lat)) * sin($deltaLong/2) * sin($deltaLong/2);
      $c = 2 * atan2(sqrt($a), sqrt(1-$a));

      $distance = $earthRadius * $c;
      return $distance;    // in km
  }
}
