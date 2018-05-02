<!DOCTYPE html>
<html>
    <head>
		<?php
        include './../../head.php';
        include ("./../mod_conexion/conexionBaseDatos.php");
        
        $titulo="Crear Etiquetas de Código Barras";
        $fechaEnv=date('Y-m-d H:i:s');
        $fechaCad=date('Y-m-d');
        $numAlb="";
        $nomPro="";
        if (isset($_GET['idProducto'])){
			
		}
        ?>
     </head>
	<body>
	<?php     
        include './../../header.php';
        ?>
        <div class="container">
		<h2 class="text-center"> <?php echo $titulo;?></h2>
		<form action="" method="post" name="formEtiqueta" onkeypress="return anular(event)">
			<div class="col-md-12">
				<div class="col-md-12 btn-toolbar">
					<a class="text-ritght" href="./../mod_producto/ListaProductos.php">Volver Atrás</a>
					<input type="submit" name="Guardar" value="Guardar">
					<input type="submit" name="Cancelar" value="Cancelar">
				</div>
				
				<div class="col-md-12">
					<div class="col-md-2">
						<label>Fecha Envasado</label>
						<input type="date" name="fechaEnv" id="fechaEnv" size="17"  value="<?php echo $fechaEnv;?>" readonly>
					</div>
					<div class="col-md-2">
						<label>Fecha Caducidad</label>
						<input type="date" name="fechaCad" id="fechaCad" size="10" data-obj= "cajaFecha"  value="<?php echo $fechaCad;?>" onkeydown="controlEventos(event)" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder='yyyy-mm-dd' title=" Formato de entrada yyyy-mm-dd">
					</div>
					<div class="col-md-2">
						<label>Tipo</label>
					<select name="tipo" id="tipo">
						<option value='0'>Selecciona</option>
						<option value='1'>Por unidad</option>
						<option value='2'>Por peso</option>
					</select>
					</div>
					<div class="col-md-2">
						<label>Num Albarán</label>
						<input type="text" id="numAlb" name="numAlb" value="<?php echo $numAlb;?>" size="10">
					</div>
				</div>
				<div class="col-md-12">
					<div class="col-md-5">
						<label>Producto:</label>
						<input type="text" id="producto" name="producto" value="<?php echo $nomPro;?>" size="50" readonly>
					</div>
				</div>
				<div class="col-md-12">
					<table id="tabla" class="table table-striped">
						<thead>
							<tr>
								<th>L</th>
								<th>Nombre del producto</th>
								<th>Tipo</th>
								<th>Precio</th>
								<th>Fecha</th>
								<th>Num Alb</th>
								<th>Num Cod Barras</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</form>
		</div>
	</body>
</html>
      
