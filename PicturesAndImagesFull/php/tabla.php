<?php

        //CREA UN ELEMENTO TBODY QUE SERA RELLENADO MEDIANTE INSERTCELL Y INSERTROW
        function crearTabla() {
            $tabla = "<tbody>";
            echo $tabla;
           $celda="";
           $pags = -1;
           $fotos = -1;
           for ($f = 0; $f < 15; $f++) {
               echo $fila= "<tr>";
               for ($c = 0; $c < 6; $c++) {
                   if ($c == 0) {
                       $calculo = $f + 1;
                       echo $celda = "<td> $calculo </td>";
                       $pags = $f + 1;
                   }
                   else if ($c == 1) {
                        $calculo = 3 * ($f + 1);
                        echo $celda = "<td> $calculo </td>";
                        $fotos = 3 * ($f + 1);
                   }
                   else {
                        $calculo = calcularCelda($c, $pags, $fotos);
                        echo $celda = "<td> $calculo </td>";
                   }
               }
           }
       }
       /* --------------------------------------------------------------- */

       // Calcula los valores de las celdas en funcion del número de celda
       function calcularCelda( $celda , $pags , $fotos ) {
       $valor = 0;

       for ( $i = 0 ; $i < $pags ; $i++ ) {
           if      ($i < 5)           $valor += 0.10;
           else if ($i > 5 && $i < 11) $valor += 0.08;
           else                      $valor += 0.07;
       }

       switch( $celda ) {
           case 2:                                           break;
           case 3: $valor += (0.02 * $fotos);                  break;
           case 4: $valor += (0.05 * $fotos);                  break;
           case 5: $valor += (0.05 * $fotos) + (0.02 * $fotos); break;
           default:
               console.error('HA HABIDO UN ERROR AL CALCULAR EL PRECIO');
               return false;
       }

       $valor = round($valor,2);
       return $valor;
       }
       /* --------------------------------------------------------------- */
       
?>