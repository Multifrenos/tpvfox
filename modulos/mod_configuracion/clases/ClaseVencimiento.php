<?php 
include_once $RutaServidor . $HostNombre . '/modulos/claseModelo.php';


class ClaseVencimiento extends modelo{
	public function cargarDatos(){
		$sql= 'SELECT * FROM tiposVencimiento ';
		return $this->consulta($sql);
	}
	
}



?>