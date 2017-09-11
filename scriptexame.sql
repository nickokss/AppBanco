CREATE DATABASE bdbanco;

USE bdbanco;

CREATE USER 'xestor'@'localhost' IDENTIFIED BY 'abc123';

GRANT ALL PRIVILEGES ON bdbanco.* TO 'xestor'@'localhost' WITH GRANT OPTION;

CREATE TABLE sucursal 
(id SMALLINT UNSIGNED NOT NULL,
 nome VARCHAR(30) NOT NULL,
 enderezo VARCHAR(30) NOT NULL,
 cidade VARCHAR(30) NOT NULL,
 cod_postal CHAR(5),
 CONSTRAINT pk_sucursal PRIMARY KEY(id) 
);

CREATE TABLE departamento
(id SMALLINT UNSIGNED NOT NULL,
 nome VARCHAR(20) NOT NULL,
 CONSTRAINT pk_dpto PRIMARY KEY(id) 
);

CREATE TABLE empregado
(id SMALLINT UNSIGNED NOT NULL,
 nome VARCHAR(20) NOT NULL,
 apelido1 VARCHAR(20) NOT NULL,
 apelido2 VARCHAR(20) NOT NULL,
 id_dpto SMALLINT UNSIGNED NULL,
 id_sucursal SMALLINT UNSIGNED NULL,
 CONSTRAINT pk_empregado PRIMARY KEY(id),
 CONSTRAINT fk_depto FOREIGN KEY(id_dpto) REFERENCES departamento(id),
 CONSTRAINT fk_empregado_sucursal FOREIGN KEY(id_sucursal) REFERENCES sucursal(id)
);

CREATE TABLE cliente
(id SMALLINT UNSIGNED NOT NULL,
 nomecompleto VARCHAR(40) NOT NULL,
 tipocliente VARCHAR(10) NOT NULL,
 enderezo VARCHAR(30) NOT NULL,
 cidade VARCHAR(30) NOT NULL,
 cod_postal CHAR(5),
 CONSTRAINT pk_cliente PRIMARY KEY(id),
 CONSTRAINT ck_tipo CHECK (tipocliente IN ('persoal','entidade','autonomo','empresa'))
);

CREATE TABLE tipoconta
(id SMALLINT UNSIGNED NOT NULL,
 nome VARCHAR(20) NOT NULL,
 CONSTRAINT pk_tipoconta PRIMARY KEY(id),
 CONSTRAINT ck_tipoconta CHECK(nome IN ('aforro','credito','valores','prazo fixo'))
);

CREATE TABLE conta
(id SMALLINT UNSIGNED NOT NULL,
 id_tipoconta SMALLINT UNSIGNED NOT NULL,
 id_sucursal SMALLINT UNSIGNED NOT NULL,
 id_cliente SMALLINT UNSIGNED NOT NULL,
 data_apertura DATE NOT NULL,
 data_peche DATE NULL,
 estado VARCHAR(10) NOT NULL,
 balance FLOAT(10,2),
 CONSTRAINT pk_conta PRIMARY KEY(id),
 CONSTRAINT fk_tipoconta FOREIGN KEY(id_tipoconta) REFERENCES tipoconta(id),
 CONSTRAINT fk_conta_sucursal FOREIGN KEY(id_sucursal) REFERENCES sucursal(id),
 CONSTRAINT fk_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id),
 CONSTRAINT ck_estado CHECK (estado IN ('aberta','pechada','suspensa'))
);



INSERT INTO sucursal
VALUES 
(1,'COMPOSTELA CENTRO', 'Doutor Teixeiro 62', 'Santiago de Compostela', '15702'), 
(2,'LALIN CENTRO', 'Pintor Laxeiro 12', 'Lalin', '36500'), 
(3,'SILLEDA', 'Concepcion Arenal 1', 'Silleda', '36540'),
(4,'BANDEIRA', 'Avda Liberdade 25', 'Bandeira - Silleda', '36570'),
(5,'OURENSE COUTO', 'Remedios 19', 'Ourense', '32003'),
(6,'OURENSE CENTRO', 'Paseo 60', 'Ourense', '32005'),
(7,'VIGO TEIS', 'Humberto Baena 76', 'Vigo', '36300'),
(8,'LUGO CENTRO', 'Pza do Campo 13', 'Lugo', '27001');

