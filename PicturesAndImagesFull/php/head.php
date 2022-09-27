<?php 
    $valor=-1;
    if( !empty( $_SESSION ) || !empty( $_COOKIE["credenciales"] ) ){
        require_once("php/funciones.php");
        $valor = cambiarEstilo();
    }

?>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="generator" content="Visual Studio Code">
<meta name="author" content="Daniel Giménez y Ian Bucu">
<meta name="keywords" content="Fotos, imágenes, compartir">
<meta name="description" content="<?= $descripcion ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $titulo ?> - Pictures and Images</title>
<!--Icono-->
<link rel="icon" href="img/logo.png">
<!--CSS-->
<?php
        switch($valor){
            case 1: echo '<link rel="stylesheet" href = "css/css/estilo.css" title="Por defecto">'; break;
            case 2: echo '<link rel="stylesheet" href = "css/css/noche.css" title="Modo noche">'; break;
            case 3: echo '<link rel="stylesheet" href = "css/css/contraste-bajo.css" title="Bajo contraste">'; break;
            case 4: echo '<link rel="stylesheet" href = "css/css/x-grande.css"  title="Texto Extra-grande">'; break;
            default: echo '<link rel="stylesheet" href = "css/css/estilo.css" title="Por defecto">';
        }
?>

<link rel="stylesheet" href="css/css/impresa.css" media="print">
<link rel="alternate stylesheet" href="css/css/noche.css" title="Modo noche">
<link rel="alternate stylesheet" href="css/css/contraste-bajo.css" title="Bajo contraste">
<link rel="alternate stylesheet" href="css/css/contraste-x-grande.css" title="Contraste/X-grande">
<link rel="alternate stylesheet" href="css/css/contraste-grande.css" title="Contraste/Grande">
<link rel="alternate stylesheet" href="css/css/grande.css" title="Texto grande"> 
<link rel="alternate stylesheet" href="css/css/x-grande.css" title="Texto Extra-grande">
<link rel="stylesheet" href="css/fontello.css">
<!--Fuentes-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
<!--JavaScript-->
<script src="js/codigo.js"></script>


