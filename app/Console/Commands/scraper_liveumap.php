<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Http\Controllers\scraper;

class scraper_liveumap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraper:liveuamap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scraper for liveuamap.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scraper = new scraper();
        $info = $scraper->liveumap();

        $this->info('Scraper for https://israelpalestine.liveuamap.com/');
        // $this->info('-------------------------');
        $this->info('Total: ' . count($info));
        // $this->info('-------------------------');
        //Make a table with the info
        $headers = ['Title', 'Data ID', 'Data Link', 'Lat', 'Lng'];
        $this->table($headers, $info);

    }
}
