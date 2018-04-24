<?php 
$pulsado = $_POST['pulsado'];

include_once ("./../../configuracion.php");
include_once ("./../mod_conexion/conexionBaseDatos.php");
include_once ("./popup_incidencias.php");

switch ($pulsado) {
	case 'abririncidencia':
		$dedonde=$_POST['dedonde'];
		$usuario=$_POST['usuario'];
		
		$tipo="mod_compras";
		$fecha=date('Y-m-d');
		$datos=array(
		'dedonde'=>$dedonde
		);
		$datos=json_encode($datos);
		$estado="No resuelto";
		$html=modalIncidencia($usuario, $datos, $fecha, $tipo, $estado);
		$respuesta['html']=$html;
		$respuesta['datos']=$datos;
		echo json_encode($respuesta);
		break;
		
	case 'nuevaIncidencia':
		$usuario= $_POST['usuario'];
		$fecha= $_POST['fecha'];
		$datos= $_POST['datos'];
		$dedonde= $_POST['dedonde'];
		$estado= $_POST['estado'];
		$mensaje= $_POST['mensaje'];
		if($mensaje){
			$nuevo=addIncidencia($usuario, $fecha, $dedonde, $datos, $estado, $mensaje, $BDTpv);
			$respuesta=$nuevo['sql'];
		}
	echo json_encode($respuesta);
	
	break;
	
}
?>