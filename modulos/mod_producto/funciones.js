//recogemos valor de la caja de busqueda que tenemos en Listado tickets o productos
function BuscarProducto (){
	$(document).ready(function()
	{
		// Lo ideal sería identificar palabras..
		// de momento solo una palabra..
		NuevoValorBuscar = $('input[name=buscar').val();
		NuevoValorBuscar = $.trim(NuevoValorBuscar);
		if (NuevoValorBuscar !== ''){
			BProductos= NuevoValorBuscar;
			console.log('Filtro:'+BProductos);
		} else {
			alert (' Debes poner algun texto ');
			BProductos = '';
		}
		return;
	});
}
//parametros:
//adonde : a donde quiero ir o donde quiero permanecer: ListaTickets, ListaProductos.. 
function metodoClick(pulsado,adonde){
	console.log("Inicimos switch de control pulsar");
	switch(pulsado) {
		case 'VerProducto':
			console.log('Entro en Ver producto');
			// Cargamos variable global ar checkID = [];
			VerIdSeleccionado ();
			if (checkID.length >1 || checkID.length=== 0) {
				alert ('Que items tienes seleccionados? \n Solo puedes tener uno seleccionado');
				return
			}
			// Ahora redireccionamos 
			window.location.href = './'+adonde+'.php?id='+checkID[0];
			//window.location.href = './producto.php?id='+checkID[0];
			
			
			break;
		
		case 'AgregarProducto':
			console.log('entro en agregar producto');
			window.location.href = './producto.php';
			
			break;
		
		case 'NuevaBusqueda':
			// Obtenemos puesto en input de Buscar
			BuscarProducto ();
			// Ahora redireccionamos 
			if (BProductos !== ''){
				window.location.href = './'+adonde+'.php?buscar='+BProductos;
			} else {
				// volvemos sin mas..
				return;
			}
			console.log('Resultado Buscar:'+BProductos);
			break;
		
		
	 }
} 

function agregoCodBarrasVacio(contNuevo){
	//ajax
	// @ Objetivo
	//agrego campo codigo barras vacio en html
	var tablaC=document.getElementById("tcodigo");
	var cont=tablaC.childElementCount;
	
	var parametros = {
		"pulsado"    : 'HtmlCodigoBarrasVacio',
		"filas": cont
	};
	$.ajax({
		data       : parametros,
		url        : 'tareas.php',
		type       : 'post',
		beforeSend : function () {
			console.log('*********  Obteniendo html de codBarras vacio  ****************');
		},
		success    :  function (response) {
			console.log('Repuesta de Obtener HTML linea de FUNCION -> agregoCodBarrasVacio');
			
			var resultado =  $.parseJSON(response);
			var nuevafila = resultado['html'];
			console.log(nuevafila);
			
			$("#tcodigo").prepend(nuevafila);
			
		}
	});
	
}

			
			
function anular(e) {
  // Objetivo:
  // Evitar recargar el formulario al pulsar intro, ya que sino lo recarga.
  // [NO COMPRENDO]
  // No se porque pero lo hace...
  tecla = (document.all) ? e.keyCode : e.which;
  return (tecla != 13);
}
//Función para eliminar el código de barras . Busca los elementos a eliminar mediante DOM
//Cuando encuentra el elemento TBODY elimina el hijo que le indicamos
 function eliminarCodBarras(e){
	var padre=e.parentNode; 
	var abuelo=padre.parentNode; 
	var bisa=abuelo.parentNode; 
	bisa.removeChild(abuelo);
 }
	 


function recalcularPrecioSegunCosteBeneficio (caja){
	// @ Objetivo
	// Recalcular precio de PVP sin iva y con iva, segun ultimo coste y beneficio.
	
	// Obtenemos el iva que selecciono.
	console.log('RecalculoPreciosSegunCosteBeneficio');
	var iva = obtenerIva();

	var coste = parseFloat($( "#coste" ).val());
	var beneficio = parseFloat($( "#beneficio" ).val());
	if (beneficio >0 ){
		// No puedo dividir entre 0
		beneficio = beneficio/100;
	}
	var precioSiva = coste+(coste*beneficio);
	var precioCiva = precioSiva+(precioSiva*iva);
	// Ahora cambiamos los datos en input.
	destacarCambioCaja('pvpSiva');
	destacarCambioCaja('pvpCiva');

	$('#pvpSiva').val(precioSiva.toFixed(2));
	$('#pvpCiva').val(precioCiva.toFixed(2));
	
	
	
	


}

