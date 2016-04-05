<html>
<head>
	<title>Movie Information</title>
	<style></style>
</head>	
<body>
			
<?php

	$db_connection = mysql_connect('localhost','cs143', "");
	mysql_select_db("CS143",$db_connection);
	if(!$db_connection){
		die('Could not connect:'.mysql_error());
	}
	if($_GET['mid']){
		$movieInfoInTuple = mysql_query('SELECT * FROM Movie WHERE id ='.$_GET['mid'], $db_connection);
		$movieDirectorInTuple = mysql_query('SELECT concat_ws(" ",first,last) AS fullname,dob FROM Director,(SELECT did FROM MovieDirector WHERE mid = '.$_GET['mid'].') as Temp WHERE Director.id = Temp.did;',$db_connection);
		$movieGenreInTuple = mysql_query('SELECT genre FROM MovieGenre WHERE mid ='.$_GET['mid'],$db_connection);
		$movieActorRoleInTuple = mysql_query('SELECT concat_ws(" ",first,last) AS fullname,role,id FROM (SELECT aid,role FROM MovieActor WHERE mid = '.$_GET['mid'].') as Role,Actor WHERE Actor.id = Role.aid;',$db_connection);
		$movieReviewInTuple = mysql_query('SELECT * FROM Review WHERE mid = '.$_GET['mid'],$db_connection);
		$movieAvgScoreInTuple = mysql_query('SELECT avg(rating) FROM Review WHERE mid = '.$_GET['mid'],$db_connection);
		$movieReviewCntInTuple = mysql_query('SELECT count(*) FROM Review WHERE mid = '.$_GET['mid'],$db_connection);
		$movieInfoInAssoc = mysql_fetch_assoc($movieInfoInTuple);
		$movieAvgScoreInArray =  mysql_fetch_row($movieAvgScoreInTuple);
		$movieReviewCntInArray = mysql_fetch_row($movieReviewCntInTuple);
	}
	mysql_close($db_connection);
?>	

<h2>-- Show Movie Info --</h2>
Title: <?php echo $movieInfoInAssoc['title'].'('.$movieInfoInAssoc['year'].')';?><br/>
Producer: <?php echo $movieInfoInAssoc['company'];?><br/>
MPAA Rating: <?php echo $movieInfoInAssoc['rating'];?><br/>
Director: 
<font color='Green'>
<?php
	$commaNeedPrint = FALSE;
	while($movieDirectorInAssoc = mysql_fetch_assoc($movieDirectorInTuple)){
		if($commaNeedPrint){
			echo ', ';
		}else{
			$commaNeedPrint = TRUE;
		}
		echo $movieDirectorInAssoc[fullname].'('.$movieDirectorInAssoc[dob].')';
	}		
?>
</font><br/>
Genre: 
<font color='Brown'>
<?php
	$commaNeedPrint = FALSE;
	while($movieGenreInAssoc = mysql_fetch_assoc($movieGenreInTuple)){
		if($commaNeedPrint){
			echo ', ';
		}else{
			$commaNeedPrint = TRUE;
		}
		echo $movieGenreInAssoc['genre'];
	}
?>
</font>

<h2>-- Actor in this movie -- </h2>
<?php
	while($movieActorRoleInAssoc = mysql_fetch_assoc($movieActorRoleInTuple)){
		echo '<a href="./showActorInfo.php?aid='.$movieActorRoleInAssoc[id].'">'.$movieActorRoleInAssoc[fullname].'</a> act as "'.$movieActorRoleInAssoc[role].'"<br/>';
	}
?>

<h2>-- User Review -- </h2>
Average Score: 
<?php
	if($movieReviewInAssoc = mysql_fetch_assoc($movieReviewInTuple)){
		echo $movieAvgScoreInArray[0].'/5 (5.0 is best) by '.$movieReviewCntInArray[0].' reviews(s).';
		echo '<a href="./addComment.php">  Add your review now!!</a><br/>';
		echo 'All Comments in Details:<br/>';
		do{
			echo '<font color="Blue">In '.$movieReviewInAssoc[time].', <font color="Red">Mr. '.$movieReviewInAssoc[name].'</font> said: I rate this movie score <font color="Red">'.$movieReviewInAssoc[rating].'</font> point(s), here is my comment. </font> <br/>'.$movieReviewInAssoc[comment].'<br/><br/>';
		}while($movieReviewInAssoc = mysql_fetch_assoc($movieReviewInTuple));
	}else{
		echo ' (Sorry, none review for this movie)';
		echo '<a href="./addComment.php">  Add your review now!!</a><br/>';
		echo 'All Comments in Details:<br/>';
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
