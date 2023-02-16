<?php

// 'Moderator' role file

namespace Ecolomind\role;

class ModeratorRole

{ 
  const ROLE_SLUG = "moderator";

  public static function register(){
    $capabilities = [
        'moderate_comments'     => true,
        'delete_tips'           => true,
        'edit_tips'             => true,
        'publish_tips'          => true,
        'manage_categories'     => true,
        'manage_links'          => true,
        'edit_others_tips'      => true,
        'delete_others_tips'    => true,
        'edit_published_tips'   => true,
        'upload_files'          => true,
        'delete_published_tips' => true,
        'read'                  => true,
    ];

    add_role(self::ROLE_SLUG, "Modérateur", $capabilities);

  }

  public static function unregister()
  {
      remove_role(self::ROLE_SLUG);
  }

}