function destacarCambioCaja(idcaja){
	$("#"+idcaja).css("outline-style","solid");
	$("#"+idcaja).css("outline-color","coral");
	$("#"+idcaja).animate({
			"opacity": "0.3"
		 },2000);
	t = setTimeout(volverMostrar,2000,idcaja);
	
}
function volverMostrar(idcaja){
	console.log('Entro volver mostrar');
	$("#"+idcaja).animate({
			"opacity": "1"
		 },1000);
	$("#"+idcaja).css("outline-color","transparent")
}

function recalcularPvp(dedonde){
	// @ Objetivo:
	// Recalcular precio s/iva y precio c/iva segun los datos que tengan las cjas y de donde venga.
	// @ Parametros:
	//  dedonde = (string) id_input.
	// Obtenemos iva ( deberías ser funcion)
	var iva = obtenerIva();
	console.log('De donde:'+dedonde);
	if (dedonde === 'pvpSiva'){
		var precioSiva = parseFloat($('#pvpSiva').val(),2);
		var precioCiva = precioSiva+(precioSiva*iva);
		// Ahora destacamos los input que cambiamos.		
		destacarCambioCaja('pvpCiva');
	} else {
		var precioCiva = parseFloat($('#pvpCiva').val(),2);
		var precioSiva = precioCiva -(precioCiva*iva);
		// Ahora destacamos los input que cambiamos		
		destacarCambioCaja('pvpSiva');
	}

	//~ // Ahora cambiamos los datos en input.
	$('#pvpSiva').val(precioSiva.toFixed(2));
	$('#pvpCiva').val(precioCiva.toFixed(2));
	
	
}

function obtenerIva(){
	// @ Objetivo
	// Obtener el iva a aplicar según el que tengamos seleccionado.
	var id_iva=$( "#idIva option:selected" ).val();
	var iva = 0;
	ivas.forEach(function(element){
		if (element.idIva === id_iva){
			iva = parseFloat(element.iva,2);
			console.log('id:'+element.idIva+ ' Busco:'+ id_iva + ' Iva:'+element.iva);
			console.log('Iva encontrado.'+iva);
		}
	});
	if (iva >0){
		// No puedo dividir entre 0
		iva = iva/100;
	}
	return iva;
	
}


function AnhadirCodbarras(){
	// @ Objetivo
	// Añadir una caja de codbarras, pero solo si las que hay tiene valor, sino no añade.
	
	// Contamos los tr que hay body tcodigo
	var num_tr = $('#tcodigo>tbody>tr').length; 
	
	var vacio = 'No';
	for (i = 0; i < num_tr; i++) { 
		// Comprobamos que input codbarras tenga valor.
		var valor = $('#codBarras_'+i).val() ;
		if ( valor.length === 0){
			vacio = 'Si';
		}
	}
	// Solo continuamos si vacio es No, ya que sino hay una caja codBarras vacio.
	if (vacio === 'No'){
		var parametros = {
		"pulsado"    : 'HtmlLineaCodigoBarras',
		"fila": num_tr
		};
		$.ajax({
			data       : parametros,
			url        : 'tareas.php',
			type       : 'post',
			beforeSend : function () {
				console.log('*********  Obteniendo html linea de codBarras  ****************');
			},
			success    :  function (response) {
				console.log('******  Respuesta de html lineas de codBarras *********');
				var resultado =  $.parseJSON(response);
				var nuevafila = resultado['html'];
				console.log(nuevafila);
				$("#tcodigo>tbody").prepend(nuevafila);
				
			}
		});
		
	}
	
}

