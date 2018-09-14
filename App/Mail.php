<?php

namespace App;

use App\Config;


/**
 * Mail class
 *
 * PHP version 7.0
 */
class Mail
{


   /**
    * Sends login notification email to `brokers`.`broker_email`
    *
    * @param  Object   $broker   The broker
    * @param  Object   $user     The user
    *
    * @return boolean
    */
   public static function loginNotify($user)
   {
      // test
      // echo "Connected to loginNotification() method in Mail Controller!<br><br>";
      // exit();

      /**
       * create instance of PHPMailer object
       */
      $mail = new \PHPMailer(); // backslash required if class in root namespace

      // settings
      $mail->isSMTP();
      // $mail->Host = Config::SMTP_HOST;             // production
      $mail->Host = Config::SMTP_HOST_GMAIL;    // development
      $mail->Port = Config::SMTP_PORT;            // not required for server to server mail
      $mail->SMTPAuth = true;
      // $mail->Username = Config::SMTP_USER;             // production
      $mail->Username = Config::SMTP_USER_GMAIL;    // development
      $mail->Password = Config::SMTP_PASSWORD;
      $mail->SMTPSecure = 'tls';                // not required for server to server mail
      $mail->CharSet = 'UTF-8';


      /**
       * Enable SMTP debug messages
      */
      // $mail->SMTPDebug = 2;
      // $mail->Debugoutput = 'html';

      /**
      * solution
      * @https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting
      */
      $mail->SMTPOptions = [
         'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
          ]
      ];

      /**
      * Send email
      */
      $mail->setFrom('noreply@smilestylist.dental', 'Login ' . $user->name);
      $mail->addAddress(Config::JIMGMAIL);     // development
      // $mail->addAddress(Config::DANAGMAIL); // production
      // $mail->addBCC(Config::DANASSMAIL);    // production
      // $mail->addBCC(Config::JIMGMAIL);      // production

      $mail->isHTML(true);

      // Subject & body
      $mail->Subject = 'Log In';
      $mail->Body = '<h2 style="color:#0000FF;">Message from SmileStylist.dental</h2>'
                 . '<h3>Log In Notification</h3>'
                 . '<p>Registered user <strong><q>' . $user->name . ' (' . $user->email . ') </q></strong> just logged in.</p>'
                 . '<p>End of message</p>';

      // send mail & return $result to controller
      if($mail->send())
      {
         $result = true;

         return $result;
      }

      // if mail fails display error message
      if(!$mail->send())
      {
         echo $mail->ErrorInfo;
      }
   }




   /**
     * Sends logout notification email
     *
     * @param  Object   $user     The user
     *
     * @return boolean
     */
    public static function logoutNotify($user)
    {
        // test
        // echo "Connected to logoutNotification() method in Mail Controller!<br><br>";
        // echo '<pre>';
        // echo $user;
        // echo '</pre>';
        // exit();

        /**
         * create instance of PHPMailer object
         */
        $mail = new \PHPMailer(); // backslash required if class in root namespace

        // settings
        $mail->isSMTP();
        // $mail->Host = Config::SMTP_HOST;       // production
        $mail->Host     = Config::SMTP_HOST_GMAIL;    // development
        $mail->Port     = Config::SMTP_PORT;
        $mail->SMTPAuth = true;
        // $mail->Username = Config::SMTP_USER;        // production
        $mail->Username = Config::SMTP_USER_GMAIL;     // development
        $mail->Password = Config::SMTP_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->CharSet    = 'UTF-8';


        /**
         * solution
         * @https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting
         */
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
         ];


        /**
         * Send email
         */
        $mail->setFrom('noreply@smilestylist.dental', 'Logout');
        $mail->addAddress(Config::JIMGMAIL);  // development
        // $mail->addAddress(Config::DANAGMAIL);  // production
        // $mail->addBCC(Config::DANASSMAIL);   // production
        // $mail->addBCC(Config::JIMGMAIL);  // production

        $mail->isHTML(true);

        // Subject & body
        $mail->Subject = 'Log Out';
        $mail->Body = '<h2 style="color:#0000FF;">Message from SmileStylist.dental</h2>'
                    . '<h3>User: ' . $user->name  . '</h3>'
                    . '<h3>Log Out Notification</h3>'
                    . '<p>Authorized user <strong><q>'. $user->name . '</strong></q> just logged out.</p>'
                    . '<p>end of message</p>';

        // send mail & return $result to controller
        if($mail->send())
        {
            $result = true;

            return $result;
        }

        // if mail fails display error message
        if(!$mail->send())
        {
           echo $mail->ErrorInfo;
        }
    }

}
