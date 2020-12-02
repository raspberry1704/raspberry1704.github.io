DROP TABLE IF EXISTS BOOK CASCADE;
DROP TABLE IF EXISTS USERS CASCADE;


CREATE TABLE USERS(
	id  SERIAL PRIMARY KEY,
	ad_level integer,
	username varchar(30),
	password varchar(160),
	email varchar(50),
	adresse varchar(150),
	dateDeNaissance varchar(10)
);

CREATE TABLE BOOK(
	id SERIAL PRIMARY KEY,
	title varchar(75),
	image varchar(75),
	author varchar(75),
	description varchar(500),
	owner integer,
	date_ajout varchar(10),
	foreign key (owner) REFERENCES USERS(id)
);


INSERT INTO users( id, ad_level, username, password ) VALUES( 0, 0, 'insert-init', 'lol');

INSERT INTO Book (title, image, author, description, owner) VALUES('Harry potter à lécole des sorciers', 'harry_potter_1.jpg', 'JK_Rowlling', 'Livre sur la magie', 0);
INSERT INTO Book (title, image, author, description, owner) VALUES('Harry potter et la chambre des secrets', 'harry_potter_2.jpg', 'JK_Rowlling', 'Livre sur la magie', 0);
INSERT INTO Book (title, image, author, description, owner) VALUES('Harry potter et le prisonnier daskaban', 'harry_potter_3.jpg', 'JK_Rowlling', 'Livre sur la magie', 0);
