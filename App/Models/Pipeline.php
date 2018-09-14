<?php

namespace App\Models;

use PDO;
use \App\Config;


class Pipeline extends \Core\Model
{
    public static function addTreatmentPlan()
    {

        // retrieve form data
        $date = date('Y-m-d');
        $first_name = ( isset($_REQUEST['first_name']) ) ? filter_var($_REQUEST['first_name'],FILTER_SANITIZE_STRING) : '';
        $last_name = ( isset($_REQUEST['last_name']) ) ? filter_var($_REQUEST['last_name'],FILTER_SANITIZE_STRING) : '';
        $email = ( isset($_REQUEST['email']) ) ? filter_var($_REQUEST['email'],FILTER_SANITIZE_EMAIL) : '';
        $treatment = ( isset($_REQUEST['treatment']) ) ? strtolower(filter_var($_REQUEST['treatment'],FILTER_SANITIZE_STRING)) : '';
        $explain_other_treatment = ( isset($_REQUEST['explain_other_treatment']) ) ? filter_var($_REQUEST['explain_other_treatment'],FILTER_SANITIZE_STRING) : '';
        $presented = ( isset($_REQUEST['presented']) ) ? filter_var($_REQUEST['presented'],FILTER_SANITIZE_NUMBER_INT) : '';
        $accepted = ( isset($_REQUEST['accepted']) ) ? filter_var($_REQUEST['accepted'],FILTER_SANITIZE_NUMBER_INT) : '';
        if($presented != '' && $accepted != '')
        {
            $ratio = ($accepted / $presented);
            $ratio = round($ratio, 2) * 100 . '%';
        }
        else
        {
            $ratio = null;
        }
        $scheduled = ( isset($_REQUEST['scheduled']) ) ? strtolower(filter_var($_REQUEST['scheduled'],FILTER_SANITIZE_STRING)) : '';
        $payment_plan = ( isset($_REQUEST['payment_plan']) ) ? strtolower(filter_var($_REQUEST['payment_plan'],FILTER_SANITIZE_STRING)) : '';
        $explain_other_payment_plan = ( isset($_REQUEST['explain_other_payment_plan']) ) ? filter_var($_REQUEST['explain_other_payment_plan'],FILTER_SANITIZE_STRING) : '';
        $deposit = ( isset($_REQUEST['explain_other_payment_plan']) ) ? filter_var($_REQUEST['explain_other_payment_plan'],FILTER_SANITIZE_STRING) : '';
        $follow_up = ( isset($_REQUEST['follow_up']) ) ? filter_var($_REQUEST['follow_up'],FILTER_SANITIZE_STRING) : '';
        $notes = ( isset($_REQUEST['notes']) ) ? filter_var($_REQUEST['notes'],FILTER_SANITIZE_STRING) : '';

        // test
        // echo '<pre>';
        // print_r($_REQUEST);
        // echo '</pre>';
        // exit();
        // if(!file_exists($_FILES['patient_photo']['tmp_name']) || !is_uploaded_file($_FILES['patient_photo']['tmp_name']))
        // {
        //     echo 'No upload';
        // }
        // else
        // {
        //     echo "File uploaded.";
        // }
        // exit();

        // upload patient photo to server
        if(!empty($_FILES['patient_photo']['tmp_name']) && $_FILES['patient_photo']['tmp_name'] != '')
        {

            // Assign target directory based on server
            if($_SERVER['SERVER_NAME'] != 'localhost')
            {
              // path for live server
              // UPLOAD_PATH = '/home/pamska5/public_html/smilestylist.dental/public'
              $target_dir = Config::UPLOAD_PATH . '/assets/images/uploaded_patient_photos/';
            }
            else
            {
              // path for local machine
              $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/uploaded_patient_photos/';
            }

            // Access $_FILES global array for uploaded files
            $file_name = $_FILES['patient_photo']['name'];
            $file_tmp  = $_FILES['patient_photo']['tmp_name'];
            $file_type = $_FILES['patient_photo']['type'];
            $file_size = $_FILES['patient_photo']['size'];
            $file_err_msg   = $_FILES['patient_photo']['error'];

            // get image width (1st index of array)
            $size = getimagesize($_FILES['patient_photo']['tmp_name']);

            // store height in variable (2nd index is $size array)
            $img_height = $size[1];

            // Separate file name into an array by the dot
            $kaboom = explode(".", $file_name);

            // Assign last element of array to file_extension variable (in case file has more than one dot)
            $file_extension = end($kaboom);

            // add unique prefix to file name
            $file_name = time() . '-' . $file_name;

            // create target file string of upload address
            $target_file = $target_dir . $file_name;

            // test - great test!
            // echo '$target_dir: ' . $target_dir . '<br>';
            // echo '$target_file: ' . $target_file . '<br>';
            // exit();


            /* - - - - -  Error handling  - - - - - - */

            // create gateway variable
            $upload_ok = 1;

            // Check if file already exists
            if (file_exists($target_file))
            {
                $upload_ok = 0;
                echo "Sorry, photo file already exists. Please select a
                      different file or rename file and try again.";
                exit();
            }

            // Check if file size < 5 MB
            if($file_size > 8388608)
            {
                $upload_ok = 0;
                unlink($file_tmp);
                echo 'File must be less than 5 Megabytes to upload.';
                exit();
            }
            // Check if file is gif, jpg, jpeg or png
            if(!preg_match("/\.(gif|jpg|jpeg|png)$/i", $file_name))
            {
                $upload_ok = 0;
                unlink($file_tmp);
                echo 'Image must be gif, jpg, jpeg, or png to upload.';
                exit();
            }
            // Check for any errors
            if($file_err_msg == 1)
            {
                $upload_ok = 0;
                echo 'Error uploading file. Please try again.';
                exit();
            }

            if( $upload_ok = 1 )
            {
                // Upload file to server into designated folder
                $move_result = move_uploaded_file($file_tmp, $target_file);

                // Check for boolean result of move_uploaded_file()
                if ($move_result != true)
                {
                    unlink($file_tmp);
                    echo 'File not uploaded. Please try again.';
                    exit();
                }

                /*  - - - -   Image Re-sizing & over-writing   - - - - - - -  */
                // resize only if image > 750px wide
                if($img_height > 100)
                {
                    include_once 'Library/image-resizing-to-scale.php';

                    // Assign target directory based on server
                    if($_SERVER['SERVER_NAME'] != 'localhost')
                    {
                      // path for live server
                      $target_file  = Config::UPLOAD_PATH . "/assets/images/uploaded_patient_photos/$file_name";
                      $resized_file = Config::UPLOAD_PATH . "/assets/images/uploaded_patient_photos/$file_name";
                      $wmax = 150;
                      $hmax = 100;
                      image_resize($target_file, $resized_file, $wmax, $hmax, $file_extension);
                    }
                    else
                    {
                      // path for local machine
                      $target_file  = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/uploaded_patient_photos/$file_name";
                      $resized_file = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/uploaded_patient_photos/$file_name";
                      $wmax = 150;
                      $hmax = 100;
                      image_resize($target_file, $resized_file, $wmax, $hmax, $file_extension);
                    }
                }
            }
            else
            {
                echo 'File not uploaded. Please try again.';
                exit();
            }

            try
            {
                // establish db connection
                $db = static::getDB();

                $sql = "INSERT INTO pipeline SET
                        date         = :date,
                        first_name   = :first_name,
                        last_name    = :last_name,
                        email        = :email,
                        image        = :image,
                        treatment    = :treatment,
                        explain_other_treatment = :explain_other_treatment,
                        presented    = :presented,
                        accepted     = :accepted,
                        ratio        = :ratio,
                        scheduled    = :scheduled,
                        payment_plan = :payment_plan,
                        explain_other_payment_plan = :explain_other_payment_plan,
                        deposit      = :deposit,
                        follow_up    = :follow_up,
                        notes        = :notes";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':date'         => $date,
                    ':first_name'   => $first_name,
                    ':last_name'    => $last_name,
                    ':email'        => $email,
                    ':image'        => $file_name,
                    ':treatment'    => $treatment,
                    ':explain_other_treatment'   => $explain_other_treatment,
                    ':presented'    => $presented,
                    ':accepted'     => $accepted,
                    ':ratio'        => $ratio,
                    ':scheduled'    => $scheduled,
                    ':payment_plan' => $payment_plan,
                    ':explain_other_payment_plan'   => $explain_other_payment_plan,
                    ':deposit'      => $deposit,
                    ':follow_up'    => $follow_up,
                    ':notes'        => $notes
                ];
                $results = $stmt->execute($parameters);

                // return to Admin Controller
                return $results;
            }
            catch(PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
        // no photo being uploaded
        else
        {
            try
            {
                // establish db connection
                $db = static::getDB();

                $sql = "INSERT INTO pipeline SET
                        date         = :date,
                        first_name   = :first_name,
                        last_name    = :last_name,
                        email        = :email,
                        treatment    = :treatment,
                        explain_other_treatment = :explain_other_treatment,
                        presented    = :presented,
                        accepted     = :accepted,
                        ratio        = :ratio,
                        scheduled    = :scheduled,
                        payment_plan = :payment_plan,
                        explain_other_payment_plan = :explain_other_payment_plan,
                        deposit      = :deposit,
                        follow_up    = :follow_up,
                        notes        = :notes";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':date'         => $date,
                    ':first_name'   => $first_name,
                    ':last_name'    => $last_name,
                    ':email'        => $email,
                    ':treatment'    => $treatment,
                    ':explain_other_treatment'   => $explain_other_treatment,
                    ':presented'    => $presented,
                    ':accepted'     => $accepted,
                    ':ratio'        => $ratio,
                    ':scheduled'    => $scheduled,
                    ':payment_plan' => $payment_plan,
                    ':explain_other_payment_plan'   => $explain_other_payment_plan,
                    ':deposit'      => $deposit,
                    ':follow_up'    => $follow_up,
                    ':notes'        => $notes
                ];
                $results = $stmt->execute($parameters);

                // return to Admin Controller
                return $results;
            }
            catch(PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
    }




    public static function getPlans($status)
    {
        if($status != '')
        {
            $status = $status;
        }

        try
        {
            // establish db connection
            $db = static::getDB();

            // retrieve data
            $sql = "SELECT * FROM pipeline
                    $status
                    ORDER BY id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $plans = $stmt->fetchAll(PDO::FETCH_OBJ);


            // Initialize variables for foreach loop results
            $presented_total = 0;
            $accepted_total = 0;
            $deposit_total = 0;

            // Loop through $plans and compute totals
            foreach($plans as $item){
                $presented_total += $item->presented;
                $accepted_total += $item->accepted;
                $deposit_total += $item->deposit;
            }

            // Create ratio percentage to display in footer totals
            if(isset($presented_total) && isset($accepted_total) && $accepted_total > 0){
                $ratio = number_format($accepted_total/$presented_total, 2);
                $ratio_formatted  = $ratio * 100;
            }
            else
            {
              $ratio_formatted = NULL;
            }

            // store values in array for Controller to pass in view
            $treatment_plans_data = [
                'presented_total' => $presented_total,
                'accepted_total'  => $accepted_total,
                'deposit_total'   => $deposit_total,
                'ratio_formatted' => $ratio_formatted,
                'plans'           => $plans
            ];

            // return to Admin Controller
            return $treatment_plans_data;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    public static function getOtherYearPlans($status, $year)
    {
        if($status == 'all')
        {
            $status = "WHERE status IN ('open', 'closed') AND date BETWEEN CAST('$year-01-01' AS DATE) AND CAST('$year-12-31' AS DATE)";
        }
        if($status == 'open')
        {
            $status = "WHERE status = 'open' AND date BETWEEN CAST('$year-01-01' AS DATE) AND CAST('$year-12-31' AS DATE)";
        }
        if($status == 'closed')
        {
            $status = "WHERE status = 'closed' AND date BETWEEN CAST('$year-01-01' AS DATE) AND CAST('$year-12-31' AS DATE)";
        }

        try
        {
            // establish db connection
            $db = static::getDB();

            // retrieve data
            $sql = "SELECT * FROM pipeline
                    $status
                    ORDER BY created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $plans = $stmt->fetchAll(PDO::FETCH_OBJ);


            // Initialize variables for foreach loop results
            $presented_total = 0;
            $accepted_total = 0;
            $deposit_total = 0;

            // Loop through $plans and compute totals
            foreach($plans as $item){
                $presented_total += $item->presented;
                $accepted_total += $item->accepted;
                $deposit_total += $item->deposit;
            }

            // Create ratio percentage to display in footer totals
            if(isset($presented_total) && isset($accepted_total) && $accepted_total > 0){
                $ratio = number_format($accepted_total/$presented_total, 2);
                $ratio_formatted  = $ratio * 100;
            }
            else
            {
              $ratio_formatted = NULL;
            }

            // store values in array for Controller to pass in view
            $treatment_plans_data = [
                'presented_total' => $presented_total,
                'accepted_total'  => $accepted_total,
                'deposit_total'   => $deposit_total,
                'ratio_formatted' => $ratio_formatted,
                'plans'           => $plans
            ];

            // return to Admin Controller
            return $treatment_plans_data;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    public static function getPlansByTreatment($search)
    {
        if($search != '')
        {
            $search = $search;
        }

        try
        {
            // establish db connection
            $db = static::getDB();

            // retrieve data
            $sql = "SELECT * FROM pipeline
                    $search
                    ORDER BY date DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $plans = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Initialize variables for foreach loop results
            $presented_total = 0;
            $accepted_total = 0;
            $deposit_total = 0;

            // Loop through $plans and compute totals
            foreach($plans as $item){
                $presented_total += $item->presented;
                $accepted_total += $item->accepted;
                $deposit_total += $item->deposit;
            }

            // Create ratio percentage to display in footer totals
            if(isset($presented_total) && isset($accepted_total) && $accepted_total > 0){
                $ratio = number_format($accepted_total/$presented_total, 2);
                $ratio_formatted  = $ratio * 100;
            }
            else
            {
              $ratio_formatted = NULL;
            }

            // store values in array for Controller to pass in view
            $treatment_plans_data = [
                'presented_total' => $presented_total,
                'accepted_total'  => $accepted_total,
                'deposit_total'   => $deposit_total,
                'ratio_formatted' => $ratio_formatted,
                'plans'           => $plans
            ];

            // return to Admin Controller
            return $treatment_plans_data;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    public static function searchPlans($last_name, $treatment, $status)
    {
      // check if empty form submitted
      if($last_name == '' && $treatment == '' && $status == '')
      {
          echo '<script>alert("Data missing.")</script>';
          echo '<script>window.location.href="/admin/search"</script>';
      }

      if($last_name != '')
      {
          $last_name = "WHERE last_name LIKE '$last_name%'";
          $pagetitle = 'Treatment - by patient last name';
      }

      if($treatment != '')
      {
          $treatment = "WHERE treatment = '$treatment'";
          $pagetitle = "Treatment - by category ($treatment)";
      }

      if( ($status != '') && ($status != 'all') )
      {
          $status = "WHERE status = '$status'";
          $pagetitle = "Treatment - by status ($status)";
      }
      if( ($status != '') && ($status == 'all') )
      {
          $status = "WHERE status IN ('open', 'closed')";
          $pagetitle = 'Treatment - by open and closed';
      }


        // execute query
        try
        {
            // establish db connection
            $db = static::getDB();

            // retrieve data
            $sql = "SELECT * FROM pipeline
                    $last_name
                    $treatment
                    $status
                    ORDER BY date DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $plans = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Initialize variables for foreach loop results
            $presented_total = 0;
            $accepted_total = 0;
            $deposit_total = 0;

            // Loop through $plans and compute totals
            foreach($plans as $item){
                $presented_total += $item->presented;
                $accepted_total += $item->accepted;
                $deposit_total += $item->deposit;
            }

            // Create ratio percentage to display in footer totals
            if(isset($presented_total) && isset($accepted_total) && $accepted_total > 0){
                $ratio = number_format($accepted_total/$presented_total, 2);
                $ratio_formatted  = $ratio * 100;
            }
            else
            {
              $ratio_formatted = NULL;
            }

            // store values in array for Controller to pass in view
            $treatment_plans_data = [
                'pagetitle'       => $pagetitle,
                'presented_total' => $presented_total,
                'accepted_total'  => $accepted_total,
                'deposit_total'   => $deposit_total,
                'ratio_formatted' => $ratio_formatted,
                'plans'           => $plans
            ];

            // return to Admin Controller
            return $treatment_plans_data;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    public static function getPlansByDate($start, $end, $status)
    {

        if( $start == '' || $end == '')
        {
            // send javascript error alert
            echo '<script>alert("Please enter a start date and end date")</script>';

            // return to same page
            echo '<script>window.location.href="/admin/search"</script>';
        }

        if( ($status != '') && ($status != 'all') )
        {
            $status = "AND status = '$status'";
        }
        else
        {
            $status = "AND status IN ('open', 'closed')";
        }

        // execute query
        try
        {
            // establish db connection
            $db = static::getDB();

            // retrieve data
            $sql = "SELECT * FROM pipeline
                    WHERE date
                    BETWEEN '$start'
                    AND '$end'
                    $status
                    ORDER BY date DESC";
            // Optional:"WHERE date BETWEEN date($start) AND date($end)";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $plans = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Initialize variables for foreach loop results
            $presented_total = 0;
            $accepted_total = 0;
            $deposit_total = 0;

            // Loop through $plans and compute totals
            foreach($plans as $item){
                $presented_total += $item->presented;
                $accepted_total += $item->accepted;
                $deposit_total += $item->deposit;
            }

            // Create ratio percentage to display in footer totals
            if(isset($presented_total) && isset($accepted_total) && $accepted_total > 0){
                $ratio = number_format($accepted_total/$presented_total, 2);
                $ratio_formatted  = $ratio * 100;
            }
            else
            {
              $ratio_formatted = NULL;
            }

            // create pagetitle
            $pagetitle = 'Treatment plans: ' . $start . ' to ' . $end . ' (' . $status . ')';

            // store values in array for Controller to pass in view
            $treatment_plans_data = [
                'pagetitle'       => $pagetitle,
                'presented_total' => $presented_total,
                'accepted_total'  => $accepted_total,
                'deposit_total'   => $deposit_total,
                'ratio_formatted' => $ratio_formatted,
                'plans'           => $plans
            ];

            // return to Admin Controller
            return $treatment_plans_data;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    public static function getPlan($id)
    {
        try
        {
            // establish db connection
            $db = static::getDB();

            // retrieve data
            $sql = "SELECT * FROM pipeline
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':id' => $id
            ];
            $stmt->execute($parameters);
            $plan = $stmt->fetch(PDO::FETCH_OBJ);

            // return to Admin Controller
            return $plan;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    public static function updateTreatmentPlan($id)
    {
        // retrieve form data
        // $staff = ( isset($_POST['staff']) ) ? filter_var($_POST['staff'],FILTER_SANITIZE_STRING) : '';
        $first_name = ( isset($_POST['first_name']) ) ? filter_var($_POST['first_name'],FILTER_SANITIZE_STRING) : '';
        $last_name = ( isset($_POST['last_name']) ) ? filter_var($_POST['last_name'],FILTER_SANITIZE_STRING) : '';
        $email = ( isset($_POST['email']) ) ? filter_var($_POST['email'],FILTER_SANITIZE_EMAIL) : '';
        // $rating = ( isset($_POST['rating']) ) ? filter_var($_POST['rating'],FILTER_SANITIZE_STRING) : '';
        $treatment = ( isset($_POST['treatment']) ) ? strtolower(filter_var($_POST['treatment'],FILTER_SANITIZE_STRING)) : '';
        $explain_other_treatment = ( isset($_POST['explain_other_treatment']) ) ? filter_var($_POST['explain_other_treatment'],FILTER_SANITIZE_STRING) : '';
        $presented = ( isset($_POST['presented']) ) ? filter_var($_POST['presented'],FILTER_SANITIZE_NUMBER_INT) : '';
        $accepted = ( isset($_POST['accepted']) ) ? filter_var($_POST['accepted'],FILTER_SANITIZE_NUMBER_INT) : '';
        if( ($presented == 0 || $presented == '') && ($accepted == 0 || $accepted == '') )
        {
            $ratio = null;
        }
        else
        {
            $ratio = ($accepted / $presented);
            $ratio = round($ratio, 2) * 100 . '%';
        }
        $scheduled = ( isset($_POST['scheduled']) ) ? strtolower(filter_var($_POST['scheduled'],FILTER_SANITIZE_STRING)) : '';
        // $next_appt = ( isset($_POST['next_appt']) ) ? filter_var($_POST['next_appt'],FILTER_SANITIZE_STRING) : '';
        $payment_plan = ( isset($_POST['payment_plan']) ) ? strtolower(filter_var($_POST['payment_plan'],FILTER_SANITIZE_STRING)) : '';
        $explain_other_payment_plan = ( isset($_POST['explain_other_payment_plan']) ) ? filter_var($_POST['explain_other_payment_plan'],FILTER_SANITIZE_STRING) : '';
        $deposit = ( isset($_POST['explain_other_payment_plan']) ) ? filter_var($_POST['explain_other_payment_plan'],FILTER_SANITIZE_STRING) : '';
        // $signed_fa = ( isset($_POST['signed_fa']) ) ? strtolower(filter_var($_POST['signed_fa'],FILTER_SANITIZE_STRING)) : '';
        $follow_up = ( isset($_POST['follow_up']) ) ? filter_var($_POST['follow_up'],FILTER_SANITIZE_STRING) : '';
        $notes = ( isset($_POST['notes']) ) ? filter_var($_POST['notes'],FILTER_SANITIZE_STRING) : '';

        // test
        // echo '<pre>';
        // print_r($_REQUEST);
        // echo '</pre>';
        // exit();
        // if(!file_exists($_FILES['patient_photo']['tmp_name']) || !is_uploaded_file($_FILES['patient_photo']['tmp_name']))
        // {
        //     echo 'No upload';
        // }
        // else
        // {
        //     echo "File uploaded.";
        // }
        // exit();

        // upload patient photo to server
        if(!empty($_FILES['patient_photo']['tmp_name']))
        {

            // Assign target directory based on server
            if($_SERVER['SERVER_NAME'] != 'localhost')
            {
              // path for live server
              // UPLOAD_PATH = '/home/pamska5/public_html/smilestylist.dental/public'
              $target_dir = Config::UPLOAD_PATH . '/assets/images/uploaded_patient_photos/';
            }
            else
            {
              // path for local machine
              $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/uploaded_patient_photos/';
            }


            // Access $_FILES global array for uploaded files
            $file_name = $_FILES['patient_photo']['name'];
            $file_tmp  = $_FILES['patient_photo']['tmp_name'];
            $file_type = $_FILES['patient_photo']['type'];
            $file_size = $_FILES['patient_photo']['size'];
            $file_err_msg   = $_FILES['patient_photo']['error'];

            // get image width (1st index of array)
            $size = getimagesize($_FILES['patient_photo']['tmp_name']);

            // test
            // echo '<pre>';
            // print_r($size);
            // echo '</pre>';
            // exit();

            // store height in variable (2nd index is $size array)
            $img_height = $size[1];

            // Separate file name into an array by the dot
            $kaboom = explode(".", $file_name);

            // Assign last element of array to file_extension variable (in case file has more than one dot)
            $file_extension = end($kaboom);

            // add unique prefix to file name
            $file_name = time() . '-' . $file_name;

            // create target file string of upload address
            $target_file = $target_dir . $file_name;

            // test - great test!
            // echo '$target_dir: ' . $target_dir . '<br>';
            // echo '$target_file: ' . $target_file . '<br>';
            // exit();


            /* - - - - -  Error handling  - - - - - - */

            $upload_ok = 1;

            // Check if file already exists
            if (file_exists($target_file))
            {
                $upload_ok = 0;
                echo "Sorry, photo file already exists. Please select a
                      different file or rename file and try again.";
                exit();
            }

            // Check if file size < 5 MB
            if($file_size > 5242880)
            {
                $upload_ok = 0;
                unlink($file_tmp);
                echo 'File must be less than 5 Megabytes to upload.';
                exit();
            }
            // Check if file is gif, jpg, jpeg or png
            if(!preg_match("/\.(gif|jpg|jpeg|png)$/i", $file_name))
            {
                $upload_ok = 0;
                unlink($file_tmp);
                echo 'Image must be gif, jpg, jpeg, or png to upload.';
                exit();
            }
            // Check for any errors
            if($file_err_msg == 1)
            {
                $upload_ok = 0;
                echo 'Error uploading file. Please try again.';
                exit();
            }

            if( $upload_ok = 1 )
            {
                // Upload file to server into designated folder
                $move_result = move_uploaded_file($file_tmp, $target_file);

                // Check for boolean result of move_uploaded_file()
                if ($move_result != true)
                {
                    unlink($file_tmp);
                    echo 'File not uploaded. Please try again.';
                    exit();
                }

                /*  - - - -   Image Re-sizing & over-writing   - - - - - - -  */
                // resize only if image > 150px wide
                if($img_height > 100)
                {
                    include_once 'Library/image-resizing-to-scale.php';

                    // Assign target directory based on server
                    if($_SERVER['SERVER_NAME'] != 'localhost')
                    {
                      // path for live server
                      $target_file  = Config::UPLOAD_PATH . "/assets/images/uploaded_patient_photos/$file_name";
                      $resized_file = Config::UPLOAD_PATH . "/assets/images/uploaded_patient_photos/$file_name";
                      $wmax = 150;
                      $hmax = 100;
                      image_resize($target_file, $resized_file, $wmax, $hmax, $file_extension);
                    }
                    else
                    {
                      // path for local machine
                      $target_file  = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/uploaded_patient_photos/$file_name";
                      $resized_file = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/uploaded_patient_photos/$file_name";
                      $wmax = 150;
                      $hmax = 100;
                      image_resize($target_file, $resized_file, $wmax, $hmax, $file_extension);
                    }
                }
            }
            else
            {
                echo 'File not uploaded. Please try again.';
                exit();
            }

            try
            {
                // establish db connection
                $db = static::getDB();

                $sql = "UPDATE pipeline SET
                        first_name   = :first_name,
                        last_name    = :last_name,
                        email        = :email,
                        image        = :image,
                        treatment    = :treatment,
                        explain_other_treatment = :explain_other_treatment,
                        presented    = :presented,
                        accepted     = :accepted,
                        ratio        = :ratio,
                        scheduled    = :scheduled,
                        payment_plan = :payment_plan,
                        explain_other_payment_plan = :explain_other_payment_plan,
                        deposit      = :deposit,
                        follow_up    = :follow_up,
                        notes        = :notes
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id'           => $id,
                    ':first_name'   => $first_name,
                    ':last_name'    => $last_name,
                    ':email'        => $email,
                    ':image'        => $file_name,
                    ':treatment'    => $treatment,
                    ':explain_other_treatment'   => $explain_other_treatment,
                    ':presented'    => $presented,
                    ':accepted'     => $accepted,
                    ':ratio'        => $ratio,
                    ':scheduled'    => $scheduled,
                    ':payment_plan' => $payment_plan,
                    ':explain_other_payment_plan'   => $explain_other_payment_plan,
                    ':deposit'      => $deposit,
                    ':follow_up'    => $follow_up,
                    ':notes'        => $notes
                ];

                $result = $stmt->execute($parameters);

                // return to Admin Controller
                return $result;
            }
            catch(PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
        // no photo uploaded
        else
        {
            try
            {
                // establish db connection
                $db = static::getDB();

                $sql = "UPDATE pipeline SET
                        first_name   = :first_name,
                        last_name    = :last_name,
                        email        = :email,
                        treatment    = :treatment,
                        explain_other_treatment = :explain_other_treatment,
                        presented    = :presented,
                        accepted     = :accepted,
                        ratio        = :ratio,
                        scheduled    = :scheduled,
                        payment_plan = :payment_plan,
                        explain_other_payment_plan = :explain_other_payment_plan,
                        deposit      = :deposit,
                        follow_up    = :follow_up,
                        notes        = :notes
                        WHERE id = :id";
                $stmt = $db->prepare($sql);
                $parameters = [
                    ':id'           => $id,
                    ':first_name'   => $first_name,
                    ':last_name'    => $last_name,
                    ':email'        => $email,
                    ':treatment'    => $treatment,
                    ':explain_other_treatment'  => $explain_other_treatment,
                    ':presented'    => $presented,
                    ':accepted'     => $accepted,
                    ':ratio'        => $ratio,
                    ':scheduled'    => $scheduled,
                    ':payment_plan' => $payment_plan,
                    ':explain_other_payment_plan' => $explain_other_payment_plan,
                    ':deposit'      => $deposit,
                    ':follow_up'    => $follow_up,
                    ':notes'        => $notes
                ];

                $result = $stmt->execute($parameters);

                // return to Admin Controller
                return $result;
            }
            catch(PDOException $e)
            {
                echo $e->getMessage();
                exit();
            }
        }
    }



   /**
    * retrieve status of record by ID
    * @param  Integer   $id   Treatment plan ID
    * @return String          The value of the status field
    */
   public static function getStatusById($id)
   {
      try
      {
         // establish db connection
         $db = static::getDB();

         $sql = "SELECT status FROM pipeline
                 WHERE id = :id";
         $stmt = $db->prepare($sql);
         $parameters = [
            ':id' => $id
         ];
         $stmt->execute($parameters);
         $result = $stmt->fetch(PDO::FETCH_OBJ);

         return $result;
      }
      catch (PDOException $e) {
         echo $e->getMessage();
         exit();
      }

   }




    public static function deleteTreatmentPlan($id)
    {
        try
        {
            // establish db connection
            $db = static::getDB();

            $sql = "DELETE FROM pipeline
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':id' => $id
            ];
            $result = $stmt->execute($parameters);

            // return to Admin Controller
            return $result;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }


    /**
     * updates contents of image field for referenced record with null
     *
     * @param  Integer    $id     The ID of the record
     * @return Boolean
     */
    public static function deletePatientImage($id)
    {
        try
        {
            // establish db connection
            $db = static::getDB();

            $sql = "UPDATE pipeline SET
                    image = ''
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':id' => $id
            ];
            $result = $stmt->execute($parameters);

            // return to Admin Controller
            return $result;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }



    /**
     * change plan status
     *
     * @param  Integer $id     The plan ID
     * @param  String $action  Value to change to (new value)
     * @return boolean
     */
    public static function changePlanStatus($id, $action)
    {
        try
        {
            // establish db connection
            $db = static::getDB();

            // change status
            $sql = "UPDATE pipeline SET
                    status = '$action'
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters  = [
                ':id' => $id
            ];
            $result = $stmt->execute($parameters);

            // return to Admin Controller
            return $result;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }




    /**
     * change status of boolean field
     *
     * @param  Integer $id     The plan ID
     * @param  String $action  Value to change to (new value)
     * @return boolean
     */
    public static function updateBooleanStatus($id, $action)
    {
        try
        {
            // establish db connection
            $db = static::getDB();

            // get current value (0 or 1) of $action field
            $sql = "SELECT $action FROM pipeline
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':id' => $id
            ];
            $stmt->execute($parameters);
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            // store value in variable
            $current_value = $result->$action;

            // assign new value (opposite)
            if($current_value == 0)
            {
                $new_value = 1;
            }
            else
            {
                $new_value = 0;
            }

            // change boolean status
            $sql = "UPDATE pipeline SET
                    $action = $new_value
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $parameters = [
                ':id' => $id
            ];
            $result = $stmt->execute($parameters);

            // return to Admin Controller
            return $result;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            exit();
        }
    }

}
