/*---------------Primary key constraints---------------*/

/*Every Movie has a unique identification number.*/
/*Insert folloing tuple will generate error:
  Duplicate entry '2' for key 'PRIMARY'*/
INSERT INTO Movie VALUES(2,'Harry Potter',2002,'PG-13','Warner Bros.');

/*Every Actor has a unique identification number.*/
/*Insert folloing tuple will generate error:
  Duplicate entry '1' for key 'PRIMARY'*/
INSERT INTO Actor VALUES(1,'Bond','James','Male','1965-05-25','2010-05-25');

/*Every Director has a unique identification number.*/
/*Insert folloing tuple will generate error:
  Duplicate entry '16' for key 'PRIMARY'*/
INSERT INTO Director VALUES(16,'Bond','James','1965-05-25','2010-05-25');

/*---------------Referential integrity constraints---------------*/

/*Inserting or update a tuple into MovieGenre whose mid doesn't appear in Movie
  will generate error: 
  Cannot add or update a child row: a foreign key constraint fails 
  (`CS143`.`MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) 
  	REFERENCES `Movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)*/
INSERT INTO MovieGenre VALUES(69001,'Comedy');
UPDATE MovieGenre SET mid=4751 WHERE mid=3;

/*Inserting or update a tuple into MovieDirector whose mid doesn't appear in Movie
  will generate error: 
  Cannot add or update a child row: a foreign key constraint fails 
  (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) 
  	REFERENCES `Movie` (`id`) ON DELETE CASCADE ON UPDATE SET NULL)*/
INSERT INTO MovieDirector VALUES(69001,2);
UPDATE MovieDirector SET mid=4751 WHERE mid=3;

/*Inserting or update a tuple into MovieDirector whose did doesn't appear in Director
  will generate error: 
  Cannot add or update a child row: a foreign key constraint fails 
  (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_2` FOREIGN KEY (`did`) 
  	REFERENCES `Director` (`id`) ON DELETE CASCADE ON UPDATE SET NULL)*/
INSERT INTO MovieDirector VALUES(2,4751);
UPDATE MovieDirector SET did=69001 WHERE did=112;

/*Inserting or update a tuple into MovieActor whose mid doesn't appear in Movie
  will generate error: 
  Cannot add or update a child row: a foreign key constraint fails 
  (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) 
  	REFERENCES `Movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)*/
INSERT INTO MovieActor VALUES(69001,2,'Herself');
UPDATE MovieActor SET mid=4751 WHERE mid=100;

/*Inserting update a tuple into MovieActor whose aid doesn't appear in Actor
  will generate error: 
  Cannot add or update a child row: a foreign key constraint fails 
  (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) 
  	REFERENCES `Actor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)*/
INSERT INTO MovieActor VALUES(100,69001,'Herself');
UPDATE MovieActor SET aid=69001 WHERE mid=10208;

/*Inserting or update a tuple into Review whose mid doesn't appear in Movie
  will generate error: 
  Cannot add or update a child row: a foreign key constraint fails 
  (`CS143`.`Review`, CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`mid`) 
  	REFERENCES `Movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)*/
INSERT INTO Review VALUES('Tom','2015-08-01','69001','5','Good.');


/*---------------CHECK Constraints---------------*/

/*The identification number of Movie should not be a negative number.
 The following tuple doesn't make sense.*/
INSERT INTO Movie VALUES(-5,'Harry Potter',2002,'PG-13','Warner Bros.');

/*The identification number of Actor and Director should not be a negative number.
  The following tuples don't make sense.*/
INSERT INTO Actor VALUES(-1,'Bond','James','Male','1965-05-25','2010-05-25');
INSERT INTO Director VALUES(-1,'Bond','James','1965-05-25',NULL);

/*The released year of Movie should ealier than or equal to 2015 
  and later than or equal to 1895. Because this year is 2015 and 1895 is 
  the year that first movie was released.
  The following tuples don't make sense.*/
INSERT INTO Movie VALUES(69001,'Harry Potter',1110,'PG-13','Warner Bros.');
INSERT INTO Movie VALUES(69001,'Harry Potter',2110,'PG-13','Warner Bros.');

/*The Actor's date of birth should earlier than current date.
  The following tuple does't make sense.*/
INSERT INTO Actor VALUES(4751,'Bond','James','Male','2055-05-25','2010-05-25');

/*---------------NOT NULL constraints---------------*/

/*Every Movie has title.*/
/*Insert folloing tuple will generate error:
  Column 'title' cannot be null*/
INSERT INTO Movie VALUES(69001,NULL,2002,'PG-13','Warner Bros.');

/*Every Actor and Director has a date of birth.*/
/*Insert folloing tuple will generate error:
   Column 'dob' cannot be null*/
INSERT INTO Actor VALUES(4751,'Bond','James','Male',NULL,NULL);
INSERT INTO Director VALUES(4751,'Bond','James',NULL,NULL);

/*Every Actor and Director has a his last name.*/
/*Insert folloing tuple will generate error:
   Column 'last' cannot be null*/
INSERT INTO Actor VALUES(4751,NULL,'James','Male','1965-05-25',NULL);
INSERT INTO Director VALUES(4751,NULL,'James','1965-05-25',NULL);

/*Every Actor and Director has a his first name.*/
/*Insert folloing tuple will generate error:
   Column 'last' cannot be null*/
INSERT INTO Actor VALUES(4751,'Bond',NULL,'Male','1965-05-25',NULL);
INSERT INTO Director VALUES(4751,'Bond',NULL,'1965-05-25',NULL);

/*Every Actor has a sex.*/
/*Insert folloing tuple will generate error:
   Column 'sex' cannot be null*/
INSERT INTO Actor VALUES(4751,'Bond','James',NULL,'1965-05-25',NULL);
