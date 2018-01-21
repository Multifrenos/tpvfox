
<!DOCTYPE html>
<html>
<head>
<?php
include './../../head.php';
	include './funciones.php';
	include ("./../../plugins/paginacion/paginacion.php");
	include ("./../../controllers/Controladores.php");
	include 'clases/pedidosVentas.php';
	include '../../clases/cliente.php';
	
	$Cpedido=new PedidosVentas($BDTpv);
	$Ccliente=new Cliente($BDTpv);
	$Controler = new ControladorComun; 
	$Tienda = $_SESSION['tiendaTpv'];
	$Usuario = $_SESSION['usuarioTpv'];// array con los datos de usuario
	if ($_GET['id']){
		$idPedido=$_GET['id'];
		$titulo="Modificar Pedido De Cliente";
		$estado='Modificado';
		$estadoCab="'".'Modificado'."'";
		$datosPedido=$Cpedido->datosPedidos($idPedido);
		$productosPedido=$Cpedido->ProductosPedidos($idPedido);
		$ivasPedido=$Cpedido->IvasPedidos($idPedido);
		$fecha=$datosPedido['FechaPedido'];
		$idCliente=$datosPedido['idCliente'];
		if ($idCliente){
				// Si se cubrió el campo de idcliente llama a la función dentro de la clase cliente 
				$datosCliente=$Ccliente->DatosClientePorId($idCliente);
				$nombreCliente=$datosCliente['Nombre'];
		}
	
		$productos=json_decode(json_encode($productosPedido));
		$Datostotales = recalculoTotales($productos);
		
		echo '<pre>';
		print_r($productos);
		echo '</pre>';
	}else{
		$titulo="Crear Pedido De Cliente";
		$bandera=1;
		$estado='Abierto';
		$estadoCab="'".'Abierto'."'";
		$fecha=date('Y-m-d');
		
	}
	if ($_GET['tActual']){
		// Si recibe el número de pedido temporal cubre los campos 
			$pedido_numero=$_GET['tActual'];
			$pedidoTemporal= $Cpedido->BuscarIdTemporal($pedido_numero);
			$estadoCab="'".$pedidoTemporal['estadoPedCli']."'";
			$estado=$pedidoTemporal['estadoPedCli'];
			$idCliente=$pedidoTemporal['idClientes'];
			$pedido=$pedidoTemporal;
			$productos = json_decode( $pedidoTemporal['Productos'] ); // Array de objetos
			if ($idCliente){
				// Si se cubrió el campo de idcliente llama a la función dentro de la clase cliente 
				$datosCliente=$Ccliente->DatosClientePorId($idCliente);
				$nombreCliente=$datosCliente['Nombre'];
			}
		}else{
			$pedido_numero = 0;
		}
		if(isset($pedido['Productos'])){
			// Obtenemos los datos totales ( fin de ticket);
			// convertimos el objeto productos en array
			$Datostotales = recalculoTotales($productos);
			$productos = json_decode(json_encode($productos), true); // Array de arrays	
		}
		if (isset($_POST['Nuevo'])){
			$idTemporal=$_POST['idTemporal'];
			$pedidoTemporal= $Cpedido->BuscarIdTemporal($idTemporal);
			if($pedidoTemporal['total']){
				$total=$pedidoTemporal['total'];
			}else{
				$total=0;
			}
			$fechaCreacion=date("Y-m-d H:i:s");
			$datosPedido=array(
			'NPedidoTemporal'=>$idTemporal,
			'fecha'=>$_POST['fecha'],
			'idTienda'=>$Tienda['idTienda'],
			'idUsuario'=>$Usuario['id'],
			'idCliente'=>$pedidoTemporal['idClientes'],
			'estado'=>"Guardado",
			'formaPago'=>" ",
			'entregado'=>" ",
			'total'=>$total,
			'fechaCreacion'=>$fechaCreacion,
			'productos'=>$pedidoTemporal['Productos'],
			'DatosTotales'=>$Datostotales
			);
			//~ echo '<pre>';
			//~ print_r($datosPedido);
			//~ echo '</pre>';
			$addNuevo=$Cpedido->AddPedidoGuardado($datosPedido);
			$eliminarTemporal=$Cpedido->EliminarRegistroTemporal($idTemporal);
			header('Location: pedidosListado.php');
				//~ echo '<pre>';
			//~ print_r($addNuevo);
			//~ echo '</pre>';
		}else{
		//	echo "else de post nuevo";
		}
		if (isset ($pedido)){
			$style="";
		}else{
			$style="display:none;";
		}
	//~ echo $pedido_numero;	
		$parametros = simplexml_load_file('parametros.xml');
	
