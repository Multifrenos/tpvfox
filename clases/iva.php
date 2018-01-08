<?php 
require_once "BD.php";
class iva{
	private $id;
	private $descripcion;
	private $iva;
	private $recargo;
	
	public function __construct($datos){
		$this->id=$datos['idIva'];
		$this->descripcion=$datos['descripcionIva'];
		$this->iva=$datos['iva'];
		$this->recargo=$datos['recargo'];
	}
	
	public function getId(){
		return $this->id;
	}
	public function getDescripcion(){
		return $this->descripcion;
	}
	public function getIva(){
		return $this->iva;
	}
	public function getRecargo(){
		return $this->recargo;
	}
	public function setId($id){
		$this->id=$id;
	}
	public function setDescripcion($descripcion){
		$this->descripcion=$descripcion;
	}
	public function setIva($iva){
		$this->iva=$iva;
	}
	public function setRecargo($recargo){
		$this->recargo=$recargo;
	}
	
	static public function todoIvas(){
		try{
		$db = BD::conectar ();
		$smt = $db->query ( 'SELECT * FROM iva' );
		$ivasPrincipal=array();
		while ( $result = $smt->fetch () ) {
			$iva=new iva($result);
			array_push($ivasPrincipal, $iva);
		}
		return $ivasPrincipal;
	} catch ( PDOException $e ) {
			echo 'Error: ' . $e->getMessage ();
		}
	}
}

?>