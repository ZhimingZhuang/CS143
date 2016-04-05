/*Give me the names of all the actors in the movie 'Die Another Day'.*/
SELECT CONCAT(first,' ',last)
FROM Movie A,MovieActor B,Actor C
WHERE A.title='Die Another Day' AND A.id=B.mid AND B.aid=C.id;

/*Give me the count of all the actors who acted in multiple movies.*/
SELECT COUNT(DISTINCT S.aid)
FROM MovieActor S,MovieActor E
WHERE S.aid=E.aid AND S.mid<>E.mid;

/*Give me the names of all the people who are both actors and directors.*/
SELECT CONCAT(S.first,' ',S.last)
FROM Actor S,Director E
WHERE S.id=E.id;