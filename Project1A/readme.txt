This php first uses preg_match() to judge an expression is a valid mathematical expression. Otherwise, it would output “Invalid expression!”. And then it deals with the case that a number minus a negative number(eg.1--1=2). After that I judge whether there exists division by 0. Finally, I use eval() to calculate the mathematical expression.


