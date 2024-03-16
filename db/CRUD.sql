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

-- Create note
-- :username -> nombre interactivo - registrado
-- :email -> email interactivo - registrado
INSERT INTO usuarios (username, password, correo) 
VALUES (:username, :password, :email)


-- Read / Get note per user
-- :id -> notas por usuario logeado 
SELECT note, id FROM notas WHERE id_user = :id;


-- Update note
-- :noteContent -> seleccion de nota para modificar.
-- :idNote -> id de la nota a actualizar.
UPDATE notas SET note = :noteContent WHERE id = :idNote;


-- Delete note
-- :idDelete -> id de la nota a eliminar.
DELETE FROM notas WHERE id = :idDelete;