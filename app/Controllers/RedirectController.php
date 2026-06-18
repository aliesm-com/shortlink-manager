<?php

namespace App\Controllers;

use App\Helpers;

class RedirectController extends BaseController
{
    public function show(array $params)
    {
        $slug = isset($params['slug']) ? $params['slug'] : '';

        if ($slug === '' || $slug === 'assets' || strpos($slug, 'admin') === 0) {
            http_response_code(404);
            Helpers::render('errors/404', ['config' => $this->config]);
            return;
        }

        $link = $this->links->findBySlug($slug);

        if ($link === null || !$link->is_active) {
            http_response_code(404);
            Helpers::render('errors/404', ['config' => $this->config]);
            return;
        }

        $this->links->recordClick($link->id, $this->config);

        $delay = isset($this->config['redirect_delay']) ? (int) $this->config['redirect_delay'] : 10;

        Helpers::render('redirect', [
            'config' => $this->config,
            'link' => $link,
            'delay' => $delay,
        ]);
    }
}
