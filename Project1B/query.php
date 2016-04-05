<!DOCTYPE html>
<html>
<body>
<h2> Solve MYSQL</h2>
<form method="get" action="<?php echo $_SERVER['PHP_SELF'];?>">
   Please input your query in the below textarea.<br />
   Example: select * from Actor<br /><br />
   <textarea cols="60" rows="10" name="query"></textarea><br /><br />
   <input type="submit" value="Submit"><br /><br />
   <h2> MYSQL Result </h2>
</form>

<style>
table, th, td {
    border: 1px solid black;
}
th, td {
    padding: 5px;
}
th {
    text-align: left;
}
</style>

<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $contents = $_GET['query']; 
  $db_connection=mysql_connect("localhost","cs143","");
  if(!$db_connection){
      $errmsg=mysql_error($db_connection);
      echo "Connection failed: $errmsg<br />";
      exit(1);    
  }else{
      mysql_select_db("CS143",$db_connection);
      $sanitized_contents=mysql_real_escape_string($contents,$db_connection);
      $query_to_issue=sprintf($contents,$sanitized_contents);
      $rs=mysql_query($contents,$db_connection);
      
      if($rs){
        $cols=mysql_num_fields($rs);
        $i=0;
        echo"<table border='1' style='width:50%'>";
        echo"<tr>";
        for($i=0;$i<$cols;$i++){
          $field=mysql_field_name($rs,$i);
          echo '<th>'.$field.'</th>';
        }
        echo"</tr>";
        
        while($row = mysql_fetch_row($rs)) {
          echo"<tr>";
          foreach($row as $value){
                  if($value){
                    echo '<td>'.$value.'</td>';
                  }else{
                    echo '<td>'.'N/A'.'</td>';
                  }
          }
          echo"</tr>";
        }
        echo"</table>"; 
        mysql_free_result($rs);
        mysql_close($db_connection);    
      }
      else{
        die(mysql_error());
      }

  }

}

?>

</body>
</html>