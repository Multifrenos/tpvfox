<?php
// Llegamos aquí de tareas , donde necesitamos creamos incidencia.
include_once ($RutaServidor . $HostNombre."/modulos/mod_incidencias/popup_incidencias.php");
include_once ($RutaServidor . $HostNombre."/modulos/mod_cliente/funciones.php");

$dedonde=$_POST['dedonde'];
$usuario=$_POST['usuario'];
$idReal=0;
if(isset($_POST['idReal'])){
	$idReal=$_POST['idReal'];
}

$configuracion=$_POST['configuracion'];
$numInicidencia=0;
$tipo="mod_cliente";
$fecha=date('Y-m-d');
$datos=array(
'dedonde'=>$dedonde,
'idReal'=>$idReal
);
$datos=json_encode($datos);
$estado="No resuelto";
$html=modalIncidencia($usuario, $datos, $fecha, $tipo, $estado, $numInicidencia, $configuracion, $BDTpv);
$respuesta['html']=$html;
$respuesta['datos']=$datos;

