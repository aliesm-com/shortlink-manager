<?php

namespace App\Controllers;

use App\Helpers;
use App\Middleware\AuthMiddleware;

class AdminController extends BaseController
{
    private function requireAuth()
    {
        $auth = new AuthMiddleware($this->config);
        $auth->handle();
    }

    public function dashboard(array $params = [])
    {
        $this->requireAuth();

        $chartData = $this->stats->clicksPerDay(7);
        $topLink = $this->links->topLink();

        Helpers::render('admin/dashboard', [
            'config' => $this->config,
            'pageTitle' => 'داشبورد',
            'activeNav' => 'dashboard',
            'totalLinks' => $this->links->totalLinks(),
            'totalClicks' => $this->links->totalClicks(),
            'topLink' => $topLink,
            'chartData' => $chartData,
        ]);
    }

    public function links(array $params = [])
    {
        $this->requireAuth();

        Helpers::render('admin/links', [
            'config' => $this->config,
            'pageTitle' => 'مدیریت لینک‌ها',
            'activeNav' => 'links',
            'links' => $this->links->all(),
            'success' => Helpers::flash('success'),
            'error' => Helpers::flash('error'),
        ]);
    }

    public function createLink(array $params = [])
    {
        $this->requireAuth();

        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!Helpers::verifyCsrf($token)) {
            Helpers::flash('error', 'درخواست نامعتبر است.');
            Helpers::redirect(Helpers::url($this->config, 'admin/links'));
        }

        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';

        $result = $this->links->create($url, $title, $slug !== '' ? $slug : null);

        if ($result['success']) {
            Helpers::flash('success', 'لینک با موفقیت ساخته شد: ' . Helpers::shortUrl($this->config, $result['link']->slug));
        } else {
            Helpers::flash('error', $result['error']);
        }

        Helpers::redirect(Helpers::url($this->config, 'admin/links'));
    }

    public function toggleLink(array $params)
    {
        $this->requireAuth();

        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!Helpers::verifyCsrf($token)) {
            Helpers::flash('error', 'درخواست نامعتبر است.');
            Helpers::redirect(Helpers::url($this->config, 'admin/links'));
        }

        $id = isset($params['id']) ? (int) $params['id'] : 0;
        $link = $this->links->findById($id);

        if ($link !== null) {
            $this->links->setActive($id, !$link->is_active);
            Helpers::flash('success', 'وضعیت لینک به‌روزرسانی شد.');
        }

        Helpers::redirect(Helpers::url($this->config, 'admin/links'));
    }

    public function deleteLink(array $params)
    {
        $this->requireAuth();

        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!Helpers::verifyCsrf($token)) {
            Helpers::flash('error', 'درخواست نامعتبر است.');
            Helpers::redirect(Helpers::url($this->config, 'admin/links'));
        }

        $id = isset($params['id']) ? (int) $params['id'] : 0;
        $this->links->delete($id);
        Helpers::flash('success', 'لینک حذف شد.');

        Helpers::redirect(Helpers::url($this->config, 'admin/links'));
    }

    public function linkStats(array $params)
    {
        $this->requireAuth();

        $id = isset($params['id']) ? (int) $params['id'] : 0;
        $link = $this->links->findById($id);

        if ($link === null) {
            http_response_code(404);
            Helpers::render('errors/404', ['config' => $this->config]);
            return;
        }

        Helpers::render('admin/link-stats', [
            'config' => $this->config,
            'pageTitle' => 'آمار لینک',
            'activeNav' => 'links',
            'link' => $link,
            'dailyChart' => $this->stats->clicksPerDay(14, $id),
            'hourlyChart' => $this->stats->clicksPerHourToday($id),
            'recentClicks' => $this->stats->recentClicks($id, 20),
        ]);
    }
}
