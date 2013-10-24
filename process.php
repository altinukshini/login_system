<?php
/**
 * Process.php
 * 
 * The Process class is meant to simplify the task of processing
 * user submitted forms, redirecting the user to the correct
 * pages if errors are found, or if form is successful, either
 * way. Also handles the logout procedure.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 19, 2004
 */
include("include/session.php");

class Process
{
   /* Class constructor */
   function Process(){
      global $session;
      /* User submitted login form */
      if(isset($_POST['sublogin'])){
         $this->procLogin();
      }
      /* User submitted registration form */
      else if(isset($_POST['subjoin'])){
         $this->procRegister();
      }
      /* User submitted forgot password form */
      else if(isset($_POST['subforgot'])){
         $this->procForgotPass();
      }
      /* User submitted edit account form */
      else if(isset($_POST['subedit'])){
         $this->procEditAccount();
      }
      /* User submitted edit account form */
      else if(isset($_POST['pupload'])){
         $this->procChangePic();
      }
      /**
       * The only other reason user should be directed here
       * is if he wants to logout, which means user is
       * logged in currently.
       */
      else if($session->logged_in){
         $this->procLogout();
      }
      /**
       * Should not get here, which means user is viewing this page
       * by mistake and therefore is redirected.
       */
       else{
          header("Location: main.php");
       }
   }

   /**
    * procLogin - Processes the user submitted login form, if errors
    * are found, the user is redirected to correct the information,
    * if not, the user is effectively logged in to the system.
    */
   function procLogin(){
      global $session, $form;
      /* Login attempt */
      $retval = $session->login($_POST['user'], $_POST['pass'], isset($_POST['remember']));
      
      /* Login successful */
      if($retval){
         header("Location: ".$session->referrer);
      }
      /* Login failed */
      else{
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
   }
   
   /**
    * procLogout - Simply attempts to log the user out of the system
    * given that there is no logout form to process.
    */
   function procLogout(){
      global $session;
      $retval = $session->logout();
      header("Location: main.php");
   }
   
   /**
    * procRegister - Processes the user submitted registration form,
    * if errors are found, the user is redirected to correct the
    * information, if not, the user is effectively registered with
    * the system and an email is (optionally) sent to the newly
    * created user.
    */
   function procRegister(){
      global $session, $form;
      /* Convert username to all lowercase (by option) */
      if(ALL_LOWERCASE){
         $_POST['user'] = strtolower($_POST['user']);
      }
      /* Registration attempt */
      $retval = $session->register($_POST['user'], $_POST['pass'], $_POST['email']);
      
      /* Registration Successful */
      if($retval == 0){
         $_SESSION['reguname'] = $_POST['user'];
         $_SESSION['regsuccess'] = true;
         header("Location: ".$session->referrer);
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer);
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $_SESSION['reguname'] = $_POST['user'];
         $_SESSION['regsuccess'] = false;
         header("Location: ".$session->referrer);
      }
   }
   
   /**
    * procForgotPass - Validates the given username then if
    * everything is fine, a new password is generated and
    * emailed to the address the user gave on sign up.
    */
   function procForgotPass(){
      global $database, $session, $mailer, $form;
      /* Username error checking */
      $subuser = $_POST['user'];
      $field = "user";  //Use field name for username
      if(!$subuser || strlen($subuser = trim($subuser)) == 0){
         $form->setError($field, "* Username not entered<br>");
      }
      else{
         /* Make sure username is in database */
         $subuser = stripslashes($subuser);
         if(strlen($subuser) < 5 || strlen($subuser) > 30 ||
            !eregi("^([0-9a-z])+$", $subuser) ||
            (!$database->usernameTaken($subuser))){
            $form->setError($field, "* Username does not exist<br>");
         }
      }
      
      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
      }
      /* Generate new password and email it to user */
      else{
         /* Generate new password */
         $newpass = $session->generateRandStr(8);
         
         /* Get email of user */
         $usrinf = $database->getUserInfo($subuser);
         $email  = $usrinf['email'];
         
         /* Attempt to send the email with new password */
         if($mailer->sendNewPass($subuser,$email,$newpass)){
            /* Email sent, update database */
            $database->updateUserField($subuser, "password", md5($newpass));
            $_SESSION['forgotpass'] = true;
         }
         /* Email failure, do not change password */
         else{
            $_SESSION['forgotpass'] = false;
         }
      }
      
      header("Location: ".$session->referrer);
   }
   
   /**
    * procEditAccount - Attempts to edit the user's account
    * information, including the password, which must be verified
    * before a change is made.
    */
   function procEditAccount(){
      global $database, $session, $form, $function;

       /* Requested Username error checking */
       if(!$function->getUser() || strlen($function->getUser()) == 0 ||
          !eregi("^([0-9a-z])+$", $function->getUser()) ||
          !$database->usernameTaken($function->getUser())){
          die("Username not specified!");
      }
      /* Account edit attempt */
      $retval = $session->editAccount($function->getUser(), $_POST['curpass'], $_POST['newpass'], $_POST['email']);

      /* Account edit successful */
      if($retval){
         $_SESSION['useredit'] = true;
         header("Location: ".$session->referrer."?user=".$function->getUser());
      }
      /* Error found with form */
      else{
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer."?user=".$function->getUser());
      }
   }

   /**
    *  Edit User Profile Picture
    */
   function procChangePic(){
      global $database, $session, $function, $form;

      $get_user = $function->getUser();

       /* Requested Username error checking */
       if(!$get_user || strlen($get_user) == 0 ||
          !eregi("^([0-9a-z])+$", $get_user) ||
          !$database->usernameTaken($get_user)){
          die("Username not specified!");
      }
    
      $random = rand(000000000, 999999999);

      $filetype = $_FILES["file"]["type"];
      $filename = $_FILES["file"]["name"];
      $filesize = $_FILES["file"]["size"];
      $fileerror = $_FILES["file"]["error"];
      $tmp_file = $_FILES["file"]["tmp_name"];

      $allowedExts = array("jpg", "jpeg", "gif", "png", "JPG", "JPEG", "PNG");
      $allowedType = array("image/gif", "image/jpeg", "image/png", "image/jpg");
      $ext = @end(explode(".", $filename));

      $new_filename = $random."_".$get_user.".".$ext;
      $target = "uploads/images/profilepic/".$new_filename;

      $field = "file";

      if (!$filename) {
          $form->setError($field, "* Please select a file to upload!");
      }else{

        if (in_array($filetype, $allowedType) && in_array($ext, $allowedExts)){

          if ($filesize < 4194304) {

                if ( $fileerror > 0){
                  $form->setError($field, "* Return Code: ". $fileerror);
                }
                else{

                    if (move_uploaded_file($tmp_file,  $target)) {
                      /* Account edit attempt */
                      $retval = $session->editUserProfilePic($get_user, $new_filename);
                      /* Account edit successful */
                      if($retval){
                         $_SESSION['useredit'] = true;
                         header("Location: ".$session->referrer."?user=".$get_user);
                      }else{
                        $form->setError($field, "* Error saving filename to database!");
                      }

                    }else{
                     $form->setError($field, "* Error moving file to target path on the server, please check the folder permissions!");
                    }
                }
          }else{
            $form->setError($field, "* File too big! (Allowed size: 4MB)");
          }

        }
        else{
          $form->setError($field, "* File type / Extension not allowed!");
        }

      }
      /* Errors exist, have user correct them */
      if($form->num_errors > 0){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $form->getErrorArray();
         header("Location: ".$session->referrer."?user=".$get_user);
      }
   }

};

/* Initialize process */
$process = new Process;

?>
