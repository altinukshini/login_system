<?
/**
 * functions.php
 */

class Functions
{
    // Checks if user picture exists in the database
    // If there is no pic, it returns the default picture from the styles folder
    function getUserPic($username){
      global $database;
      $userpic = $database->checkUserPic($username);
      $pathuserpic = "uploads/images/profilepic/".$userpic;
      if ($userpic == NULL) {
        return "style/images/default.png";
      }
      elseif (!file_exists($pathuserpic)) {
        return "style/images/default.png";
      }
      else{
        return $pathuserpic;
      }
    }

    // Gets the username from ?user=
    function getUser(){
      $username= trim($_GET['user']);
      return $username  ;
    }

};

$function = new Functions;

?>
