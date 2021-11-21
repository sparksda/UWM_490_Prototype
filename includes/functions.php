<?php

    function getBlogsByLimit($order_by, $start, $display){
        global $dbc;
        //Query the blogs and users table to bring back all blogs and users who created them.
        //Return results are ordered by set criteria defined in order_by variable and the number of records 
        //returned are limited to
        //values assigned to start and display variables. The start variable is the starting index
        //of the first record to display and display is the number of records to bring back.
        $query = "SELECT *, DATE_FORMAT(b.blog_date, '%b %d, %Y') AS date 
                  FROM blogs AS b 
                  LEFT JOIN users AS u ON b.user_id = u.user_id 
                  Order By $order_by LIMIT $start, $display";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        
        //Get blogs to display on the current page
        $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        //Array to hold blogs and their comments
        $blogs_comments = array();
        
        //Loop through each blog returned and create a link to the comments that are associated with them.
        foreach($blogs as $blog){
            $blog['comment'] = getCommentsByBlog($blog['blog_id']);
            array_push($blogs_comments, $blog); //Add the group of comments returned with the assosicated blog in an array.
        }
        
        return $blogs_comments;
    }

    function getBlogs($order_by){
        global $dbc;
        //Query the blogs and users table to bring back all blogs and users who created them.
        $query = "SELECT *, DATE_FORMAT(b.blog_date, '%b %d, %Y') AS date 
                  FROM blogs AS b 
                  LEFT JOIN users AS u ON b.user_id = u.user_id
                  Order By $order_by";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        
        //Get blogs to display on the current page
        $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        //Array to hold blogs and their comments
        $blogs_comments = array();
        
        //Loop through each blog returned and create a link to the comments that are associated with them.
        foreach($blogs as $blog){
            $blog['comment'] = getCommentsByBlog($blog['blog_id']);
            array_push($blogs_comments, $blog); //Add the group of comments returned with the assosicated blog in an array.
        }
        
        return $blogs_comments;
    }

    function getBlog($blog_id){
        global $dbc;

        $query = "SELECT *, DATE_FORMAT(blog_date, '%b %d, %Y') AS date 
                  FROM blogs 
                  Where blog_id = $blog_id";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        $blog = mysqli_fetch_assoc($result);
        return $blog;
    }

    function getBlogsByUser($order_by, $start, $display, $user_id){
        global $dbc;
        //Query the blogs and users table to bring back all blogs and users who created them.
        //Return results are ordered by set criteria defined in order_by variable and the number of records 
        //returned are limited to
        //values assigned to start and display variables. The start variable is the starting index
        //of the first record to display and display is the number of records to bring back.
        $query = "SELECT *, DATE_FORMAT(b.blog_date, '%b %d, %Y') AS date 
                  FROM blogs AS b 
                  LEFT JOIN users AS u ON b.user_id = u.user_id
                  WHERE b.user_id = $user_id 
                  Order By $order_by LIMIT $start, $display";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        
        //Get blogs to display on the current page
        $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        //Array to hold blogs and their comments
        $blogs_comments = array();
        
        //Loop through each blog returned and create a link to the comments that are associated with them.
        foreach($blogs as $blog){
            $blog['comment'] = getCommentsByBlog($blog['blog_id']);
            array_push($blogs_comments, $blog); //Add the group of comments returned with the assosicated blog in an array.
        }
        
        return $blogs_comments;
    }

    function getCommentsByBlog($blog_id){
        global $dbc;
        //Bring back all comments for the specified blog_id
        $query = "SELECT *, DATE_FORMAT(c.comment_date, '%b %d, %Y at %l:%i %p') AS date 
                  FROM comments c
                  JOIN users u ON c.user_id = u.user_id
                  WHERE blog_id = $blog_id 
                  Order By c.comment_date DESC";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $comments;
    }
    
    //Get the total blogs in the blogs table.
    function getBlogCount(){
        global $dbc;
        $query = "SELECT COUNT(blog_id) FROM blogs";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        $count = 0;

        if(!empty($row[0])){
            $count = $row[0];
        }

        return $count;
    }

    //Get the total blogs in the blogs table created by a specific user.
    function getBlogCountByUser($user_id){
        global $dbc;
        $query = "SELECT COUNT(blog_id) FROM blogs WHERE user_id = $user_id";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        
        $count = 0;

        if(!empty($row[0])){
            $count = $row[0];
        }

        return $count;
    }
    
    //Get the total comments in the comments table.
    function getCommentCount($blog_id){
        global $dbc;
        $query = "SELECT COUNT(comment_id) FROM comments WHERE blog_id = $blog_id";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        
         $count = 0;

        if(!empty($row[0])){
            $count = $row[0];
        }

        return $count;
    }

    function getComments(){
        global $dbc;

        $query = "SELECT *, DATE_FORMAT(comment_date, '%b %d, %Y') AS comment_date 
                  FROM comments";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        $comments = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $comments;
    }

   function getCommentCountByUser($user_id){
        global $dbc;
        $query = "SELECT COUNT(comment_id) FROM comments WHERE user_id = $user_id";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        
        $count = 0;

        if(!empty($row[0])){
            $count = $row[0];
        }

        return $count;
    }
    
    function getBlogRatingByUser($blog_id, $user_id){
        //Add user's rating to ratings table
        global $dbc;
        $query = "SELECT user_rating FROM ratings WHERE blog_id = '$blog_id' AND user_id = '$user_id'";
        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        
        $rating = 0;

        if(!empty($row[0])){
            $rating = $row[0];
        }

        return $rating;

    }

	function getAvgBlogRating($blog_id){
		//Add user's rating to ratings table
		global $dbc;
		$query = "SELECT ROUND(AVG(user_rating), 1) FROM ratings WHERE blog_id = '$blog_id'";
		$result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
		$row = mysqli_fetch_array($result, MYSQLI_NUM);

        $avg = 0;

        if(!empty($row[0])){
            $avg = $row[0];
        }

        return $avg;	
	}

     function getUsers(){
        global $dbc;
        //Query the users and blogs tables to bring back all blogs and users from both tables even if there are no matching records.
        $query = "SELECT *, DATE_FORMAT(reg_date, '%b %d, %Y') AS reg_date 
                  FROM users";

        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        
        //Get blogs to display on the current page
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $users;
    }

    function getTopBlogger(){
         global $dbc;
        //Query the users and blogs tables to bring back all blogs and users from both tables even if there are no matching records.
        $query = "SELECT u.fname, u.lname, count(b.user_id) as total
                  FROM blogs b
                  RIGHT JOIN users u ON b.user_id = u.user_id
                  Group By b.user_id, u.fname, u.lname
                  Order By total DESC
                  LIMIT 1";

        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        
        //Get blogs to display on the current page
        $blogger = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $blogger;
    }

    function getTopCommentator(){
         global $dbc;
        //Query the users and blogs tables to bring back all blogs and users from both tables even if there are no matching records.
        $query = "SELECT u.fname, u.lname, count(c.user_id) as total
                  FROM comments c
                  RIGHT JOIN users u ON c.user_id = u.user_id
                  Group By c.user_id, u.fname, u.lname
                  Order By total DESC
                  LIMIT 1";

        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        
        $commentator = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $commentator;
    }

    function getTopRatedBlog(){
        //Add user's rating to ratings table
        global $dbc;
        $query = "SELECT u.fname, u.lname, b.title, DATE_FORMAT(b.blog_date, '%b %d, %Y') AS create_date, ROUND(AVG(user_rating),1) AS top
                  FROM ratings r
                  LEFT JOIN blogs b ON r.blog_id = b.blog_id
                  LEFT JOIN users u ON u.user_id = b.user_id
                  GROUP By r.blog_id
                  ORDER By top DESC
                  LIMIT 1";

        $result = mysqli_query($dbc, $query) or trigger_error("Query: $query\n<br>MySQL Error: " . mysqli_error($dbc));
        
        $blog = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $blog;
    }
	
    //Determine if a parameter in the $_GET or $_POST arrays contains a specific value
    //This is mainly used to determine which sorting option was chosen on the main page.
    function hasParamValue(array $get, array $post, $key, $value){
        
        if(isset($get[$key]) && $get[$key] == $value){
            return TRUE; 
        }
        
        if(isset($post[$key]) && $post[$key] == $value){
            return TRUE; 
        }
        return FALSE;
    }


    //Removes a parameter and its value from a URL string.
    //This is used to remove parameters added by the GET method when a link was clicked.
    //This is used for the Show/Hide buttons in each blog.
    function removeParamValUrl($refresh_url, $param, $value){
        $new_url = $refresh_url;

        $search1 = '&'.$param."=$value"; //example: &bi2coms=YES
        $search2 = $param."=$value&";    //example: bi2coms=YES
        $search3 = "?".$param."=$value"; //example: ?bi2coms=YES

        if(strpos($refresh_url, $search1) !==false){
            $new_url = str_ireplace($search1, "", $refresh_url);   
        }else if(strpos($refresh_url, $search2) !==false){
            $new_url = str_ireplace($search2, "", $refresh_url); 
        }else{
            $new_url = str_ireplace($search3, "", $refresh_url);
        }

        return $new_url;
    }



?>
