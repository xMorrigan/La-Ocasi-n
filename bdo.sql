CREATE DATABASE login;

CREATE TABLE Personas (
    id INT PRIMARY KEY IDENTITY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    aumero VARCHAR(15) NOT NULL,
    direccion VARCHAR(100) NOT NULL,
    codigo_postal VARCHAR(10) NOT NULL,
    area VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
	password VARCHAR(10) NOT NULL,
    estado_region VARCHAR(50) NOT NULL
)
SELECT * FROM personas