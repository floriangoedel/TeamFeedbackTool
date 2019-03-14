DROP DATABASE IF EXISTS DoctrineTest;

CREATE DATABASE DoctrineTest;

USE DoctrineTest;

DROP TABLE IF EXISTS Blog;
CREATE TABLE Blog (
  pk_blogId INT PRIMARY KEY,
  beschreibung VARCHAR(50)
);

DROP TABLE IF EXISTS Autor;
CREATE TABLE Autor (
  pk_autorId INT PRIMARY KEY,
  firstname VARCHAR(15),
  lastname VARCHAR(15),
  geburtsdatum DATE,
);

DROP TABLE IF EXISTS ownsBlog;
CREATE TABLE ownsBlog(
  pk_fk_blogId INT,
  pk_fk_autorId INT,
  
  CONSTRAINT FOREIGN KEY (pk_fk_blogId) REFERENCES Blog (pk_blogId),
  CONSTRAINT FOREIGN KEY (pk_fk_autorId) REFERENCES Autor (pk_autorId),
  CONSTRAINT pk_ownsBlog PRIMARY KEY (pk_fk_blogId, pk_fk_autorId)
);

