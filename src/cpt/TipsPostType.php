<?php


namespace Ecolomind\cpt;

class TipsPostType {

    const CPT_SLUG = "tips";

    const CAPABILITIES = [
        'edit_posts' => 'edit_tips',
        'publish_posts' => 'publish_tips',
        'edit_post' => 'edit_tip',
        'read_post' => 'read_tip',
        'delete_post' => 'delete_tip',
        'edit_others_posts' => 'edit_others_tips',
        'delete_others_posts' =>  'delete_others_tips',
        'delete_published_posts' => 'delete_published_tips',
      ];


    public static function register(){
        
        register_post_type(self::CPT_SLUG, [
            'labels' => [ 
              'name' => 'Astuces',
              'all_items' => 'Toutes les astuces',
              'singular_name' => 'Astuce',
              'add_new_item' => 'Créer une astuce',
              'edit_name' => 'Modifier une astuce',
              'menu_name' => 'Astuces' //Affichage dans le menu du BO
            ],
            'public' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'supports' => [ // features de ce CPT
                'title', // titre
                'editor', 
                'author', // les posts de ce CPT sont liés à un Auteur
                'thumbnail', // active les featured image
                'revisions', // active l'historique de versions
                'custom-fields',
                'tags',
                'excerpt',
                'comments' // Nos astuces pourront être commentées par les utilisateurs
            ],
            'capabilities' => self::CAPABILITIES,
            'map_meta_cap' => true,
            'menu_position' => 1, // position dans le BO de WP
            'menu_icon' => 'dashicons-book-alt' 
            
        ]);  
    }
    
    public static function addCapsToAdmin(){
        $customerRole = get_role("administrator");
    
        foreach(self::CAPABILITIES as $caps){
          $customerRole->add_cap($caps);
        }
    }
    
    public static function removeCapsToAdmin(){
        $customerRole = get_role("administrator");
    
        foreach(self::CAPABILITIES as $caps){
          $customerRole->remove_cap($caps);
        }
    }

}


?>