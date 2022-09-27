-- Crear la base de datos
DROP DATABASE IF EXISTS pibd; -- RESETEA LA BASE DE DATOS --
CREATE DATABASE pibd CHARACTER SET utf8mb4;
USE pibd;

-- CREACIÓN DE TABLAS --
-- Tabla PAISES
CREATE TABLE paises (
  id     INTEGER     auto_increment ,
  nombre VARCHAR(30)                ,

  PRIMARY KEY ( id )
 
) ENGINE = InnoDB;

-- Tabla ESTILOS
CREATE TABLE estilos (
  id          INTEGER      NOT NULL auto_increment ,
  nombre      VARCHAR(20)                          ,
  descripcion VARCHAR(254)                         ,
  fichero     VARCHAR(60)                          ,

  PRIMARY KEY ( id )
) ENGINE = InnoDB;

-- Tabla USUARIOS
CREATE TABLE usuarios (
  id          INTEGER      NOT NULL auto_increment ,
  nombre      VARCHAR(15)  UNIQUE                  ,
  clave       VARCHAR(150)                         ,
  email       VARCHAR(254)                         ,
  sexo        BOOLEAN                              ,
  fNacimiento DATE                                 ,
  ciudad      VARCHAR(45)                          ,
  pais        INTEGER                              ,
  foto        VARCHAR(45)                          ,
  fRegistro   DATETIME                             ,
  estilo      INTEGER                              ,

  PRIMARY KEY ( id )                                                ,
  FOREIGN KEY ( pais )   REFERENCES paises( id )  ON DELETE CASCADE ,
  FOREIGN KEY ( estilo ) REFERENCES estilos( id ) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Tabla ALBUMES
CREATE TABLE albumes (
  id          INTEGER      NOT NULL auto_increment ,
  titulo      VARCHAR(50)                          ,
  descripcion VARCHAR(254)                         ,
  usuario     INTEGER                              ,

  PRIMARY KEY ( id )                                                  ,
  FOREIGN KEY ( usuario ) REFERENCES usuarios( id ) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Tabla FOTOS
CREATE TABLE fotos (
  id          INTEGER      NOT NULL auto_increment ,
  titulo      VARCHAR(50)                          ,
  descripcion VARCHAR(254)                         ,
  fecha       DATETIME                             ,
  pais        INTEGER                              ,
  album       INTEGER      NOT NULL                ,
  fichero     VARCHAR(60)                          ,
  alternativo VARCHAR(255)                         , -- LIMITADO EL MÍNIMO A 10 CON PHP --
  fRegistro   DATETIME                             ,

  PRIMARY KEY ( id )                                               ,
  FOREIGN KEY ( pais )  REFERENCES paises( id )  ON DELETE CASCADE ,
  FOREIGN KEY ( album ) REFERENCES albumes( id ) ON DELETE CASCADE

) ENGINE = InnoDB;

-- Tabla SOLICITUDES
CREATE TABLE solicitudes (
  id          INTEGER       NOT NULL auto_increment ,
  album       INTEGER                               ,
  nombre      VARCHAR(75)                           ,
  apellidos   VARCHAR(125)                          ,
  titulo      VARCHAR(200)                          ,
  descripcion VARCHAR(4000)                         ,
  email       VARCHAR(200)                          ,
  direccion   VARCHAR(200)                          ,
  color       VARCHAR(7)                            ,
  copias      INTEGER                               ,
  resolucion  INTEGER                               ,
  fecha       DATETIME                              ,
  iColor      BOOLEAN                               ,
  fRegistro   DATETIME                              ,
  coste       DOUBLE                                ,

  PRIMARY KEY ( id )                                               ,
  FOREIGN KEY ( album ) REFERENCES albumes( id ) ON DELETE CASCADE
) ENGINE = InnoDB;

-- INSERCIÓN DE DATOS DE EJEMPLO --
-- Tabla PAISES
INSERT INTO paises( nombre ) VALUES
    ( "No especificado" ),
    ( "España" ),
    ( "Francia" ),
    ( "Portugal" ),
    ( "Estados Unidos" ),
    ( "Rusia" ),
    ( "Alemania" ),
    ( "Inglaterra" ),
    ( "Bélgica "),
    ( "Arabia Saudí" );

-- Tabla PAISES
INSERT INTO estilos( nombre , descripcion , fichero ) VALUES
    ( "Por defecto" , "Estilo predeterminado" , "css/css/estilo.css" ),
    ( "Noche" , "Modo oscuro" , "css/css/noche.css" ),
    ( "Bajo contraste" , "Bajo contraste" , "css/css/contraste-bajo.css" ),
    ( "Texto grande" , "Tamaño de fuente aumentado" , "css/css/grande.css" ),
    ( "Texto extra grande" , "Tamaño de fuente muy aumentado" , "css/css/x-grande.css" ),
    ( "Contraste/Grande" , "Combinación de bajo contraste y texto grande" , "css/css/contraste-grande.css" ),
    ( "Contraste/X-Grande" , "Combinación de bajo contraste y texto extra grande" , "css/css/contraste-x-grande.css" );

-- Tabla USUARIOS
INSERT INTO usuarios( nombre , clave , email , sexo , fNacimiento , ciudad , pais , foto , fRegistro , estilo ) VALUES
    ( "Pepe" , "$2y$10$Rt3Kg/A3cN4lvUkaI0Ce2uVnWMjgxO2ofk/9/pzMHXa.U3aSmXbu2" , "pepe@ejemplo.com" , 0 , STR_TO_DATE( "2000-06-15" , "%Y-%m-%d" ) ,
      "Alicante" , 2 , "img/pfp/yes.jpg" , STR_TO_DATE( "2020-03-20 15:50" , "%Y-%m-%d %H:%i" ) , 1 ),
    ( "Sara" , "$2y$10$Rt3Kg/A3cN4lvUkaI0Ce2uVnWMjgxO2ofk/9/pzMHXa.U3aSmXbu2" , "sara@ejemplo.com" , 1 , STR_TO_DATE( "2001-12-03" , "%Y-%m-%d" ) ,
      "París" , 3 , "img/pfp/default.jpg" , STR_TO_DATE( "2021-08-08 09:34" , "%Y-%m-%d %H:%i" ) , 2 ),
    ( "Juan" , "$2y$10$Rt3Kg/A3cN4lvUkaI0Ce2uVnWMjgxO2ofk/9/pzMHXa.U3aSmXbu2" , "juan@ejemplo.com" , 0 , STR_TO_DATE( "1998-08-30" , "%Y-%m-%d" ) ,
      "Lisboa" , 4 , "img/pfp/default.jpg" , STR_TO_DATE( "2018-03-20 00:03" , "%Y-%m-%d %H:%i" ) , 3 ),
    ( "Alba" , "$2y$10$Rt3Kg/A3cN4lvUkaI0Ce2uVnWMjgxO2ofk/9/pzMHXa.U3aSmXbu2" , "alba@ejemplo.com" , 1 , STR_TO_DATE( "1987-04-09" , "%Y-%m-%d" ) ,
      "Oslo" , 7 , "img/pfp/yesf.jpg" , STR_TO_DATE( "2019-07-14 14:14" , "%Y-%m-%d %H:%i" ) , 4 );

-- Tabla ALBUMES
INSERT INTO albumes ( titulo , descripcion , usuario ) VALUES
  ( "Paisajes rurales" , "Una colección de fotos del campo." , 1 ),
  ( "Vacaciones en Shangai" , "Un álbum de fotos familiar." , 1 ),
  ( "Carreteras inhóspitas" , "Colección de imágenes de sendas y carreteras en lugares apenas vistos, desde la densa jungla hasta los peligrosos Himalayas." , 1 ),
  ( "Mis platos preferidos" , "Entrecot, cocido, fabada asturiana..." , 1 ),
  ( "Mejores calabazas de Halloween" , "Una recopilación de las calabazas mejor talladas de este Halloween 2024" , 1 ),
  ( "Ciudades por el mundo" , "Capturas de ciudades y lugares emblemáticos" , 2 ),
  ( "Bosques nocturnos" , "Bosques por la noche" , 4 );

-- Tabla FOTOS
INSERT INTO fotos ( titulo , descripcion , fecha , pais , album , fichero , alternativo , fRegistro ) VALUES
  ( "Pradera" , "Una pradera" , STR_TO_DATE( "2020-10-07 15:00" , "%Y-%m-%d %H:%i" ) , 2 , 1 ,
    "img/pic/1.jpg", "Una imagen de una pradera" , STR_TO_DATE( "2020-10-08 09:34" , "%Y-%m-%d %H:%i" ) ),
  ( "Río" , "Un río" , STR_TO_DATE( "2020-10-07 16:00" , "%Y-%m-%d %H:%i" ) , 2 , 1 ,
    "img/pic/2.jpg", "Una imagen de un río" , STR_TO_DATE( "2020-10-08 09:36" , "%Y-%m-%d %H:%i" ) ),
  ( "Cascada" , "Una cascada" , STR_TO_DATE( "2020-10-07 17:00" , "%Y-%m-%d %H:%i" ) , 2 , 1 ,
    "img/pic/3.jpg", "Una imagen de una cascada" , STR_TO_DATE( "2020-10-08 09:38" , "%Y-%m-%d %H:%i" ) ),

  ( "Anochecer en París" , "Una ciudad" , STR_TO_DATE( "2020-11-07 12:00" , "%Y-%m-%d %H:%i" ) , 3 , 6 ,
    "img/pic/4.jpg", "Una imagen de una ciudad" , STR_TO_DATE( "2020-12-05 10:02" , "%Y-%m-%d %H:%i" ) ),
  ( "Perspectiva del Ojo de Londres" , "Una ciudad" , STR_TO_DATE( "2020-11-08 12:00" , "%Y-%m-%d %H:%i" ) , 8 , 6 ,
    "img/pic/5.jpg", "Una imagen de una ciudad" , STR_TO_DATE( "2020-12-05 10:04" , "%Y-%m-%d %H:%i" ) ),
  ( "Un viaje por los EE.UU." , "Una ciudad" , STR_TO_DATE( "2020-11-09 12:00" , "%Y-%m-%d %H:%i" ) , 5 , 6 ,
    "img/pic/6.jpg", "Una imagen de una ciudad" , STR_TO_DATE( "2020-12-05 10:06" , "%Y-%m-%d %H:%i" ) ),
  ( "La exposición del Atomium" , "Una ciudad" , STR_TO_DATE( "2020-11-10 12:00" , "%Y-%m-%d %H:%i" ) , 9 , 6 ,
    "img/pic/7.jpg", "Una imagen de una ciudad" , STR_TO_DATE( "2020-12-05 10:08" , "%Y-%m-%d %H:%i" ) ),
  ( "Abu Dhabi soleada" , "Una ciudad" , STR_TO_DATE( "2020-11-11 13:34" , "%Y-%m-%d %H:%i" ) , 10 , 6 ,
    "img/pic/8.jpg", "Una imagen de una ciudad" , STR_TO_DATE( "2021-03-10 02:10" , "%Y-%m-%d %H:%i" ) ),

  ( "Cielo tapado" , "Un bosque" , STR_TO_DATE( "2021-01-14 02:24" , "%Y-%m-%d %H:%i" ) , 2 , 7 ,
    "img/pic/9.jpg", "Una imagen de un bosque" , STR_TO_DATE( "2021-02-05 20:32" , "%Y-%m-%d %H:%i" ) ),
  ( "друг" , "Una criatura" , STR_TO_DATE( "2021-01-14 03:35" , "%Y-%m-%d %H:%i" ) , 6 , 7 ,
    "img/pic/10.jpg", "Una imagen de una criatura" , STR_TO_DATE( "2021-02-05 20:33" , "%Y-%m-%d %H:%i" ) );

-- Tabla SOLICITUDES
INSERT INTO solicitudes ( album , nombre , apellidos , titulo , descripcion , email ,
                          direccion , color , copias , resolucion , fecha , iColor , fRegistro , coste ) VALUES
  ( 1 , "José" , "Bosé" , "La España rural" , "Paisajes variados del interior de la Península Ibérica" ,
    "josebose@gameel.com" , "UN JSON",
    "#550000" , 2 , 300 , STR_TO_DATE( "2021-11-02 14:00" , "%Y-%m-%d %H:%i" ) ,
    TRUE , STR_TO_DATE( "2021-10-31 23:59" , "%Y-%m-%d %H:%i" ) , 5.23 );

-- Se crea un usuario
DROP USER IF EXISTS 'piadmin'@'localhost'; -- Lo borra si ya estaba
flush privileges;
CREATE USER 'piadmin'@'localhost' IDENTIFIED WITH mysql_native_password BY 'clave';
GRANT ALL ON pibd.* TO 'piadmin'@'localhost';