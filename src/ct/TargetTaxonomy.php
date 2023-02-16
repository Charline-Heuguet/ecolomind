<?php

// "target" taxonomy table file

namespace Ecolomind\ct;


class TargetTaxonomy
{
    const CT_SLUG = "target";

    public static function register()
    {
        register_taxonomy(
            self::CT_SLUG,
            ['tips'],
            [
                'labels' => [
                    'name' => 'Cible',
                    'new_item_name' => 'Définir une cible'
                ],
                'public' => true,
                'show_in_rest' => true,
                'hierarchical' => true
            ]
        );
    }
}