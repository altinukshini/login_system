<?
/**
 * UserInfo.php
 *
 * This page is for users to view their account information
 * with a link added for them to edit the information.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 26, 2004
 */
include("include/session.php");
?>

<html>
<title>Jpmaster77's Login Script</title>
<body>

<?

/* You need to be loged in to view this page */
if($session->logged_in){
		
	// $req_user = trim($_GET['user']);
	// If user is admin, let him view this page (user info)
	if($session->isAdmin() || ($session->username == trim($_GET['user']))){

		/* Requested Username error checking */
		$req_user = trim($_GET['user']);
		if(!$function->getUser() || strlen($function->getUser()) == 0 ||
		   !eregi("^([0-9a-z])+$", $function->getUser()) ||
		   !$database->usernameTaken($function->getUser())){
		   die("Username not registered or specified!");
		}

		/* Logged in user viewing own account */
		if(strcmp($session->username,$function->getUser()) == 0){
		   echo "<h1>My Account</h1>";
		}
		/* Visitor not viewing own account */
		else{
		   echo "<h1>User Info</h1>";
		}

		/* Display requested user information */
		$req_user_info = $database->getUserInfo($function->getUser());

		echo "<img src='".$function->getUserPic($function->getUser())."' alt='profile picture' width='150' class='doubleborder'/><br /><br />";
		
		/* Name */
		echo "<b>".$req_user_info['firstname']." ". $req_user_info['lastname']."</b><br><br>";

		// Username
		echo "<b>Username: </b>".$req_user_info['username']."<br>";

		/* Email */
		echo "<b>Email:</b> ".$req_user_info['email']."<br>";

		/**
		 * Note: when you add your own fields to the users table
		 * to hold more information, like homepage, location, etc.
		 * they can be easily accessed by the user info array.
		 *
		 * $session->user_info['location']; (for logged in users)
		 *
		 * ..and for this page,
		 *
		 * $req_user_info['location']; (for any user)
		 */

		/* If logged in user viewing own account, give link to edit */
		// if(strcmp($session->username,$req_user) == 0){
		if ($session->isAdmin() || ($session->username == $function->getUser())){
		   echo "<br><a href=\"useredit.php?user=$req_user\">Edit Account Information</a><br>";
		}

		/* Link back to main */
		echo "<br>Back To [<a href=\"main.php\">Main</a>]<br>";
	}
	else{
		   echo "You don't have permission to view this page!";
	}
}
else{
  echo "You are not allowed to view this page, please [<a href=\"main.php\">Login</a>]";
}

?>

</body>
</html>
