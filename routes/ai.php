<?php

use App\Mcp\Servers\KinholdServer;
use Laravel\Mcp\Facades\Mcp;

// OAuth discovery endpoints (.well-known) + dynamic client registration
Mcp::oauthRoutes('oauth');

// MCP endpoint — accepts Passport OAuth tokens AND Sanctum bearer tokens
Mcp::web('/mcp', KinholdServer::class)
    ->middleware(['auth:api,sanctum']);
