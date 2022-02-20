<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReadStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Outputs ticket stats';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $total = DB::table('tickets')->count();
        $unprocessed = DB::table('tickets')->where('status', '!=', true)->count();
        $last_processed = DB::table('tickets')->where('status', '=', true)->orderBy('updated_at', 'desc')->first();

        $lpv = (isset($last_processed->id)) ? $last_processed->updated_at : 'Never';

        $author = DB::selectOne(DB::raw('select author,  count(1) as `total` from tickets
group by author
order by count(1) desc'));

        $this->info('Total: '.$total);
        $this->info('Unprocessed: '.$unprocessed);
        $this->info('Last Processed: '.$lpv);
        $this->info('Autor: '.$author->author);
    }
}
