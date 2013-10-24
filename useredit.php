<?php
/**
 * UserEdit.php
 *
 * This page is for users to edit their account information
 * such as their password, email address, etc. Their
 * usernames can not be edited. When changing their
 * password, they must first confirm their current password.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 26, 2004
 */
include("include/session.php");
?>

<html>
<title>Jpmaster77's Login Script</title>
<body>

<?php

/**
 * If user is not logged in, then do not display anything.
 * If user is logged in, then display the form to edit
 * account information, with the current email address
 * already in the field.
 */
if($session->logged_in){

	$get_user = $function->getUser();

	if(!$get_user || strlen($get_user) == 0 ||
	   !eregi("^([0-9a-z])+$", $get_user) ||
	   !$database->usernameTaken($get_user)){
	   die("Username not registered or specified!");
	}

	// $get_user = trim($_GET['user']);
	// If user is admin, let him view this page (user info)
	if($session->isAdmin() || ($session->username == $get_user)){

		/**
		 * User has submitted form without errors and user's
		 * account has been edited successfully.
		 */
		if(isset($_SESSION['useredit'])){
		   unset($_SESSION['useredit']);
		   echo "<div style=\"color:green;\"><b>$get_user"."'s"."</b>,  account has been successfully updated.</div> "
		       ."<br />See $get_user"."'s "."<a href=\"userinfo.php?user=$get_user\">profile page</a>.";
		}

		/* Logged in user viewing own account */
		if(strcmp($session->username,$get_user) == 0){
		   echo "<h1>Edit my account:</h1>";
		}
		/* Admin viewing others account */
		else{
		   echo "<h1>User Account Edit : $get_user</h1>";;
		}	

		/* Display requested user information */
		$req_user_info = $database->getUserInfo($get_user);

		if($form->num_errors > 0){
		   echo "<td><font size=\"2\" color=\"#ff0000\">".$form->num_errors." error(s) found</font></td><br />";
		   echo "<td><font size=\"2\" color=\"#ff0000\">".$form->spec_error."</font></td>";
		}
?>
		<form action="<?php echo 'process.php?user='.$get_user  ?>" method="POST" enctype="multipart/form-data">   
		    <?php
		      echo '<div style="float:left;" class="imgLow">';
		      echo "<img src='".$function->getUserPic($get_user)."' alt='Profile picture' style='padding:5px;' width='85'   class='doubleborder'/></div>";         
		      ?>
		      <input style="margin-top:5px;" type="file" name="file" /><br />
		      <input type="hidden" name="pupload" value="1">
		      <input type="submit" name="upload" value="Upload"><br /><?php echo $form->error("file"); ?></td>
		</form>
		<form style="clear:both;" action="process.php?user=<?php echo $get_user  ?>" method="POST">
			<table align="left" border="0" cellspacing="0" cellpadding="3">
				<tr>
				<td>Current Password:</td>
				<td><input type="password" name="curpass" maxlength="30" value="<?php echo $form->value("curpass"); ?>"></td>
				<td><?php echo $form->error("curpass"); ?></td>
				</tr>

				<tr>
				<td>New Password:</td>
				<td><input type="password" name="newpass" maxlength="30" value="<?php echo $form->value("newpass"); ?>"></td>
				<td><?php echo $form->error("newpass"); ?></td>
				</tr>
				
				<tr>
				<td>Email:</td>
				<td><input type="text" name="email" maxlength="50" value="<?php
					if($form->value("email") == ""){
					   echo $req_user_info['email'];
					}else{
					   echo $form->value("email");
					}
					?>">
				</td>
				<td><?php echo $form->error("email"); ?></td>
				</tr>

				<tr>
				<td colspan="2" align="right">
					<input type="hidden" name="subedit" value="1">
					<input type="submit" value="Edit Account"></td>
				</tr>
				<tr>
				<td colspan="2" align="left"></td>
				</tr>
			</table>
		</form>
		<a style="float:left; clear:both;" href="main.php">Back to Main</a>

<?php
	}else{
	   // echo "You don't have permission to edit other users profiles!";
	   header("Location: main.php");
	}
}
else{
	echo "You are not allowed to view this page, please [<a href=\"main.php\">Login</a>]";
}


?>

</body>
</html>
