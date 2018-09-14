<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Pipeline;
use \App\Models\Collection;


class Admin extends \Core\Controller
{
    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        //if SESSION is not set, send to login page
        if(!isset($_SESSION['user']))
        {
            header("Location: /");
            exit();
        }
    }




    public function indexAction()
    {

        View::renderTemplate('Admin/index.html', [
            'home'  => 'active'
        ]);

    }


    public function toDoAction()
    {
      View::renderTemplate('Admin/Todo/index.html', [
          'todo'  => 'active'
      ]);
    }



    public function addTreatmentPlanAction()
    {
        // create arrays for form
        $rating_choices = ['A','B','C'];
        $staff_choices = ['Dana','Jessica','Mary','Liz'];
        $treatment_type_choices = ['Full', 'Ortho', 'Other'];
        $scheduled_choices = ['Yes','No'];
        $payment_choices = ['Cash', 'Credit Card','CareCredit','Lending Club','Other'];
        $signed_fa_choices = ['Yes','No'];

        View::renderTemplate('Admin/Add/index.html', [
            'rating_choices'          => $rating_choices,
            'staff_choices'           => $staff_choices,
            'treatment_type_choices'  => $treatment_type_choices,
            'scheduled_choices'       => $scheduled_choices,
            'payment_choices'         => $payment_choices,
            'signed_fa_choices'       => $signed_fa_choices,
            'addplan'                 => 'active'
        ]);
    }




    public function postTreatmentPlanAction()
    {
        // add treatment plan to pipeline table
        $results = Pipeline::addTreatmentPlan();

        if($results)
        {
            header("Location: /admin");
            exit();
        }
        else
        {
            echo "Error. Unable to add treatment plan.";
            exit();
        }
    }




    public function openPlansAction()
    {
        // get treatment plans
        $treatment_plans_data = Pipeline::getPlans($status='WHERE status = "open"');

        // test
        // echo '<pre>';
        // print_r($plans);
        // echo '</pre>';
        //exit();

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => 'Treatment plans - open',
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted'],
            'open'            => 'active'
        ]);
    }




    public function closedPlansAction()
    {
        // get treatment plans
        $treatment_plans_data = Pipeline::getPlans($status='WHERE status = "closed"');

        // test
        // echo '<pre>';
        // print_r($treatment_plans_data);
        // echo '</pre>';
        //exit();

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => 'Treatment plans - closed',
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted'],
            'closed'          => 'active'
        ]);
    }




    public function allPlansAction()
    {
        // get treatment plans
        $treatment_plans_data = Pipeline::getPlans( $status='WHERE status IN ("open", "closed")' );

        // test
        // echo '<pre>';
        // print_r($treatment_plans_data);
        // echo '</pre>';
        // exit();

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => 'Treatment plans - all',
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted'],
            'all'             => 'active'
        ]);
    }



    public function getPlansAction()
    {
        // retrieve query string data
        $status = ( isset($_REQUEST['status']) ) ? filter_var($_REQUEST['status'], FILTER_SANITIZE_STRING) : '';
        $year = ( isset($_REQUEST['year']) ) ? filter_var($_REQUEST['year'], FILTER_SANITIZE_STRING) : '';

        // get treatment plans
        $treatment_plans_data = Pipeline::getOtherYearPlans($status, $year);

        // test
        // echo '<pre>';
        // print_r($treatment_plans_data);
        // echo '</pre>';
        // exit();

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => "Treatment plans - $status",
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted'],
            'all'             => 'active',
            'year'            => $year
        ]);
    }




    public function plansByTreatmentAction()
    {
        // retrieve query string data
        $treatment = ( isset($_REQUEST['treatment']) ) ? filter_var($_REQUEST['treatment'], FILTER_SANITIZE_STRING) : '';

        // get treatment plans
        $treatment_plans_data = Pipeline::getPlansByTreatment( $search="WHERE treatment = '$treatment' AND status = 'open'" );

        // test
        // echo '<pre>';
        // print_r($treatment_plans_data);
        // echo '</pre>';
        //exit();

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => 'Treatment plans - ' . $treatment,
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted'],
            'index'           => $treatment
        ]);
    }



    /**
     * displays search/index.html
     *
     * @return void
     */
    public function searchAction()
    {
        // arrays for drop-downs
        $treatment = ['full', 'ortho', 'other'];
        $status = ['open', 'closed', 'all'];

        // render view
        View::renderTemplate('Search/index.html', [
            'pagetitle' => 'Search',
            'treatment' => $treatment,
            'status'    => $status,
            'search'    => 'active'
        ]);
    }




    public function searchPipelineByPatientAction()
    {
        // retrieve form data
        $last_name = ( isset($_REQUEST['last_name']) ) ?  filter_var($_REQUEST['last_name'], FILTER_SANITIZE_STRING): '';

        // get plans
        $treatment_plans_data = Pipeline::searchPlans($last_name, $treatment='', $status='');

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => $treatment_plans_data['pagetitle'],
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted']
        ]);
    }




    public function searchPipelineByStatusAction()
    {
        // retrieve form data
        $status = ( isset($_REQUEST['status']) ) ?  filter_var($_REQUEST['status'], FILTER_SANITIZE_STRING): '';

        // get plans
        $treatment_plans_data = Pipeline::searchPlans($last_name='', $treatment='', $status);

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => $treatment_plans_data['pagetitle'],
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted']
        ]);
    }




    public function searchPipelineByTreatmentAction()
    {
        // retrieve form data
        $treatment = ( isset($_REQUEST['treatment']) ) ?  filter_var($_REQUEST['treatment'], FILTER_SANITIZE_STRING): '';

        // get plans
        $treatment_plans_data = Pipeline::searchPlans($last_name='', $treatment ,$status='');

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => $treatment_plans_data['pagetitle'],
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted']
        ]);
    }




    public function searchPipelineByDateAction()
    {
        // retrieve form data
        $start = ( isset($_REQUEST['start_date']) ) ?  filter_var($_REQUEST['start_date'], FILTER_SANITIZE_STRING): '';
        $end = ( isset($_REQUEST['end_date']) ) ?  filter_var($_REQUEST['end_date'], FILTER_SANITIZE_STRING): '';
        $status = ( isset($_REQUEST['status']) ) ?  filter_var($_REQUEST['status'], FILTER_SANITIZE_STRING): '';

        // test
        // echo $start . '<br>';
        // echo $end . '<br>';
        // echo $status . '<br>';
        // exit();

        // get plans
        $treatment_plans_data = Pipeline::getPlansByDate($start, $end, $status);

        // render view
        View::renderTemplate('Admin/Show/index.html', [
            'pagetitle'       => $treatment_plans_data['pagetitle'],
            'plans'           => $treatment_plans_data['plans'],
            'presented_total' => $treatment_plans_data['presented_total'],
            'accepted_total'  => $treatment_plans_data['accepted_total'],
            'deposit_total'   => $treatment_plans_data['deposit_total'],
            'ratio_formatted' => $treatment_plans_data['ratio_formatted']
        ]);

    }




    public function updatePlanAction()
    {
        // retrieve ID from URL
        $id = $this->route_params['id'];

        // get plan
        $plan = Pipeline::getPlan($id);

        // test
        // echo '<pre>';
        // print_r($plan);
        // echo '</pre>';
        // exit();

        // create arrays for form
        $rating_choices = ['A','B','C'];
        $staff_choices = ['Dana','Jessica','Mary','Liz'];
        $treatment_type_choices = ['Full', 'Ortho', 'Other'];
        $scheduled_choices = ['Yes','No'];
        $payment_choices = ['Cash', 'Credit Card','CareCredit','Lending Club','Other'];
        $signed_fa_choices = ['Yes','No'];

        // render view
        View::renderTemplate('Admin/Update/index.html', [
            'plan' => $plan,
            'rating_choices'          => $rating_choices,
            'staff_choices'           => $staff_choices,
            'treatment_type_choices'  => $treatment_type_choices,
            'scheduled_choices'       => $scheduled_choices,
            'payment_choices'         => $payment_choices,
            'signed_fa_choices'       => $signed_fa_choices
        ]);
    }


    public function updateTreatmentPlanAction()
    {
        // retrieve ID from URL
        $id = $this->route_params['id'];

        // get status
        $result = Pipeline::getStatusById($id);

        // store status in variable
        $status = $result->status;

        switch ($status) {
           case 'closed':
           $status = 'closed-plans';
           break;
           case 'open':
           $status = 'open-plans';
        }

        // echo $status; exit();

        // update treatment plan
        $result = Pipeline::updateTreatmentPlan($id);


        if($result)
        {
            header("Location: /admin/$status");
            exit();
        }
    }




    public function deletePlanAction()
    {
        // retrieve ID from URL
        $id = $this->route_params['id'];

        // update treatment plan
        $result = Pipeline::deleteTreatmentPlan($id);

        if($result)
        {
            header("Location: /admin/open-plans");
            exit();
        }
        else
        {
           echo "Error deleting plan.";
           exit();
        }
    }



    public function deletePatientImageAction()
    {
        // get treatment plan ID
        $id = $this->route_params['id'];

        // delete image field for referenced record
        $result = Pipeline::deletePatientImage($id);

        if($result)
        {
            header("Location: /admin/update-plan/$id");
            exit();
        }
        else
        {
           echo "Error deleting image.";
           exit();
        }
    }




    public function updatePlanStatusAction()
    {
        // retrieve ID from URL
        $id = $this->route_params['id'];

        // retrieve action instructions from query string
        $action = ( isset($_REQUEST['action'])) ? filter_var($_REQUEST['action'], FILTER_SANITIZE_STRING) : '';

        // update treatment plan
        $result = Pipeline::changePlanStatus($id, $action);

        if($result)
        {
            header("Location: /admin/open-plans");
            exit();
        }
    }




    public function updateStatusAction()
    {
        // retrieve ID from URL
        $id = $this->route_params['id'];

        // retrieve action instructions from query string
        $action = ( isset($_REQUEST['action'])) ? filter_var($_REQUEST['action'], FILTER_SANITIZE_STRING) : '';

        // update treatment plan
        $result = Pipeline::updateBooleanStatus($id, $action);

        if($result)
        {
            header("Location: /admin/open-plans");
            exit();
        }
    }



    /* - - - - - -  Bonus section - - - - - - - - - - - - - - - - - - - - - -*/

    /**
     * displays bonus table populated by db data
     * @return [type] [description]
     */
   public function bonusAction()
   {
       // create new DateTime object
       $date = new \DateTime();

       // store month in string variable (e.g. Jan = 01, Oct = 10)
       $month = $date->format('m');

       // store month in string variable (e.g. Jan, Feb)
       $monthName = $date->format('M');

       // store year
       $year = $date->format('Y');

      // get bonus data for current year
      $collections = Collection::getCollections($year);

      // loop thru collections and add properties to object
      foreach($collections as $collection) {
         $collection->collectionsLastTwoMonths = $collection->colTwoMonthsAgo + $collection->colPrevMonth;
         $collection->overheadLastTwoMonths    = $collection->ohTwoMonthsAgo + $collection->ohPrevMonth;
         $collection->overheadAvg              = ($collection->overheadLastTwoMonths + $collection->ohCurrent)/3;
         $collection->collectionAvg            = ($collection->collectionsLastTwoMonths + $collection->colCurrent)/3;
         $collection->target                   = $collection->collectionsLastTwoMonths - $collection->overheadLastTwoMonths - $collection->ohCurrent;
         $collection->plusMinus                = $collection->collectionAvg - $collection->overheadAvg;
         $collection->bonus                    = $collection->plusMinus * .05;
      }

      // test
      // echo $year;
      // echo '<pre>';
      // print_r($collections);
      // echo '</pre>';
      // exit();

      // render view
      View::renderTemplate('Admin/Bonus/index.html', [
         'pagetitle'    => 'Bonus',
         'collections'  => $collections,
         'bonus'        => 'active',
         'currentMonth' => $month,
         'currentYear'  => $year,
         'thisMonth'    => $monthName
      ]);
   }



    /*
     * displays populated update form
     */
    public function updateAction()
    {
        // echo "Connected to update() in Admin controller!<br>";

        // retrieve params
        $month = $this->route_params['month'];
        $year = $this->route_params['year'];

        // get collections data for specific month - pass id/month
        $collection = Collection::getBonusMonth($year, $month);

        // test
        // echo '<pre>';
        // print_r($collection);
        // echo '</pre>';
        // exit();

        // create months array
        $months = [
           '-1' => 'Nov',
           '0'  => 'Dec',
           '1'  => 'Jan',
           '2'  => 'Feb',
           '3'  => 'Mar',
           '4'  => 'Apr',
           '5'  => 'May',
           '6'  => 'Jun',
           '7'  => 'Jul',
           '8'  => 'Aug',
           '9'  => 'Sep',
           '10' => 'Oct',
           '11' => 'Nov',
           '12' => 'Dec'
        ];

        // assign numeric value to currentMonth
        foreach($months as $key => $value)
        {
            if($value == $month)
            {
                $monthId = $key;
            }
        }

        // assign numeric values to previous month & two months ago
        $prevMonth = $monthId - 1;
        $twoMonthsAgo = $monthId - 2;

        // test
        // echo $monthId . '<br>';
        // echo $prevMonth . '<br>';
        // echo $twoMonthsAgo . '<br>';
        // exit();

        // get names of prevMonth & twoMonthsAgo
        foreach($months as $key => $value)
        {
            if ($key == $prevMonth)
            {
                $prevMonthName = $value;
            }
            if ($key == $twoMonthsAgo)
            {
                $twoMonthsAgoName = $value;
            }
        }

        // test
        // echo $prevMonthName . '<br>';
        // echo $twoMonthsAgoName . '<br>';
        // exit();

        // create new DateTime object
        $date = new \DateTime();

        // store current year in variable
        $year = $date->format('Y');

        // render view
        View::renderTemplate('Admin/Bonus/update.html', [
            'collection'   => $collection,
            'currentYear'  => $year,
            'twoMonthsAgo' => $twoMonthsAgoName,
            'prevMonth'    => $prevMonthName,
            'currentMonth' => $month
        ]);
   }


   /*
    * displays populated update form
    */
   // public function updateBonusAction()
   // {
   //    // retrieve ID from URL (number = month e.g. 9 = Sep)
   //    $id = $this->route_params['id'];
   //
   //    // get collections data for specific month - pass id/month
   //    $collection = Collection::getBonusMonth($id);
   //
   //    // create variables for prev month & 2 months ago with conditional
   //    // logic for Jan & Feb
   //    $prevMonth = ($id - 1);
   //    // for Jan
   //    if($id == 1){
   //       $twoMonthsAgo = 11;
   //       $prevMonth = 12;
   //    }
   //    // for Feb
   //    else if($id == 2){
   //       $twoMonthsAgo = 12;
   //    }
   //    // other 10 months
   //    else {
   //       $twoMonthsAgo = ($id - 2);
   //    }
   //
   //    // check values
   //    // echo "Current month ID: $id<br>";
   //    // echo "Prev month ID: $prevMonth<br>";
   //    // echo "2 months ago ID: $twoMonthsAgo<br><br>";
   //
   //    // create months array
   //    $months = [
   //       '1'  => 'Jan',
   //       '2'  => 'Feb',
   //       '3'  => 'Mar',
   //       '4'  => 'Apr',
   //       '5'  => 'May',
   //       '6'  => 'Jun',
   //       '7'  => 'Jul',
   //       '8'  => 'Aug',
   //       '9'  => 'Sep',
   //       '10' => 'Oct',
   //       '11' => 'Nov',
   //       '12' => 'Dec'
   //    ];
   //
   //    // loop thru months array to find current month & assign name to variable
   //    foreach($months as $key => $value){
   //       // search for two months ago
   //       if($key == $twoMonthsAgo){
   //          $twoMonthsAgo = $value;
   //       }
   //       // search for previous month
   //       if($key == $prevMonth){
   //          $prevMonth = $value;
   //       }
   //       // search for current month
   //       if($key == $id){
   //          $month = $value;
   //       }
   //    }
   //
   //    // check values
   //    // echo "Selected/current month: $month <br>";
   //    // echo "Last month: $prevMonth <br>";
   //    // echo "Two months ago: $twoMonthsAgo <br>";
   //    // exit();
   //
   //    // create new DateTime object
   //    $date = new \DateTime();
   //
   //    // store current year in variable
   //    $year = $date->format('Y');
   //
   //    // echo "Current year is $year<br>";
   //
   //    // render view
   //    View::renderTemplate('Admin/Bonus/update.html', [
   //       'collection'   => $collection,
   //       'currentYear'  => $year,
   //       'twoMonthsAgo' => $twoMonthsAgo,
   //       'prevMonth'    => $prevMonth,
   //       'currentMonth' => $month
   //    ]);
   // }



   /*
    * updates bonus data for month
    */
   public function updateBonusMonthAction()
   {
      // retrieve ID from URL (id = month, e.g. Sep = 9)
      $id = $this->route_params['id'];

      // update collections table
      $results = Collection::updateBonusMonth($id);

      if($results)
     {
          header("Location: /admin/bonus");
          exit();
     } else {
        echo "Error updating data.";
        exit();
     }
   }

}
