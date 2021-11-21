<?php
 session_start();
 include('config.php');
 
 //Delete a blog from the blog table.
    if(isset($_GET['delete-blogId'])){
        global $dbc;
        $blog_id = $_GET['delete-blogId'];
            
        $query = "DELETE FROM blogs WHERE blog_id = $blog_id";
        $result = mysqli_query($dbc, $query);

        $affected_rows = mysqli_affected_rows($dbc);

        if($affected_rows == 1){//query executed successfully
            $_SESSION['message'] = "Your blog was successfully deleted!";
            $_SESSION['status-confirm-blog'] = "status-confirm-blog";
         }
        
        $returnURL = $_GET['returnURL'];
        echo '<script type="text/JavaScript">setTimeout("location.href = \''.$returnURL.'\';");</script>';
    }

?>