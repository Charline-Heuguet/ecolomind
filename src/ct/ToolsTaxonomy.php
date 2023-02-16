<?php

// 'tools" taxonomy table file

namespace Ecolomind\ct;
use Ecolomind\cpt\TipsPostType;


class ToolsTaxonomy 
{
    const CT_SLUG = "tools";

    public static function register()
    {
        register_taxonomy(
            self::CT_SLUG,
            ['tips'],
            [
                'labels' => [
                    'name' => 'Ustensiles',
                    'new_item_name' => 'Ajouter un nouvel ustensile'
                ],
                'public' => true,
                'show_in_rest' => true
            ]
        );
    }
}