<?php
// This is the login page for wanderingbloggers.com
$page_title = 'Login';
$url_style3 = ' style="color: red;"';
include ("includes/header.php");
include('config.php');
$url = BASE_URL . '/index.php';
$err_msg = "";

// User ís already in an active login session on the site.
if(isset($_SESSION['user_id']) && isset($_SESSION['activated'])){

    if($_SESSION['activated'] == TRUE){
        ob_end_clean(); // Delete the buffer.
        // Redirect the user:
        echo '<script type="text/JavaScript">setTimeout("location.href = \''.$url.'\';");</script>';
        exit(); // Quit the script.
    }
  
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validation of the email address:
    if (!empty($_POST['email'])) {
        $e = mysqli_real_escape_string($dbc, $_POST['email']);
    } else {
        $e = FALSE;
        $err_msg = $err_msg ."<br>-You forgot to enter your email!";
    }
    
    // Validation of the password:
    if (!empty($_POST['pass'])) {
        $p = trim($_POST['pass']);
    } else {
        $p = FALSE;
        $err_msg = $err_msg ."<br>-You forgot to enter your password!";
    }
    
    if ($e && $p) { // If everything is OK.
    
        // Query the database:
        $q = "SELECT user_id, fname, lname, email, password, user_level, reg_date FROM users WHERE email='$e' AND password=SHA1('$p')";
        $r = mysqli_query($dbc, $q) or trigger_error("Query: $q\n<br>MySQL Error: " . mysql_error($dbc));
        
		if (mysqli_num_rows($r) == 1) { // A match was made.

			// Fetch the values: 
			list($user_id, $fname, $lname, $email, $password, $user_level, $reg_date) = mysqli_fetch_array($r, MYSQLI_NUM);
			mysqli_free_result($r);
			
			$_SESSION['user_id'] = $user_id;
			$_SESSION['fname'] = $fname;
			$_SESSION['lname'] = $lname;
			$_SESSION['email'] = $email;
			$_SESSION['user_level'] = $user_level;
			$_SESSION['reg_date'] = $reg_date;
			$_SESSION['activated'] = TRUE;
			
			//Redirect to main page.
			echo '<script type="text/JavaScript">setTimeout("location.href = \''.$url.'\';");</script>';
			exit(); // Quit the script.

		} else { // No match was made.
           $err_msg = $err_msg ."<br>-Either the email address and/or password you entered do match those on file."; 
		}
        
	} 
              
}

?>

<div class="container">
    <div class="form-wrap">
        <p id="error" style="color:red; font-weight:bold"></p>
        <form action="login.php" method="post">
            <h1>Login</h1>
            <p>Your browser must allow cookies for a successful log in.</p>
            <hr><br />
            <fieldset>
               <div class="form-group">
                    <p class="label"><strong>Email Address</strong></p> <input type="email" name="email" size="25" maxlength="65"
                    value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>">
                </div>

                <div class="form-group">
                    <p class="label"><strong>Password</strong></p> <input type="password" name="pass" size="25" 
                    value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>">
                </div>

                <div class="form-group"><input type="submit" name="submit" value="Login"></div>
            </fieldset>
        </form>
    </div>
</div>

<?php
     if(!empty($err_msg)){
        echo '<script type="text/javascript">showMsgById("'.$err_msg.'", "error");</script>';
        echo 'Failed';
     }
     
?>

<?php include ("includes/footer.html") ?>
