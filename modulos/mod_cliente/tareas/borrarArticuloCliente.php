<?php

/*
 * @Copyright 2018, Alagoro Software. 
 * @licencia   GNU General Public License version 2 or later; see LICENSE.txt
 * @Autor Alberto Lago Rodríguez. Alagoro. alberto arroba alagoro punto com
 * @Descripción	
 */


//~ require_once $URLCom.'/modulos/mod_clientes/clases/claseTarifaCliente.php';

$idarticulo = $_POST['idarticulo'];
$idcliente = $_POST['idcliente'];

// validar datos
//if( datos validados y ok)
$resultado = [];
$tarifaCliente = new TarifaCliente($BDTpv);
$existetarifa = $tarifaCliente->existeArticulo($idcliente, $idarticulo);

if ($existetarifa) {
    $resultado = $tarifaCliente->update([
        'fechaActualizacion' => date(FORMATO_FECHA_MYSQL),
        'estado'=> K_TARIFACLIENTE_ESTADO_BORRADO
    ],['idArticulo= ' . $idarticulo, 'idClientes= ' . $idcliente]);
}

//~ $resultado['existe'] = $existetarifa 

