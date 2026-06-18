<?php

namespace App\Services;

use App\Helpers;
use App\Models\Link;
use PDO;

class LinkService
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Link[]
     */
    public function all()
    {
        $stmt = $this->pdo->query('
            SELECT l.*, COUNT(c.id) AS click_count
            FROM links l
            LEFT JOIN clicks c ON c.link_id = l.id
            GROUP BY l.id
            ORDER BY l.created_at DESC
        ');

        $links = [];
        while ($row = $stmt->fetch()) {
            $links[] = Link::fromRow($row);
        }

        return $links;
    }

    /**
     * @param int $id
     * @return Link|null
     */
    public function findById($id)
    {
        $stmt = $this->pdo->prepare('
            SELECT l.*, COUNT(c.id) AS click_count
            FROM links l
            LEFT JOIN clicks c ON c.link_id = l.id
            WHERE l.id = :id
            GROUP BY l.id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Link::fromRow($row) : null;
    }

    /**
     * @param string $slug
     * @return Link|null
     */
    public function findBySlug($slug)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM links WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        return $row ? Link::fromRow($row) : null;
    }

    /**
     * @param string $url
     * @param string|null $title
     * @param string|null $customSlug
     * @return array{success: bool, link?: Link, error?: string}
     */
    public function create($url, $title = null, $customSlug = null)
    {
        if (!Helpers::isValidUrl($url)) {
            return ['success' => false, 'error' => 'آدرس URL معتبر نیست.'];
        }

        $slug = $customSlug !== null && $customSlug !== '' ? $customSlug : $this->generateSlug();

        if ($customSlug !== null && $customSlug !== '' && !Helpers::isValidSlug($customSlug)) {
            return ['success' => false, 'error' => 'کد لینک فقط می‌تواند شامل حروف، اعداد، خط تیره و زیرخط باشد.'];
        }

        if ($this->findBySlug($slug) !== null) {
            return ['success' => false, 'error' => 'این کد لینک قبلاً استفاده شده است.'];
        }

        $stmt = $this->pdo->prepare('
            INSERT INTO links (slug, original_url, title, is_active, created_at)
            VALUES (:slug, :url, :title, 1, :created_at)
        ');

        $stmt->execute([
            'slug' => $slug,
            'url' => $url,
            'title' => $title !== '' ? $title : null,
            'created_at' => gmdate('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'link' => $this->findById((int) $this->pdo->lastInsertId())];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM links WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * @param int $id
     * @param int $active
     * @return bool
     */
    public function setActive($id, $active)
    {
        $stmt = $this->pdo->prepare('UPDATE links SET is_active = :active WHERE id = :id');
        return $stmt->execute(['id' => $id, 'active' => $active ? 1 : 0]);
    }

    /**
     * @param int $linkId
     * @param array $config
     * @return void
     */
    public function recordClick($linkId, array $config)
    {
        $ip = Helpers::clientIp();
        $salt = isset($config['ip_salt']) ? $config['ip_salt'] : '';
        $ipHash = $ip !== '' ? hash('sha256', $ip . $salt) : null;

        $stmt = $this->pdo->prepare('
            INSERT INTO clicks (link_id, clicked_at, ip_hash, user_agent, referer)
            VALUES (:link_id, :clicked_at, :ip_hash, :user_agent, :referer)
        ');

        $stmt->execute([
            'link_id' => $linkId,
            'clicked_at' => gmdate('Y-m-d H:i:s'),
            'ip_hash' => $ipHash,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 512) : null,
            'referer' => isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 512) : null,
        ]);
    }

    /**
     * @return int
     */
    public function totalLinks()
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM links')->fetchColumn();
    }

    /**
     * @return int
     */
    public function totalClicks()
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM clicks')->fetchColumn();
    }

    /**
     * @return Link|null
     */
    public function topLink()
    {
        $stmt = $this->pdo->query('
            SELECT l.*, COUNT(c.id) AS click_count
            FROM links l
            LEFT JOIN clicks c ON c.link_id = l.id
            GROUP BY l.id
            ORDER BY click_count DESC
            LIMIT 1
        ');
        $row = $stmt->fetch();

        return $row ? Link::fromRow($row) : null;
    }

    /**
     * @return string
     */
    private function generateSlug()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = 7;

        do {
            $slug = '';
            for ($i = 0; $i < $length; $i++) {
                $slug .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while ($this->findBySlug($slug) !== null);

        return $slug;
    }
}
