<?php

namespace App\Console\Commands;

use App\Services\LoginService;
use Illuminate\Console\Command;

class IbrohimCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ibrohim:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'My command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->loginPost();
    }

    public function loginPost()
    {
        $data = ['email' => 'ahmadjonovibrohim404@gmail.com', 'password' => 'Gaara6377'];
        (new LoginService())->loginPost($data);
        dd('hello');
    }
}
