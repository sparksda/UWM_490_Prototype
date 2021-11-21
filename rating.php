<?php
session_start();
include('config.php');

$err_msg = "";
$img_path = "";
$returnURL = "";

if(isset($_POST['rating']) && isset($_POST['blog_id']) && isset($_POST['user_id']) && isset($_POST['operation'])){
	$user_id = $_POST['user_id'];
	$blog_id = $_POST['blog_id'];
	$rating = $_POST['rating'];
	$operation = $_POST['operation'];
	$s = $_POST['s'];
	$p = $_POST['p'];
	$url = $_POST['url'];
	$focus = "blog-text".$blog_id;

	//$returnURL = "$url?s=$s&p=$p#$focus";
	$returnURL = "$url#$focus";

	if($operation=="add"){
		//Add user's rating to ratings table
		$q = "INSERT INTO ratings (rating_id, user_id, blog_id, user_rating) VALUES ('', '$user_id', '$blog_id', '$rating')";
		$r = mysqli_query($dbc, $q) or trigger_error("Query: $q\n<br>MySQL Error: " . mysqli_error($dbc));

		if(mysqli_affected_rows($dbc) == 1){//query executed successfully
			$_SESSION['message'] = "Your rating was added!";
			$_SESSION['status-confirm-rating'] = "status-confirm-rating".$blog_id;
			//redirect user to index.php page.
			header("location: $returnURL");
		}

	}
	
	if($operation=="update"){
		
		//Add user to the database
		$q = "UPDATE ratings SET user_rating = '$rating' WHERE blog_id = $blog_id AND user_id = '$user_id'";
		$r = mysqli_query($dbc, $q) or trigger_error("Query: $q<br/>MySQL Error: ".mysqli_error($dbc));

		if(mysqli_affected_rows($dbc) == 1){//query executed successfully
			$_SESSION['message'] = "Your rating was updated!";
			$_SESSION['status-confirm-rating'] = "status-confirm-rating".$blog_id;
			header("location: $returnURL");
		}

	}

	if($operation=="delete"){

		//Add user to the database
		$q = "DELETE FROM ratings WHERE blog_id = $blog_id AND user_id = '$user_id'";
		$r = mysqli_query($dbc, $q) or trigger_error("Query: $q<br/>MySQL Error: ".mysqli_error($dbc));

		if(mysqli_affected_rows($dbc) == 1){//query executed successfully
			$_SESSION['message'] = "Your rating was deleted!";
			$_SESSION['status-confirm-rating'] = "status-confirm-rating".$blog_id;
			header("location: $returnURL");
		}

	}

}

?>