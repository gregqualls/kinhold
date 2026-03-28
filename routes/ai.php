<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\KinholdServer;

Mcp::web('/mcp', KinholdServer::class)
    ->middleware(['auth:sanctum']);
