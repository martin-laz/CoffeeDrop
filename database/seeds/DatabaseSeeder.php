<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    protected $table;
    protected $filename;
    public function __construct(){

      $this->table = 'coffee_drops';
      $this->filename = base_path('database/seeds/csvs/coffee_drops.csv');

  }
    public function run()
    {
      DB::table($this->table)->delete();
      $seedData = $this->seedFromCSV($this->filename, ',');
      DB::table($this->table)->insert($seedData);
    }
    private function seedFromCSV($filename, $delimitor = ",")
      {
          if(!file_exists($filename) || !is_readable($filename))
          {
              return FALSE;
          }

          $header = NULL;
          $data = array();

          if(($handle = fopen($filename, 'r')) !== FALSE)
          {
              while(($row = fgetcsv($handle, 1000, $delimitor)) !== FALSE)
              {
                  if(!$header) {
                      $header = $row;
                  } else {
                      $data[] = array_combine($header, $row);
                  }
              }
              fclose($handle);
          }

          return $data;
      }

}


  
