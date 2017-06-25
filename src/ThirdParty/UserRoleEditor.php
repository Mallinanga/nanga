<?php

namespace Nanga\ThirdParty;

class UserRoleEditor
{

    public static function init()
    {
        if ( ! class_exists('User_Role_Editor')) {
            return;
        }
        remove_action('edit_user_profile', [$GLOBALS['user_role_editor'], 'edit_user_profile']);
    }
}
