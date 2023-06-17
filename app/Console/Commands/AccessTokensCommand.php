<?php

namespace App\Console\Commands;

use App\Services\User\UserService;
use Illuminate\Console\Command;

class AccessTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AccessTokensCommand:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return array
     */
    public function handle()
    {
        return (new UserService)->access_tokens();
    }
}
