<?php

namespace App\Models;

class Link
{
    /** @var int */
    public $id;

    /** @var string */
    public $slug;

    /** @var string */
    public $original_url;

    /** @var string|null */
    public $title;

    /** @var int */
    public $is_active;

    /** @var string */
    public $created_at;

    /** @var int */
    public $click_count = 0;

    /**
     * @param array $row
     * @return Link
     */
    public static function fromRow(array $row)
    {
        $link = new self();
        $link->id = (int) $row['id'];
        $link->slug = $row['slug'];
        $link->original_url = $row['original_url'];
        $link->title = isset($row['title']) ? $row['title'] : null;
        $link->is_active = (int) $row['is_active'];
        $link->created_at = $row['created_at'];

        if (isset($row['click_count'])) {
            $link->click_count = (int) $row['click_count'];
        }

        return $link;
    }
}
