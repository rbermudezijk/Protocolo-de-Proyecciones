<?php
/**
 * 
 * @author Ricardo Bermudez Bermudez, <rbermudez@mig.com.mx>
 * @since v.1.0
 * 
 * Ejemplo 3. Del siguiente  menú en  formato  XML,  obtenga  un  listado  de  las
 * comidas disponibles  (aquellas donde  el  elemento  <Disponibilidad> es igual a
 * “DISPONIBLE”), solo interesa recuperar el nombre de  la  comida  (<Nombre>), su
 * precio (atributo @neto en <Precio>) y moneda de cotización (atributo @moneda en
 * <Precio>).
 * 
 */
    include_once '../Ejemplo/Proyeccion.php';
    /**Se carga el XML en una variable tipo string.*/
    $xmlMenu = file_get_contents('Menu.xml');
    /**Inicializa el motor el intérprete de las proyecciones.*/
    $pxmlEngine = new IAPI_XMLProjection();
    
    /**Se define el descriptor de la proyección.*/
    
    $dpComidas = [
        '>_MAP' => [
            'nombre'  => 'Nombre',
            'precio'  => 'Precio/@neto',
            'moneda'  => 'Precio/@moneda',
            'extras'  => 'Extra',
        ],
        '>_FROOT' => '//Menu/Comida[Disponibilidad="DISPONIBLE"]'
    ];
    
    echo print_r($pxmlEngine->runProjection($dpComidas, $xmlMenu), true);
