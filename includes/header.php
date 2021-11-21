<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" >
<head>
    <title><?php echo $page_title;?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width = device-width, initial-scale = 1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css">
	<link rel="stylesheet" media="screen" href="static/css/style.css">
	<script src="static/js/script.js"></script>
</head>

<body id="header">
	<!-- Navbar -->
	<nav id="navbar">
		<h1>
			<span>
                <a href="index.php"><i class="fas fa-hiking fa-lg"></i> WanderingBloggers.com</a></span>
		</h1>
		<h2 id="welcome"><?php if(isset($_SESSION['fname']) && isset($_SESSION['activated']) && $_SESSION['activated'] == TRUE) echo 'User: '.$_SESSION['fname'] ?></h2>
		<ul> 
			<li><a href="index.php" <?php if(isset($url_style1)){echo $url_style1;}?>>Home <i class="fa fa-home"></i></a></li>


		    <?php
	            if(isset($_SESSION['user_id']) && isset($_SESSION['activated']) && $_SESSION['activated']){
	            	if(isset($url_style2)){
	                	echo '<li><a href="myposts.php"'.$url_style2.'>My Posts<i class="fas fa-edit"></i></a></li>';
	            	}else{
	                	echo '<li><a href="myposts.php">My Posts<i class="fas fa-edit"></i></a></li>';
	            	}
	            }
			?>

			<?php
				//enable admin page for admins only
	            if(isset($_SESSION['user_id']) && isset($_SESSION['activated']) && $_SESSION['activated'] 
	            	&& isset($_SESSION['user_level']) && $_SESSION['user_level'] == 1){

			      	if(isset($url_style5)){
						echo '<li><a href="reports.php"'.$url_style5.'>Reports<i class="fas fa-user-shield"></i></a></li>';
	            	}else{
						echo '<li><a href="reports.php">Reports<i class="fas fa-user-shield"></i></a></li>';
	            	}
					
	            }
			?>
			
			<?php
 
                if(!isset($_SESSION['user_id']) || isset($_SESSION['activated']) && !$_SESSION['activated']){

        	      	if(isset($url_style3)){
	                    echo '<li><a href="login.php"'.$url_style3.'>Sign in <i class="fas fa-sign-in-alt"></i></a></li>';
	            	}else{
                    	echo '<li><a href="login.php">Sign in <i class="fas fa-sign-in-alt"></i></a></li>';
	            	}
                }
			?>
           <?php
                if(isset($_SESSION['user_id']) && isset($_SESSION['activated']) && $_SESSION['activated']){
                    echo '<li><a href="logout.php">Sign out <i class="fas fa-sign-out-alt"></i></a></li>';
                }
			?>
			
			<?php
                if(!isset($_SESSION['user_id']) || isset($_SESSION['activated']) && !$_SESSION['activated']){

	             	if(isset($url_style4)){
			         	echo '<li><a href="registration.php"'.$url_style4.'>Register <i class="fa fa-user-plus"></i></a></li>';
	            	}else{
			         	echo '<li><a href="registration.php">Register <i class="fa fa-user-plus"></i></a></li>';
	            	}

                }
            ?>

		</ul>
	</nav>

