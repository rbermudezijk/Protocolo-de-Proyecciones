<?php
/**
 * Este es un ejemplo de aplicación de Proyecciones XML.
 * 
 * @author Ricardo Bermudez Bermudez, <rbermudez@mig.com.mx>
 * @since v.1.0
 *
 * Ejemplo 7. En un XML se almacenan los datos geográficos y socioeconómicos
 * de los municipios de México, en los siguientes recuadros se muestra el XML que
 * contiene los datos requeridos. Los municipios son agrupados en regiones
 * económicas de los Estados y los Estados en regiones federales. Se necesita
 * recuperar los datos de los conteos poblacionales hechos en el Estado de
 * Aguascalientes entre 1990 y 2010 con una estructura como la mostrada en la
 * figura PX3.9. Proponga una solución a este problema.
 *
 * Figura PX3.9
 * $censosAguascalientes = array(
 *     Indice1 => array(
 *         'nombreMunicipio' => ..., //Nombre del municipio
 *         'noConteosPoblacionales' => ...,
 *         'conteosPoblacionales' => array(
 *             Indice1 => array(
 *                 'añoDelCenso'      => ..., //Año del censo
 *                 'poblacionHombres' => ..., //Población de Hombres
 *                 'poblacionMujeres' => ..., //Población de Mujeres
 *                 'poblacionTotal'   => ..., //Población Total
 *             )
 *             .
 *             .
 *             .
 * 
 *         )
 *     ),
 *     .
 *     .
 *     .
 * );
 */
    /**Carga a la ejecución el script que contiene la clase Projection.*/ 
    include_once '../Ejemplo/Proyeccion.php';
    
    /**Se carga el XML en una variable tipo string.*/
    $xmlCensosInegi = file_get_contents('ConteosInegi.xml');
    
    /**Inicializa el motor o intérprete de las proyecciones.*/
    $pxmlEngine = new IAPI_XMLProjection();
    
    /**Definición del descriptor de la proyección*/
    $dpCensosMxAgu = [
        '>_MAP' => [
            'nombreMunicipio'         => 'Nombre',
            'noConteosPoblacionales' => 'count(DatosSocioEconomicos/ConteoPoblacion[@año>1990 and @año<2010])',
            'censosPoblacionales'      => [
                '>_MAP' => [
                    'añoDelCenso'        => '@año',
                    'poblacionHombres' => 'conteo[@variable="Hombres"]/@total',
                    'poblacionMujeres'  => 'conteo[@variable="Mujeres"]/@total',
                    'poblacionTotal'      => 'sum(conteo/@total)',
                ],
                '>_FROOT' => 'DatosSocioEconomicos/ConteoPoblacion'
                                 . '[@año>1990 and @año<2010]'
            ]
        ],
        '>_FROOT' => '//RegionesFederales/Region'
                         . '/EntidadFederativa[@nombre="Aguascalientes"]'
                         . '/RegionSocioeconomica/Municipios'
                         . '/Municipio'
    ];
    
    /**Ejecución de la proyección e impresión en pantalla.*/
    echo print_r($pxmlEngine->runProjection($dpCensosMxAgu, $xmlCensosInegi), true);
