<?php 
$page_title = "Reports";
$url_style5 = ' style="color: red;"';
include('config.php');
require_once( ROOT_PATH . '/includes/functions.php'); 
include( ROOT_PATH . '/includes/header.php'); 
$current_page = basename($_SERVER['PHP_SELF']);

$display = 3; //Set the number of blogs to display per page
$confirm_msg_ok = "Your comment was posted!";
$confirm_msg_error = "Your comment could not be added to the database due to a system error!";

$user_level = 0;

//canModify determines if a user can add or update a POST.
//1 = user is logged in and should have CRUD privileges. 0 = user has not CRUD privileges.
$canModify = 0; 
$user_id = 0;    

if(isset($_SESSION['user_id']) && isset($_SESSION['activated']) && $_SESSION['activated'] == TRUE){
    $user_id = $_SESSION['user_id'];
    $canModify = 1;
    $curr_page = "current_page";
}

//user_level determines if signed in user have CRUD privileges for all posts or theirs only. A value of 1 means the person has all CRUD privileges.
if(isset($_SESSION['user_level']) && is_numeric($_SESSION['user_level'])){
    $user_level = $_SESSION['user_level'];
}
    
?>


<!--Container-->
<div class="container" id="blogs">

    <div class="table-container">
    <h1>Reports</h1>

<?php
 
    if($user_level == 1){

        $order_by = 'b.blog_date DESC';   
        //Retrieve blogs, comments, and the users that created them.
        $blogs = getBlogs($order_by);
        $users = getUsers();
        $comments = getComments();
        $top_blogger = getTopBlogger()[0];
        $top_commentator = getTopCommentator()[0];
        $top_blog = getTopRatedBlog()[0];

        echo '<div id="system-report">';
        echo "<h3>System Report:</h3>";
        echo '<div id="data">';
        echo '<strong>Total Users</strong>: '.count($users);
        echo '<br>';
        echo '<strong>Total Blogs</strong>: '.count($blogs);
        echo '<br>';
        echo '<strong>Total Comments</strong>: '.count($comments);
        echo '<br>';
        echo '<strong>Top Blogger</strong>: ';
        echo '<br ><div class="mg-2">Name: '.$top_blogger['fname'].' '.$top_blogger['lname'];
        echo  '<br>Total Blogs: '.$top_blogger['total'].'</div>';

        echo '<strong>Top Commentator</strong>: ';
        echo '<br ><div class="mg-2">Name: '.$top_commentator['fname'].' '.$top_commentator['lname'];
        echo  '<br>Total Comments: '.$top_commentator['total'].'</div>';

        echo '<strong>Top Blog</strong>: ';
        echo '<br ><div class="mg-2">Title: '.$top_blog['title'].'<br> Create Date: '.$top_blog['create_date'];
        echo '<br>Author: '.$top_blog['fname'].' '.$top_blog['lname'];
        echo  '<br>Average Rating: '.$top_blog['top'].'</div>';

        echo "</div>";
        echo '</div>';


        //***********************************************************Blogs Table************************************************************
        
        echo '<h2 class="panel-header">Blogs</h2>';

        //Table of current user blogs
        echo '<div class="panel">';
        echo '<table class="table table-highlight table-hover">';
        echo '<tbody>';
        
        //Header row
        echo '<tr>';
        echo '<th>Title</th><th>Created</th><th>Author</th><th>Comments</th><th>Average Rating</th>';
        echo '</tr>';

        foreach($blogs as $blog){

            $blog_id = $blog['blog_id'];
            $title = $blog['title'];
            $date = $blog['date'];
            $total = getCommentCount($blog_id);
            $avg_rating = getAvgBlogRating($blog_id);
            $author = substr($blog['fname'], 0, 1).". ".$blog['lname'];

            //Row
            echo '<tr>';
            echo "<td>$title</td>";
            echo "<td>$date</td>";
            echo "<td>$author</td>";
            echo "<td>$total</td>";
            echo "<td>$avg_rating</td>";
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        //***********************************************************Users Table************************************************************
        
        echo '<h2 class="panel-header">Users</h2>';

        //Table of current user blogs
        echo '<div class="panel">';
        echo '<table class="table table-highlight table-hover">';
        echo '<tbody>';
        
        //Header row
        echo '<tr>';
        echo '<th>First Name</th><th>Last Name</th><th>IsAdmin?</th><th>Reg_Date</th><th>Total Blogs</th><th>Total Comments</th>';
        echo '</tr>';

        foreach($users as $user){
            $u_id = $user['user_id'];
            $fname = $user['fname'];
            $lname = $user['lname'];
            $date = $user['reg_date'];
            $user_level = $user['user_level'];
            $is_admin = "";

            if($user_level == 1){
                $is_admin = "TRUE";
            }else{
                $is_admin = "FALSE";
            }

            $total_blogs = getBlogCountByUser($u_id);
            $total_comments = getCommentCountByUser($u_id);

            //Row
            echo '<tr>';
            echo "<td>$fname</td>";
            echo "<td>$lname</td>";
            echo "<td>$is_admin</td>";
            echo "<td>$date</td>";
            echo "<td>$total_blogs</td>";
            echo "<td>$total_comments</td>";
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

?>

    </div>
</div>

<?php include( ROOT_PATH . '/includes/footer.html') ?>  