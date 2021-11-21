<?php
// This is the logout page for wanderingbloggers.com
session_start();
include('config.php');
$url = BASE_URL . '/index.php';

// If no fname seesion variable exists , redirect the user;
if (!isset($_SESSION['user_id'])) {
    
   
    ob_end_clean(); // Delete the buffer.
    header("Location: $url");
    exit();
    
} else { //Log out the user.

    $_SESSION = []; // Destroy the variables.
    session_destroy(); // Destroy the session itself.
    setcookie(session_name(), '', time()-3600); // Destroy the cookie.
}
echo '<title>See you again soon!!!</title>';
//redirect user to login.php page after 5 seconds.
echo '<script type="text/JavaScript">setTimeout("location.href = \''.$url.'\';",5000);</script>';
// Print a message:
echo '<h3 style="text-align: center;">You have successfully logged out. Thank you for sharing your experiences. Wanderers are always welcome!</h3>';
echo '<h3 style="text-align: center;">Redirecting to login page in 5 seconds...</h3>';
?>