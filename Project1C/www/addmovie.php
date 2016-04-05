<!DOCTYPE html>
<html>
<body>
<head>
	<title>Add a new movie</title>
	<style>
	.error{color:#FF0000;}
	</style>
</head>
<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {	
   
	    if (empty($_POST["title"])) {
	    	$titleErr = "Title is required";
	    }else{
	    	$title = $_POST['title'];
	    }

		if (empty($_POST["company"])) {
			$company = "NULL";
		}else{
			$company = $_POST['company'];
		}

		if (empty($_POST["year"])) {
			$year = "NULL";
		}else{
			$year = $_POST['year'];
		}

		if (empty($_POST["rating"])) {
			$rating = "NULL";
		}else{
			$rating = $_POST['rating'];
		}

		if (empty($_POST["genre"])) {
			$genreErr = "Genre is required";
		}else{
			$genre = $_POST['genre'];
		}

	}
	
?>

<h2> Add new movie:</h2>
<p><span class="error">* Required field.</span></p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	Title: <input type="text" name="title">
	<span class="error">* <?php echo "$titleErr";?></span><br/><br/>
	Company: <input type="text" name="company"><br/><br/>
	Year: <input type="text" name="year"><br/><br/>
	MPAA Rating:<SELECT NAME="rating" style="width:100px">
				<OPTION value="G" SELECTED>G
				<OPTION value="NC-17">NC-17
				<OPTION value="PG">PG
				<OPTION value="PG-13">PG-13
				<OPTION value="R">R
				<OPTION value="surrendere">surrendere
				</SELECT><br/><br/>
	Genre: <br/>
	<input type="checkbox" name="genre[]" value="Action">Action
	<input type="checkbox" name="genre[]" value="Adult">Adult
	<input type="checkbox" name="genre[]" value="Adventure">Adventure<br/>
	<input type="checkbox" name="genre[]" value="Animation">Animation
	<input type="checkbox" name="genre[]" value="Comedy">Comedy
	<input type="checkbox" name="genre[]" value="Crime">Crime<br/>
	<input type="checkbox" name="genre[]" value="Documentary">Documentary
	<input type="checkbox" name="genre[]" value="Drama">Drama
	<input type="checkbox" name="genre[]" value="Family">Family<br/>
	<input type="checkbox" name="genre[]" value="Fantasy">Fantasy
	<input type="checkbox" name="genre[]" value="Horror">Horror
	<input type="checkbox" name="genre[]" value="Musical">Musical<br/>
	<input type="checkbox" name="genre[]" value="Mystery">Mystery
	<input type="checkbox" name="genre[]" value="Romance">Romance
	<input type="checkbox" name="genre[]" value="Sci-Fi">Sci-Fi<br/>
	<input type="checkbox" name="genre[]" value="Short">Short
	<input type="checkbox" name="genre[]" value="Thriller">Thriller
	<input type="checkbox" name="genre[]" value="War">War
	<input type="checkbox" name="genre[]" value="Western">Western<br/>
	<span class="error">* <?php echo "$genreErr";?></span><br/><br/>
	<input type="submit" name ="submit" value="Add"><br/><br/>

</form>

<?php
	/*select the max movie ID and update the MaxMovieID table*/
	if($title && $genre){
		$query="SELECT id FROM MaxMovieID;";

		$db_connection=mysql_connect("localhost","cs143","");
		if(!$db_connection){
	      $errmsg=mysql_error($db_connection);
	      echo "Connection failed: $errmsg<br />";
	      exit(1);    
	  	}else{
	  		mysql_select_db("CS143",$db_connection);
	 		$maxpid=mysql_query($query,$db_connection); 	
	 		$row = mysql_fetch_row($maxpid);
	 		$id=current($row);

		  	if($maxpid){
		  		$newid=$id+1;
		  		$query_addmovie="INSERT INTO Movie VALUES($newid,'$title',$year,'$rating','$company');";
				mysql_query($query_addmovie,$db_connection);
				$query_maxmovieid="UPDATE MaxMovieID Set id=$newid;";
				mysql_query($query_maxmovieid,$db_connection);
				

				if(in_array('Action',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Action');";	
					mysql_query($query_insert,$db_connection);
				}	  		
				if(in_array('Adult',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Adult');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Adventure',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Adventure');";	
					mysql_query($query_insert,$db_connection);
				}

				if(in_array('Animation',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Animation');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Comedy',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Comedy');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Crime',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Crime');";	
					mysql_query($query_insert,$db_connection);
				}

				if(in_array('Documentary',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Documentary');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Drama',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Drama');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Family',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Family');";	
					mysql_query($query_insert,$db_connection);
				}

				if(in_array('Fantasy',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Fantasy');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Horror',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Horror');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Musical',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Musical');";	
					mysql_query($query_insert,$db_connection);
				}

				if(in_array('Mystery',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Mystery');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Romance',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Romance');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Sci-Fi',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Sci-Fi');";	
					mysql_query($query_insert,$db_connection);
				}

				if(in_array('Short',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Short');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Thriller',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Thriller');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('War',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'War');";	
					mysql_query($query_insert,$db_connection);
				}
				if(in_array('Western',$genre)){
					$query_insert="INSERT INTO MovieGenre VALUES($newid,'Western');";	
					mysql_query($query_insert,$db_connection);
				}
				mysql_close($db_connection); 																		  			 
				echo "Add succeed!"; 
		

								
	 		}
	 		

	  	}
  	}
?>
</body>
</html>