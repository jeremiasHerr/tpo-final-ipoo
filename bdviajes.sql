CREATE DATABASE bdviajes;

CREATE TABLE empresa(
    idempresa bigint AUTO_INCREMENT,
    enombre varchar(150),
    edireccion varchar(150),
    PRIMARY KEY (idempresa)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- Tabla Persona
CREATE TABLE persona(
    documento varchar(15),
    nombre varchar(30),
    apellido varchar (30),
    PRIMARY KEY (documento)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE responsable (
    rnumeroempleado bigint AUTO_INCREMENT,
    rnumerolicencia bigint,
    rdocumento varchar(15),
    PRIMARY KEY (rnumeroempleado),
    FOREIGN KEY (rdocumento) REFERENCES persona (documento)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    )ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;;
	
CREATE TABLE viaje (
    idviaje bigint AUTO_INCREMENT, /*codigo de viaje*/
	vdestino varchar(150),
    vcantmaxpasajeros int,
	idempresa bigint,
    rnumeroempleado bigint,
    vimporte float,
    PRIMARY KEY (idviaje),
    FOREIGN KEY (idempresa) REFERENCES empresa (idempresa),
	FOREIGN KEY (rnumeroempleado) REFERENCES responsable (rnumeroempleado)
    ON UPDATE RESTRICT
    ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT = 1;
	
CREATE TABLE pasajero (
    pdocumento varchar(15),
	ptelefono int, 
	idviaje bigint,
    PRIMARY KEY (pdocumento),
	FOREIGN KEY (idviaje) REFERENCES viaje (idviaje)	
    ON UPDATE CASCADE 
    ON DELETE RESTRICT,
    FOREIGN KEY (pdocumento) REFERENCES persona (documento)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    )ENGINE=InnoDB DEFAULT CHARSET=utf8; 
 
-- Insertar 1 empresa
INSERT INTO empresa (enombre, edireccion) 
VALUES ('Viajes Felices S.A.', 'Calle Principal 123');

-- Insertar 1 persona (responsable del viaje)
INSERT INTO persona (documento, nombre, apellido) 
VALUES ('12345678', 'Juan', 'Pérez');

-- Insertar 1 responsable vinculado a esa persona
INSERT INTO responsable (rnumerolicencia, rdocumento) 
VALUES (987654321, '12345678');

-- Insertar 2 viajes con el mismo responsable
INSERT INTO viaje (vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte) 
VALUES 
  ('Buenos Aires', 50, 1, 1, 1500.00),
  ('Mendoza', 40, 1, 1, 2000.00);

-- Insertar 2 personas (pasajeros)
INSERT INTO persona (documento, nombre, apellido) 
VALUES 
  ('11111111', 'Carlos', 'López'),
  ('22222222', 'María', 'García');

-- Insertar 1 pasajero para el primer viaje
INSERT INTO pasajero (pdocumento, ptelefono, idviaje) 
VALUES ('11111111', 1234567890, 1);

-- Insertar 1 pasajero para el segundo viaje
INSERT INTO pasajero (pdocumento, ptelefono, idviaje) 
VALUES ('22222222', 9876543210, 2);


