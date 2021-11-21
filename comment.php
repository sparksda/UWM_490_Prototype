<?php
    session_start();
    include('config.php');
    $err = "A system error has occurred!!!";

    //Add a comment to the comments table
    if(isset($_POST['add'])){
        //global $dbc;
        $trimmed = array_map('trim', $_POST);
        $text = mysqli_real_escape_string($dbc, $trimmed['post-msg']);
        $blog_id = $_POST['blog_id'];
        $user_id = $_POST['user_id'];
        //A session variable to indicate which status-confirm div should be displayed in a blog item.
        //Its value will be used by the timeMsg function in the script.js file to display the value contained in
        $_SESSION['status-confirm-msg'] = "status-confirm$blog_id";
        
        if(empty($text)){
            $_SESSION['message'] = "The text entry cannot be empty!!!";
        }else{
            $text = mysqli_real_escape_string($dbc, $text); 
            $query = "INSERT INTO comments (comment_id, comment_text, comment_date, blog_id, user_id) VALUES ('NULL', '$text', NOW(), '$blog_id', '$user_id')";
            $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));

            $affected_rows = mysqli_affected_rows($dbc);
        
            if($affected_rows == 1){//query executed successfully
                $_SESSION['message'] = "Your comment was posted!";
            }else{
                $_SESSION['message'] = $err;
            }
        }
    
        $returnURL = $_POST['returnURL'].'#post-msg'.$blog_id;
        header("location: $returnURL");
    }

    //Update a comment in the comments table.
   if(isset($_POST['update'])){
       //global $dbc;
        $comment_id = $_POST['comment_id'];
        $blog_id = $_POST['blog_id'];
        $_SESSION['status-confirm-msg'] = "status-confirm$blog_id";
        $trimmed = array_map('trim', $_POST);
        $text = mysqli_real_escape_string($dbc, $trimmed['txtarea']);
        
       
       if(empty($text)){
            $_SESSION['message'] = "The text entry cannot be empty!!!";
        }else{
            //Query the comments table to get the text that is currently stored and compare it to the text that we want to replace it with.
           //The reason for this query is when an attempt is made to replace text in the database with the same text
           //an update is not made and the SQL result returns 0 affected rows. The update query below determines if
           //the query was successfull if 1 row is returned. If 0 rows are returned then we display a system error.
           $query = "SELECT comment_text FROM comments WHERE comment_id = $comment_id";
           $result = mysqli_query($dbc, $query) or trigger_error("Query: $query<br/>MySQL Error: ".mysqli_error($dbc));
           $affected_rows = mysqli_affected_rows($dbc);
       
            if($affected_rows == 1){//query executed successfully
                $row = mysqli_fetch_array($result, MYSQLI_NUM);
                if($row[0] == $text){
                    $_SESSION['message'] = "Your comment text already exists in the database!";
                }else{
                    $query = "UPDATE comments SET comment_text ='$text' WHERE comment_id = $comment_id";
                    $result = mysqli_query($dbc, $query) or trigger_error("Query: $query<br/>MySQL Error: ".mysqli_error($dbc));
                    $affected_rows = mysqli_affected_rows($dbc);

                    if($affected_rows == 1){//query executed successfully
                        $_SESSION['message'] = "Your comment was updated!";
                     }else{
                        $_SESSION['message'] = $err;
                     } 
                }
            }else{
                $_SESSION['message'] = $err;
            }
        }
         
       $returnURL = $_POST['returnURL'].'#post-msg'.$blog_id;
        header("location: $returnURL");    
    }

    //Delete a comment from the comments table.
    if(isset($_POST['delete'])){
        //global $dbc;
        $blog_id = $_POST['blog_id'];
        $_SESSION['status-confirm-msg'] = "status-confirm$blog_id";
            
        if($_POST['choice'] == "YES"){ //User selected the YES radio button to confirm deletion.
            $comment_id = $_POST['comment_id'];
            $query = "DELETE FROM comments WHERE comment_id = $comment_id";
            $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));

            $affected_rows = mysqli_affected_rows($dbc);

            if($affected_rows == 1){//query executed successfully
                $_SESSION['message'] = "Your comment was deleted!";
             }else{
                $_SESSION['message'] = $err;
             }
        }
        
        
        $returnURL = $_POST['returnURL'].'#post-msg'.$blog_id;
        header("location: $returnURL");
    }

?>