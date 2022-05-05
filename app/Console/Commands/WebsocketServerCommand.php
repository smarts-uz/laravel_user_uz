<?php

namespace App\Console\Commands;

use BeyondCode\LaravelWebSockets\Console\StartWebSocketServer;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use BeyondCode\LaravelWebSockets\Server\WebSocketServerFactory;
use React\EventLoop\Factory as LoopFactory;

class WebsocketServerCommand extends StartWebSocketServer
{

    /**
     * @var int
     */
    private $default_port;

    public function __construct()
    {
        parent::__construct();

        $this->loop = LoopFactory::create();
    }

    public function handle()
    {
        $this->default_port = config('broadcasting.connections.pusher.options.port');
        $this
            ->configureStatisticsLogger()
            ->configureHttpLogger()
            ->configureMessageLogger()
            ->configureConnectionLogger()
            ->configureRestartTimer()
            ->registerEchoRoutes()
            ->registerCustomRoutes()
            ->startWebSocketServer();
    }

    protected function startWebSocketServer()
    {
        $this->info("Starting the WebSocket server on port {$this->default_port}...");

        $routes = WebSocketsRouter::getRoutes();

        /* 🛰 Start the server 🛰  */
        (new WebSocketServerFactory())
            ->setLoop($this->loop)
            ->useRoutes($routes)
            ->setHost($this->option('host'))
            ->setPort($this->default_port)
            ->setConsoleOutput($this->output)
            ->createServer()
            ->run();
    }
}
