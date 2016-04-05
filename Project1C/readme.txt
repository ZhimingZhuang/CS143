
(1) In the “add a new actor/director” page, I notice that the demo using radio to select an identity. But if I first added an actor and then tried to add a director for the same person, this person might get two different person IDs. Instead, I use checkbox which ensures that a person can add two different identities at a time using same person ID.

(2) For search.php, let me take searching Tom hanks as an example. I first considered using like “%Tom%hanks%” to search information. But I cannot search something having keywords like “hanks Tom”. To improve it, I first create a view for the results that have keyword “Tom”, and then I search the keyword “hanks” in this view. In that case, I can find any results with keywords ”Tom hanks” and the order of the keywords doesn’t matter.

(3) To better improve my page interface, I use several framesets. But more than one html using frameset couldn’t pass the Selenium IDE test. The error will occur when the IDE selecting a new window. So I leave only one html which is “p1c.html” showing a terse interface.

Notes:
(1)
“p1c.html” is my final version page. If you want to see the final results, open “p1c.html”.
(2)
Please use slow speed to replay my test cases, Otherwise it may output errors!





