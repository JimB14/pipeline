<?php

namespace App\Models;

use PDO;


class Log extends \Core\Model
{
    /**
     * inserts login or logout data into login_log
     *
     * @param  Object $user      The user's record
     * @param  String $ip        The user's IP address
     * @param  String $action    Login or logout
     *
     * @return boolean
     */
    public static function storeLogin($user, $ip, $action)
    {
        // log in
        if ($action == "login")
        {
            try
            {
                // establish db connection
                $db = static::getDB();

                $sql = "INSERT INTO login_log SET
                        user_id = :user_id,
                        ip      = :ip,
                        action  = :action";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':user_id' => $user->id,
                    ':ip'      => $ip,
                    ':action'  => $action
                ];
                $result = $stmt->execute($parameters);

                return $result;
            }
            catch (PDOException $e)
            {
                $_SESSION['loginerror'] = "Error checking credentials";
                header("Location: /");
                exit();
            }
        }
        // log out
        else
        {
            try
            {
                // establish db connection
                $db = static::getDB();

                $sql = "INSERT INTO login_log SET
                        user_id = :user_id,
                        ip      = :ip,
                        action  = :action";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':user_id' => $user->id,
                    ':ip'      => $ip,
                    ':action'  => $action
                ];
                $result = $stmt->execute($parameters);

                return $result;
            }
            catch (PDOException $e)
            {
                $_SESSION['loginerror'] = "Error checking credentials";
                header("Location: /");
                exit();
            }
        }

    }

    
}
