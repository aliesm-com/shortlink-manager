<?php

namespace App\Models;

class Click
{
    /** @var int */
    public $id;

    /** @var int */
    public $link_id;

    /** @var string */
    public $clicked_at;

    /** @var string|null */
    public $ip_hash;

    /** @var string|null */
    public $user_agent;

    /** @var string|null */
    public $referer;

    /**
     * @param array $row
     * @return Click
     */
    public static function fromRow(array $row)
    {
        $click = new self();
        $click->id = (int) $row['id'];
        $click->link_id = (int) $row['link_id'];
        $click->clicked_at = $row['clicked_at'];
        $click->ip_hash = isset($row['ip_hash']) ? $row['ip_hash'] : null;
        $click->user_agent = isset($row['user_agent']) ? $row['user_agent'] : null;
        $click->referer = isset($row['referer']) ? $row['referer'] : null;

        return $click;
    }
}
