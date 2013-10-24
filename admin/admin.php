<?php
/**
 * Admin.php
 *
 * This is the Admin Center page. Only administrators
 * are allowed to view this page. This page displays the
 * database table of users and banned users. Admins can
 * choose to delete specific users, delete inactive users,
 * ban users, update user levels, etc.
 *
 * Written by: 
 * Last Updated: 
 */
include("../include/session.php");

/**
 * displayUsers - Displays the users database table in
 * a nicely formatted html table.
 */
function displayUsers(){
   global $database;
   $q = "SELECT username,userlevel,email,timestamp "
       ."FROM ".TBL_USERS." ORDER BY userlevel DESC,username";
   $result = $database->query($q);
   /* Error occurred, return given name by default */
   $num_rows = mysql_numrows($result);
   if(!$result || ($num_rows < 0)){
      echo "Error displaying info";
      return;
   }
   if($num_rows == 0){
      echo "Database table empty";
      return;
   }
   /* Display table contents */
   echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr><td><b>Username</b></td><td><b>Level</b></td><td><b>Email</b></td><td><b>Last Active</b></td></tr>\n";
   for($i=0; $i<$num_rows; $i++){
      $uname  = mysql_result($result,$i,"username");
      $ulevel = mysql_result($result,$i,"userlevel");
      $email  = mysql_result($result,$i,"email");
      $time   = mysql_result($result,$i,"timestamp");

      echo "<tr><td>$uname</td><td>$ulevel</td><td>$email</td><td>$time</td></tr>\n";
   }
   echo "</table><br>\n";
}

/**
 * displayBannedUsers - Displays the banned users
 * database table in a nicely formatted html table.
 */
function displayBannedUsers(){
   global $database;
   $q = "SELECT username,timestamp "
       ."FROM ".TBL_BANNED_USERS." ORDER BY username";
   $result = $database->query($q);
   /* Error occurred, return given name by default */
   $num_rows = mysql_numrows($result);
   if(!$result || ($num_rows < 0)){
      echo "Error displaying info";
      return;
   }
   if($num_rows == 0){
      echo "Database table empty";
      return;
   }
   /* Display table contents */
   echo "<table align=\"left\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n";
   echo "<tr><td><b>Username</b></td><td><b>Time Banned</b></td></tr>\n";
   for($i=0; $i<$num_rows; $i++){
      $uname = mysql_result($result,$i,"username");
      $time  = mysql_result($result,$i,"timestamp");

      echo "<tr><td>$uname</td><td>$time</td></tr>\n";
   }
   echo "</table><br>\n";
}
   
/**
 * User not an administrator, redirect to main page
 * automatically.
 */
if(!$session->isAdmin()){
   header("Location: ../main.php");
}
else{
/**
 * Administrator is viewing page, so display all
 * forms.
 */
?>
<html>
<title>Employee management system</title>
<body>
<h1>Admin Center</h1>
<font size="5" color="#ff0000">
<b>::::::::::::::::::::::::::::::::::::::::::::</b></font>
<font size="4">Logged in as <b><?php echo $session->username; ?></b></font><br><br>
Back to [<a href="../main.php">Main Page</a>]<br><br>
<?php
if($form->num_errors > 0){
   echo "<font size=\"4\" color=\"#ff0000\">"
       ."!*** Error with request, please fix</font><br><br>";
}
?>
<table align="left" border="0" cellspacing="5" cellpadding="5">
<tr><td>
<?php
/**
 * Display Users Table
 */
?>
<h3>Users Table Contents:</h3>
<?php
displayUsers();
?>
</td></tr>
<tr>
<td>
<br>
<?php
/**
 * Update User Level
 */
?>
<h3>Update User Level</h3>
<?php echo $form->error("upduser"); ?>
<table>
<form action="adminprocess.php" method="POST">
<tr><td>
Username:<br>
<input type="text" name="upduser" maxlength="30" value="<?php echo $form->value("upduser"); ?>">
</td>
<td>
Level:<br>
<select name="updlevel">
<option value="1">1
<option value="9">9
</select>
</td>
<td>
<br>
<input type="hidden" name="subupdlevel" value="1">
<input type="submit" value="Update Level">
</td></tr>
</form>
</table>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr>
<td>
<?php
/**
 * Delete User
 */
?>
<h3>Delete User</h3>
<?php echo $form->error("deluser"); ?>
<form action="adminprocess.php" method="POST">
Username:<br>
<input type="text" name="deluser" maxlength="30" value="<?php echo $form->value("deluser"); ?>">
<input type="hidden" name="subdeluser" value="1">
<input type="submit" value="Delete User">
</form>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr>
<td>
<?php
/**
 * Delete Inactive Users
 */
?>
<h3>Delete Inactive Users</h3>
This will delete all users (not administrators), who have not logged in to the site<br>
within a certain time period. You specify the days spent inactive.<br><br>
<table>
<form action="adminprocess.php" method="POST">
<tr><td>
Days:<br>
<select name="inactdays">
<option value="3">3
<option value="7">7
<option value="14">14
<option value="30">30
<option value="100">100
<option value="365">365
</select>
</td>
<td>
<br>
<input type="hidden" name="subdelinact" value="1">
<input type="submit" value="Delete All Inactive">
</td>
</form>
</table>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr>
<td>
<?php
/**
 * Ban User
 */
?>
<h3>Ban User</h3>
<?php echo $form->error("banuser"); ?>
<form action="adminprocess.php" method="POST">
Username:<br>
<input type="text" name="banuser" maxlength="30" value="<?php echo $form->value("banuser"); ?>">
<input type="hidden" name="subbanuser" value="1">
<input type="submit" value="Ban User">
</form>
</td>
</tr>
<tr>
<td><hr></td>
</tr>
<tr><td>
<?php
/**
 * Display Banned Users Table
 */
?>
<h3>Banned Users Table Contents:</h3>
<?php
displayBannedUsers();
?>
</td></tr>
<tr>
<td><hr></td>
</tr>
<tr>
<td>
<?php
/**
 * Delete Banned User
 */
?>
<h3>Delete Banned User</h3>
<?php echo $form->error("delbanuser"); ?>
<form action="adminprocess.php" method="POST">
Username:<br>
<input type="text" name="delbanuser" maxlength="30" value="<?php echo $form->value("delbanuser"); ?>">
<input type="hidden" name="subdelbanned" value="1">
<input type="submit" value="Delete Banned User">
</form>
</td>
</tr>

<td><hr></td>

<tr>
<td>
<?php
/**
 * Add  User
 */
?>
<h3>Add user</h3>

<form action="adminprocess.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Username:</td><td><input type="text" name="user" maxlength="30" value="<?php echo $form->value("user"); ?>"></td><td><?php echo $form->error("user"); ?></td></tr>
<tr><td>Password:</td><td><input type="password" name="pass" maxlength="30" value="<?php echo $form->value("pass"); ?>"></td><td><?php echo $form->error("pass"); ?></td></tr>
<tr><td>Email:</td><td><input type="text" name="email" maxlength="50" value="<?php echo $form->value("email"); ?>"></td><td><?php echo $form->error("email"); ?></td></tr>
<tr><td colspan="2" align="right">
<input type="hidden" name="adduser" value="1">
<input type="submit" value="Add!"></td></tr>
</table>
</form>
</td>
</tr>
<tr><td colspan="2" align="left"><a href="main.php">Back to Main</a></td></tr>
<?php
if($form->num_errors > 0){
   echo "<td><font size=\"2\" color=\"#ff0000\">".$form->num_errors." error(s) found</font></td>";
}
?>


</table>
</body>
</html>
<?php
}
?>

