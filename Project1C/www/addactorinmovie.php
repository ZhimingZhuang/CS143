<!DOCTYPE html>
<html>
<body>
<head>
	<title>Add a new actor into a movie</title>
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

		if (empty($_POST["actor"])) {
			$actorErr = "Actor is required";
		}else{
			$actor = $_POST['actor'];
		}

		if (empty($_POST["role"])) {
			$roleErr = "Role is required";
		}else{
			$role = $_POST['role'];
		}
	}
	
?>

<h2> Add new actor in a movie:</h2>
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

	Actor: <SELECT name="actor" style="width:250px">
	<OPTION SELECTED>
	<?php
	$sqlactor="SELECT id,first,last,dob FROM Actor ORDER BY first;";
	$query_selectactor=mysql_query($sqlactor);
	while($resultactor=mysql_fetch_array($query_selectactor)){
	$afname=$resultactor[first];
	$alname=$resultactor[last];
	$adob=$resultactor[dob];
	$aid=$resultactor[id];
	?>	
	<OPTION value="<?=$aid?>"><?php echo"$afname $alname($adob)"?></OPTION>	
	<?php
	}
	?>
	</SELECT>
	<span class="error">* <?php echo "$actorErr";?></span><br/><br/>

	Role:   <input type="text" name="role">
	<span class="error">* <?php echo "$roleErr";?></span><br/><br/>
	<input type="submit" value="Add"><br/><br/>

</form>

<?php
	
	/*insert the new tuple into MovieActor*/
	if($movie && $actor && $role){
		$sql_addrole="INSERT INTO MovieActor VALUES($movie,$actor,'$role');";
		mysql_query($sql_addrole,$db_connection);
		mysql_close($db_connection); 
		echo "Add succeed!";
	}
?>
</body>
</html>