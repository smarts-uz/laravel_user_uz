<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;

class TaskCreateNotifCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "TaskCreateNotifCommand:run {--user_id= : task create qiladigan user id} {--id= : create task id} {--name= : task nomi} {--category_id= : child category id} {--title= : task title} {--body= : task body}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TaskCreateNotifCommand CMD';

    /**
     * Execute the console command.
     *
     * @return JsonResponse
     */
    public function handle()
    {
        $notificationService = new NotificationService();
        $user_id = $this->option("user_id");
        $task_id = $this->option("id");
        $task_name = $this->option("name");
        $task_category_id = $this->option("category_id");
        $title = $this->option("title");
        $body = $this->option("body");

        return $notificationService->task_create_notification($user_id, $task_id, $task_name, $task_category_id, $title, $body);
    }
}