// -------------- Obtenemos de parametros cajas con sus acciones ---------------  //
		$VarJS = $Controler->ObtenerCajasInputParametros($parametros);
?>
	<script type="text/javascript">
	// Esta variable global la necesita para montar la lineas.
	// En configuracion podemos definir SI / NO
		
	var CONF_campoPeso="<?php echo $CONF_campoPeso; ?>";
	var cabecera = []; // Donde guardamos idCliente, idUsuario,idTienda,FechaInicio,FechaFinal.
		cabecera['idUsuario'] = <?php echo $Usuario['id'];?>; // Tuve que adelantar la carga, sino funcionaria js.
		cabecera['idTienda'] = <?php echo $Tienda['idTienda'];?>; 
		cabecera['estadoPedido'] =<?php echo $estadoCab ;?>; // Si no hay datos GET es 'Nuevo'
		cabecera['numPedidoTemp'] = <?php echo $pedido_numero ;?>;
		 // Si no hay datos GET es 'Nuevo';
	var productos = []; // No hace definir tipo variables, excepto cuando intentamos añadir con push, que ya debe ser un array

<?php 
	if (isset($pedidoTemporal)){ 
?>
	console.log("entre en el javascript");
<?php
	$i= 0;
		foreach($productos as $product){
?>
			datos=<?php echo json_encode($product); ?>;

			productos.push(datos);
	
<?php 
		// cambiamos estado y cantidad de producto creado si fuera necesario.
			if ($product->estado !== 'Activo'){
			?>	productos[<?php echo $i;?>].estado=<?php echo'"'.$product['estado'].'"';?>;
			<?php
			}
			$i++;
		}
	
	}	
?>
</script>
</head>
<body>
	<script src="<?php echo $HostNombre; ?>/modulos/mod_venta/funciones.js"></script>
    <script src="<?php echo $HostNombre; ?>/controllers/global.js"></script> 
<?php
	include '../../header.php';
?>
<script type="text/javascript">
// Objetos cajas de tpv
<?php echo $VarJS;?>
     function anular(e) {
          tecla = (document.all) ? e.keyCode : e.which;
          return (tecla != 13);
      }
</script>
<script src="<?php echo $HostNombre; ?>/lib/js/teclado.js"></script>
<div class="container">
			<?php 
			if (isset($_GET)){
				$mensaje=$_GET['mensaje'];
				$tipomensaje=$_GET['tipo'];
			}
			if (isset($mensaje) || isset($error)){   ?> 
				<div class="alert alert-<?php echo $tipomensaje; ?>"><?php echo $mensaje ;?></div>
				<?php 
				if (isset($error)){
				// No permito continuar, ya que hubo error grabe.
				return;
				}
				?>
			<?php
			}
			?>
			<h2 class="text-center"> <?php echo $titulo;?></h2>
			<a  href="./pedidosListado.php">Volver Atrás</a>
			<form action="" method="post" name="formProducto" onkeypress="return anular(event)">
				<?php 
				if ($_GET['id']){	?>
					<input type="submit" value="Guardar" name="Guardar">
					<?php
				}else{?>
					<input type="submit" value="Nuevo" name="Nuevo">
					<?php 
				}
				if ($_GET['tActual']){
					?>
					<input type="text" style="display:none;" name="idTemporal" value=<?php echo $_GET['tActual'];?>>
					<?php
				}
					?>