INSERT INTO departamento
VALUES
(1,'particulares'),
(2,'empresas'),
(3,'seguros'),
(4,'investimentos');


INSERT INTO empregado
VALUES
(1,'Pablo','Albores','Cabanas',1,1),
(2,'Emilio','Díaz','Rodrigues',1,2),
(3,'Vicente','Pérez','Lamas',1,3),
(4,'Íñigo','Barreiro','García',1,4),
(5,'Marinha','Crespo','Cide',1,5),
(6,'Xiana','Dios','Ariza',1,6),
(7,'Lourdes','Ouxea','González',1,7),
(8,'Javier','Lago','Troncoso',2,2),
(9,'Ana','Louzao','Leirachá',3,3),
(10,'Rosa','Palmeiro','Vázquez',3,4),
(11,'Iago','Vilar','Requeixo',4,4),
(12,'Lorena','García','Pumar',4,6);

INSERT INTO cliente
VALUES
(1,'Paulo Gantes Padín','persoal','Prado 13','Lalín','36550'),
(2,'Emilia Murias Lemos','persoal','Buenos Aires 3','Lalín','36500'),
(3,'Fundación Amigos da Vaca','entidade','Cervaña 10','Silleda','36540'),
(4,'Íñaki Valdés Egia','autonomo','Prego de Oliver 6 4º','Ourense','32003'),
(5,'María Fontes Laranho','autonomo','Manuel Pereira 3 5º','Ourense','32004'),
(6,'Xistral SL','empresa','Garcia Barbón 99 1º','Vigo','36300'),
(7,'Louis Mbazi','persoal','Ronda da Muralla 106','Lugo','27002'),
(8,'Xavier Rodríguez Rodríguez','persoal','Doutor Troncoso 9 1º','Silleda','36540'),
(9,'Concello de Silleda','entidade','Pza de Galiza 1','Silleda','36540'),
(10,'Rosalía Campos Pi','autonomo','Prego de Oliver 17 3ºB','Ourense','32003'),
(11,'Iago Castro Balboa','autonomo','Lobeira 33','Ourense','32006'),
(12,'Lorcabe Coop','empresa','Fortuna 17','Santiago de Compostela', '15300');

INSERT INTO tipoconta
VALUES
(1,'aforro'),
(2,'credito'),
(3,'valores'),
(4,'prazo fixo');


INSERT INTO conta
VALUES
(1,1,1,12,'2000-1-12','2010-1-12','pechada',null),
(2,1,1,12,'2000-1-15',null,'aberta',300000.00),
(3,2,2,1,'2000-1-19','2012-6-6','pechada',null),
(4,2,2,2,'2005-1-12',null,'aberta',10000.00),
(5,2,2,9,'2005-1-16','2015-4-3','pechada',null),
(6,3,3,8,'2010-10-12',null,'suspensa',5000.00),
(7,4,4,9,'2010-11-1',null,'aberta',75000.00),
(8,4,4,3,'2011-2-1',null,'aberta',100000.00),
(9,4,5,10,'2011-5-23',null,'aberta',321.13),
(10,1,5,10,'2012-6-11',null,'aberta',80.12),
(11,1,6,4,'2013-7-31','2016-1-1','pechada',null),
(12,1,6,11,'2013-9-9',null,'suspensa',-3.21),
(13,2,7,6,'2014-4-30',null,'aberta',1200.25),
(14,2,7,12,'2014-5-2',null,'aberta',3220.00),
(15,3,7,6,'2015-3-1',null,'aberta',776.80);

    