<!DOCTYPE html>
<html>
<body>
<head>
	<title>Add a new director into a movie</title>
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

		if (empty($_POST["director"])) {
			$actorErr = "Director is required";
		}else{
			$director = $_POST['director'];
		}

	}
	
?>

<h2> Add new director in a movie:</h2>
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

	Director: <SELECT name="director" style="width:250px">
	<OPTION SELECTED>
	<?php
	$sqldirector="SELECT id,first,last,dob FROM Director ORDER BY first;";
	$query_selectdirector=mysql_query($sqldirector);
	while($resultdirector=mysql_fetch_array($query_selectdirector)){
	$afname=$resultdirector[first];
	$alname=$resultdirector[last];
	$adob=$resultdirector[dob];
	$aid=$resultdirector[id];
	?>	
	<OPTION value="<?=$aid?>"><?php echo"$afname $alname($adob)"?></OPTION>	
	<?php
	}
	?>
	</SELECT>
	<span class="error">* <?php echo "$directorErr";?></span><br/><br/>

	<input type="submit" value="Add"><br/><br/>

</form>

<?php
	
	/*insert the new tuple into MovieDirector*/
	if($movie && $director){

		$sql_adddirector="INSERT INTO MovieDirector VALUES($movie,$director);";
		mysql_query($sql_adddirector,$db_connection);
		mysql_close($db_connection); 
		echo "Add succeed!";
	}
?>
</body>
</html>