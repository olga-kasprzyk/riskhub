<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Database\Factories\UserFactory;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Nette\Utils\Random;

class GenerateTicketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:ticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a dummy ticket';

    protected array $authors = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        while(count($this->authors) < 10){
            $user = (new \Database\Factories\UserFactory)->definition();
            array_push($this->authors, $user);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $author = Arr::random($this->authors, 1);

        $ticket = Ticket::create([
            'subject' => 'Subject '.date('H:i:s'),
            'content' => 'some content',
            'author' => $author[0]['name'],
            'email' => $author[0]['email']
        ]);

        $this->info('Dummy Ticket generated');
    }
}
