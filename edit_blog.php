<?php 
$page_title = "Edit Post";
include('config.php');
require_once( ROOT_PATH . '/includes/functions.php'); 
include( ROOT_PATH . '/includes/header.php'); 
$current_page = basename($_SERVER['PHP_SELF']);

$ok_msg = "Your blog was successfully updated!";
$err_msg = "";
$url = "index.php";


if(isset($_GET['edit-blogId'])) {
  $user_id = $_SESSION['user_id'];
  $file_name = "";
  $blog_id = $_GET['edit-blogId'];
  $blog = getBlog($blog_id);
  $s = $_GET['s'];
  $p = $_GET['p'];
  $order_by = $_GET['orderBy'];

  if(isset($_GET['returnURL'])){
    $url = $_GET['returnURL'].'?s='.$s.'&p='.$p.'&orderBy='.$order_by;
  }

  $title = $blog['title'];
  $text = $blog['blog_text'];
  $img_path = $blog['image_path'];

  $curr_title = $blog['title'];
  $curr_text = $blog['blog_text'];
  $curr_img_path = $blog['image_path'];

  if(isset($_FILES['imgfile']) && !empty($_FILES['imgfile']['name'])){    
      $file_name = $_FILES['imgfile']['name'];
      $file_size = $_FILES['imgfile']['size'];
      $file_tmp = $_FILES['imgfile']['tmp_name'];
      $file_type = $_FILES['imgfile']['type'];
      $tmp = explode('.',$_FILES['imgfile']['name']);
      $file_ext = strtolower(end($tmp));
      
      $ext= array("jpeg","jpg","png","bmp","gif");
      
      if(in_array($file_ext,$ext)=== false){
         $err_msg = $err_msg ."<br>-Invalid file. Choose JPEG, PNG, or GIF file.";
      }
      
      if($file_size > 268435456) {
         $err_msg = $err_msg .'<br>-The file size cannot exceed 256 MB';
      }
      
      if(empty($err_msg)) {
         $img_path = "img/".$file_name;
         move_uploaded_file($file_tmp, $img_path);
      }

    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['blog-title'];
        $text = $_POST['content'];

        //Check to see if any of the data fields were changed. If not, then cannot send update to database. An error will occur.
        if($title == $curr_title && $text == $curr_text && $img_path == $curr_img_path){
            $err_msg = "-The title, blog text, and image matches what is already in the database. Please change one of the 3 fields or hit the Cancel button to return to the dashboard";
        }else{
            //trim data
            $trimmed = array_map('trim', $_POST);

            if (!empty($_POST['blog-title'])) {
                $title = mysqli_real_escape_string($dbc, $trimmed['blog-title']);
            }else{
                $err_msg = $err_msg ."<br>-You must fill out the blog title.";
            }

            if (!empty($_POST['content'])) {
                $text = mysqli_real_escape_string($dbc, $trimmed['content']);
            }else{
                $err_msg  = $err_msg ."<br>-You must fill out the content section.";
            }

            if (empty($img_path)) {
                $err_msg  = $err_msg ."<br>-You must upload an image for the post.";
            }

            //Write to db if no errors.
            if(empty($err_msg)){
              
                //Add user to the database
                $q = "UPDATE blogs SET title = '$title', blog_text = '$text', image_path = '$img_path' WHERE blog_id = $blog_id;";
                $r = mysqli_query($dbc, $q) or trigger_error("Query: $q<br/>MySQL Error: ".mysqli_error($dbc));

                if(mysqli_affected_rows($dbc) == 1){//query executed successfully
                    $_SESSION['message'] = "$ok_msg";
                    $_SESSION['status-confirm-blog'] = "status-confirm-blog";
                    echo '<script type="text/JavaScript">setTimeout("location.href = \''.$url.'\';");</script>';
                }else{
                    $err_msg = "<br>Your blog could not be updated in the database due to a system error!";
                    echo $err_msg;
                }

                mysqli_close($dbc);

            }else{//remove escape characters
                 if(strpos($title, "\\") !==false){
                    $title = str_ireplace("\\", "", $title);  
                 }
                 if(strpos($text, "\\") !==false){
                    $text = str_ireplace("\\", "", $text);  
                 }
            }
        }

    } //End POST if

}

?>


<div class="container">
    <div class="form-wrap new-blog">
        <p id="error" style="color:red; font-weight:bold;"></p>
        <form action="" method="post" enctype="multipart/form-data">
            <a href="<?php echo $url ?>" name="add" class="btn blog-btn">Cancel</a>
            <h1>Create New Blog Post</h1>
            <p>Fill out the form with correct values.</p>
            <hr><br />

            <fieldset>
                <div class="form-group">
                    <label for="blog-title"><b>Title</b></label>
                    <input type="text" name="blog-title" id="blog-title" value="<?php echo $title ?>" />
                </div>

                <div class="form-group">
                    <label for="content"><b>Content</b></label>
                    <textarea name="content" rows="4" cols="50" id = "content"><?php echo $text ?></textarea>
                </div>

                 <div class="form-group">
                     <label for="imgfile"><b>Select an image to upload</b></label>
                     <input type="file" name="imgfile" id="imgfile" onchange="preview('imgpreview');"  />
                </div>

                <div class="form-group">
                     <div class="blog-pic"><img src="<?php echo $img_path?>" alt="preview image" id="imgpreview"></div>
                </div>

                <div class="form-group">
                    <input type="submit" name="submit" value="Submit Post" />
                </div>
            </fieldset>
        </form>
    </div>
</div>

<?php
     if(!empty($err_msg)){
        echo '<script type="text/javascript">showMsgById("'.$err_msg.'", "error");</script>';
     }
?>

<?php include ("includes/footer.html") ?>
