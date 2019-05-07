<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CoffeeDrop extends Model
{
     protected $fillable = ['postcode','open_Monday','open_Tuesday','open_Wednesday','open_Thursday','open_Friday','open_Saturday','open_Sunday','closed_Monday','closed_Tuesday','closed_Wednesday','closed_Thursday','closed_Friday','closed_Saturday','closed_Sunday'];
     protected $hidden = ['id', 'latitude', 'longitude', 'created_at', 'updated_at'];

}
