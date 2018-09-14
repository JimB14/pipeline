<?php

namespace App\Models;

use PDO;
use \App\Config;


class Collection extends \Core\Model
{


   public static function getCollections($year)
   {

      try
      {
         // establish db connection
         $db = static::getDB();

         // retrieve data
         $sql = "SELECT * FROM collections
                 WHERE year = :year
                 ORDER BY year, id";
         $stmt = $db->prepare($sql);
         $parameters = [
            ':year' => $year
         ];
         $stmt->execute($parameters);

         $collections = $stmt->fetchAll(PDO::FETCH_OBJ);

         // return to Admin Controller
         return $collections;
      }
      catch(PDOException $e)
      {
         echo $e->getMessage();
         exit();
      }
   }














   public static function getCollectionsLastMonth($month)
   {
      try
      {
         // set id value to last month
         if(isset($month) && $month > 2){
            $month = $month - 2;
         }
         // else {
         //    echo "Code fails for Jan or Feb. Contact your developer to fix.";
         //    exit();
         // }

         // establish db connection
         $db = static::getDB();

         // retrieve data
         $sql = "SELECT colCurrent FROM collections
                 WHERE id = :month";
         $stmt = $db->prepare($sql);
         $parameters = [
            ':month' => $month
         ];
         $stmt->execute($parameters);

         $result = $stmt->fetchAll(PDO::FETCH_OBJ);

         // return to Admin Controller
         return $result;
      }
      catch(PDOException $e)
      {
         echo $e->getMessage();
         exit();
      }
   }




   public static function getCollectionsTwoMonthsAgo($month)
   {
      try
      {
         // set id value to last month
         if(isset($month) && $month > 2){
            $month = $month - 2;
         }
         // else {
         //    echo "Code fails for Jan or Feb. Contact your developer to fix.";
         //    exit();
         // }

         // establish db connection
         $db = static::getDB();

         // retrieve data
         $sql = "SELECT colCurrent FROM collections
                 WHERE id = :month";
         $stmt = $db->prepare($sql);
         $parameters = [
            ':month' => $month
         ];
         $stmt->execute($parameters);

         $result = $stmt->fetchAll(PDO::FETCH_OBJ);

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
    * get all data from last month
    *
    * @param  String  $month  The current month
    * @return Object          Last month's data
    */
   public static function getLastMonth($month)
   {
      try
      {
         // set id value to last month
         if(isset($month) && $month > 2){
            $month = $month - 1;
         }

         // establish db connection
         $db = static::getDB();

         // retrieve data
         $sql = "SELECT * FROM collections
                 WHERE id = :month";
         $stmt = $db->prepare($sql);
         $parameters = [
            ':month' => $month
         ];
         $stmt->execute($parameters);

         $result = $stmt->fetch(PDO::FETCH_OBJ);

         // return to Admin Controller
         return $result;
      }
      catch(PDOException $e)
      {
         echo $e->getMessage();
         exit();
      }
   }



   public static function getOverheadTwoMonthsAgo($month)
   {
      try
      {
         // set id value to last month
         if(isset($month) && $month > 2){
            $month = $month - 2;
         }

         // establish db connection
         $db = static::getDB();

         // retrieve data
         $sql = "SELECT ohTwoMonthsAgo FROM collections
                 WHERE id = :month";
         $stmt = $db->prepare($sql);
         $parameters = [
            ':month' => $month
         ];
         $stmt->execute($parameters);

         $result = $stmt->fetch(PDO::FETCH_OBJ);

         // return to Admin Controller
         return $result;
      }
      catch(PDOException $e)
      {
         echo $e->getMessage();
         exit();
      }
   }





   /*
    * Retrieves data by ID from collections table
    */
   public static function getBonusMonth($year, $month)
   {
      try
      {
         // establish db connection
         $db = static::getDB();

         // retrieve data
         $sql = "SELECT * FROM collections
                 WHERE year = :year
                 AND month = :month";
         $stmt = $db->prepare($sql);
         $parameters = [
            ':year'  => $year,
            ':month' => $month
         ];
         $stmt->execute($parameters);

         $collection = $stmt->fetch(PDO::FETCH_OBJ);

         // return to Admin Controller
         return $collection;
      }
      catch(PDOException $e)
      {
         echo $e->getMessage();
         exit();
      }
   }


   /**
    * updated collections table in db
    *
    * @param  Int  $id   ID of record to update
    *
    * @return boolean
    */
   public static function updateBonusMonth($id)
   {
      $ohTwoMonthsAgo = (isset($_REQUEST['ohTwoMonthsAgo'])) ? filter_var($_REQUEST['ohTwoMonthsAgo'], FILTER_SANITIZE_NUMBER_INT) : '';
      $ohPrevMonth = (isset($_REQUEST['ohPrevMonth'])) ? filter_var($_REQUEST['ohPrevMonth'], FILTER_SANITIZE_NUMBER_INT) : '';
      $ohCurrent = (isset($_REQUEST['ohCurrent'])) ? filter_var($_REQUEST['ohCurrent'], FILTER_SANITIZE_NUMBER_INT) : '';
      $colTwoMonthsAgo = (isset($_REQUEST['colTwoMonthsAgo'])) ? filter_var($_REQUEST['colTwoMonthsAgo'], FILTER_SANITIZE_NUMBER_INT) : '';
      $colPrevMonth = (isset($_REQUEST['colPrevMonth'])) ? filter_var($_REQUEST['colPrevMonth'], FILTER_SANITIZE_NUMBER_INT) : '';
      $colCurrent = (isset($_REQUEST['colCurrent'])) ? filter_var($_REQUEST['colCurrent'], FILTER_SANITIZE_NUMBER_INT) : '';

      // echo '<pre>';
      // print_r($_REQUEST);
      // echo '</pre>';
      // exit();

      try
      {
         // establish db connection
         $db = static::getDB();

         $sql = "UPDATE collections SET
                  ohTwoMonthsAgo  = :ohTwoMonthsAgo,
                  ohPrevMonth     = :ohPrevMonth,
                  ohCurrent       = :ohCurrent,
                  colTwoMonthsAgo = :colTwoMonthsAgo,
                  colPrevMonth    = :colPrevMonth,
                  colCurrent      = :colCurrent
                  WHERE id = :id";
         $stmt = $db->prepare($sql);
         $parameters = [
            ':id'              => $id,
            ':ohTwoMonthsAgo'  => $ohTwoMonthsAgo,
            ':ohPrevMonth'     => $ohPrevMonth,
            ':ohCurrent'       => $ohCurrent,
            ':colTwoMonthsAgo' => $colTwoMonthsAgo,
            ':colPrevMonth'    => $colPrevMonth,
            ':colCurrent'      => $colCurrent
         ];
         $result = $stmt->execute($parameters);

         return $result;
      }
      catch(PDOException $e)
      {
         echo $e->getMessage();
         exit();
      }
   }

}
