<?php

// Plugin file with hooks, init and activation methods

namespace Ecolomind\security;

use Ecolomind\role\UserRole;

class AccessBO
{
    // We create a function that will check if the roles are allowed to access the BO
    public static function checkAccess()
    {
        // We define the roles that will not have access to the BO
        $noAccessRoles = 
        [
            UserRole::ROLE_SLUG,
            'subscriber',
            'author',
            'contributor',
        ];

        // wp_get_current_user() defines the current user (logged-in user). If there is no logged-in user, the current user will be set to 0 (so no permissions)
        $current_user = wp_get_current_user();

        // So if the ID === 0 (regarding the previous function)
        if($current_user->ID === 0){
            // no permissions : no need to continue
            return;
        }

        // a user can have multiple roles.
        // roles[] is native to WP and comes from WP_User
        // 0 will get the first role of the current user
        $current_user_role = $current_user->roles[0];

        // if the current request is for an administrative interface page (it does not check if the current user is an admin)
        // and if the current visitor is a logged in user
        // and if the role of the current user exists in $noAccessRoles
        if(is_admin() && is_user_logged_in() && in_array($current_user_role, $noAccessRoles)){
            // We redirect to the home page with an 403 error
            wp_redirect(home_url());
            exit;
        }
    }
}