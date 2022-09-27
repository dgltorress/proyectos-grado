<?php

// Destruye la cookie
if( isset( $_COOKIE["credenciales"]) ) setcookie( "credenciales" , "" , time() - 3600 , "/" );

$_SESSION = array();

// Borra la cookie que almacena la sesión
if( isset( $_COOKIE[ session_name() ] ) ) setcookie( session_name() , '' , time() - 3600 , '/' );

// Finalmente, destruye la sesión
session_destroy();

// Redirige a la página principal
header( "Location: ../index.php" );

?>