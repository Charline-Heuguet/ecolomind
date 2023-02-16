<?php

// 'User' role file

namespace Ecolomind\role;

class UserRole
{
    const ROLE_SLUG = "users";
    
    public static function register()
    {        
        $capabilities = [
            'delete_tip'        => true,
            'edit_tips'         => true,
            'edit_tip'          => true,
            'read_tip'          => true,
            'publish_tips'      => true,
            'edit_comment'      => true,
            'moderate_comments' => true,
            'read'              => true
        ];

        add_role(self::ROLE_SLUG, "Utilisateurs", $capabilities);
    }

    public static function unregister()
    {
        remove_role(self::ROLE_SLUG);
    }
}