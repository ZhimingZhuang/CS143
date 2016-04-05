<!DOCTYPE html>
<html>
<body>
<head>
	<title>add new comments</title>
	<style>
	.error{color:#FF0000;}
	</style>
</head>
<?php

	if ($_SERVER["REQUEST_METHOD"] == "POST") {	
   
	    if (empty($_POST["movie"])) {
	    	$movieErr = "Movie is required";
	    }else{
	    	$movie = $_POST['movie'];
	    }

		if (empty($_POST["name"])) {
			$name = "NULL";
		}else{
			$name = $_POST['name'];
		}

		if (empty($_POST["rating"])) {
			$rating = "NULL";
		}else{
			$rating= $_POST['rating'];
		}

		if (empty($_POST["comments"])) {
			$comments = "NULL";
		}else{
			$comments= $_POST['comments'];
		}
	}
	
?>

<h2> Add new comment:</h2>
<p><span class="error">* Required field.</span></p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	Movie:<SELECT name="movie" style="width:500px">
	<OPTION SELECTED>
	<?php
	$sqlmovie="SELECT id,title,year FROM Movie;";
	$db_connection=mysql_connect("localhost","cs143","");
	if(!$db_connection){
	    $errmsg=mysql_error($db_connection);
	    echo "Connection failed: $errmsg<br />";
	    exit(1);    
	}else{
	  	mysql_select_db("CS143",$db_connection);
		$query_selectmovie=mysql_query($sqlmovie);
	}
	while($resultmovie=mysql_fetch_array($query_selectmovie)){
	$mtitle=$resultmovie[title];
	$myear=$resultmovie[year];
	$movieid=$resultmovie[id];	
	?>	
	<OPTION value="<?=$movieid?>"><?php echo"$mtitle($myear)"?></OPTION>	
	<?php
	}
	?>
	</SELECT>
	<span class="error">* <?php echo "$movieErr";?></span><br/><br/>

	Your Name:<input type="text" name="name"><br/><br/>
	Rating:<SELECT name="rating" sytle="width:100px">
	<OPTION value="5">5 - Excellent</OPTION>
	<OPTION value="4"> 4 - Good</OPTION>
	<OPTION value="3" SELECTED>3 - It's ok</OPTION>
	<OPTION value="2">2 - Not Worth</OPTION>
	<OPTION value="1">1 - I hate it</OPTION>
	</SELECT><br/><br/>
	Comments:<br/><br/>
	<textarea type="textarea"cols="60" rows="10" name="comments"></textarea><br/><br/>
	<input type="submit" value="Add"><br/><br/>

</form>

<?php
	/*insert the new tuple into Review*/
	if($movie){
		$sql_time="SELECT now()";
		$time=mysql_query($sql_time,$db_connection);
		$curtimestamp=mysql_fetch_array($time);	
		$query_insertreview="INSERT INTO Review VALUES('$name','$curtimestamp[0]',$movie,$rating,'$comments');";
		mysql_query($query_insertreview,$db_connection);
		mysql_close($db_connection); 
		echo "Add succeed!";
	}
?>
</body>
</html>