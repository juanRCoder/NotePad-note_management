-- CRUD : NOTES:

CREATE DATABASE notepad;

-- table 'usuarios'
CREATE TABLE IF NO EXISTS usuarios(
    id INT AUTO_INCREMENT,
    username VARCHAR(250),
    password VARCHAR(500),
    PRIMARY KEY(id)
)

-- table 'notes'
CREATE TABLE IF NO EXISTS notas(
    id INT AUTO_INCREMENT,
    id_user INT,
    note VARCHAR(500),
    PRIMARY KEY(id),
    FOREIGN KEY(id_user) REFERENCES usuarios(id)
)

-- create note
-- :username -> nombre interactivo - registrado
-- :email -> email interactivo - registrado
INSERT INTO usuarios (username, password, correo) 
VALUES (:username, :password, :email)


-- get note per user: