<?php 
class AlbaranesVentas{
	private $idalbcli;
	private $numalbcli;
	private $numtemp_albcli;
	private $fecha;
	private $idTienda;
	private $idUsuario;
	private $idCliente;
	private $estado;
	private $formaPago;
	private $entregado;
	private $total;
	private $idalbcclitemporal;
	private $fechaInicioTemporal;
	private $fechaFinalTemporal;
	private $totalTemporal;
	private $total_ivasTemporal;
	private $productos;
	private $idabcclilinea;
	private $idArticulo;
	private $cref;
	private $ccodbar;
	private $cdetalle;
	private $ncant;
	private $nunidades;
	private $precioCiva;
	private $iva;
	private $nfila;
	private $estadoLinea;
	private $idalbIva;
	private $ivaalbIva;
	private $importeIva;
	private $totalbase;
	
	public function __construct($conexion){
		$this->db = $conexion;
		// Obtenemos el numero registros.
		$sql = 'SELECT count(*) as num_reg FROM pedclit';
		$respuesta = $this->consulta($sql);
		$this->num_rows = $respuesta->fetch_object()->num_reg;
		// Ahora deberiamos controlar que hay resultado , si no hay debemos generar un error.
	}
	public function consulta($sql){
		$db = $this->db;
		$smt = $db->query($sql);
		return $smt;
	}
	public function insertarDatosAlbaranTemporal($idUsuario, $idTienda, $estadoAlbaran, $fecha , $pedidos, $productos, $idCliente){
		$db = $this->db;
		$UnicoCampoPedidos=json_encode($pedidos);
		$UnicoCampoProductos=json_encode($productos);
		$smt = $db->query ('INSERT INTO albcliltemporales ( idUsuario , idTienda , estadoAlbCli , fechaInicio, idClientes, Pedidos, Productos ) VALUES ('.$idUsuario.' , '.$idTienda.' , "'.$estadoAlbaran.'" , "'.$fecha.'", '.$idCliente.' , '."'".$UnicoCampoPedidos."'".', '."'".$UnicoCampoProductos."'".')');
		$sql='INSERT INTO albcliltemporales ( idUsuario , idTienda , estadoAlbCli , fechaInicio, idClientes, Productos, Pedidos) VALUES ('.$idUsuario.' , '.$idTienda.' , "'.$estadoAlbaran.'" , "'.$fecha.'", '.$idCliente.' , '."'".$UnicoCampoProductos."'".', '."'".$UnicoCampoPedidos."'".')';

		$id=$db->insert_id;
		$respuesta['id']=$id;
		$respuesta['sql']=$sql;
		$respuesta['productos']=$productos;
		
		return $respuesta;
	}
	public function modificarDatosAlbaranTemporal($idUsuario, $idTienda, $estadoAlbaran, $fecha , $pedidos, $idTemporal, $productos){
		$db = $this->db;
		$UnicoCampoPedidos=json_encode($pedidos);
		$UnicoCampoProductos=json_encode($productos);
		$smt=$db->query('UPDATE albcliltemporales SET idUsuario='.$idUsuario.' , idTienda='.$idTienda.' , estadoAlbCli="'.$estadoAlbaran.'" , fechaInicio='.$fecha.' , Pedidos='."'".$UnicoCampoPedidos."'". ' ,Productos='."'".$UnicoCampoProductos."'".'  WHERE id='.$idTemporal);
		$sql='UPDATE albcliltemporales SET idUsuario='.$idUsuario.' , idTienda='.$idTienda.' , estadoAlbCli='.$estadoAlbaran.' , fechaInicio='.$fecha.' , Pedidos='."'".$UnicoCampoPedidos."'". ' ,Productos='."'".$UnicoCampoProductos."'".'  WHERE id='.$idTemporal;
		$respuesta['sql']=$sql;
		$respuesta['idTemporal']=$idTemporal;
		$respuesta['productos']=$UnicoCampoProductos;
	
		return $respuesta;
	}
	public function addNumRealTemporal($idTemporal,  $numAlbaran){
		$db = $this->db;
		$UnicoCampoPedidos=json_encode($pedidos);
		$smt=$db->query('UPDATE albcliltemporales SET numalbcli ='.$numAlbaran.' WHERE id='.$idTemporal);
		$sql='UPDATE albcliltemporales SET numalbcli ='.$numAlbaran.' WHERE id='.$idTemporal;
		return $sql;
	}
	public function buscarDatosAlabaranTemporal($idAlbaranTemporal) {
		$db=$this->db;
		$smt=$db->query('SELECT * FROM albcliltemporales WHERE id='.$idAlbaranTemporal);
		if ($result = $smt->fetch_assoc () ){
			$albaran=$result;
		}
		return $albaran;
	}
	public function buscarTemporalNumReal($idAlbaran){
		$db=$this->db;
		$smt=$db->query('SELECT * FROM albcliltemporales WHERE numalbcli ='.$idAlbaran);
		if ($result = $smt->fetch_assoc () ){
			$albaran=$result;
		}
		return $albaran;
	}
	
	public function modTotales($res, $total, $totalivas){
		$db=$this->db;
		$smt=$db->query('UPDATE albcliltemporales set total='.$total .' , total_ivas='.$totalivas .' where id='.$res);
		$sql='UPDATE albcliltemporales set total='.$total .' , total_ivas='.$totalivas .' where id='.$res;
		$resultado['sql']=$sql;
		return $resultado;
	}
	public function eliminarAlbaranTablas($idAlbaran){
		$db=$this->db;
		$smt=$db->query('DELETE FROM albclit where id='.$idAlbaran );
		$smt=$db->query('DELETE FROM albclilinea where idalbcli ='.$idAlbaran );
		$smt=$db->query('DELETE FROM albcliIva where idalbcli ='.$idAlbaran );
		$smt=$db->query('DELETE FROM pedAlbCli where idAlbaran ='.$idAlbaran );
		
	}
		public function AddAlbaranGuardado($datos, $idAlbaran){
		$db = $this->db;
		if ($idAlbaran>0){
		$smt = $db->query ('INSERT INTO albclit (Numalbcli, Fecha, idTienda , idUsuario , idCliente , estado , total) VALUES ('.$idAlbaran.', "'.$datos['Fecha'].'", '.$datos['idTienda'].', '.$datos['idUsuario'].', '.$datos['idCliente'].', "'.$datos['estado'].'", '.$datos['total'].')');
		$id=$db->insert_id;
		$resultado='INSERT INTO albclit (Numalbcli, Fecha, idTienda , idUsuario , idCliente , estado , total) VALUES ('.$idAlbaran.', "'.$datos['Fecha'].'", '.$datos['idTienda'].', '.$datos['idUsuario'].', '.$datos['idCliente'].', "'.$datos['estado'].'", '.$datos['total'].')';
		}else{
		$smt = $db->query ('INSERT INTO albclit (Numtemp_albcli, Fecha, idTienda , idUsuario , idCliente , estado , total) VALUES ('.$datos['Numtemp_albcli'].' , "'.$datos['Fecha'].'", '.$datos['idTienda']. ', '.$datos['idUsuario'].', '.$datos['idCliente'].' , "'.$datos['estado'].'", '.$datos['total'].')');
		$id=$db->insert_id;
		$smt = $db->query('UPDATE albclit SET Numalbcli  = '.$id.' WHERE id ='.$id);
		$resultado='INSERT INTO albclit (Numtemp_albcli, Fecha, idTienda , idUsuario , idCliente , estado , total) VALUES ('.$datos['Numtemp_albcli'].' , "'.$datos['Fecha'].'", '.$datos['idTienda']. ', '.$datos['idUsuario'].', '.$datos['idCliente'].' , "'.$datos['estado'].'", '.$datos['total'].')';
		}
		$productos = json_decode($datos['productos'], true); 
		foreach ( $productos as $prod){
			if ($prod['ccodbar']){
				$codBarras=$prod['ccodbar'];
			}else{
				$codBarras=0;
			}
			if ($prod['Numpedcli']){
				$numPed=$prod['Numpedcli'];
			}else{
				$numPed=0;
			}
			if ($idAlbaran>0){
			$smt=$db->query('INSERT INTO albclilinea (idalbcli  , Numalbcli , idArticulo , cref, ccodbar, cdetalle, ncant, nunidades, precioCiva, iva, nfila, estadoLinea, NumpedCli ) VALUES ('.$id.', '.$idAlbaran.' , '.$prod['idArticulo'].', '.$prod['cref'].', '.$codBarras.', "'.$prod['cdetalle'].'", '.$prod['ncant'].' , '.$prod['ncant'].', '.$prod['precioCiva'].' , '.$prod['iva'].', '.$prod['nfila'].', "'. $prod['estadoLinea'].'" , '.$numPed.')' );

			}else{
			$smt=$db->query('INSERT INTO albclilinea (idalbcli  , Numalbcli , idArticulo , cref, ccodbar, cdetalle, ncant, nunidades, precioCiva, iva, nfila, estadoLinea, NumpedCli ) VALUES ('.$id.', '.$id.' , '.$prod['idArticulo'].', '.$prod['cref'].', '.$codBarras.', "'.$prod['cdetalle'].'", '.$prod['ncant'].' , '.$prod['ncant'].', '.$prod['precioCiva'].' , '.$prod['iva'].', '.$prod['nfila'].', "'. $prod['estadoLinea'].'" , '.$numPed.')' );
			}
		}
		foreach ($datos['DatosTotales']['desglose'] as  $iva => $basesYivas){
			if($idAlbaran>0){
			$smt=$db->query('INSERT INTO albcliIva (idalbcli  ,  Numalbcli  , iva , importeIva, totalbase) VALUES ('.$id.', '.$idAlbaran.' , '.$iva.', '.$basesYivas['iva'].' , '.$basesYivas['base'].')');

			}else{
			$smt=$db->query('INSERT INTO albcliIva (idalbcli  ,  Numalbcli  , iva , importeIva, totalbase) VALUES ('.$id.', '.$id.' , '.$iva.', '.$basesYivas['iva'].' , '.$basesYivas['base'].')');

			}
		}
		$pedidos = json_decode($datos['pedidos'], true); 
		foreach ($pedidos as $pedido){
			if($idAlbaran>0){
				$smt=$db->query('INSERT INTO pedAlbCli (idAlbaran  ,  numAlbaran   , idPedido , numPedido) VALUES ('.$id.', '.$idAlbaran.' ,  '.$pedido['idPedCli'].' , '.$pedido['Numpedcli'].')');

				}else{
				$smt=$db->query('INSERT INTO pedAlbCli (idAlbaran  ,  numAlbaran   , idPedido , numPedido) VALUES ('.$id.', '.$id.' ,  '.$pedido['idPedCli'].' , '.$pedido['Numpedcli'].')');
				$resultado='INSERT INTO pedAlbCli (idAlbaran  ,  numAlbaran   , idPedido , numPedido) VALUES ('.$id.', '.$id.' ,  '.$pedido['idPedCli'].' , '.$pedido['Numpedcli'].')';
				}
		}
		return $resultado;
	}
	
	
	public function EliminarRegistroTemporal($idTemporal, $idAlbaran){
		$db=$this->db;
		if ($idAlbaran>0){
			$smt=$db->query('DELETE FROM albcliltemporales WHERE numalbcli ='.$idAlbaran);
			$sql='DELETE FROM albcliltemporales WHERE numalbcli ='.$idAlbaran;
		}else{
			$smt=$db->query('DELETE FROM albcliltemporales WHERE id='.$idTemporal);
		}
		return $sql;
	}
	
	public function TodosAlbaranes(){
		$db=$this->db;
		$smt=$db->query('SELECT a.id , a.Numalbcli , a.Fecha , b.Nombre, a.total, a.estado FROM `albclit` as a LEFT JOIN clientes as b on a.idCliente=b.idClientes ');
		$albaranesPrincipal=array();
		while ( $result = $smt->fetch_assoc () ) {
			array_push($albaranesPrincipal,$result);
		}
		return $albaranesPrincipal;
	}
		public function sumarIva($numAlbaran){
		$db=$this->db;
		$smt=$db->query('select sum(importeIva ) as importeIva , sum(totalbase) as  totalbase from albcliIva where  Numalbcli  ='.$numAlbaran);
		if ($result = $smt->fetch_assoc () ){
			$albaran=$result;
		}
		return $albaran;
	}
		public function TodosTemporal(){
			$db = $this->db;
			$smt = $db->query ('SELECT * from albcliltemporales');
			$albaranPrincipal=array();
		while ( $result = $smt->fetch_assoc () ) {
			array_push($albaranPrincipal,$result);
		}
		return $albaranPrincipal;
		
	}
	public function datosAlbaran($idAlbaran){
		$db=$this->db;
		$smt=$db->query('SELECT * FROM albclit WHERE id= '.$idAlbaran );
		if ($result = $smt->fetch_assoc () ){
			$albaran=$result;
		}
		return $albaran;
	}
	public function ProductosAlbaran($idAlbaran){
		$db=$this->db;
		$smt=$db->query('SELECT * FROM albclilinea WHERE idalbcli= '.$idAlbaran );
		$albaranPrincipal=array();
		while ( $result = $smt->fetch_assoc () ) {
			array_push($albaranPrincipal,$result);
		}
		return $albaranPrincipal;
	}
	public function IvasAlbaran($idAlbaran){
		$db=$this->db;
		$smt=$db->query('SELECT * FROM albcliIva WHERE idalbcli= '.$idAlbaran );
		$albaranPrincipal=array();
		while ( $result = $smt->fetch_assoc () ) {
			array_push($albaranPrincipal,$result);
		}
		return $albaranPrincipal;
	}
	public function PedidosAlbaranes($idAlbaran){
		$db=$this->db;
		$smt=$db->query('SELECT * FROM pedAlbCli WHERE idAlbaran= '.$idAlbaran );
		$albaranPrincipal=array();
		while ( $result = $smt->fetch_assoc () ) {
			array_push($albaranPrincipal,$result);
		}
		return $albaranPrincipal;
	}

	
	
}

?>