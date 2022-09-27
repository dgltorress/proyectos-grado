<?php
// Conectar con BD
require_once( "conexionBD.php");
require_once("controlArchivos.php");
echo $_POST["usr"];
$datosUsuarios = @mysqli_query( $conexion ,'SELECT u.nombre,u.clave,u.estilo,u.id,u.foto from usuarios as u where UPPER(u.nombre)= UPPER("'.$_POST["usr"].'") ') 
OR die( "Ha habido un problema al comprobar usuarios existentes" );
$display = !empty($datosUsuarios);
if($display) {
    $Usuario = @mysqli_fetch_assoc( $datosUsuarios );
    echo $Usuario['id'];
    if(!empty($Usuario))
    {
        $checked = password_verify($_POST['password'], $Usuario['clave']);
        if( $checked )
        {
            $sentencia = 'DELETE FROM usuarios where nombre = ?';
            $borrado = @mysqli_prepare( $conexion , $sentencia ) OR die( "No se ha podido preparar la inserción." );      
            
            @mysqli_stmt_bind_param( $borrado , "s" , $_POST['usr'] );
            
            if( @mysqli_execute( $borrado ) ) 
            {
                $exito = borrar( $Usuario['foto'] );

                if( $exito ) header( "Location: ../index.php?msg=14" );
                else header( "Location: ../index.php?msg=15" );
            }
            else header( "Location: ../index.php?msg=15" );
        }
        else header( "Location: ../index.php?msg=15" );
    }
    else header( "Location: ../index.php?msg=15" );
}
?>