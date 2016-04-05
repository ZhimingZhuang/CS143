DROP TABLE IF EXISTS MovieGenre;
DROP TABLE IF EXISTS MovieDirector;
DROP TABLE IF EXISTS MovieActor;
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS MaxPersonID;
DROP TABLE IF EXISTS MaxMovieID;
DROP TABLE IF EXISTS Movie;
DROP TABLE IF EXISTS Actor;
DROP TABLE IF EXISTS Director;

CREATE TABLE Movie(
	id int NOT NULL,
	/*Every Movie has title.*/
	title varchar(100) NOT NULL,
	year int,
	rating varchar(10),
	company varchar(50),
	/*Every Movie has a unique identification number.*/
	PRIMARY KEY(id),
	/*The released year of Movie should ealier than or equal to 2015 
  	and later than or equal to 1895. Because this year is 2015 and 1895 is 
  	the year that first movie was released.	*/
	CHECK(year >= 1895 AND year<=2015),
	/*The identification number of Movie should not be a negative number.*/
	CHECK(id>=0)
)ENGINE=INNODB;

CREATE TABLE Actor(
	id int NOT NULL,
	/*Every Actor has a his name,date of birth and sex.*/
	last varchar(20) NOT NULL,
	first varchar(20) NOT NULL,
	sex varchar(6) NOT NULL, 
	dob date NOT NULL,
	dod date,
	/*Every Actor has a unique identification number.*/
	PRIMARY KEY(id),
	/*The identification number of Actor and Director should not be a negative number.
    The following tuples don't make sense.*/
	CHECK(id>=0),
	/*The Actor's date of birth should earlier than current date.*/
	CHECK(dob<date_format(curdate(),'%Y%m%d') )
)ENGINE=INNODB;

CREATE TABLE Director(
	id int NOT NULL,
	/*Every Director has a his name,date of birth.*/
	last varchar(20) NOT NULL,
	first varchar(20) NOT NULL,
	dob date NOT NULL,
	dod date,
	/*Every Actor has a unique identification number.*/
	PRIMARY KEY(id),
	/*Every Director has a unique identification number.*/	
	CHECK(id>=0),
	/*The Director's date of birth should earlier than current date.*/
	CHECK(dob<date_format(curdate(),'%Y%m%d') )
)ENGINE=INNODB;

CREATE TABLE MovieGenre(
	mid int,
	/*Every movie has its genre.*/
	genre varchar(20) NOT NULL,
	/*Every mid appears in MovieGenre should exist first in Movie*/
	FOREIGN KEY (mid) REFERENCES Movie(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE MovieDirector(
	mid int,
	/*Every movie has its director.*/
	did int NOT NULL,
	/*Every mid appears in MovieDirector should exist first in Movie*/
	FOREIGN KEY (mid) REFERENCES Movie(id)
		ON DELETE CASCADE
		ON UPDATE SET NULL,
	/*Every did appears in MovieDirector should exist first in Director*/
	FOREIGN KEY (did) REFERENCES Director(id)	
		ON DELETE CASCADE
		ON UPDATE CASCADE	
)ENGINE=INNODB;

CREATE TABLE MovieActor(
	mid int,
	/*Every movie has its actor.*/
	aid int NOT NULL,
	/*Every actor has his role.*/
	role varchar(50) NOT NULL,
	/*Every mid appears in MovieActor should exist first in Movie*/
	FOREIGN KEY (mid) REFERENCES Movie(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	/*Every aid appears in MovieActor should exist first in Actor*/
	FOREIGN KEY (aid) REFERENCES Actor(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE		
)ENGINE=INNODB;

CREATE TABLE Review(
	name varchar(20),
	time timestamp,
	mid int,
	rating int,
	comment varchar(500),
	/*Every mid appears in Review should exist first in Movie*/
	FOREIGN KEY (mid) REFERENCES Movie(id)
		ON DELETE CASCADE
		ON UPDATE CASCADE
)ENGINE=INNODB;

CREATE TABLE MaxPersonID(
	id int NOT NULL
)ENGINE=INNODB;

CREATE TABLE MaxMovieID(
	id int NOT NULL
)ENGINE=INNODB;

INSERT INTO MaxMovieID VALUES(4750);
INSERT INTO MaxPersonID VALUES(69000);