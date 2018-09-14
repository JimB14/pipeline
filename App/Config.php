<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{
    /**
     * Updating images to live server
     * @var string
     */
    const UPLOAD_PATH = '/home/pamska5/public_html/smilestylist.dental/public';


    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'smilestylist';
    //const DB_NAME = 'pamska5_smilestylist';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';
    //const DB_USER = 'pamska5_jburns14';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';
    //const DB_PASSWORD = 'Hopehope1!';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    //const SHOW_ERRORS = true;
    const SHOW_ERRORS = true;

    /* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

    /**
     * SMTP Host
     *
     * @var string
     */
    const SMTP_HOST_GMAIL = 'smtp.gmail.com';
    const SMTP_HOST = 'mail.smilestylist.dental';

    // use for mail from contact form to email on same server (info@attorneywes.com)
    const SMTP_SEND_MAIL_INTERNALLY_HOST = 'localhost';

    /**
     * SMTP port
     *
     * @var int
     */
    const SMTP_PORT = 587;

    /**
     * SMTP username
     *
     * @var string
     */
    const SMTP_USER_GMAIL = 'jim.burns14@gmail.com';  // development
    // const SMTP_USER = 'noreply@smilestylist.dental';  // production

    /**
     * SMTP password
     *
     * @var string
     */
    const SMTP_PASSWORD = 'Hopehope2@';  // development
    // const SMTP_PASSWORD = '$ecureMAIL!';  // production


    /**
     * Email addresses
     * @var string
     */
    const DANAGMAIL = 'danat927@gmail.com';
    const DANASSMAIL = 'dana@smilestylist.com';
    const JIMGMAIL = 'jim.burns14@gmail.com';

}
