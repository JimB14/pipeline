<?php

namespace App\Controllers;

use \Core\View;
use \App\Mail;
use \App\Models\User;
use \App\Models\Log;


/**
 * Logout controller
 *
 * PHP version 7.0
 */
class Logout extends \Core\Controller
{

    public function indexAction()
    {
        // get user
        $user = User::getUser($_SESSION['user_id']);

        // get IP Address
        $ip = $_SERVER['REMOTE_ADDR'];

        // store login data in log table
        $log_result = Log::storeLogin($user, $ip, $action='logout');

        // db insertion successful
        if ($log_result)
        {
            unset($_SESSION['user']);
            unset($_SESSION['loggedIn']);
            unset($_SESSION['user_id']);
            unset($_SESSION['access_level']);
            unset($_SESSION['full_name']);
            session_destroy();

            $message = "You have been logged out";

            View::renderTemplate('Home/index.html', [
                'message' => $message
            ]);
        }
        // db insertion failure
        else
        {
            echo "Unable to log logout data. Notify webmaster.";
            exit();
        }
    }

}
