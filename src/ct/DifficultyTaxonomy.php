<?php

// 'difficulty' taxonomy table file

namespace Ecolomind\ct;


class DifficultyTaxonomy 
{
    const CT_SLUG = "difficulty";

    public static function register()
    {
        register_taxonomy(
            self::CT_SLUG,
            ['tips'], //On appelle la constante dans le CPT pour récupérer le slug. En cas de modif du slug, pas besoin de modifier le CT associé
            [
                'labels' => [
                    'name' => 'Difficulté',
                    'new_item_name' => 'Ajouter une nouvelle difficulté'
                ],
                'public' => true,
                'show_in_rest' => true,
                'hierarchical' => true,
                
                'capabilities'=>[
                    'manage_terms'=> true,
                    'edit_terms'=>true,
                    'delete_terms'=>false,
                    'assign_terms'=> true
                ]
                    
                
            ]
        );
    }
}