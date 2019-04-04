<?php
/**
 * 
 * @author Ricardo Bermudez Bermudez, <rbermudez@mig.com.mx>
 * @since v.1.0
 * 
 * Ejemplo 1. A través de un sistema web, un restaurante recibe diariamente  una  lista  de  productos de su proveedor de
 * insumos, de estos solo requiere conocer los apartados de frutas y verduras,  pues  es  un  restaurante vegetariano. En
 * la lista final de productos, al gerente del restaurante no le interesa clasificar los insumos como frutas o  verduras,
 * sino en “Aprobados para la compra” o “Por revisar”. Los aprobados para la compra son aquellos cuyo  precio  por unidad
 * es menor a 20 pesos y los clasificados como “por revisar” aquellos cuyo precio es mayor o igual a 20  pesos.  De  cada
 * producto solo le interesa conocer los siguientes datos: nombre, precio, no. de unidades disponibles y unidad de medida.
 * 
 * Propóngase una estructura de salida que represente los datos necesitados y muéstrese cuál es el resultado de utilizarla
 * para representar la información requerida.
 */

include_once('Proyeccion.php');

$projectionEngine = new IAPI_XMLProjection();

$xmlProductos = file_get_contents('ejemplo.xml');

$mapProducto = ['nombre_prod'   => 'Nombre',
            'unidad_medida' => '@Unidad',
            'costo_unidad'  => 'CostoUnidad',
            'disponibles'   => 'Disponibles'];

$tpProducto = '*[self::Frutas|self::Verduras]/Producto';

$dpListaProd = [
    '>_MAP' => [
        'Caros'  => [
            '>_MAP'   => $mapProducto,
            '>_FROOT' => "{$tpProducto}[Disponibles>0 and CostoUnidad>=20]"
        ],
        'Baratos' => [
            '>_MAP'   => $mapProducto,
            '>_FROOT' => "{$tpProducto}[Disponibles>0 and CostoUnidad<20]"
        ],
    ],
    '>_FROOT' => '//Alimentos'
];

$afListaProd = $projectionEngine->runProjection($dpListaProd,$xmlProductos);

echo print_r($afListaProd,true);