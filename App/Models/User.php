<?php

namespace App\Models;

use PDO;


class User extends \Core\Model
{
    /**
     * validates user credentials
     *
     * @param  string $email     The user's email
     * @param  string $password  The user's password
     *
     * @return boolean
     */
    public static function validateLoginCredentials($email, $password)
    {
        // clear variable for new values
        unset($_SESSION['loginerror']);

        // set gate-keeper to true
        $okay = true;

        // check if fields have length
        if($email == "" || $password == "")
        {
            $_SESSION['loginerror'] = 'Please enter login email and password.';
            $okay = false;
            header("Location: /login");
            exit();
        }

        // validate email
        if(filter_var($email, FILTER_SANITIZE_EMAIL === false))
        {
            $_SESSION['loginerror'] = 'Please enter a valid email address';
            $okay = false;
            header("Location: /login");
            exit();
        }

        if($okay)
        {
            // check if email exists & retrieve password
            try
            {
                // establish db connection
                $db = static::getDB();

                $sql = "SELECT * FROM users WHERE
                        email = :email
                        AND access_level = 1";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':email' => $email
                ];
                $stmt->execute($parameters);
                $user = $stmt->fetch(PDO::FETCH_OBJ);
            }
            catch (PDOException $e)
            {
                $_SESSION['loginerror'] = "Error checking credentials";
                header("Location: /");
                exit();
            }
        }

        // check if email & active match found
        if(empty($user))
        {
            $_SESSION['loginerror'] = "User not found";
            header("Location: /");
            exit();
        }

        // returning user verified
        if( (!empty($user)) && (password_verify($password, $user->password)) )
        {
            // return $user object to Login controller
            return $user;
        }
        else
        {
            $_SESSION['loginerror'] = "Matching credentials not found.
            Please verify and try again.";
            header("Location: /");
            exit();
        }
    }


    public static function getUser($id)
    {
        // check if email exists & retrieve password
        try
        {
            // establish db connection
            $db = static::getDB();

            // query db table
            $sql = "SELECT * FROM users WHERE
                    id = :id
                    AND access_level = 1";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':id' => $id
            ];
            $stmt->execute($parameters);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            return $user;
        }
        catch (PDOException $e)
        {
            $_SESSION['loginerror'] = "Error checking credentials";
            header("Location: /");
            exit();
        }
    }
}
