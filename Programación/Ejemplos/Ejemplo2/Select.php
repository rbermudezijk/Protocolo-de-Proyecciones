<?php
/**
 * Este es un ejemplo de aplicación de Proyecciones XML.
 * 
 * @author Ricardo Bermudez Bermudez, <rbermudez@mig.com.mx>
 * @since v.1.0
 *
 * Un ciclón tropical se aproxima al malecón de Veracruz, en las coordenadas 19.2019°N Latitud y 96.136856°O Longitud.
 * El Servicio Meteorológico Nacional a medido su isobara mas extensa en 3  grados latitud.  Se  necesita  lanzar  una
 * alerta preventiva de huracan a todos los municipios en la zona que se verá afectada por este ciclon,  para  ello el
 * SMN cuenta con un registro de  todos  los municipios del país con datos de contacto de las oficinas del Servicio de
 * Protección Civil, Cruz Roja y Albergues instalados en dichos municipios. El registro  completo esta  almacenado  en
 * formato XML en el documento "Municipios.xml".
 *
 * Diseñe una método  que obtenga un reporte de las oficinas e instalaciones a las cuales haya que enviar la alerta de
 * huracán en función de la zona de influencia. El reporte debe tener la siguiente estructura:
 *
 * $alertar = array(
 *     0 => array(
 *         0 => Entidad Federativa,
 *         1 => Nombre del Municipio,
 *         2 => Tipo de institución,
 *         3 => Teléfono de contacto,
 *         4 => Horario de atención,
 *     )
 *     .
 *     .
 *     .
 *     N => array(
 *         ...
 *     )
 * )
 */
    include_once('../Ejemplo/Proyeccion.php');

    function prevencionCivilHuracan($la,$lo,$r)
    {
        $xmlRegiones = file_get_contents('Ubicaciones.xml');
        $pEngine = new IAPI_XMLProjection();
        
        $cx = "((@LA)-($la))*((@LA)-($la))";
        $cy = "((@LO)-($lo))*((@LO)-($lo))";
        $cr = $r*$r;
        
        $dpListaProductos = [
            '>_MAP' => [
                'test'=>'../../../../@nombre',
                '../../Nombre',
                'Tipo',
                'Nombre',
                'TelefonoContacto',
                'Horario',
            ],
            '>_FROOT' => '//Regiones/Region/EntidadFederativa'
                       . '/RegionSocioeconomica/Municipio'
                       . "[Coordenadas[($cx)+($cy)<=$cr]]"
                       . '/Instituciones/Institucion'
        ];
        
        return $pEngine->runProjection($dpListaProductos,$xmlRegiones);
    }

$institucionesPreventivas = prevencionCivilHuracan(19.2019431, -96.136856, 3);

echo print_r($institucionesPreventivas,true);