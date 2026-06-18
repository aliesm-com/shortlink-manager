<?php

namespace App\Services;

use App\Models\Click;
use PDO;

class StatsService
{
    /** @var PDO */
    private $pdo;

    /** @var SqlDialect */
    private $sql;

    public function __construct(PDO $pdo, SqlDialect $sql)
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
    }

    /**
     * @param int $days
     * @param int|null $linkId
     * @return array<int, array{date: string, count: int}>
     */
    public function clicksPerDay($days = 7, $linkId = null)
    {
        $days = max(1, (int) $days);
        $result = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $result[$date] = ['date' => $date, 'count' => 0];
        }

        $dayExpr = $this->sql->dateExpr('clicked_at');
        $since = $this->sql->sinceDaysCondition('clicked_at', $days);

        if ($linkId !== null) {
            $stmt = $this->pdo->prepare("
                SELECT $dayExpr AS day, COUNT(*) AS count
                FROM clicks
                WHERE link_id = :link_id
                  AND $since
                GROUP BY day
            ");
            $stmt->execute(['link_id' => $linkId]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT $dayExpr AS day, COUNT(*) AS count
                FROM clicks
                WHERE $since
                GROUP BY day
            ");
            $stmt->execute();
        }

        while ($row = $stmt->fetch()) {
            if (isset($result[$row['day']])) {
                $result[$row['day']]['count'] = (int) $row['count'];
            }
        }

        return array_values($result);
    }

    /**
     * @param int $linkId
     * @return array<int, array{hour: string, count: int}>
     */
    public function clicksPerHourToday($linkId)
    {
        $result = [];

        for ($h = 0; $h < 24; $h++) {
            $label = sprintf('%02d:00', $h);
            $result[$label] = ['hour' => $label, 'count' => 0];
        }

        $hourExpr = $this->sql->hourExpr('clicked_at');
        $today = $this->sql->isTodayCondition('clicked_at');

        $stmt = $this->pdo->prepare("
            SELECT $hourExpr AS hour_label, COUNT(*) AS count
            FROM clicks
            WHERE link_id = :link_id
              AND $today
            GROUP BY hour_label
        ");
        $stmt->execute(['link_id' => $linkId]);

        while ($row = $stmt->fetch()) {
            if (isset($result[$row['hour_label']])) {
                $result[$row['hour_label']]['count'] = (int) $row['count'];
            }
        }

        return array_values($result);
    }

    /**
     * @param int $linkId
     * @param int $limit
     * @return Click[]
     */
    public function recentClicks($linkId, $limit = 20)
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM clicks
            WHERE link_id = :link_id
            ORDER BY clicked_at DESC
            LIMIT :limit
        ');
        $stmt->bindValue('link_id', $linkId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $clicks = [];
        while ($row = $stmt->fetch()) {
            $clicks[] = Click::fromRow($row);
        }

        return $clicks;
    }
}
