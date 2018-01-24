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

include_once("clases/pedidosVentas.php");
$CcliPed=new PedidosVentas($BDTpv);

include_once("../../clases/producto.php");
$Cprod=new Producto($BDTpv);

include_once ("clases/albaranesVentas.php");
$CalbAl=new AlbaranesVentas($BDTpv);

include_once("../../clases/cliente.php");
$Ccliente=new Cliente($BDTpv);

switch ($pulsado) {
    
    case 'buscarProductos':
		$busqueda = $_POST['valorCampo'];
		$campoAbuscar = $_POST['campo'];
		$id_input = $_POST['cajaInput'];
		$idcaja=$_POST['idcaja'];
		$deDonde = $_POST['dedonde']; // Obtenemos de donde viene
		$idPedidoTemporal=$_POST['idTemporal'];
		$productos=$_POST['productos'];
		$res = BuscarProductos($id_input,$campoAbuscar, $idcaja, $busqueda,$BDTpv);
		if ($res['Nitems']===1){
			$respuesta=$res;
			$respuesta['Nitems']=$res['Nitems'];	
		}else{
			// Cambio estado para devolver que es listado.
			$respuesta['listado']= htmlProductos($res['datos'],$id_input,$campoAbuscar,$busqueda);
			$respuesta['Estado'] = 'Listado';
			$respuesta['sql']=$res['sql'];
			$respuesta['datos']=$res['datos'];
		
		}
		
		
		echo json_encode($respuesta);  
		break;
		
		case 'añadirProductos';
		$datos=$_POST['productos'];
		$idTemporal=$_POST['idTemporal'];
		//$respuesta['datos']=$datos;		
		
		$productos_para_recalculo = json_decode( json_encode( $_POST['productos'] ));
		$CalculoTotales = recalculoTotales($productos_para_recalculo);
		$total=round($CalculoTotales['total'],2);
		
		$respuesta['total']=$total;
		$modProducto=$CcliPed->AddProducto($idTemporal,$datos , $total);
		
		$nuevoArray = array(
						'desglose'=> $CalculoTotales['desglose'],
						'total' => $CalculoTotales['total']
							);
		$respuesta['totales']=$nuevoArray;
		
		echo json_encode($respuesta);

		return $respuesta;
		break;
		
		case 'HtmlLineaTicket';
		$respuesta = array();
		$product 					=$_POST['producto'];
		$num_item					=$_POST['num_item'];
		$CONF_campoPeso		=$_POST['CONF_campoPeso'];
		$res 	= htmlLineaPedido($product,$num_item,$CONF_campoPeso);
		$respuesta['html'] =$res;
		$respuesta['producto']=$product;
		echo json_encode($respuesta);
		break;
	    case 'buscarClientes':
		// Abrimos modal de clientes
		$busqueda = $_POST['busqueda'];
		$dedonde = $_POST['dedonde'];
		$idcaja=$_POST['idcaja'];
		$tabla='clientes';
		$numPedidoTemp=$_POST['numPedidoTemp'];
		$idTienda=$_POST['idTienda'];
		$idUsuario=$_POST['idUsuario'];
		$estadoPedido=$_POST['estadoPedido'];
		$idPedido=$_POST['idPedido'];		
		$res = array( 'datos' => array());
		//funcion de buscar clientes
		//luego html mostrar modal 
		$res = BusquedaClientes($busqueda,$BDTpv,$tabla, $idcaja);
		$respuesta['items']=$res['Nitems'];
		if ($res['Nitems']===1 & $idPedido==0){
			//~ if ($numPedidoTemp>0){
				//~ //Si el número de busquedas es uno quiere decir que la busqueda fue por id
			//~ $modCliente=$CcliPed->ModClienteTemp($busqueda, $numPedidoTemp, $idTienda, $idUsuario, $estadoPedido);
			//~ $respuesta['sql']=$modCliente;
			//~ $respuesta['busqueda']=$busqueda;
			//~ $respuesta['numPedidoTemp']=$numPedidoTemp;
			//~ $respuesta['idPedido']=$idPedido;
			//~ }else{
			//~ $addCliente=$CcliPed->AddClienteTemp($busqueda, $idTienda, $idUsuario, $estadoPedido);
			//~ $respuesta['numPedidoTemp']=$addCliente['id'];
			//~ $respuesta['sql']=$sql;
			//~ $respuesta['idPedido']=$idPedido;
			//~ }
			$respuesta['nombre']=$res['datos'][0]['nombre'];
			$respuesta['idCliente']=$res['datos'][0]['idClientes'];
		}elseif($res['Nitems']>1 & $idPedido===0){
			$respuesta = htmlClientes($busqueda,$dedonde, $idcaja, $res['datos']);
		}else if($res['Nitems']===1 & $idPedido>0){
		//if ($numPedidoTemp>0){
			//~ $modCliente=$CcliPed->ModClienteTemp($busqueda, $numPedidoTemp, $idTienda, $idUsuario, $estadoPedido);
			//~ $respuesta['busqueda']=$busqueda;
			//~ $respuesta['numPedidoTemp']=$numPedidoTemp;
			//~ $respuesta['idPedido']=$idPedido;
			//~ }else{
			//~ $addCliente=$CcliPed->AddClienteTempPedidoGuardado($busqueda, $idTienda, $idUsuario, $estadoPedido, $idPedido);
			//~ $respuesta['numPedidoTemp']=$addCliente['id'];
			//~ $respuesta['sql']=$addCliente['sql'];
			//~ $respuesta['idPedido']=$idPedido;
			//~ }
			$respuesta['nombre']=$res['datos'][0]['nombre'];
			$respuesta['idCliente']=$res['datos'][0]['idClientes'];
		}else{
			$respuesta = htmlClientes($busqueda,$dedonde, $idcaja, $res['datos']);
		
		}
	
		echo json_encode($respuesta);
		break;	
		case 'escribirCliente':
		// Cuando la busqueda viene a traves de  la ventana modal
		$id=$_POST['idcliente'];
		$tabla='clientes';
		$numPedidoTemp=$_POST['numPedidoTemp'];
		$idTienda=$_POST['idTienda'];
		$idUsuario=$_POST['idUsuario'];
		$estadoPedido=$_POST['estadoPedido'];
		$idPedido=$_POST['idPedido'];
		$respuesta['idCliente']=$id;
		if ($numPedidoTemp>0){
			$modCliente=$CcliPed->ModClienteTemp($id, $numPedidoTemp, $idTienda, $idUsuario, $estadoPedido);
			$respuesta['sql']=$modCliente;
			$respuesta['busqueda']=$id;
			$respuesta['numPedidoTemp']=$numPedidoTemp;
		}else{
			$addCliente=$CcliPed->AddClienteTemp($id, $idTienda, $idUsuario, $estadoPedido);
			$respuesta['numPedidoTemp']=$addCliente['id'];
			$numPedidoTemp=$addCliente['id'];
		}
		if ($idPedido>0){
			$modIdPedido=$CcliPed->ModNumPedidoTtemporal($numPedidoTemp, $idPedido);
			$respuesta['sqlMod']=$modIdPedido;
		}
		echo json_encode($respuesta);
		break;
		
		
		
		
		case 'HtmlLineaLinea':
		$respuesta = array();
		$product 					=$_POST['producto'];
		$num_item					=$_POST['num_item'];
		$CONF_campoPeso		=$_POST['CONF_campoPeso'];
		$res 	= htmlLineaTicket($product,$num_item,$CONF_campoPeso);
		$respuesta['html'] =$res;
		echo json_encode($respuesta);
		break;
		
		
	case 'buscarPedido':
		$busqueda=$_POST['busqueda'];
		$dedonde=$_POST['dedonde'];
		$idCaja=$_POST['idcaja'];
		$idAlbaranTemp=$_POST['idAlbaranTemp'];
		$idUsuario=$_POST['idUsuario'];
		$idTienda=$_POST['idTienda'];
		$estadoAlbaran=$_POST['estadoAlbaran'];
		$idAlbaran=$_POST['idAlbaran'];
		$numAlbaran=$_POST['numAlbaran'];
		$fecha=$_POST['fecha'];
		$idCliente=$_POST['idCliente'];
		//~ $respuesta['busqueda']=$_POST['busqueda'];
		$res=$CcliPed->PedidosClienteGuardado($busqueda, $idCliente);
	//	$respuesta['datos']=$res;
		if ($res['Nitem']==1){
			$temporales=$CcliPed->contarPedidosTemporal($res['id']);
			if ($temporales['numPedTemp']==0){
				$respuesta['temporales']=$temporales;
				$respuesta['datos']['Numpedcli']=$res['Numpedcli'];
				$respuesta['datos']['idPedCli']=$res['id'];
				$respuesta['datos']['fecha']=$res['FechaPedido'];
				$respuesta['datos']['total']=$res['total'];
				$respuesta['Nitems']=$res['Nitem'];
				$productosPedido=$CcliPed->ProductosPedidos($res['id']);
				$respuesta['productos']=$productosPedido;
			}
		}else{
			$respuesta=$res;
			$contad = 0;
			$respuesta['html'] .= '<table class="table table-striped"><thead>';
			$respuesta['html'] .= '<th>';
			$respuesta['html'] .='<td>Número </td>';
			$respuesta['html'] .='<td>Fecha</td>';
			$respuesta['html'] .='<td>Total</td>';
			$respuesta['html'] .='</th>';
			$respuesta['html'] .= '</thead><tbody>';
			foreach ($res['datos'] as $pedido){
			
				 $respuesta['html'] .= '<tr id="Fila_'.$contad.'" onmouseout="abandonFila('
						.$contad.')" onmouseover="sobreFilaCraton('.$contad.')"  onclick="escribirPedidoSeleccionado('.$pedido['id'].');">';
			
			$respuesta['html'] .= '<td id="C'.$contad.'_Lin" ><input id="N_'.$contad.'" name="filaproducto" onfocusout="abandonFila('
								.$contad.')" data-obj="idN" onfocus="sobreFila('.$contad.')" onkeydown="controlEventos(event)" type="image"  alt=""><span  class="glyphicon glyphicon-plus-sign agregar"></span></td>';

					$respuesta['html'].='<td>'.$pedido['Numpedcli'].'</td>';
					$respuesta['html'].='<td>'.$pedido['FechaPedido'].'</td>';
					$respuesta['html'].='<td>'.$pedido['total'].'</td>';
					$respuesta['html'].='</tr>';
					$contad = $contad +1;
					if ($contad === 10){
						break;
					}
			
			}
			$respuesta['html'].='</tbody></table>';
			
		}
		echo json_encode($respuesta);
	
		
	break;
	
	case 'añadirAlbaranTemporal':
		$idAlbaranTemp=$_POST['idAlbaranTemp'];
		$idUsuario=$_POST['idUsuario'];
		$idTienda=$_POST['idTienda'];
		$estadoAlbaran=$_POST['estadoAlbaran'];
		$idAlbaran=$_POST['idAlbaran'];
		$numAlbaran=$_POST['numAlbaran'];
		$fecha=$_POST['fecha'];
		$pedidos=$_POST['pedidos'];
		$productos=$_POST['productos'];
		$idCliente=$_POST['idCliente'];
		$nombreCliente=$_POST['nombreCliente'];
		$existe=0;
		if ($idAlbaranTemp>0){
			$rest=$CalbAl->modificarDatosAlbaranTemporal($idUsuario, $idTienda, $estadoAlbaran, $fecha , $pedidos, $idAlbaranTemp, $productos);
			$existe=1;
			$respuesta['sql']=$rest['sql'];
			$res=$rest['idTemporal'];
		}else{
			$res=$CalbAl->insertarDatosAlbaranTemporal($idUsuario, $idTienda, $estadoAlbaran, $fecha , $pedidos, $productos, $idCliente);
			$existe=0;
		}
		if ($numAlbaran===0){
			$modId=$CalbAl->addNumRealTemporal($idAlbaranTemp, $numAlbaran);
		}
		$respuesta['id']=$res;
		$respuesta['existe']=$existe;
		if ($pedidos){
			//$respuesta['html']->
		}
		
		echo json_encode($respuesta);
		break;
		
	case 'buscarClienteAl':
		$busqueda=$_POST['busqueda'];
		$dedonde=$_POST['dedonde'];
		$idCaja=$_POST['idcaja'];
		$idAlbaranTemp=$_POST['idAlbaranTemp'];
		$idUsuario=$_POST['idUsuario'];
		$idTienda=$_POST['idTienda'];
		$estadoAlbaran=$_POST['estadoAlbaran'];
		$idAlbaran=$_POST['idAlbaran'];
		$numAlbaran=$_POST['numAlbaran'];
		$fecha=$_POST['fecha'];
		$tabla='clientes';
		//$res = array( 'datos' => array());
		//$res = BusquedaClientes($busqueda,$BDTpv,$tabla, $idCaja);
		if ($idCaja==="id_clienteAl"){
		$res=$Ccliente->DatosClientePorId($busqueda);
			if ($res){
				$respuesta['idCliente']=$res['idClientes'];
				$respuesta['nombre']=$res['Nombre'];
				$respuesta['Nitems']=1;
			}
		
		}
		//~ if ($res['Nitems']===1 & $idAlbaranTemp===0){
			
		
		//~ }
		echo json_encode($respuesta);
		break;
	
	
	
	
	case 'modificarEstadoPedido':
	$idPedido=$_POST['idPedido'];
	$idTemporal=$_POST['numPedidoTemp'];
	if ($idPedido>0 & $idTemporal>0){
		$estado="Pendiende";
		$modEstado=$CcliPed->ModificarEstadoPedido($idPedido, $estado);
		$respuesta['sql']=$modEstado;
	}
	echo json_encode($respuesta);
	
	break;
	case 'comprobarPedidos':
	$idCliente=$_POST['idCliente'];
	$estado="Guardado";
	if ($idCliente>0){
		$comprobar=$CcliPed->ComprobarPedidos($idCliente, $estado);
		//$respuesta=$comprobar;
		if ($comprobar['ped']==1){
			$respuesta['ped']=1;
			$respuesta['sql']=$comprobar['sql'];
		}else{
			$respuesta['ped']=0;
		}
	}
	echo json_encode($respuesta);
	break;
	case 'htmlAgregarFilaPedido':
	$datos=$_POST['datos'];
	$respuesta['datos']=$datos;
	$respuesta['html'] ='<tr>';
	$respuesta['html'] .='<td>'.$datos['Numpedcli'].'</td>';
	$respuesta['html'] .='<td>'.$datos['fecha'].'</td>';
	$respuesta['html'] .='<td>'.$datos['total'].'</td>';
	$respuesta['html'] .='</tr>';
	
	
	
	echo json_encode($respuesta);
	break;
	 
	 case 'htmlAgregarFilasProductos':
	 $productos=$_POST['productos'];
	 foreach($productos as $producto){
		 	if ($producto['estadoLinea'] !=='Activo'){
				$classtr = ' class="tachado" ';
				$estadoInput = 'disabled';
				$funcOnclick = ' retornarFila('.$producto['filaAl'].');';
				$btnELiminar_Retornar= '<td class="eliminar"><a onclick="'.$funcOnclick.'"><span class="glyphicon glyphicon-export"></span></a></td>';
			} else {
				$funcOnclick = ' eliminarFila('.$producto['filaAl'].');';
				$btnELiminar_Retornar= '<td class="eliminar"><a onclick="'.$funcOnclick.'"><span class="glyphicon glyphicon-trash"></span></a></td>';
			}
		 $respuesta['html'] .='<tr id="Row'.$producto['filaAl'].'" '.$classtr.'>';
		 $respuesta['html'] .='<td class="linea">'.$producto['filaAl'].'</td>';
		 $respuesta['html']	.= '<td class="idArticulo">'.$producto['idArticulo'].'</td>';
		 $respuesta['html'] .='<td class="referencia">'.$producto['cref'].'</td>';
		 $respuesta['html'] .='<td class="codbarras">'.$producto['ccodbar'].'</td>';
		 $respuesta['html'] .= '<td class="detalle">'.$producto['ccodbar'].'</td>';
		 $cant=number_format($producto['ncant'],0);
		 $respuesta['html'] .= '<td><input id="Unidad_Fila_'.$producto['filaAl'].'" type="text" data-obj="Unidad_Fila" pattern="[.0-9]+" name="unidad" placeholder="unidad" size="4"  value="'.$cant.'"  '.$estadoInput.' onkeydown="controlEventos(event,'."'Unidad_Fila_".$producto['filaAl']."'".')" onBlur="controlEventos(event)"></td>';
		 $respuesta['html'] .='<td class="pvp">'.$producto['precioCiva'].'</td>';
		 $respuesta['html'] .= '<td class="tipoiva">'.$producto['iva'].'%</td>';
		 $importe = $producto['precioCiva']*$producto['ncant'];
		 $importe = number_format($importe,2);
		 $respuesta['html'] .='<td id="N'.$producto['filaAl'].'_Importe" class="importe" >'.$importe.'</td>';
		 $respuesta['html'] .= $btnELiminar_Retornar;
		 $respuesta['html'] .='</tr>';
		 
	 }
	 	echo json_encode($respuesta);
		 break;
	
		
}
