<?php

namespace App\Http\Controllers;
use Goutte\Client;
use Illuminate\Http\Request;
use App\Models\Event_map;


class scraper extends Controller
{
    //Constructor
    public function __construct()
    {
     
    }


    //Metodo para ejecutar desde un comando de laravel (php artisan scraper:liveumap)
    public function liveumap()
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://israelpalestine.liveuamap.com/');

        $info = $crawler->filter('.event')->each(function ($node) use ($client) {

            //Find info from scroller class container
            $title = $node->filter('.title')->text();
            $data_id = $node->filter('.event')->attr('data-id');
            $data_link = $node->filter('.event')->attr('data-link');
            // // Get link from source
            // // <div class="top-right"><a rel="nofollow noopener" class="source-link" href="https://twitter.com/ignis_fatum/status/1716113505655324941" target="_blank"><span class="source"></span>source</a></div>
            // $source_link = $node->filter('.top-right')->filter('a')->attr('href');
            
            // dd($source_link);        
            
            $cord_crawler = $client->request('GET', $data_link);

            $cords = $cord_crawler->filter('script')->each(function ($node) {

                if (strpos($node->text(), 'zoom=14') !== false) {
                    $script = $node->text();
                    $script = str_replace('$(document).ready(function(){', '', $script);
                    $script = str_replace('});', '', $script);
                    
                    $lat = explode('lat=', $script);
                    $lat = explode(';', $lat[1]);

                    $lng = explode('lng=', $script);
                    $lng = explode(';', $lng[1]);

                    return [
                        'lat' => $lat[0],
                        'lng' => $lng[0],
                    ];
                    
                }
            
            });
            $cords = array_values(array_filter($cords))[0];

            $get_source = $cord_crawler->filter('.head_popup')->each(function ($node) {
                $source = $node->filter('.source-link')->attr('href');
                return $source;
            });
            $source = array_values(array_filter($get_source))[0];

            //Save data to database
            $event_map = new Event_map;
            $event_map->data_id = $data_id;
            $event_map->title = $title;
            $event_map->data_link = $data_link;
            $event_map->lat = $cords['lat'];
            $event_map->lng = $cords['lng'];
            $event_map->source = $source;
            //create or update data
            $event_map->updateOrCreate(
                ['data_id' => $data_id],
                [
                    'title' => $title,
                    'data_link' => $data_link,
                    'lat' => $cords['lat'],
                    'lng' => $cords['lng'],
                    'source' => $source,
                ]
            );
            
            $event_map->save();
            $data = array(
                'title' => substr($title, 0, 15).'...',
                'data_id' => $data_id,
                'data_link' => $data_link,
                'lat' => $cords['lat'],
                'lng' => $cords['lng'],
                'source' => $source,
            );

            return $data;

        });
        $info = array_values(array_filter($info));

        return $info;
        
    }
    
}
