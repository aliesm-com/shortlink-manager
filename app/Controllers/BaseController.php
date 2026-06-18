<?php

namespace App\Controllers;

use App\Services\Database;
use App\Services\LinkService;
use App\Services\StatsService;

abstract class BaseController
{
    /** @var array */
    protected $config;

    /** @var LinkService */
    protected $links;

    /** @var StatsService */
    protected $stats;

    public function __construct(array $config)
    {
        $this->config = $config;
        $db = new Database($config);
        $db->migrate();
        $pdo = $db->connection();
        $this->links = new LinkService($pdo);
        $this->stats = new StatsService($pdo, $db->dialect());
    }
}
