<!DOCTYPE html>
<html>
<body>
<h1> Calculator </h1>
<form method="get" action="<?php echo $_SERVER['PHP_SELF'];?>">
   Type a mathematical expression in the box.(eg. 2*3-5)
   <br><br>
   Expression: <input type="text" name="mathexp">
   <input type="submit" value="Calculate">
   <br><br>
   Alert:
   <br>
   1.Only numbers and +,-,* and / operators are allowed in the expression.
   <br>
   2.The calculator does not support parentheses.
</form>
<h1> Result </h1>

<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
     // collect value of input field
     $name = $_REQUEST['mathexp']; 
     // judge an expression is valid or not
     if (preg_match('/^\s*-?([1-9]\d*\.?\d*|0\.?\d*[1-9]\d*|0?\.?0+|0)(\s*(\+|\*|\/|\-)\s*-?([1-9]\d*\.?\d*|0\.?\d*[1-9]\d*|0?\.?0+|0))*$/',$name)) {
     	   // judge an expression with division or not
         if(preg_match('/\/0/',$name)){
         
          echo "Error: Division by 0";

         }else{
             echo $name;
             echo " = ";
             // if a number minus a negitive number
             if(preg_match('/\-\-/',$name)){
              $name=preg_replace('/\-\-/','+',$name);
             }

             $s=eval("return $name;");
             echo $s;

         }

     } else {
         echo "Invalid Expression";
     }
}
?>

</body>
</html>