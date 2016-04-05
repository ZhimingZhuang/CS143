<!DOCTYPE html>
<html>
<body>
<head>
	<title>Add a new actor/director</title>
	<style>
	.error{color:#FF0000;}
	</style>
</head>
<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {	
   
	    if (empty($_POST["fname"])) {
	    	$fnameErr = "First name is required";
	    }else{
	    	$fname = $_POST['fname'];
	    }

		if (empty($_POST["lname"])) {
			$lnameErr = "Last name is required";
		}else{
			$lname = $_POST['lname'];
		}
		
		if (empty($_POST["identity"])) {
			$identityErr = "Identity is required";
		}else{
			$identity = $_POST['identity'];
		}

		if (empty($_POST["dob"])) {
			$dobErr = "Date of Birth is required";
		}else{
			$dob = $_POST['dob'];
		}

		if (empty($_POST["sex"])) {
			$sexErr = "Gender is required";
		}else{
			$sex = $_POST['sex'];
		}

		if (empty($_POST["dod"])) {
			$dod = "NULL";
		}else{
			$dod = $_POST['dod'];
		}
	}
	
?>

<h2> Add new actor/director:</h2>
<p><span class="error">* Required field.</span></p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	 Identity: 
	 <input type="checkbox" name="identity[]" value="Actor">Actor
	 <input type="checkbox" name="identity[]" value="Director">Director
	 <span class="error">* <?php echo "$identityErr";?></span><br/><br/>
	 First Name: <input type="text" name="fname">
	 <span class="error">* <?php echo "$fnameErr";?></span><br/><br/>
	 Last Name: <input type="text" name="lname">
	 <span class="error">* <?php echo "$lnameErr";?></span><br/><br/>
	 Gender: 
	 <input type="radio" name="sex" value="Male">Male
	 <input type="radio" name="sex" value="Female">Female
	 <span class="error">* <?php echo "$sexErr";?></span><br/><br/>
	 Date of Birth: <input type="text" name="dob">(Format like 19750525)
	 <span class="error">* <?php echo "$dobErr";?></span><br/><br/>
	 Date of Die:<input type="text" name="dod">(Leave blank if alive now)<br/><br/>
	 <input type="submit" name="submit" value="Add"><br/><br/>
</form>

<?php
	/*select the max person ID and update the MaxpersonID table*/
	
	if($identity && $fname && $lname && $sex && $dob){
		$query="SELECT id FROM MaxPersonID;";

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

				if(in_array('Director',$identity)){
					$query_insert="INSERT INTO Director VALUES($newid,'$lname','$fname',$dob,$dod);";	
					mysql_query($query_insert,$db_connection);
				}	  		
		  			 
				if(in_array('Actor',$identity)){
					$query_insert="INSERT INTO Actor VALUES($newid,'$lname','$fname','$sex',$dob,$dod);";
					mysql_query($query_insert,$db_connection);
				}
		
				$query_maxpersonid="UPDATE MaxPersonID Set id=$newid;";
				mysql_query($query_maxpersonid,$db_connection);
        		mysql_close($db_connection); 
				echo "Add succeed!";  				
	 		}
	  	}
  	}
?>
</body>
</html>