function GuardarConfiguracion(obj){
	// Si llega aquí es porque cambio el valor de check impresion...
	// tenemos que tomar los valores configuracion para enviarlos y cambiarlos.
	console.log('Grabar configuracion');
	if ($(obj).val() === 'Si'){
		$(obj).val('No');
	} else {
		$(obj).val('Si');
		
	}
	var valor= $(obj).val();
	var nombre = $(obj).attr("name");

	CambiarConfiguracionMostrarLista(valor,nombre); // Cambiamos el valor de la configuracion
	// Ahora ejecutamos el guardar la configuracion.. pero esperamos un segundo por si tarda en hacer CambiarConfiguracionMostrarListado.
	setTimeout(AjaxGuardarConfiguracion,500);
	// Recargo pagina en un 1 s.
	setTimeout(refresh,1000);
}
function AjaxGuardarConfiguracion(){
	// Objetivo:
	// Guardar configuracion de usuario y modulo.
	var parametros = {
		"pulsado"    		: 'Grabar_configuracion',
		"configuracion"		: configuracion,
	};
	$.ajax({
		data       : parametros,
		url        : 'tareas.php',
		type       : 'post',
		beforeSend : function () {
		console.log('*********  Grabando configuracion **************');
		},
		success    :  function (response) {
				console.log('Respuesta de grabar configuracion');
				// var resultado = $.parseJSON(response);
				var resultado = response;
				return resultado ;
			}
			
	});
	
	
}
function CambiarConfiguracionMostrarLista(valor,nombre){
	// Ahora cambiamos el valor configuracion.
	configuracion.mostrar_lista.forEach(function(element) {
		if (element.nombre === nombre){
			element.valor=valor;

		}
	});

}

function CambiarConfiguracionBuscar_default(nombre){
	// Ahora cambiamos el valor configuracion.
	configuracion.mostrar_lista.forEach(function(element) {
		if (element.nombre === nombre){
			// Creo propiedad buscar_default.
			element.buscar_default='Si';
		} else {
			// A todos los demas elimino propiedad
			delete element.buscar_default;
		}
	});

}


function GuardarBusqueda(event){
	// @ Objetivo :
	// Guardar el campo el que se busca en la configuracion del usuario y del modulo.
	// @ Parametro:
	// 		event-> Es select....
	console.log("GuardarBusqeuda");
	var campo =  event.target.value;
	CambiarConfiguracionBuscar_default(campo);
	// Ahora ejecutamos el guardar la configuracion.. pero esperamos un segundo por si tarda en hacer CambiarConfiguracionBuscar_default.
	var respuesta = setTimeout(AjaxGuardarConfiguracion,500);
	// Limpiamo la cja de busqueda, ya que cambiamos la  busqueda.
	$('input:text[name=buscar]').val("");
	
}


function refresh() {
	// Funcion para recargar pagina.
	location.reload(true);
}

function desActivarCoste(event){
	// Objetivo:
	// Activar o Desactivar input de ultimo coste, para poder recalcular precio.
	// Cambiamo el nombre de la caja para no cambiar el coste_ultimo en post.
	console.log(event.target);
	$('#coste').removeAttr('readonly', '');
	$('#coste').attr('name','coste');

}

function desActivarCajasProveedor(obj){
	// Objetivo:
	// Activar o Desactivar cjas de input proveedores coste.
	
	// Obtenemos el id del proveedor.
	var idInput= obj.id;
	var id_prov = idInput.substr(15, 4);
	// Cambiamos
	$('#prov_coste_'+ id_prov).removeAttr('readonly', '');
	$('#prov_cref_'+ id_prov).removeAttr('readonly', '');
	// Añadimos funcion a input de control de datos.
	$('#prov_coste_'+ id_prov).attr('onkeydown',"controlEventos(event)");
	$('#check_pro_'+ id_prov).removeAttr('disabled', '');
	$('#check_pro_'+ id_prov).removeAttr('readonly', '');
	$('#check_pro_'+ id_prov).attr('onclick', "cambioEstadoProvPrincipal(this)");


}
function bloquearCajaProveedor(caja){
	// Objetivo es poner solo lecturar la cja input
	console.log('Poner solo lectura '+caja.name_cja);
	$('#'+ caja.name_cja).attr('readonly', "true");

	
}

