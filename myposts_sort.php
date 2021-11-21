<?php 
$page_title = "My Posts";
$url_style2 = ' style="color: red;"';
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
$new_btn_vis = "hide";
$edit_del_vis = "hide";
$rating_vis = "hide";
$rating_lbl_vis = "hide";

if(isset($_SESSION['user_id']) && isset($_SESSION['activated']) && $_SESSION['activated'] == TRUE){
    $user_id = $_SESSION['user_id'];
    $canModify = 1;
    $new_btn_vis = "show-ib";
    $rating_vis = "show-ib";
    $rating_lbl_vis = "show-b";
}

//user_level determines if signed in user have CRUD privileges for all posts or theirs only. A value of 1 means the person has all CRUD privileges.
if(isset($_SESSION['user_level']) && is_numeric($_SESSION['user_level'])){
    $user_level = $_SESSION['user_level'];
}


$refresh_url = "";
/*capture the current URL query shown in address bar*/                
if(!empty($_SERVER['QUERY_STRING'])){
   $refresh_url = $current_page . '?' . $_SERVER['QUERY_STRING'];
}else{
    $refresh_url = $current_page;
}

?>


<!--Container-->
<div class="container">
    <div class="blog-container">
    <div class="status-confirm hide" id="status-confirm-blog"></div>
    <h1>My Posts</h1>
    <a href="add_blog.php?returnURL=myposts.php" name="add" class="btn blog-btn <?php echo $new_btn_vis ?>">NEW<i class="fas fa-plus"></i></a>

  <!--Dropdown menu that allows blogs to be sorted -->
    <form action="<?php echo $current_page . '#blogs' ?>" method="get" id="sort">
        <b>Sort By: &nbsp;</b>
        <select name="orderBy">
            <option value="dASC" <?php if(hasParamValue($_GET, $_POST, "orderBy", "dASC")) echo 'selected'?>>Date Ascending</option>
            <option value="dDESC" <?php if(hasParamValue($_GET, $_POST, "orderBy", "dDESC")) echo 'selected'?>>Date Descending</option>
            <option value="tASC" <?php if(hasParamValue($_GET, $_POST, "orderBy", "tASC")) echo 'selected'?>>Title Ascending</option>
            <option value="tDESC" <?php if(hasParamValue($_GET, $_POST, "orderBy", "tDESC")) echo 'selected'?>>Title Descending</option>
        </select>
        <!--When refresh image is clicked, the form is submitted with a POST instead of a GET which links use.-->
        <input type="image" id="sort-btn" src="img/refresh.png" alt= "refresh.png" width="28" height="28">
        
        <a href="#blogs" class="btn sort-btn-mobile">Sort</a><!--This is the button for the mobile view of the site. Doesn't work.-->
    </form>
    
        <?php
            $sort = 'dDESC'; //Set default sorting order of blogs.
            $order_by = 'b.blog_date DESC';

             if(isset($_POST['orderBy'])){
            $sort = $_POST['orderBy'];
        } 
        
        //GET initiated by a link.
        if(isset($_GET['orderBy'])){
            $sort = $_GET['orderBy'];
        } 

        //Determine the sorting order
        switch($sort){
            case 'dDESC';
                $order_by = 'b.blog_date DESC';
                break;
            case 'tASC';
                $order_by = 'title ASC';
                break;
            case 'tDESC';
                $order_by = 'title DESC';
                break;
            default:
                $order_by = 'b.blog_date ASC';
                $sort = 'dASC';
                break;
        }


          //Find how many pages of blogs there will be
        if(isset($_GET['p']) and is_numeric($_GET['p'])){//Already set from the first posting of page
            $pages = $_GET['p'];
        }else{//Determine the number of pages needed
            $records = getBlogCountByUser($user_id);

            //Calculate the number of pages per page
            if($records > $display){
                $pages = ceil($records/$display);    
            }else{
                $pages = 1;
            }

        }

        //Get the starting index of the first item in the dataset
        if(isset($_GET['s']) && is_numeric($_GET['s'])){
            $start = $_GET['s']; 
        }else{
            $start = 0; /*Indexing of records start at 0 in the database table*/
        }

            
        //Retrieve blogs, comments, and the users that created them.
        $blogs = getBlogsByUser($order_by, $start, $display, $user_id);
        $count = 1; //count used as an iterator for assigning names to buttons that are stored in the name property. example: btn1, btn2


        //Delete the parameter/value of the delete link stored in the URL after clicking it.
        if(isset($_GET['delete'])){
            $refresh_url = removeParamValUrl($refresh_url, "delete", $_GET['delete']);
        }

        //Delete the parameter/value of the delete blog button stored in the URL after clicking it.
        if(isset($_GET['delete-blogId'])){
            $refresh_url = removeParamValUrl($refresh_url, "delete-blogId", $_GET['delete-blogId']);
        }
            
        //Delete the parameter/value of the edit link stored in the URL after clicking it.
        if(isset($_GET['edit'])){
            $refresh_url = removeParamValUrl($refresh_url, "edit", $_GET['edit']);
        }

        //Delete the x parameter/value that is stored in the URL after clicking the sort button.
        if(isset($_GET['x'])){
            $refresh_url = removeParamValUrl($refresh_url, "x", $_GET['x']);
        }
        
        //Delete the y parameter/value that is stored in the URL after clicking the sort button.
        if(isset($_GET['y'])){
            $refresh_url = removeParamValUrl($refresh_url, "y", $_GET['y']);
        }
        
        if(count($blogs) == 0){   
            echo '<h3>Click "NEW +" button to add a new post.</h3>';
            echo '<div class="placeholder"></div>';
        }
            

        //************************************************************BLOGS************************************************************
        //Display blogs on the current page. The number of posts per page is determined by the value stored in $display.
        foreach($blogs as $blog){
            $blog_id = $blog['blog_id'];
            $btnId = 'btn'.$count;
            $blog_avg = getAvgBlogRating($blog_id);
            $blog_rating = getBlogRatingByUser($blog_id, $user_id);

            //Only allow the currently signed in user to edit or delete their blog. Admin can edit/delete all blogs
            if(($user_id == $blog['user_id'] || $user_level == 1) && $canModify == 1) {
                $edit_del_vis = "show";
            }

            echo '<div class="blog-item">';

            $title = $blog['title'];
            $date = $blog['date'];
            $name = $blog['fname'] . ' ' . $blog['lname'];
            $pic = $blog['image_path'];
            $text = $blog['blog_text'];


            //Delete confirmation
            echo '<div class="del-confirm hide" id="del-confirm'.$blog_id.'">';
            echo 'Are you sure you want to delete this blog?';
            echo '<br/>';
            echo '<a href ="delete_blog.php?delete-blogId='.$blog_id.'&returnURL=myposts.php?orderBy='.$sort.'" class="btn sm-btn">Yes</a>';
            echo '<a href ="'.$refresh_url.'" class="btn sm-btn">No</a>';
            echo '</div>';

            echo '<div class="blog-title">'. $title;
            
            echo '<div class="edit-del-blog '.$edit_del_vis. '" id="edit-del-blog">';

            //Edit blog button
            echo '<span class="edit-blog"><a href ="edit_blog.php?edit-blogId='.$blog_id.'&s='.$start.'&p='.$pages.'&returnURL=myposts.php&orderBy='.$sort.'">
            <i class="far fa-edit"><p>Edit</p></i></span></a>';

           //Delete blog button

            if(strpos($refresh_url, "?") !==false){
                echo '<span class="del-blog"><a href="'.$refresh_url.'&delete-blogId='.$blog_id.'">';
            }else{
                echo '<span class="del-blog"><a href="'.$refresh_url.'?delete-blogId='.$blog_id.'">';
            }

            echo '<i class="far fa-trash-alt"><p>Delete</p></i></span></a></div></div>';


            echo  '<div class="blog-date-author">Updated:<i class="far fa-clock"></i> ' . $date . ' by <span class="blog-author">' . $name . '</span></div>';
            echo  '<div class="blog-pic"><img src="' . $pic . '" id="blog'.$blog_id.'"></div>
                  <div class="blog-text"><p id="blog-text'.$blog_id.'">' . $text . '</p></div>';     

           //*****************************Form for blog rating*****************************//
            //echo '<div class="ratings">';
            echo '<p class="'.$rating_lbl_vis.'"><strong>Please rate this blog</strong></p>';
            echo '<form method="post" action="rating.php" class="ratings '.$rating_vis.'" id="rating-box"">';
            //echo '<div class="rating">';
            echo '<input type="hidden" name = "rating" id="rating'.$blog_id.'" value="0">';
            echo '<button type="submit" class="star">';
            echo '<i class="star'.$blog_id.' rating__star far fa-star" onclick="rateStars('.$blog_id.', 0)"></i></button>';
            echo '<button type="submit" class="star">';
            echo '<i class="star'.$blog_id.' rating__star far fa-star" onclick="rateStars('.$blog_id.', 1)"></i></button>';
            echo '<button type="submit" class="star">';
            echo '<i class="star'.$blog_id.' rating__star far fa-star" onclick="rateStars('.$blog_id.', 2)"></i></button>';
            echo '<button type="submit" class="star">';
            echo '<i class="star'.$blog_id.' rating__star far fa-star" onclick="rateStars('.$blog_id.', 3)"></i></button>';
            echo '<button type="submit" class="star">';
            echo '<i class="star'.$blog_id.' rating__star far fa-star" onclick="rateStars('.$blog_id.', 4)"></i></button>';
            echo '<input type="hidden" name="blog_id" value="'.$blog_id.'"/>';
            echo '<input type="hidden" name="user_id" value="'.$user_id.'"/>';
            echo '<input type="hidden" name="operation" value="" id="operation'.$blog_id.'"/>';
            echo '<input type="hidden" name="p" value="'.$pages.'"/>';
            echo '<input type="hidden" name="s" value="'.$start.'"/>';
            //echo '<input type="hidden" name="url" value="myposts.php"/>';
            echo '<input type="hidden" name="url" value="'.$refresh_url.'"/>';
            echo '<br><div class="avg-label" id="rating-box">Average Rating ('.$blog_avg.')</div><div class="status-confirm hide" id="status-confirm-rating'.$blog_id.'"></div>';
            echo '</form>';
            echo '<script type="text/javascript"> setRating("'.$blog_rating.'", "'.$blog_id.'");</script>';

            echo '</div>'; //End population of comments

          
           //*****************************Form for adding comments*****************************//
                

            echo '<div class="blog-comment">';
            
            if($canModify == 1){//Only signed in users can see the form to add a comment
                
            echo '<h2>Leave A Comment</h2>';
            echo '<p>Comment</p>';
                    
            echo '<form action="comment.php" method="post">';
            echo '<textarea name="post-msg" id="post-msg'.$blog_id.'"></textarea>';                     
            echo '<button type="submit" name="add" class="btn post-btn">Post Comment</button>';
            echo '<div class="status-confirm" id="status-confirm'.$blog_id.'"></div>'; //a status message will be written here after a CRUD operation.
            echo '<input type="hidden" name="blog_id" value="'.$blog_id.'"/>';
            echo '<input type="hidden" name="user_id" value="'.$user_id.'"/>';
            echo '<input type="hidden" name="btn_id" value="'.$btnId.'"/>';
            echo '<input type="hidden" name="returnURL" value="'.$refresh_url.'"/>';
            echo '</form>'; //End of FORM
            
            }
                
            $total = getCommentCount($blog_id);
            echo '<h2>Comments('.$total.')</h2>';

            foreach($blog['comment'] as $comment){ // Display comments for the current blog post in view.

                $hide_links = FALSE; 
                $comment_id = $comment['comment_id'];
                $name = $comment['fname'] . ' ' . $comment['lname'];
                
                //Determines if a user can see the links to update a blog post.
                //Only users with a level 1 access, stored in the user_level column in DB, has the abiliy to peform CRUD operations on all posts.
                //The currently logged in user who has a user level of 0 and is the author of a comment can only perform CRUD operations on their posts.
                if($canModify != 1 || ($comment['user_id'] != $user_id && $user_level != 1)){ //user_level 1 is admin access
                    $hide_links = TRUE;
                }

                //*****************************Form for updating or deleting comments*****************************//
                
                echo '<form action="comment.php" method="post" id ="'.$comment_id.'">';
                echo '<div class="mg-1">';
                echo '<p class="author">'.$name.'</p>';
                echo '<div class="comment-links" id="comment-links'.$comment_id.'">';
                //only one textarea will be editable and sent at a time during update or edit process. No need to assign a unique name for each textarea.
                echo '<textarea id ="txtarea'.$comment_id.'" name="txtarea" disabled>'.$comment['comment_text'].'</textarea>';

                if(!$hide_links){

                    if(strpos($refresh_url, "?") !==false){
                        echo '<a href="'.$refresh_url.'&edit='.$comment_id.'#txtarea'.$comment_id.'"  name="edit-link" class="edit-del-links">Edit</a>'; 
                        echo '<a href="'.$refresh_url.'&delete='.$comment_id.'#txtarea'.$comment_id.'" name="del-link" class="edit-del-links">Delete</a>'; 
                    }else{
                        echo '<a href="'.$refresh_url.'?edit='.$comment_id.'#txtarea'.$comment_id.'"  name="edit-link" class="edit-del-links">Edit</a>'; 
                        echo '<a href="'.$refresh_url.'?delete='.$comment_id.'#txtarea'.$comment_id.'" name="del-link" class="edit-del-links">Delete</a>'; 
                    }
                  
                }
                
                echo '<button type="submit" id="update-btn'.$comment_id.'" name="update" class="btn update-del-btn" style="display: none">Update</button>';
                echo '<a href="'.$refresh_url.'#comment-links'.$blog_id.'" id ="cancel-link'.$comment_id.'" name="cancel-link" class="edit-del-links" style="display: none">Cancel</a>'; 
                echo '<p class="comment-date">'.$comment['date'].'</p></div>';
                echo '</div>';
                
                echo '<div class="del-msg" id="del-msg'.$comment_id.'">';
                echo 'Are you sure you want to delete this comment?';
                echo '<br/>';
                echo '<input type="radio" name="choice" value="YES"/> Yes &nbsp;&nbsp;';
                echo '<input type="radio" name="choice" value="NO" checked="checked"/> No';
                echo '<button type="submit" id="submit-btn'.$comment_id.'" name="delete" class="btn sm-btn hide">Submit</button>';
                echo '</div>';
                echo '<input type="hidden" name="comment_id" value="'.$comment_id.'"/>';
                echo '<input type="hidden" name="returnURL" value="'.$refresh_url.'"/>';
                echo '<input type="hidden" name="blog_id" value="'.$blog_id.'"/>';
                echo '</form>'; //End form
            } // End foreach for adding comments


            echo '</div>'; //End creation of current blog item
            $count++;

        } //End of foreach loop for creating all blog items on the page.

        if(isset($_SESSION['message']) && isset($_SESSION['status-confirm-msg'])){
            $status_confirm_id = $_SESSION['status-confirm-msg'];
            echo '<script type="text/javascript"> timedMsgById("'.$_SESSION['message'].'", "'.$status_confirm_id.'");</script>';
            unset($_SESSION['message']);
            unset($_SESSION['status-confirm-msg']);
        }

        if(isset($_SESSION['message']) && isset($_SESSION['status-confirm-rating'])){
            $status_confirm_id = $_SESSION['status-confirm-rating'];
            echo '<script type="text/javascript"> timedMsgById("'.$_SESSION['message'].'", "'.$status_confirm_id.'");</script>';
            //echo '<script type="text/javascript"> scrollToElemById("rating-box");</script>';
            unset($_SESSION['message']);
            unset($_SESSION['status-confirm-rating']);
        }

        if(isset($_SESSION['message']) && isset($_SESSION['status-confirm-blog'])){
            echo '<script type="text/javascript"> timedMsgById("'.$_SESSION['message'].'", "status-confirm-blog");</script>';
            unset($_SESSION['message']);
            unset($_SESSION['status-confirm-blog']);
        }


        //Enable the text area for the text area whose Edit button was clicked.
        //All text areas are disabled by default except the one that allows a comment to be added to the current blog post.
        if(isset($_GET['edit'])){
            $text_area_id = 'txtarea'.$_GET['edit'];
            $update_btn = 'update-btn'.$_GET['edit'];
            $cancel_link = 'cancel-link'.$_GET['edit'];
            
            echo '<script type="text/javascript"> enableElementById("'.$text_area_id.'");</script>';

            //hide all edit and delete buttons shown for other posts that the user can edit or delete.
            echo '<script type="text/javascript"> hideElementsByName("edit-link");</script>';
            echo '<script type="text/javascript"> hideElementsByName("del-link");</script>';
            echo '<script type="text/javascript"> showElementById("'.$update_btn.'");</script>';
            echo '<script type="text/javascript"> showElementById("'.$cancel_link.'");</script>';
            
        }
        
        if(isset($_GET['delete'])){
            
            $del = 'del-msg'.$_GET['delete'];
            $submit = 'submit-btn'.$_GET['delete'];
            echo '<script type="text/javascript"> hideElementsByName("edit-link");</script>';
            echo '<script type="text/javascript"> hideElementsByName("del-link");</script>';
            echo '<script type="text/javascript"> showElementById("'.$del.'");</script>';   
            echo '<script type="text/javascript"> showElementById("'.$submit.'");</script>';
        } 

       if(isset($_GET['delete-blogId'])){
            $del_confirm = 'del-confirm'.$_GET['delete-blogId'];
            $blog_item = 'blog'.$_GET['delete-blogId'];
            echo '<script type="text/javascript"> hideElementByClass("edit-del-blog");</script>';
            echo '<script type="text/javascript"> showElementById("'.$del_confirm.'");</script>';
            echo '<script type="text/javascript"> scrollToElemById("'.$blog_item.'");</script>';
        }    

        //Make the links to other pages
        if($pages > 1){
            //Add line breaks
            echo '<div id="page-links">';
            //Determine the current page
            $curr_page = ($start/$display) + 1; //Remember indexing starts at 0 in database

            if($curr_page !=1){//Create link for the Previous button
                echo '<a href="'.$current_page.'?s='.($start - $display).'&p='.$pages.'&orderBy='.$sort.'"><i class="fas fa-arrow-alt-circle-left"></i></a>';
            }

            //Make all the numbered pages
            for($i = 1; $i <= $pages; $i++){

                if($i != $curr_page){
                     echo '<a href="'.$current_page.'?s='.(($display * ($i - 1))). '&p='.$pages.'&orderBy='.$sort.'">'.$i.'</a>';
                }else{
                     echo '<a href="" id="this-page">'.$i.'</a>';
                }

            } //End of FOR loop

            //If we aren't on the last page, create a Next button
            if($curr_page != $pages){//Create next button
                echo '<a href="'.$current_page.'?s='.($start+$display).'&p='.$pages.'&orderBy='.$sort.'"><i class="fas fa-arrow-alt-circle-right"></i></a>';    
            }

            echo '</div>';
        } //End of links section.

    ?>


    </div>
</div>

<?php include( ROOT_PATH . '/includes/footer.html') ?>  

