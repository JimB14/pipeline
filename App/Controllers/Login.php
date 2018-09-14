<?php

namespace App\Controllers;

use \App\Models\User;
use \App\Models\Log;
use \App\Mail;


class Login extends \Core\Controller
{
    public function logUserIn()
    {
        // retrieve form data
        $email = ( isset($_REQUEST['email']) ) ? filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) : '';
        $password = ( isset($_REQUEST['password']) ) ? filter_var($_REQUEST['password'], FILTER_SANITIZE_STRING) : '';

        // log user in
        $user = User::validateLoginCredentials($email, $password);

        // check if returning user; if true log in
        if($user)
        {
            // log returning user in

            // create unique id & store in SESSION variable
            $uniqId = md5($user->id);
            $_SESSION['user'] = $uniqId;
            $_SESSION['loggedIn'] = true;

            // assign user ID & access_level & full_name to SESSION variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['access_level'] = $user->access_level;
            $_SESSION['full_name'] = $user->name;

            // session timeout code in front-controller public/index.php
            $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

            // test
            // echo $_SESSION['user'] . "<br>";
            // echo $_SESSION['loggedIn'] . "<br>";
            // echo $_SESSION['user_id'] . "<br>";
            // echo $_SESSION['access_level'] . "<br>";
            // echo $_SESSION['full_name'] . "<br>";
            // exit();

            // get IP Address
            $ip = $_SERVER['REMOTE_ADDR'];

            // store login data in log table
            $log_result = Log::storeLogin($user, $ip, $action='login');

            // db insertion successful
            if ($log_result)
            {
                header("Location: /admin");
                exit();
            }
            // db insertion failure
            else
            {
                echo "Unable to log login data. Notify webmaster.";
                exit();
            }
        }
    }

}
