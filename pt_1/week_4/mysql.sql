DROP DATABASE IF EXISTS misc;
CREATE DATABASE misc;
USE misc;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
   user_id INTEGER NOT NULL AUTO_INCREMENT,
   name VARCHAR(128),
   email VARCHAR(128),
   password VARCHAR(128),
   PRIMARY KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users ADD INDEX(email);
ALTER TABLE users ADD INDEX(password);

DROP TABLE IF EXISTS Profile;
CREATE TABLE Profile (
  profile_id INTEGER NOT NULL AUTO_INCREMENT,
  user_id INTEGER NOT NULL,
  first_name TEXT,
  last_name TEXT,
  email TEXT,
  headline TEXT,
  summary TEXT,

  PRIMARY KEY(profile_id),

  CONSTRAINT profile_ibfk_2
        FOREIGN KEY (user_id)
        REFERENCES users (user_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO users (name, email, password) VALUES ('1','1@1', md5('XyZzy12*_123'));
INSERT INTO users (name, email, password) VALUES ('UMSI','umsi@umich.edu', md5('XyZzy12*_php123'));

-- GRANT ALL PRIVILEGES ON misc.* TO 'super'@'localhost' IDENTIFIED BY '1234';
SELECT first_name, last_name, headline FROM Profile;

INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (1, 'fn','ln','m@m', 'hl', 'sum');

CREATE TABLE autos (
auto_id INT UNSIGNED NOT NULL AUTO_INCREMENT KEY,
make VARCHAR(128),
year INTEGER,
mileage INTEGER
);