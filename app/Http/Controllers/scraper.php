<?php

namespace App\Http\Controllers;
use Goutte\Client;
use Illuminate\Http\Request;
use App\Models\Event_map;


class scraper extends Controller
{
 
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
                        
            //Find cords from data_link
            $cord_crawler = $client->request('GET', $data_link);
            //Find cords from script tag
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
            //Remove empty values
            $cords = array_values(array_filter($cords))[0];

            //Find source link
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
