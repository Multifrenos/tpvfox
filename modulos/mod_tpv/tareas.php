
<?php
/* Fichero de tareas a realizar.
 * 
 * 
 * Con el switch al final y variable $pulsado
 * 
 *  */
/* ===============  REALIZAMOS CONEXIONES  ===============*/


$pulsado = $_POST['pulsado'];

include_once ("./../../configuracion.php");

// Crealizamos conexion a la BD Datos
include_once ("./../mod_conexion/conexionBaseDatos.php");

// Incluimos funciones
include_once ("./funciones.php");

 
 switch ($pulsado) {
    case 'BuscarProducto':
		//~ $nombreTabla = $_POST['Fichero'];
		//~ $fichero = $RutaServidor.$CopiaDBF.'/'.$nombreTabla;
		$respuesta = BurcarProducto($buscar,$BDImportDbf);
		//~ echo json_encode($respuesta) ;
		break;
	
}
 
/* ===============  CERRAMOS CONEXIONES  ===============*/

mysqli_close($BDImportDbf);

 
 
?>