function cambioEstadoProvPrincipal(obj){
	// Objetivo:
	// Comprobar si cambio estado check de proveedor, si lo marco , desmarca el resto proveedores.
	// Solo puede haber un proveedor principal.
	var check = $('#'+obj.id).prop('checked')
	
	if (check === true){
		// Comprobamos si hay alguno marcado , entonces lo desmarcamos.
		var checks_pro = $("input:checkbox[name=check_pro]:checkbox");
		console.log(checks_pro.length);
		for (i = 0; i < checks_pro.length; i++) { 
			if ( obj.id !== checks_pro[i].id ){
				console.log(checks_pro[i].id+$("#"+checks_pro[i].id).prop('checked'));
				$('#'+checks_pro[i].id).removeAttr('checked', '');
			}
		}
		
	}
	
}

// ---------------------------------  Funciones control de teclado ----------------------------------------------- //

function after_constructor(padre_caja,event){
	// @ Objetivo:
	// Ejecuta procesos antes construir el obj. caja. ( SI ANTES) Se fue pinza.. :-)
	// Traemos 
	//		(objeto) padre_caja -> Que es objeto el padre del objeto que vamos a crear 
	//		(objeto) event -> Es la accion que hizo, que trae todos los datos input,button , check.
	
	return padre_caja;
}

function before_constructor(caja){
	// @ Objetivo :
	//  Ejecutar procesos para obtener datos despues del construtor de caja. ( SI DESPUES ) :-)
	//  Estos procesos los indicamos en parametro before_constructor, si hay
	console.log( 'Entro en before');
	console.log(caja);
	
	
	return caja;	
}



function controladorAcciones(caja,accion, tecla){
	console.log(tecla);

	switch(accion) {
		case 'controlReferencia':
			console.log("Estoy en buscar controladorAcciones-> controlReferencia");
			
		break;
		case 'salto':
			console.log("Estoy en buscar controladorAcciones-> salto + caja:");
			console.log(caja);
		break;
		case 'salto_recalcular':
			
			var re= comprobarNumero(caja.darValor());
			if ( re === true){
				recalcularPrecioSegunCosteBeneficio(caja);
			}
		break
		case 'recalcularPvp':
			var re= comprobarNumero(caja.darValor());
			if ( re === true){
				recalcularPvp(caja.id_input);
			}
		break
		
		case 'controlCosteProv':
			caja.id_input = caja.name_cja;
			console.log(caja.darValor());
			var re= comprobarNumero(caja.darValor());
			console.log(re);
			if ( re === false){
				alert( 'Error en el coste, fijate bien');
			} else {
				// Volvemos a ponerla solo lectura.
				bloquearCajaProveedor(caja);
			}
		break
		
	}
		
}


function comprobarNumero(valor){
	// Objetivo validar un numero decimal tanto positivo , como negativo.
	var RE = /^\-?\d*\.?\d*$/;
    if (RE.test(valor)) {
        return true;
    } else {
        return false;
    }
	
}


function eliminarCoste(idArticulo, dedonde, id, tipo, fila){
	
	var parametros = {
		"pulsado"    		: 'eliminarCoste',
		"idArticulo"		: idArticulo,
		"dedonde"			:dedonde,
		"id"				:id,
		"tipo"				:tipo
	};
	console.log(parametros);
		$.ajax({
		data       : parametros,
		url        : 'tareas.php',
		type       : 'post',
		beforeSend : function () {
		console.log('*********  Modificando eliminar costes  **************');
		},
		success    :  function (response) {
				console.log('Respuesta de eliminar costes ');
				// var resultado = $.parseJSON(response);
				var resultado = response;
				return resultado ;
			}
			var line;
			line = "#Row" + fila;
			$(line).addClass('tachado');
			$(line + "> .eliminar").html('<a onclick="retornarCoste('+idArticulo+')"');
			
	});
	
}
