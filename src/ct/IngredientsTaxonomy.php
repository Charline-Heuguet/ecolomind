<?php

// "Ingredients" taxonomy table file

namespace Ecolomind\ct;


class IngredientsTaxonomy 
{
    const CT_SLUG = "ingredients";

    public static function register()
    {
        register_taxonomy(
            self::CT_SLUG,
            ['tips', 'posts'],
            [
                'labels' => [
                    'name' => 'Ingrédients',
                    'new_item_name' => 'Ajouter un nouvel ingrédient'
                ],
                'public' => true,
                'show_in_rest' => true,
                
            ]
        );
    }
}