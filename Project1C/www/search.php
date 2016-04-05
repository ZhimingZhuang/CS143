<html>
<head>
	<title>Search actor/movie</title>
	<style></style>
</head>
<body>
<?php
	$db_connection = mysql_connect('localhost','cs143', "");
	mysql_select_db("CS143",$db_connection);
	if(!$db_connection){
		die('Could not connect:'.mysql_error());
	}
	if(isset($_GET['keyword']) && $_GET['keyword']){
		$inputKeywordArray = explode(" ", $_GET['keyword']);
		if(sizeof($inputKeywordArray) > 0){
					
		mysql_query('CREATE VIEW viewWithFullName AS SELECT id,dob,concat_ws(" ",first,last) AS fullname FROM Actor;',$db_connection);
		mysql_query('CREATE VIEW tempView0 AS SELECT * from viewWithFullName WHERE fullname LIKE "%'.$inputKeywordArray[0].'%";',$db_connection);
		for($i = 1;$i < sizeof($inputKeywordArray);$i++){
			mysql_query('CREATE VIEW tempView'.$i.' AS SELECT * FROM tempView'.($i - 1).' WHERE fullname LIKE "%'.$inputKeywordArray[$i].'%";',$db_connection);
		}
		$actorInfoInTuple = mysql_query('SELECT * FROM tempView'.(sizeof($inputKeywordArray)-1).';',$db_connection);
		for($i = sizeof($inputKeywordArray) - 1;$i >= 0;$i--){
			mysql_query('DROP VIEW tempView'.$i.';',$db_connection);
		}
		mysql_query('DROP VIEW viewWithFullName;',$db_connection);

		mysql_query('CREATE VIEW tempView0 AS SELECT * FROM Movie WHERE title LIKE "%'.$inputKeywordArray[0].'%";',$db_connection);
		for($i = 1;$i < sizeof($inputKeywordArray);$i++){
			mysql_query('CREATE VIEW tempView'.$i.' AS SELECT * FROM tempView'.($i - 1).' WHERE title LIKE "%'.$inputKeywordArray[$i].'%";',$db_connection);
		}
		$movieInfoInTuple = mysql_query('SELECT * FROM tempView'.(sizeof($inputKeywordArray)-1).';',$db_connection);
			for($i = sizeof($inputKeywordArray) - 1;$i >= 0;$i--){
				mysql_query('DROP VIEW tempView'.$i.';',$db_connection);
			}
		}
	}
		mysql_close($db_connection);
?>

<h2>Search for actors/movies</h2>
<form method="get" action="./search.php" >
	Search: <input type="text" name="keyword" style="width:300px">
	<input type="submit" value="Search">
</form>
<hr/>
You are searching[<?php echo $_GET['keyword']?>]results...<br/><br/>
Searching match records in Actor database...<br/>
<?php
	while($eachActorInfoAssoc = mysql_fetch_assoc($actorInfoInTuple)){
		echo 'Actor: <a href = "./showActorInfo.php?aid='.$eachActorInfoAssoc[id].'">'.$eachActorInfoAssoc[fullname].'('.$eachActorInfoAssoc[dob].')</a><br/>';
	}
?>
Searching match records in Movie database...<br/>
<?php
	while($eachMovieInfoAssoc = mysql_fetch_assoc($movieInfoInTuple)){
		echo 'Movie: <a href = "./showMovieInfo.php?mid='.$eachMovieInfoAssoc[id].'">'.$eachMovieInfoAssoc[title].'('.$eachMovieInfoAssoc[year].')</a><br/>';
	}
?>
<hr/>
</body>
</html>