<div class="col-md-12" >
	<div class="col-md-8">
		<div class="col-md-12">
			<div class="col-md-7">
				<div class="col-md-6">
					<strong>Fecha Pedido:</strong><br/>
					<input type="date" name="fecha" id="fecha" data-obj= "cajaFecha"  value="<?php echo $fecha;?>" onkeydown="controlEventos(event)" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder='yyyy-mm-dd' title=" Formato de entrada yyyy-mm-dd">
				</div>
				<div class="col-md-6">
					<strong>Estado:</strong>
					<span id="EstadoTicket"> <input type="text" id="estado" name="estado" value="<?php echo $estado;?>" readonly></span><br/>
					<?php if ($bandera<>1){?>
					<strong>NºT_temp:</strong>
					<span id="NTicket"><?php echo $ticket_numero ;?></span><br/>
					<?php 
					}
					?>
				</div>
			</div>
			<div class="col-md-3">
				<label>Empleado:</label>
				<input type="text" id="Usuario" name="Usuario" value="<?php echo $Usuario['nombre'];?>" size="25" readonly>
			</div>
		</div>
		<div class="form-group">
			<label>Cliente:</label>
			<input type="text" id="id_cliente" name="idCliente" data-obj= "cajaIdCliente" value="<?php echo $idCliente;?>" size="2" onkeydown="controlEventos(event)" placeholder='id'>
			<input type="text" id="Cliente" name="Cliente" data-obj= "cajaCliente" placeholder="Nombre de cliente" onkeydown="controlEventos(event)" value="<?php echo $nombreCliente; ?>" size="60">
			<a id="buscar" class="glyphicon glyphicon-search buscar" onclick="buscarClientes('pedidos')"></a>
		</div>
	</div>
	<!-- Tabla de lineas de productos -->
	<div>
		<table id="tabla" class="table table-striped">
		<thead>
		  <tr>
			<th>L</th>
			<th>Id Articulo</th>
			<th>Referencia</th>
			<th>Cod Barras</th>
			<th>Descripcion</th>
			<th>Unid</th>
			<th>PVP</th>
			<th>Iva</th>
			<th>Importe</th>
			<th></th>
		  </tr>
		  <tr id="Row0" style=<?php echo $style;?>>  
			<td id="C0_Linea" ></td>
			<td><input id="idArticulo" type="text" name="idArticulo" placeholder="idArticulo" data-obj= "cajaidArticulo" size="13" value=""  onkeydown="controlEventos(event)"></td>
			<td><input id="Referencia" type="text" name="Referencia" placeholder="Referencia" data-obj="cajaReferencia" size="13" value="" onkeydown="controlEventos(event)"></td>
			<td><input id="Codbarras" type="text" name="Codbarras" placeholder="Codbarras" data-obj= "cajaCodBarras" size="13" value="" data-objeto="cajaCodBarras" onkeydown="controlEventos(event)"></td>
			<td><input id="Descripcion" type="text" name="Descripcion" placeholder="Descripcion" data-obj="cajaDescripcion" size="20" value="" onkeydown="controlEventos(event)"></td>
		  </tr>
		</thead>
		<tbody>
			<?php 
			foreach (array_reverse($productos) as $producto){
				$html=htmlLineaPedido($producto, $producto['nfila'], $CONF_campoPeso);
				echo $html;
			}
		?>
		</tbody>
	  </table>
	</div>
	<?php 
	if (isset($pedido['Productos'])){
			// Ahora montamos base y ivas
			foreach ($Datostotales['desglose'] as  $iva => $basesYivas){
				switch ($iva){
					case 4 :
						$base4 = $basesYivas['base'];
						$iva4 = $basesYivas['iva'];
					break;
					case 10 :
						$base10 = $basesYivas['base'];
						$iva10 = $basesYivas['iva'];
					break;
					case 21 :
						$base21 = $basesYivas['base'];
						$iva21 = $basesYivas['iva'];
					break;
				}
			}
	
	?>
		<script type="text/javascript">
			total = <?php echo $Datostotales['total'];?>;
			</script>
			<?php
	}
	?>
	<div class="col-md-10 col-md-offset-2 pie-ticket">
		<table id="tabla-pie" class="col-md-6">
		<thead>
			<tr>
				<th>Tipo</th>
				<th>Base</th>
				<th>IVA</th>
			</tr>
		</thead>
		<tbody>
			<tr id="line4">
				<td id="tipo4">
					<?php echo (isset($base4) ? " 4%" : '');?>
				</td>
				<td id="base4">
					<?php echo (isset($base4) ? $base4 : '');?>
				</td>
				<td id="iva4">
					<?php echo (isset($iva4) ? $iva4 : '');?>
				</td>
				
			</tr>
			<tr id="line10">
				<td id="tipo10">
					<?php echo (isset($base10) ? "10%" : '');?>
				</td>
				<td id="base10">
					<?php echo (isset($base10) ? $base10 : '');?>
				</td>
				<td id="iva10">
					<?php echo (isset($iva10) ? $iva10 : '');?>
				</td>
				
			</tr>
			<tr id="line21">
				<td id="tipo21">
					<?php echo (isset($base21) ? "21%" : '');?>
				</td>
				<td id="base21">
					<?php echo (isset($base21) ? $base21 : '');?>
				</td>
				<td id="iva21">
					<?php echo (isset($iva21) ? $iva21 : '');?>
				</td>
				
			</tr>
		</tbody>
		</table>
		<div class="col-md-6">
			<div class="col-md-4">
			<h3>TOTAL</h3>
			</div>
			<div class="col-md-8 text-rigth totalImporte" style="font-size: 3em;">
				<?php echo (isset($Datostotales['total']) ? $Datostotales['total'] : '');?>
			</div>
		</div>
	</div>
</form>
</div>
<?php // Incluimos paginas modales
include $RutaServidor.'/'.$HostNombre.'/plugins/modal/busquedaModal.php';
?>
<script type="text/javascript">
	$('#id_cliente').focus();
</script>
	</body>
</html>