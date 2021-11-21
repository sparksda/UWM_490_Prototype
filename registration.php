<?php
    $page_title = 'User Registration';
    $url_style4 = ' style="color: red;"';
    include('config.php');
    include ("includes/header.php");
    $url = BASE_URL . '/index.php';
    $err_msg = "";
    $ok_msg = "";

    //Redirect user back to main page if they are signed in already.
    //A signed in user should not be able to access the registration page.
    if(isset($_SESSION['activated']) && $_SESSION['activated'] == TRUE){
        echo '<script type="text/JavaScript">setTimeout("location.href = \''.$url.'\';");</script>';
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //trim data
       $trimmed = array_map('trim', $_POST);
       $errors = array();

        if (!empty($_POST['fname'])) {
            $fn = mysqli_real_escape_string($dbc, $trimmed['fname']);
        }else{
            $err_msg = $err_msg ."<br>-You must enter the first name.";
        }

        if (!empty($_POST['lname'])) {
            $ln = mysqli_real_escape_string($dbc, $trimmed['lname']);
        }else{
            $err_msg = $err_msg ."<br>-You must enter the last name.";
        }

        if (!empty($_POST['email'])) {
            $e = mysqli_real_escape_string($dbc, $trimmed['email']);
        }else{
            $err_msg = $err_msg ."<br>-You must enter the email address.";
        }
        
        if (!empty($_POST['phonenumber'])) {
            $ph = mysqli_real_escape_string($dbc, $trimmed['phonenumber']);
        }

        if (!empty($_POST['password'])) {
            $p = mysqli_real_escape_string($dbc, $trimmed['password']);
            
            if(strlen($p) < 6){
                $err_msg = $err_msg ."<br>-The password length must be 6 or more characters";
            }
        }else{
            $err_msg = $err_msg ."<br>-You must enter the password.";
        }
        
        if(empty($err_msg)){
			//Check to see if email address already exists.
            $q = "SELECT email FROM users WHERE email='$e'";
            $r = mysqli_query($dbc, $q);

            if(mysqli_num_rows($r) == 0){
                //Add user to the database
                $q = "INSERT INTO users (user_id, fname, lname, email, password, user_level, reg_date) VALUES ('', '$fn', '$ln', '$e', SHA1('$p'), 0, NOW())";
                $r = mysqli_query($dbc, $q);

                if(mysqli_affected_rows($dbc) == 1){//query executed successfully
                    $url = BASE_URL . '/login.php';
                    //redirect user to login.php page after 5 seconds.
                    echo '<script type="text/JavaScript">setTimeout("location.href = \''.$url.'\';",5000);</script>';
                    $ok_msg = "Thank you for registering!!!<br>Redirecting to login page in 5 seconds...";
                }else{
                    $err_msg = "-You could not be registered due to a system error.";
                }

                mysqli_close($dbc);
            }else{
                $err_msg = "-The email addressed entered has already been registered.";
            }
        }
      
       
    }
?>


<div class="container">
    <div class="form-wrap">
        <p id="error" style="color:red; font-weight:bold;"></p>
        <p id="success" style="font-weight: bold;"></p>
        <form action="registration.php" method="post">
            <h1>Registration</h1>
            <p>Fill out the form with correct values.</p>
            <hr><br />

            <fieldset>
                <div class="form-group">
                    <label for="fname"><b>First Name</b></label>
                    <input type="text" name="fname" value="<?php if (isset($trimmed['fname'])) echo $trimmed['fname']; ?>">
                </div>

                <div class="form-group">
                    <label for="lname"><b>Last Name</b></label>
                    <input type="text" name="lname" value="<?php if (isset($trimmed['lname'])) echo $trimmed['lname']; ?>">
                </div>

                <div class="form-group">
                    <label for="email"><b>Email Address</b></label>
                    <input type="email" name="email" value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>">
                </div>

                <div class="form-group">
                    <label for="phonenumber"><b>Phone Number</b></label>
                    <input type="text" name="phonenumber" value="<?php if (isset($trimmed['phonenumber'])) echo $trimmed['phonenumber']; ?>">
                </div>

                <div class="form-group">
                    <label for="password"><b>Password</b></label>
                    <input type="password" name="password" value="<?php if (isset($trimmed['password'])) echo $trimmed['password']; ?>">
                </div>

                <div class="form-group">
                    <input type="submit" name="submit" value="Register">
                </div>
            </fieldset>
        </form>
    </div>
</div>

<?php
     if(!empty($err_msg)){
        echo '<script type="text/javascript">showMsgById("'.$err_msg.'", "error");</script>';
        echo 'Failed';
     }
    if(!empty($ok_msg)){
        echo '<script type="text/javascript">showMsgById("'.$ok_msg.'", "success");</script>';
        echo 'Failed';
     }
     
?>

<?php include ("includes/footer.html") ?>
