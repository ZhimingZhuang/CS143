<html>
<head>
	<title>Actor Information</title>
	<style></style>
</head>	
<body>
			
<?php

	$db_connection = mysql_connect('localhost','cs143', "");
	mysql_select_db("CS143",$db_connection);
	if(!$db_connection){
		die('Could not connect:'.mysql_error());
	}
	if( $_GET['aid']){
		$actorInfoInTuple = mysql_query('SELECT * FROM Actor WHERE id ='.$_GET['aid'], $db_connection);
		$actorActMovieInTuple = mysql_query('SELECT title,role,id FROM (SELECT mid,role FROM MovieActor WHERE aid = '.$_GET['aid'].') AS Role,Movie WHERE Movie.id = Role.mid;',$db_connection);
		$actorInfoInAssoc = mysql_fetch_assoc($actorInfoInTuple);
	}
	mysql_close($db_connection);
?>	

<h2>-- Show Actor Info -- </h2>
Name: <?php echo $actorInfoInAssoc['first'].' '.$actorInfoInAssoc['last'];?><br/>
Gender: <?php echo $actorInfoInAssoc['sex'];?><br/>
Date Of Birth: <?php echo $actorInfoInAssoc['dob'];?><br/>
Date Of Death: 
<?php
	if($actorInfoInAssoc['dod']){
		echo $actorInfoInAssoc['dod'];
	}else{
		echo '--Still Alive--';
	}
?>

<h2>-- Act in -- </h2>
<?php

	while($actorActMovieInAssoc = mysql_fetch_assoc($actorActMovieInTuple)){
		echo 'Act "'.$actorActMovieInAssoc[role].'" in <a href = "./showMovieInfo.php?mid='.$actorActMovieInAssoc[id].'">'.$actorActMovieInAssoc[title].'</a><br/>';
	}

?>
<hr/>		
<!-- search box -->
Search for other actors/movies 
<form method="get" action="./search.php" >
    Search: <input type="text" name="keyword"></input>
    <input type="submit" value="Search"/>
</form>


</body>
</html>
