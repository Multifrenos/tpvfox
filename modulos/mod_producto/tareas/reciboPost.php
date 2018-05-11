<?php
/* Objetivo
 *  Preparar y GRABAR dando la informacion de lo que hizo o los errores posibles
 * 
 * */
 
$preparados = array();
if (isset($_POST['id'])){
	$id= $_POST['id'];
} else {
	// Hubo que haber un error
	exit();
}
// Comprobamos los datos y grabamos.
$DatosPostProducto= prepararandoPostProducto($_POST,$CTArticulos);
// Ahora vemos si hay advertencias de campos
if (isset($DatosPostProducto['comprobaciones'])){
	$preparados['comprobaciones'][] = $DatosPostProducto['comprobaciones'];
}
//~ echo '<pre>';
//~ print_r($_POST);
//~ echo '</pre>';

// --- Ahora comprobamos y grabamos ---- //

//  -----------------------------------     NUEVO  O  MODIFICADO 		------------------------------------- //
if ($id >0 ){
		// ------------------------            MODIFICADO 			-------------------------------//
		// --- 	Comprobamos  y grabamos los codbarras .	---//
		$comprobaciones = $CTArticulos->ComprobarCodbarrasUnProducto($id,$DatosPostProducto['codBarras']);
		$preparados['codbarras'] = $comprobaciones;
		// ---	Comprobamos y grabamos los proveedores . ---//
		$comprobaciones = $CTArticulos->ComprobarProveedoresCostes($id,$DatosPostProducto['proveedores_costes']);
		foreach ($comprobaciones as $key => $comprobacion){
			
			if ($key === 'nuevo'){
				foreach ($comprobacion as $nuevo){
					if ($nuevo['error']){
					   $success = array ( 'tipo'=>'danger',
							 'mensaje' =>'Hubo un error al añadir un coste ,referencia de proveedor.',
							 'dato' => $nuevo
							);
					} else {
						$success = array ( 'tipo'=>'success',
							 'mensaje' =>'Se ha añadido proveedor .',
							 'dato' => $nuevo
							);
					}
				$preparados['comprobaciones']['proveedor_nuevo'] = $success;
				}
			}
	
			if ($key === 'modificado'){
				foreach ($comprobacion as $modificado){
					if ($modificado['error']){
					   $success = array ( 'tipo'=>'danger',
							 'mensaje' =>'Hubo un error al modificarr un coste ,referencia de proveedor.',
							 'dato' => $modificado
							);
					} else {
						$success = array ( 'tipo'=>'success',
							 'mensaje' =>'Se ha modificado proveedor .',
							 'dato' => $modificado
							);
					}
				$preparados['comprobaciones']['proveedor_modificado'] = $success;
				}
			}
			
		}
		// ---  Comprobamos  y grabamos datos generales . --- //
		
		
		//~ echo '<pre>';
		//~ print_r($preparados);
		//~ echo '</pre>';
} else {
		// ----------------------------  			NUEVO 				  ------------------------  //
		
		$comprobaciones = $CTArticulos->comprobacionCamposObligatoriosProducto($DatosPostProducto);
		if (count($comprobaciones)=== 0){
			$anhadir = $CTArticulos->AnhadirProductoNuevo($DatosPostProducto);
			$DatosPostProducto['Sqls']['NuevoProducto']=$anhadir;
			// Se creo uno NUEVO fijo.
			if (isset($anhadir['insert_articulos']['id_producto_nuevo'])){
				// Ponemos el id para poder mostrar los datos ya grabados.
				$id = $anhadir['insert_articulos']['id_producto_nuevo']; 
				// Montamos comprobaciones para enviar despues de cargar de nuevo producto.
				$success = array ( 'tipo'=>'success',
							 'mensaje' =>'Se creo el producto con id '.$id.' nuevo',
							 'dato' => $anhadir['consulta']
							);
				$preparados['comprobaciones'][] = $success;
				// Ahora comprobamos si añadio mas cosas en el articulo nuevo. 
				if (isset($anhadir['insert_articulos_precios'])){
					if (isset($anhadir['insert_articulos_precios']['Afectados'])){
						// Entiendo que la consulta fue correcta y que se añadio o no.
						$success = array ( 'tipo'=>'success',
							 'mensaje' =>'Se añadieron precios correctos en '
										.$anhadir['insert_articulos_precios']['Afectados'].' registros',
							 'dato' => $anhadir['consulta']
							);
						$preparados['comprobaciones'][] = $success;
					} else {
						// Hubo un error al insertar los precios.
						$preparados['comprobaciones'][] = $anhadir['insert_articulos_precios'];
					}
				}
				if (isset($anhadir['codbarras'])){
					$preparados['codbarras'] = $anhadir['codbarras'];
				}

			} 
		}else {
			// Quiere decir que hubo un error al principio
			$preparados['comprobaciones'][] = $comprobaciones;
		}
		
}
//~ echo '<pre>';
//~ print_r($preparados);
//~ echo '</pre>';
?>
