// JavaScript DocumentS

//	item
function items(form, accion) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodTipoItem" || n.id == "Descripcion" || n.id == "CodUnidad" || n.id == "CodLinea" || n.id == "CodInterno" || n.id == "CodProcedencia") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valCodigo(n.value) && n.id == "CodItem") { error = "Formato de Codigo Interno incorrecto"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "StockMax") { error = "Monto de stock maximo incorrecto"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "StockMin") { error = "Monto de stock minimo incorrecto"; break; }
		//else if (!valAlfaNumerico(n.value)) { error = "No se permiten caractéres especiales en los campos"; break; }
	}
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=items&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	commodity
function commodity(form, accion) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "Descripcion" || n.id == "Clasificacion") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
	}
	
	//	detalles
	var detalles = "";
	var frm_detalle = document.getElementById("frm_detalle");
	for(var i=0; n=frm_detalle.elements[i]; i++) {
		if (n.name == "Codigo") detalles += n.value + "|";
		else if (n.name == "CommoditySub") detalles += n.value + "|";
		else if (n.name == "Descripcion") detalles += changeUrl(n.value.trim()) + "|";
		else if (n.name == "cod_partida") detalles += n.value + "|";
		else if (n.name == "CodCuenta") detalles += n.value + "|";
		else if (n.name == "CodCuentaPub20") detalles += n.value + "|";
		else if (n.name == "CodClasificacion") detalles += n.value + "|";
		else if (n.name == "CodUnidad") detalles += n.value + "|";
		else if (n.name == "CodigoSNC") detalles += n.value + "|";
		else if (n.name == "Estado") detalles += n.value + ";";
	}
	var len = detalles.length; len--;
	detalles = detalles.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		var eliminados_detalle = $("#eliminados_detalle").val();
		var len = eliminados_detalle.length; len--;
		eliminados_detalle = eliminados_detalle.substr(0, len);
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=commodity&accion="+accion+"&"+post+"&detalles="+detalles+"&eliminados_detalle="+eliminados_detalle,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	linea
function linea(form, accion) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "Descripcion") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		//else if (!valAlfaNumerico(n.value)) { error = "No se permiten caractéres especiales en los campos"; break; }
	}
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=linea&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	familia
function familia(form, accion) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodLinea" || n.id == "Descripcion") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		//else if (!valAlfaNumerico(n.value)) { error = "No se permiten caractéres especiales en los campos"; break; }
	}
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=familia&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	sub-familia
function subfamilia(form, accion) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodLinea" || n.id == "CodFamilia" || n.id == "Descripcion") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		//else if (!valAlfaNumerico(n.value)) { error = "No se permiten caractéres especiales en los campos"; break; }
	}
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=subfamilia&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	requerimiento
function requerimiento(form, accion) {
	$(".div-progressbar").css("display", "block");
	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "Clasificacion" || n.id == "CodAlmacen" || n.id == "Prioridad" || n.id == "FechaRequerida") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valFecha(n.value) && n.id == "FechaRequerida") { error = "<strong>Fecha Requerida</strong> incorrecta"; break; }
	}
	//	detalles
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CodItem") detalles += n.value + ";char:td;";
		else if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += changeUrl(n.value.trim()) + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "FlagExonerado") {
			if (n.checked) detalles += "S;char:td;";
			else detalles += "N;char:td;";
		}
		else if (n.name == "CantidadPedida") {
			var CantidadPedida = parseFloat(setNumero(n.value));
			if (isNaN(CantidadPedida) || CantidadPedida <= 0) { error = "Se encontraron <strong>Cantidades</strong> en la ficha de <strong>Items/Commodities</strong> incorrectas"; break; }
			else detalles += CantidadPedida + ";char:td;";
		}
		else if (n.name == "FlagCompraAlmacen") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCategoriaProg") detalles += n.value + ";char:td;";
		else if (n.name == "detallesEjercicio") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCodPresupuesto") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCodFuente") detalles += n.value + ";char:td;";
		else if (n.name == "Activo") detalles += n.value + ";char:td;";
		else if (n.name == "CodCuenta") detalles += n.value + ";char:td;";
		else if (n.name == "CodCuentaPub20") detalles += n.value + ";char:td;";
		else if (n.name == "cod_partida") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	//	valido errores
	if (detalles == "") error = "Debe ingresar por lo menos un articulo en la ficha de <strong>Items/Commodities</strong>";
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lg_requerimiento_ajax.php",
			data: "modulo=requerimiento&accion="+accion+"&"+post+"&detalles="+detalles,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");				
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					if (accion == "nuevo") {
						var funct = "document.getElementById('frmentrada').submit();";
						cajaModal(partes[1], "exito", 400, funct);
					}
					else form.submit();
				}
			}
		});
	}
	return false;
}

function orden_compra(form, accion) {
	$(".div-progressbar").css("display", "block");
	//	valido
	var error = "";
	if ($("#CodOrganismo").val() == "" || $("#CodProveedor").val() == "" || $("#CodTipoServicio").val() == "" || $("#CodFormaPago").val() == "" || $("#Clasificacion").val() == "" || $("#CodAlmacen").val() == "" || $("#CodAlmacenIngreso").val() == "" || $("#PlazoEntrega").val() == "") error = "Debe llenar los campos obligatorios";
	else if (isNaN($("#PlazoEntrega").val())) error = "<strong>Plazo de Entrega</strong> incorrecto";
	else if (!valFecha($("#FechaPrometida").val())) error = "<strong>Fecha de Entrega</strong> incorrecta";
	else if (!valFecha($("#FechaOrden").val())) error = "<strong>Fecha de Orden</strong> incorrecta";
	//	listas
	if (error == "") {
		//	detalles
		var detalles = "";
		var frm_detalles = document.getElementById("frm_detalles");
		for(var i=0; n=frm_detalles.elements[i]; i++) {
			if (n.name == "CodItem") detalles += n.value + ";char:td;";
			else if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
			else if (n.name == "Descripcion") detalles += changeUrl(n.value.trim()) + ";char:td;";
			else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
			else if (n.name == "CantidadPedida") {
				var CantidadPedida = parseFloat(setNumero(n.value));
				if (isNaN(CantidadPedida) || CantidadPedida <= 0) { error = "Se encontraron <strong>Cantidades</strong> en la ficha de <strong>Items/Commodities</strong> incorrectas"; break; }
				else detalles += CantidadPedida + ";char:td;";
			}
			else if (n.name == "PrecioUnit") {
				var PrecioUnit = parseFloat(setNumero(n.value));
				if (isNaN(PrecioUnit) || PrecioUnit <= 0) { error = "Se encontraron <strong>Precios</strong> en la ficha de <strong>Items/Commodities</strong> incorrectos"; break; }
				else detalles += PrecioUnit + ";char:td;";
			}
			else if (n.name == "DescuentoPorcentaje") {
				var DescuentoPorcentaje = parseFloat(setNumero(n.value));
				if (isNaN(DescuentoPorcentaje) || DescuentoPorcentaje < 0) { error = "Se encontraron <strong>Porcentajes de Descuentos</strong> en la ficha de <strong>Items/Commodities</strong> incorrectos"; break; }
				else detalles += DescuentoPorcentaje + ";char:td;";
			}
			else if (n.name == "DescuentoFijo") {
				var DescuentoFijo = parseFloat(setNumero(n.value));
				if (isNaN(DescuentoFijo) || DescuentoFijo < 0) { error = "Se encontraron <strong>Descuentos Fijos</strong> en la ficha de <strong>Items/Commodities</strong> incorrectos"; break; }
				else detalles += DescuentoFijo + ";char:td;";
			}
			else if (n.name == "FlagExonerado") {
				if (n.checked) detalles += "S;char:td;";
				else detalles += "N;char:td;";
			}
			else if (n.name == "PrecioUnitTotal") {
				var PrecioUnitTotal = parseFloat(setNumero(n.value));
				if (isNaN(PrecioUnitTotal) || PrecioUnitTotal <= 0) { error = "Se encontraron <strong>Precios Totales</strong> en la ficha de <strong>Items/Commodities</strong> incorrectos"; break; }
				else detalles += PrecioUnitTotal + ";char:td;";
			}
			else if (n.name == "Total") {
				var Total = parseFloat(setNumero(n.value));
				if (isNaN(Total) || Total <= 0) { error = "Se encontraron <strong>Totales</strong> en la ficha de <strong>Items/Commodities</strong> incorrectos"; break; }
				else detalles += Total + ";char:td;";
			}
			else if (n.name == "detallesCategoriaProg") detalles += n.value + ";char:td;";
			else if (n.name == "detallesEjercicio") detalles += n.value + ";char:td;";
			else if (n.name == "detallesCodPresupuesto") detalles += n.value + ";char:td;";
			else if (n.name == "detallesCodFuente") detalles += n.value + ";char:td;";
			else if (n.name == "CodUnidadRec") detalles += n.value + ";char:td;";
			else if (n.name == "CantidadRec") {
				var CantidadRec = parseFloat(setNumero(n.value));
				if (isNaN(CantidadRec) || CantidadRec < 0) { error = "Se encontraron <strong>Cantidades (Rec.)</strong> en la ficha de <strong>Items/Commodities</strong> incorrectas"; break; }
				else detalles += CantidadRec + ";char:td;";
			}
			else if (n.name == "FechaPrometida") {
				if (!valFecha(n.value)) { error = "Se encontraron <strong>Fechas Prometidas</strong> en la ficha de <strong>Items/Commodities</strong> incorrectas"; break; }
				else detalles += formatFechaAMD(n.value) + ";char:td;";
			}
			else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
			else if (n.name == "cod_partida") detalles += n.value + ";char:td;";
			else if (n.name == "CodCuenta") detalles += n.value + ";char:td;";
			else if (n.name == "CodCuentaPub20") detalles += n.value + ";char:td;";
			else if (n.name == "Comentarios") detalles += changeUrl(n.value) + ";char:td;";
			else if (n.name == "CodRequerimiento") detalles += n.value + ";char:td;";
			else if (n.name == "Secuencia") detalles += n.value + ";char:td;";
			else if (n.name == "CotizacionSecuencia") detalles += n.value + ";char:td;";
			else if (n.name == "CantidadRequerimiento") detalles += n.value + ";char:tr;";
		}
		var len = detalles.length; len-=9;
		detalles = detalles.substr(0, len);
		//	detalles distribucion
		var detalles_partida = "";
		var frm_partidas = document.getElementById("frm_partidas");
		for(var i=0; n=frm_partidas.elements[i]; i++) {
			if (n.name == "cod_partida") {
				var _cod_partida = n.value;
				detalles_partida += n.value + ";char:td;";
			}
			else if (n.name == "CodCuenta") detalles_partida += n.value + ";char:td;";
			else if (n.name == "CodCuentaPub20") detalles_partida += n.value + ";char:td;";
			else if (n.name == "Monto") {
				var _Monto = new Number(n.value);
				detalles_partida += n.value + ";char:td;";
			}
			else if (n.name == "MontoDisponible") {
				var _MontoDisponible = new Number(n.value);
				if (_Monto > _MontoDisponible && accion != "anular") { error = "Se encontro la partida <strong>"+_cod_partida+"</strong> sin Disponibilidad Presupuestaria"; break; }
				else detalles_partida += n.value + ";char:tr;";
			}
		}
		var len = detalles_partida.length; len-=9;
		detalles_partida = detalles_partida.substr(0, len);
	}
	//	valido errores
	if (detalles == "") error = "Debe ingresar por lo menos un articulo en la ficha de <strong>Items/Commodities</strong>";
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	datos
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "lg_orden_compra_ajax.php",
			data: "modulo=orden_compra&accion="+accion+"&"+post+"&detalles="+detalles+"&detalles_partida="+detalles_partida,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");				
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					if (accion == "revisar") {
						var funct = "document.getElementById('frmentrada').submit();";
						cajaModal(partes[1], "exito", 400, funct);
					}
					else form.submit();
				}
			}
		});
	}
	return false;
}

//	almacen (despacho)
function almacen_despacho(form) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "Clasificacion" || n.id == "CodAlmacen" || n.id == "Prioridad" || n.id == "FechaDocumento") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valFecha(n.value) && n.id == "FechaDocumento") { error = "<strong>Fecha Transacci&oacute;n</strong> incorrecta"; break; }
		//else if (!valAlfaNumerico(n.value)) { error = "No se permiten caractéres especiales en los campos"; break; }
	}
	
	//	detalles documento
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CodItem") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += n.value + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "StockActual") {
			var StockActual = new Number(setNumero(n.value));
			detalles += StockActual + ";char:td;";
		}
		else if (n.name == "CantidadPedida") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPendiente") {
			var CantidadPendiente = new Number(n.value);
			detalles += CantidadPendiente + ";char:td;";
		}
		else if (n.name == "CantidadRecibida") {
			var CantidadRecibida = new Number(setNumero(n.value));
			if (isNaN(CantidadRecibida)) { error = "Se encontraron <strong>Cantidades</strong> incorrectas en la ficha de <strong>Items</strong>."; break; }
			else if (CantidadRecibida > StockActual) { error = "La <strong>Cantidad por Despachar</strong> no puede ser mayor que el <strong>Stock Actual</strong>."; break; }
			else if (CantidadRecibida > CantidadPendiente) { error = "La <strong>Cantidad por Despachar</strong> no puede ser mayor que la <strong>Cantidad Pendiente</strong>."; break; }
			else if (CantidadRecibida <= 0) { error = "La <strong>Cantidad por Despachar</strong> no puede ser menor o igual a cero."; break; }
			detalles += CantidadRecibida + ";char:td;";
		}
		else if (n.name == "PrecioUnit") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "Total") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaCodDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroInterno") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaSecuencia") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=almacen&accion=despacho&"+post+"&detalles="+detalles,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					var registro = $("#CodOrganismo").val() + "." + $("#CodDocumento").val() + "." + partes[1] + "." + $("#TipoMovimiento").val();
					$("#imprimir").val(registro);
					
					var msj = "Se ha generado la Transacci&oacute;n <strong>Nro. " + $("#CodDocumento").val() + "-" + partes[2] + "</strong>";
					var funct = "document.getElementById('frmentrada').submit();";
					cajaModal(msj, "info", 400, funct);
				}
			}
		});
	}
	return false;
}

//	almacen (recepcion)
function almacen_recepcion(form) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	//	errores
	if ($("#CodOrganismo").val() == "" || $("#CodDependencia").val() == "" || $("#CodCentroCosto").val() == "" || $("#CodAlmacen").val() == "" || $("#FechaDocumento").val().trim() == "" || $("#CodTransaccion").val() == "" || $("#CodDocumento").val() == "" || $("#CodDocumentoReferencia").val() == "" || $("#NroDocumentoReferencia").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if (!valFecha($("#FechaDocumento").val())) error = "<strong>Fecha Transacci&oacute;n</strong> incorrecta";
	
	//	detalles documento
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CodItem") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += n.value + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "StockActual") {
			var StockActual = parseFloat(setNumero(n.value));
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CantidadPedida") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPendiente") {
			var CantidadPendiente = parseFloat(n.value);
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CantidadRecibida") {
			var CantidadRecibida = parseFloat(setNumero(n.value));
			if (isNaN(CantidadRecibida)) { error = "Se encontraron <strong>Cantidades</strong> incorrectas en la ficha de <strong>Items</strong>."; break; }
			else if (CantidadRecibida > CantidadPendiente) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser mayor que la <strong>Cantidad Pendiente</strong>."; break; }
			else if (CantidadRecibida <= 0) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser menor o igual a cero."; break; }
			detalles += CantidadRecibida + ";char:td;";
		}
		else if (n.name == "FlagExonerado") detalles += n.value + ";char:td;";
		else if (n.name == "PrecioUnit") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "Total") detalles += setNumero(n.value) + ";char:td;";
		
		else if (n.name == "CodUnidadCompra") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPedidaCompra") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CantidadCompra") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "PrecioUnitCompra") detalles += setNumero(n.value) + ";char:td;";
		
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaCodDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroInterno") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaSecuencia") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=almacen&accion=recepcion&"+post+"&detalles="+detalles,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					var registro = $("#CodOrganismo").val() + "." + $("#CodDocumento").val() + "." + partes[1] + "." + $("#TipoMovimiento").val();
					$("#imprimir").val(registro);
					
					var msj = "Se ha generado la Transacci&oacute;n <strong>Nro. " + $("#CodDocumento").val() + "-" + partes[2] + "</strong>";
					var funct = "document.getElementById('frmentrada').submit();";
					cajaModal(msj, "info", 400, funct);
				}
			}
		});
	}
	return false;
}

//	transaccion (almacen)
function transaccion_almacen(form, accion) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var TipoMovimiento = $("#TipoMovimiento").val();
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "CodAlmacen" || n.id == "FechaDocumento" || n.id == "DocumentoReferencia" || n.id == "CodTransaccion" || n.id == "CodDocumento" || n.id == "CodDocumentoReferencia" || n.id == "NroDocumentoReferencia") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valFecha(n.value) && n.id == "FechaDocumento") { error = "<strong>Fecha Transacci&oacute;n</strong> incorrecta"; break; }
	}
	
	//	detalles documento
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CodItem") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += n.value + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "StockActual") {
			var StockActual = new Number(setNumero(n.value));
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CantidadRecibida") {
			var CantidadRecibida = new Number(setNumero(n.value));
			if (isNaN(CantidadRecibida)) {
				error = "Se encontraron <strong>Cantidades</strong> incorrectas en la ficha de <strong>Items</strong>.";
				break;
			}
			else if (CantidadRecibida > StockActual && TipoMovimiento != "I") {
				error = "La <strong>Cantidad</strong> no puede ser mayor que el <strong>Stock Actual</strong>."; 
				break;
			}
			else if (CantidadRecibida <= 0) {
				error = "La <strong>Cantidad</strong> no puede ser menor o igual a cero.";
				break;
			}
			detalles += CantidadRecibida + ";char:td;";
		}
		else if (n.name == "PrecioUnit") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "Total") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaCodDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaSecuencia") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=transaccion_almacen&accion="+accion+"&"+post+"&detalles="+detalles,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					if (accion == "ejecutar") {
						var registro = $("#CodOrganismo").val() + "." + $("#CodDocumento").val() + "." + $("#NroDocumento").val() + "." + $("#TipoMovimiento").val();
						$("#imprimir").val(registro);
						
						var funct = "document.getElementById('frmentrada').submit();";
						var msj = "Se ha generado la Transacci&oacute;n <strong>Nro. "+$("#CodDocumento").val()+"-"+partes[1]+"</strong>";
						cajaModal(msj, "exito", 400, funct);
					} else form.submit();
				}
			}
		});
	}
	return false;
}

//	transaccion commodities (recepcion)
function transaccion_commodity_recepcion(form) {
	$(".div-progressbar").css("display", "block");
	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "CodAlmacen" || n.id == "FechaDocumento" || n.id == "CodTransaccion" || n.id == "CodDocumento" || n.id == "CodDocumentoReferencia" || n.id == "NroDocumentoReferencia" || n.id == "DocumentoReferencia") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valFecha(n.value) && n.id == "FechaDocumento") { error = "<strong>Fecha Transacci&oacute;n</strong> incorrecta"; break; }
	}
	//	detalles documento
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += changeUrl(n.value) + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPedida") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPendiente") {
			var CantidadPendiente = new Number(n.value);
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CantidadRecibida") {
			var CantidadRecibida = new Number(setNumero(n.value));
			if (isNaN(CantidadRecibida)) { error = "Se encontraron <strong>Cantidades</strong> incorrectas en la ficha de <strong>Items</strong>."; break; }
			else if (CantidadRecibida > CantidadPendiente) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser mayor que la <strong>Cantidad Pendiente</strong>."; break; }
			else if (CantidadRecibida <= 0) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser menor o igual a cero."; break; }
			detalles += CantidadRecibida + ";char:td;";
		}
		else if (n.name == "FlagExonerado") detalles += n.value + ";char:td;";
		else if (n.name == "PrecioUnit") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "Total") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CodUnidadCompra") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPedidaCompra") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CantidadCompra") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "PrecioUnitCompra") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CodClasificacion") detalles += n.value + ";char:td;";
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaCodDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroInterno") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaSecuencia") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	//	detalles activo
	var activos = "";
	if ($("#FlagActivoFijo").prop("checked")) {
		var frm_activos = document.getElementById("frm_activos");
		for(var i=0; n=frm_activos.elements[i]; i++) {
			if (n.name == "Secuencia") activos += n.value + ";char:td;";
			else if (n.name == "NroSecuencia") activos += n.value + ";char:td;";
			else if (n.name == "CommoditySub") activos += n.value + ";char:td;";
			else if (n.name == "Descripcion") activos += changeUrl(n.value) + ";char:td;";
			else if (n.name == "CodClasificacion") activos += n.value + ";char:td;";
			else if (n.name == "Monto") activos += n.value + ";char:td;";
			else if (n.name == "NroSerie") activos += n.value + ";char:td;";
			else if (n.name == "FechaIngreso") {
				if (n.value.trim() == "" || !valFecha(n.value)) {
					error = "Se encontraron <strong>Fechas de Ingreso Incorrectas</strong> en la ficha de <strong>Activos Asociados</strong>";
					break;
				}
				else activos += n.value + ";char:td;";
			}
			else if (n.name == "Modelo") activos += n.value + ";char:td;";
			else if (n.name == "CodBarra") activos += n.value + ";char:td;";
			else if (n.name == "CodUbicacion") {
				if (n.value.trim() == "") {
					error = "Se encontraron lineas sin <strong>Ubicaciones</strong> en la ficha de <strong>Activos Asociados</strong>";
					break;
				}
				else activos += n.value + ";char:td;";
			}
			else if (n.name == "CodCentroCosto") {
				if (n.value.trim() == "") {
					error = "Se encontraron lineas sin <strong>Centro de Costo</strong> en la ficha de <strong>Activos Asociados</strong>";
					break;
				}
				else activos += n.value + ";char:td;";
			}
			else if (n.name == "NroPlaca") activos += n.value + ";char:td;";
			else if (n.name == "CodMarca") activos += n.value + ";char:td;";
			else if (n.name == "Color") activos += n.value + ";char:tr;";
		}
		var len = activos.length; len-=9;
		activos = activos.substr(0, len);
	}
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=almacen-commodity&accion=recepcion&"+post+"&detalles="+detalles+"&activos="+activos,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					var registro = $("#CodOrganismo").val() + "." + $("#CodDocumento").val() + "." + partes[1] + "." + $("#TipoMovimiento").val();
					$("#imprimir").val(registro);
					
					var msj = "Se ha generado la Transacci&oacute;n <strong>Nro. " + $("#CodDocumento").val() + "-" + partes[2] + "</strong>";
					var funct = "document.getElementById('frmentrada').submit();";
					cajaModal(msj, "exito", 400, funct);
				}
			}
		});
	}
	return false;
}

//	transaccion commodities (despacho)
function transaccion_commodity_despacho(form) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "CodAlmacen" || n.id == "FechaDocumento" || n.id == "CodTransaccion" || n.id == "CodDocumento") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valFecha(n.value) && n.id == "FechaDocumento") { error = "<strong>Fecha Transacci&oacute;n</strong> incorrecta"; break; }
	}
	
	//	detalles documento
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += n.value + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "StockActual") {
			var StockActual = new Number(n.value);
			detalles += StockActual + ";char:td;";
		}
		else if (n.name == "CantidadPedida") {
			var CantidadPedida = new Number(n.value);
			detalles += CantidadPedida + ";char:td;";
		}
		else if (n.name == "CantidadPendiente") {
			var CantidadPendiente = new Number(n.value);
			detalles += CantidadPendiente + ";char:td;";
		}
		else if (n.name == "CantidadRecibida") {
			var CantidadRecibida = new Number(setNumero(n.value));
			if (isNaN(CantidadRecibida)) { error = "Se encontraron <strong>Cantidades</strong> incorrectas en la ficha de <strong>Items</strong>."; break; }
			else if (CantidadRecibida > CantidadPendiente) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser mayor que la <strong>Cantidad Pendiente</strong>."; break; }
			else if (CantidadRecibida <= 0) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser menor o igual a cero."; break; }
			detalles += CantidadRecibida + ";char:td;";
		}
		else if (n.name == "PrecioUnit") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "Total") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaCodDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroInterno") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaSecuencia") detalles += n.value + ";char:td;";
		else if (n.name == "CodRequerimiento") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=almacen-commodity&accion=despacho&"+post+"&detalles="+detalles,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					var registro = $("#CodOrganismo").val() + "." + $("#CodDocumento").val() + "." + partes[1] + "." + $("#TipoMovimiento").val();
					$("#imprimir").val(registro);
					
					var msj = "Se ha generado la Transacci&oacute;n <strong>Nro. " + $("#CodDocumento").val() + "-" + partes[2] + "</strong>";
					var funct = "document.getElementById('frmentrada').submit();";
					cajaModal(msj, "exito", 400, funct);
				}
			}
		});
	}
	return false;
}

//	transaccion (commodities)
function transaccion_commodity(form, accion) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var TipoMovimiento = $("#TipoMovimiento").val();
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "CodAlmacen" || n.id == "FechaDocumento" || n.id == "DocumentoReferencia" || n.id == "CodTransaccion" || n.id == "CodDocumento" || n.id == "CodDocumentoReferencia" || n.id == "NroDocumentoReferencia") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valFecha(n.value) && n.id == "FechaDocumento") { error = "<strong>Fecha Transacci&oacute;n</strong> incorrecta"; break; }
	}
	
	//	detalles documento
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += n.value + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "StockActual") {
			var StockActual = new Number(setNumero(n.value));
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CantidadRecibida") {
			var CantidadRecibida = new Number(setNumero(n.value));
			if (accion != 'anular') {
				if (isNaN(CantidadRecibida)) {
					error = "Se encontraron <strong>Cantidades</strong> incorrectas en la ficha de <strong>Items</strong>.";
					break;
				}
				else if (CantidadRecibida > StockActual && TipoMovimiento != "I") {
					error = "La <strong>Cantidad</strong> no puede ser mayor que el <strong>Stock Actual</strong>."; 
					break;
				}
				else if (CantidadRecibida <= 0) {
					error = "La <strong>Cantidad</strong> no puede ser menor o igual a cero.";
					break;
				}
			}
			detalles += CantidadRecibida + ";char:td;";
		}
		else if (n.name == "PrecioUnit") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "Total") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaCodDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaSecuencia") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=transaccion_commodity&accion="+accion+"&"+post+"&detalles="+detalles,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					if (accion == "ejecutar") {
						var registro = $("#CodOrganismo").val() + "." + $("#CodDocumento").val() + "." + $("#NroDocumento").val() + "." + $("#TipoMovimiento").val();
						$("#imprimir").val(registro);
						
						var funct = "document.getElementById('frmentrada').submit();";
						var msj = "Se ha generado la Transacci&oacute;n <strong>Nro. "+$("#CodDocumento").val()+"-"+partes[2]+"</strong>";
						cajaModal(msj, "exito", 400, funct);
					} else form.submit();
				}
			}
		});
	}
	return false;
}

//	transaccion caja chica (recepcion)
function transaccion_cajachica_recepcion(form) {
	$(".div-progressbar").css("display", "block");

	if (document.getElementById("FlagActivoFijo").checked) var FlagActivoFijo = "S"; else var FlagActivoFijo = "N";
	if (document.getElementById("FlagManual").checked) var FlagManual = "S"; else var FlagManual = "N";
	
	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "CodAlmacen" || n.id == "FechaDocumento" || n.id == "CodTransaccion" || n.id == "CodDocumento" || n.id == "CodDocumentoReferencia" || n.id == "NroDocumentoReferencia" || n.id == "DocumentoReferencia" || (n.id == "CodUbicacion" && FlagActivoFijo == "S")) && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valFecha(n.value) && n.id == "FechaDocumento") { error = "<strong>Fecha Transacci&oacute;n</strong> incorrecta"; break; }
	}
	
	//	detalles documento
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CodItem") detalles += n.value + ";char:td;";
		else if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += changeUrl(n.value) + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPedida") {
			var CantidadPedida = new Number(setNumero(n.value));
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CantidadRecibida") {
			var CantidadRecibida = new Number(setNumero(n.value));
			if (isNaN(CantidadRecibida)) { error = "Se encontraron <strong>Cantidades</strong> incorrectas en la ficha de <strong>Items</strong>."; break; }
			else if (CantidadRecibida > CantidadPedida) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser mayor que la <strong>Cantidad Pedida</strong>."; break; }
			else if (CantidadRecibida <= 0) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser menor o igual a cero."; break; }
			detalles += CantidadRecibida + ";char:td;";
		}
		else if (n.name == "PrecioUnit") {
			var PrecioUnit = new Number(setNumero(n.value));
			if (PrecioUnit <= 0) { error = "El Precio Unitario no puede ser menor o igual a cero. (Valorizaci&oacute;n Manual Activo)"; break; }
			detalles += PrecioUnit + ";char:td;";
		}
		else if (n.name == "Total") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaCodDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroInterno") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaSecuencia") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	//	detalles activo
	var activos = "";
	var frm_activos = document.getElementById("frm_activos");
	for(var i=0; n=frm_activos.elements[i]; i++) {
		if (n.name == "Secuencia") activos += n.value + ";char:td;";
		else if (n.name == "NroSecuencia") activos += n.value + ";char:td;";
		else if (n.name == "CommoditySub") activos += n.value + ";char:td;";
		else if (n.name == "Descripcion") activos += changeUrl(n.value) + ";char:td;";
		else if (n.name == "CodClasificacion") activos += n.value + ";char:td;";
		else if (n.name == "Monto") activos += n.value + ";char:td;";
		else if (n.name == "NroSerie") activos += n.value + ";char:td;";
		else if (n.name == "FechaIngreso") {
			if (n.value.trim() == "" || !valFecha(n.value)) {
				error = "Se encontraron <strong>Fechas de Ingreso Incorrectas</strong> en la ficha de <strong>Activos Asociados</strong>";
				break;
			}
			else activos += n.value + ";char:td;";
		}
		else if (n.name == "Modelo") activos += n.value + ";char:td;";
		else if (n.name == "CodBarra") activos += n.value + ";char:td;";
		else if (n.name == "CodUbicacion") activos += n.value + ";char:td;";
		else if (n.name == "CodCentroCosto") activos += n.value + ";char:td;";
		else if (n.name == "NroPlaca") activos += n.value + ";char:td;";
		else if (n.name == "CodMarca") activos += n.value + ";char:td;";
		else if (n.name == "Color") activos += n.value + ";char:tr;";
	}
	var len = activos.length; len-=9;
	activos = activos.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=transaccion-cajachica&accion=recepcion&"+post+"&detalles="+detalles+"&activos="+activos,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					var registro = $("#CodOrganismo").val() + "." + $("#CodDocumento").val() + "." + partes[1] + "." + $("#TipoMovimiento").val();
					$("#imprimir").val(registro);
					
					var funct = "document.getElementById('frmentrada').submit();";
					cajaModal(partes[1], "exito", 400, funct);
				}
			}
		});
	}
	return false;
}

//	transaccion caja chica (despacho)
function transaccion_cajachica_despacho(form) {
	$(".div-progressbar").css("display", "block");

	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "CodAlmacen" || n.id == "FechaDocumento" || n.id == "CodTransaccion" || n.id == "CodDocumento" || n.id == "CodDocumentoReferencia" || n.id == "NroDocumentoReferencia" || n.id == "DocumentoReferencia") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (!valFecha(n.value) && n.id == "FechaDocumento") { error = "<strong>Fecha Transacci&oacute;n</strong> incorrecta"; break; }
	}
	
	//	detalles documento
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CodItem") detalles += n.value + ";char:td;";
		else if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += changeUrl(n.value) + ";char:td;";
		else if (n.name == "CodUnidad") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPedida") {
			var CantidadPedida = new Number(setNumero(n.value));
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CantidadRecibida") {
			var CantidadRecibida = new Number(setNumero(n.value));
			if (isNaN(CantidadRecibida)) { error = "Se encontraron <strong>Cantidades</strong> incorrectas en la ficha de <strong>Items</strong>."; break; }
			else if (CantidadRecibida > CantidadPedida) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser mayor que la <strong>Cantidad Pedida</strong>."; break; }
			else if (CantidadRecibida <= 0) { error = "La <strong>Cantidad por Recepcionar</strong> no puede ser menor o igual a cero."; break; }
			detalles += CantidadRecibida + ";char:td;";
		}
		else if (n.name == "PrecioUnit") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "Total") detalles += setNumero(n.value) + ";char:td;";
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaCodDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroInterno") detalles += n.value + ";char:td;";
		else if (n.name == "ReferenciaSecuencia") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=transaccion-cajachica&accion=despacho&"+post+"&detalles="+detalles,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					var registro = $("#CodOrganismo").val() + "." + $("#CodDocumento").val() + "." + partes[1] + "." + $("#TipoMovimiento").val();
					$("#imprimir").val(registro);
					
					var funct = "document.getElementById('frmentrada').submit();";
					cajaModal(partes[1], "exito", 400, funct);
				}
			}
		});
	}
	return false;
}

//	facturacion de activos
function facturacion_activos(form) {
	$(".div-progressbar").css("display", "block");
	
	//	valido
	var error = "";
	if ($("#CodTipoDocumento").val() == "" || $("#NroDocumento").val() == "") error = "Debe seleccionar la obligaci&oacute;n relacionada";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=facturacion_activos&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	invitar a cotizar a proveedores
function cotizaciones_items_invitar_proveedores(form) {
	$(".div-progressbar").css("display", "block");
	//	valido
	var error = "";
	if ($("#FechaLimite").val().trim() == "") error = "Debe ingresar la Fecha L&iacute;mite de Cotizaci&oacute;n";
	else if (!valFecha($("#FechaLimite").val())) error = "Formato de Fecha L&iacute;mite de Cotizaci&oacute;n Incorrecta";
	if (error == "") {
		//	detalles
		var detalles_proveedores = "";
		var frm_proveedores = document.getElementById("frm_proveedores");
		for(var i=0; n=frm_proveedores.elements[i]; i++) {
			if (n.name == "CodPersona") detalles_proveedores += n.value + ";char:td;";
			else if (n.name == "NomPersona") detalles_proveedores += changeUrl(n.value) + ";char:td;";
			else if (n.name == "CodFormaPago") detalles_proveedores += n.value + ";char:tr;";
		}
		if (detalles_proveedores == "") error = "Debe ingresar por lo menos un Proveedor";
		var len = detalles_proveedores.length; len-=9;
		detalles_proveedores = detalles_proveedores.substr(0, len);
	}
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=cotizaciones&accion=cotizaciones_items_invitar_proveedores&"+post+"&detalles_proveedores="+detalles_proveedores,
			async: false,
			success: function(resp) {
				var datos = resp.split("|");
				if (datos[0].trim() != "") cajaModal(datos[0], "error", 400);
				else {
					$("#frmentrada").attr("action", "gehen.php?anz=lg_cotizaciones_items_invitar_lista&imprimir=lg_cotizaciones_invitacion_pdf&Numero="+datos[1]);
					form.submit();
				}
			}
		});
	}
	return false;
}

//	proceso de cotizacion x items
function cotizaciones_items_invitar_cotizar_borrar() {
	var id = $("#sel_proveedores").val();
	var partes = id.split("_");
	var borrar_proveedores = $("#borrar_proveedores").val();
	if (borrar_proveedores != "") borrar_proveedores += "|" + partes[1];
	else borrar_proveedores += partes[1];
	$("#borrar_proveedores").val(borrar_proveedores);
}
function cotizaciones_items_invitar_cotizar_validar(form) {
	//	detalles
	var sw = false;
	var detalles_proveedores = "";
	var frm_proveedores = document.getElementById("frm_proveedores");
	for(var i=0; n=frm_proveedores.elements[i]; i++) {
		if (n.name == "NomProveedor") {
			var NomProveedor = n.value;
		}
		else if (n.name == "FlagAsignado") {
			if (n.checked) var FlagAsignado = "S"; else var FlagAsignado = "N";
		}
		else if (n.name == "FlagMejorPrecio") {
			if (n.checked) var FlagMejorPrecio = "S"; else var FlagMejorPrecio = "N";
			if (FlagAsignado == "S" && FlagMejorPrecio == "N") { sw = true; break; }
		}
	}
	if (sw) $("#observaciones-form").dialog("open");
	else cotizaciones_items_invitar_cotizar(form);
	return false;
}
function cotizaciones_items_invitar_cotizar(form) {
	$(".div-progressbar").css("display", "block");
	
	//	valido
	var error = "";
	//	detalles
	var detalles_proveedores = "";
	var frm_proveedores = document.getElementById("frm_proveedores");
	for(var i=0; n=frm_proveedores.elements[i]; i++) {
		if (n.name == "CotizacionSecuencia") detalles_proveedores += n.value + ";char:td;";
		else if (n.name == "CodProveedor") {
			var CodProveedor = n.value;
			detalles_proveedores += n.value + ";char:td;";
		}
		else if (n.name == "NomProveedor") {
			var NomProveedor = n.value;
			detalles_proveedores += changeUrl(n.value) + ";char:td;";
		}
		else if (n.name == "FlagAsignado") {
			if (n.checked) var FlagAsignado = "S"; else var FlagAsignado = "N";
			detalles_proveedores += FlagAsignado + ";char:td;";
		}
		else if (n.name == "CodUnidad") detalles_proveedores += n.value + ";char:td;";
		else if (n.name == "Cantidad") {
			var Cantidad = setNumero(n.value);
			if (isNaN(Cantidad)) { error = "Formato de Cantidad Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += Cantidad + ";char:td;";
		}
		else if (n.name == "CodUnidadCompra") detalles_proveedores += n.value + ";char:td;";
		else if (n.name == "CantidadCompra") {
			var CantidadCompra = setNumero(n.value);
			if (isNaN(CantidadCompra)) { error = "Formato de Cantidad de Compra Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += CantidadCompra + ";char:td;";
		}
		else if (n.name == "PrecioUnitInicio") {
			var PrecioUnitInicio = setNumero(n.value);
			if (isNaN(PrecioUnitInicio)) { error = "Formato de Precio Unitario Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += PrecioUnitInicio + ";char:td;";
		}
		else if (n.name == "FlagExonerado") {
			if (n.checked) var FlagExonerado = "S"; else var FlagExonerado = "N";
			detalles_proveedores += FlagExonerado + ";char:td;";
		}
		else if (n.name == "PrecioUnitInicioIva") {
			var PrecioUnitInicioIva = setNumero(n.value);
			if (isNaN(PrecioUnitInicioIva)) { error = "Formato de Precio Unitario con Impuesto Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += PrecioUnitInicioIva + ";char:td;";
		}
		else if (n.name == "DescuentoPorcentaje") {
			var DescuentoPorcentaje = setNumero(n.value);
			if (isNaN(DescuentoPorcentaje)) { error = "Formato de % Descuento Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += DescuentoPorcentaje + ";char:td;";
		}
		else if (n.name == "DescuentoFijo") {
			var DescuentoFijo = setNumero(n.value);
			if (isNaN(DescuentoFijo)) { error = "Formato de Monto Descuento Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += DescuentoFijo + ";char:td;";
		}
		else if (n.name == "PrecioUnitIva") {
			var PrecioUnitIva = setNumero(n.value);
			if (isNaN(PrecioUnitIva)) { error = "Formato de Precio Unitario Final Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += PrecioUnitIva + ";char:td;";
		}
		else if (n.name == "Total") {
			var Total = setNumero(n.value);
			if (isNaN(Total)) { error = "Formato del Total Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += Total + ";char:td;";
		}
		else if (n.name == "PrecioUnitFinal") {
			var PrecioUnitFinal = setNumero(n.value);
			if (isNaN(PrecioUnitFinal)) { error = "Formato del Monto a Comparar Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += PrecioUnitFinal + ";char:td;";
		}
		else if (n.name == "FlagMejorPrecio") {
			if (n.checked) var FlagMejorPrecio = "S"; else var FlagMejorPrecio = "N";
			detalles_proveedores += FlagMejorPrecio + ";char:td;";
		}
		else if (n.name == "CodFormaPago") detalles_proveedores += n.value + ";char:td;";
		else if (n.name == "FechaInvitacion") {
			if (!valFecha(n.value)) { error = "Formato de Fecha de Invitaci&oacute;n Incorrecta"; break; }
			else detalles_proveedores += formatFechaAMD(n.value) + ";char:td;";
		}
		else if (n.name == "FechaEntrega") {
			if (!valFecha(n.value)) { error = "Formato de Fecha de Entrega Incorrecta"; break; }
			else detalles_proveedores += formatFechaAMD(n.value) + ";char:td;";
		}
		else if (n.name == "FechaRecepcion") {
			if (!valFecha(n.value)) { error = "Formato de Fecha de Recepci&oacute;n Incorrecta"; break; }
			else detalles_proveedores += formatFechaAMD(n.value) + ";char:td;";
		}
		else if (n.name == "FechaLimite") {
			if (!valFecha(n.value)) { error = "Formato de Fecha Limite Incorrecta"; break; }
			else detalles_proveedores += formatFechaAMD(n.value) + ";char:td;";
		}
		else if (n.name == "Condiciones") detalles_proveedores += changeUrl(n.value) + ";char:td;";
		else if (n.name == "Observaciones") {
			var Observaciones = n.value.trim();
			detalles_proveedores += changeUrl(n.value) + ";char:td;";
		}
		else if (n.name == "DiasEntrega") {
			var DiasEntrega = setNumero(n.value);
			if (isNaN(DiasEntrega)) { error = "Formato de Dias de Entrega Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += DiasEntrega + ";char:td;";
		}
		else if (n.name == "ValidezOferta") {
			var ValidezOferta = setNumero(n.value);
			if (isNaN(ValidezOferta)) { error = "Formato de Dias de Entrega Incorrecto para el Proveedor <strong>"+NomProveedor+"</strong>"; break; }
			detalles_proveedores += ValidezOferta + ";char:td;";
		}
		else if (n.name == "NumeroCotizacion") detalles_proveedores += n.value + ";char:td;";
		else if (n.name == "FechaDocumento") {
			if (!valFecha(n.value)) { error = "Formato de Fecha de Cotizaci&oacute;n Incorrecta"; break; }
			else detalles_proveedores += formatFechaAMD(n.value) + ";char:tr;";
		}
	}
	var len = detalles_proveedores.length; len-=9;
	detalles_proveedores = detalles_proveedores.substr(0, len);
	if (detalles_proveedores == "") error = "Debe insertar por lo menos un Proveedor";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		post = post + "&Observaciones=("+$("#Observaciones").val()+")";
		post = post + "&borrar_proveedores="+$("#borrar_proveedores").val();
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=cotizaciones&accion=cotizaciones_items_invitar_cotizar&"+post+"&detalles_proveedores="+detalles_proveedores,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	proceso de cotizacion x proveedores
function cotizaciones_proveedores_invitar_cotizar(form) {
	$(".div-progressbar").css("display", "block");
	
	//	valido
	var error = "";
	//	detalles
	var detalles_items = "";
	var frm_items = document.getElementById("frm_items");
	for(var i=0; n=frm_items.elements[i]; i++) {
		if (n.name == "CotizacionSecuencia") detalles_items += n.value + ";char:td;";
		else if (n.name == "Codigo") {
			var Codigo = n.value;
		}
		else if (n.name == "CodUnidad") detalles_items += n.value + ";char:td;";
		else if (n.name == "Cantidad") {
			var Cantidad = setNumero(n.value);
			if (isNaN(Cantidad)) { error = "Formato de Cantidad Incorrecto para el Item <strong>"+Codigo+"</strong>"; break; }
			detalles_items += Cantidad + ";char:td;";
		}
		else if (n.name == "CodUnidadCompra") detalles_items += n.value + ";char:td;";
		else if (n.name == "CantidadCompra") {
			var CantidadCompra = setNumero(n.value);
			if (isNaN(CantidadCompra)) { error = "Formato de Cantidad de Compra Incorrecto para el Item <strong>"+Codigo+"</strong>"; break; }
			detalles_items += CantidadCompra + ";char:td;";
		}
		else if (n.name == "PrecioUnitInicio") {
			var PrecioUnitInicio = setNumero(n.value);
			if (isNaN(PrecioUnitInicio)) { error = "Formato de Precio Unitario Incorrecto para el Item <strong>"+Codigo+"</strong>"; break; }
			detalles_items += PrecioUnitInicio + ";char:td;";
		}
		else if (n.name == "FlagAsignado") {
			if (n.checked) var FlagAsignado = "S"; else var FlagAsignado = "N";
			detalles_items += FlagAsignado + ";char:td;";
		}
		else if (n.name == "FlagExonerado") {
			if (n.checked) var FlagExonerado = "S"; else var FlagExonerado = "N";
			detalles_items += FlagExonerado + ";char:td;";
		}
		else if (n.name == "PrecioUnitInicioIva") {
			var PrecioUnitInicioIva = setNumero(n.value);
			if (isNaN(PrecioUnitInicioIva)) { error = "Formato de Precio Unitario con Impuesto Item para el Proveedor <strong>"+Codigo+"</strong>"; break; }
			detalles_items += PrecioUnitInicioIva + ";char:td;";
		}
		else if (n.name == "DescuentoPorcentaje") {
			var DescuentoPorcentaje = setNumero(n.value);
			if (isNaN(DescuentoPorcentaje)) { error = "Formato de % Descuento Incorrecto para el Item <strong>"+Codigo+"</strong>"; break; }
			detalles_items += DescuentoPorcentaje + ";char:td;";
		}
		else if (n.name == "DescuentoFijo") {
			var DescuentoFijo = setNumero(n.value);
			if (isNaN(DescuentoFijo)) { error = "Formato de Monto Descuento Incorrecto para el Item <strong>"+Codigo+"</strong>"; break; }
			detalles_items += DescuentoFijo + ";char:td;";
		}
		else if (n.name == "PrecioUnit") {
			var PrecioUnit = setNumero(n.value);
			if (isNaN(PrecioUnit)) { error = "Formato de Precio Unitario s/Imp. c/Desc Incorrecto para el Item <strong>"+Codigo+"</strong>"; break; }
			detalles_items += PrecioUnit + ";char:td;";
		}
		else if (n.name == "PrecioUnitIva") {
			var PrecioUnitIva = setNumero(n.value);
			if (isNaN(PrecioUnitIva)) { error = "Formato de Precio Unitario c/Imp. c/Desc Incorrecto para el Item <strong>"+Codigo+"</strong>"; break; }
			detalles_items += PrecioUnitIva + ";char:td;";
		}
		else if (n.name == "Total") {
			var Total = setNumero(n.value);
			if (isNaN(Total)) { error = "Formato del Total Incorrecto para el Item <strong>"+Codigo+"</strong>"; break; }
			detalles_items += Total + ";char:td;";
		}
		else if (n.name == "Observaciones") {
			var Observaciones = n.value.trim();
			detalles_items += changeUrl(n.value) + ";char:tr;";
		}
	}
	var len = detalles_items.length; len-=9;
	detalles_items = detalles_items.substr(0, len);
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=cotizaciones&accion=cotizaciones_proveedores_invitar_cotizar&"+post+"&detalles_items="+detalles_items,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	cierre mensual
function cierre_mensual(form) {
	$(".div-progressbar").css("display", "block");
	
	//	valido
	if ($("#CodOrganismo").val() == "" || $("#Periodo").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if (!valPeriodo($("#Periodo").val())) error = "Periodo Incorrecto";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=cierre_mensual&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else {
					
				}
			}
		});
	}
	return false;
}

function orden_servicio(form, accion) {
	$(".div-progressbar").css("display", "block");
	//	formulario
	var Estado = $("#Estado").val();
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		//	errores
		if ((n.id == "CodOrganismo" || n.id == "CodDependencia" || n.id == "CodCentroCosto" || n.id == "CodProveedor" || n.id == "CodTipoServicio" || n.id == "CodFormaPago" || n.id == "CodTipoPago" || n.id == "PlazoEntrega" || n.id == "FechaEntrega" || n.id == "DiasPago" || n.id == "FechaValidoDesde" || n.id == "FechaValidoHasta") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"; break; }
		else if (isNaN(n.value) && n.id == "PlazoEntrega") { error = "<strong>Plazo de entrega</strong> incorrecto"; break; }
		else if (isNaN(n.value) && n.id == "DiasPago") { error = "<strong>Dias para pagar</strong> incorrecto"; break; }
		else if (!valFecha(n.value) && n.id == "FechaPrometida") { error = "<strong>Fecha Entrega</strong> incorrecta"; break; }
		else if (!valFecha(n.value) && n.id == "FechaValidoDesde") { error = "<strong>Fecha Desde</strong> incorrecta"; break; }
		else if (!valFecha(n.value) && n.id == "FechaValidoHasta") { error = "<strong>Fecha Hasta</strong> incorrecta"; break; }
	}
	//	detalles
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles += changeUrl(n.value.trim()) + ";char:td;";
		else if (n.name == "CantidadPedida") {
			var CantidadPedida = parseFloat(setNumero(n.value));
			if (isNaN(CantidadPedida) || CantidadPedida <= 0) { error = "Se encontraron <strong>Cantidades</strong> en la ficha de <strong>Items/Commodities</strong> incorrectas"; break; }
			else detalles += CantidadPedida + ";char:td;";
		}
		else if (n.name == "CodUnidadRec") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadRec") detalles += n.value + ";char:td;";
		else if (n.name == "PrecioUnit") {
			var PrecioUnit = parseFloat(setNumero(n.value));
			if (isNaN(PrecioUnit) || PrecioUnit <= 0) { error = "Se encontraron <strong>Precios</strong> en la ficha de <strong>Items/Commodities</strong> incorrectos"; break; }
			else detalles += PrecioUnit + ";char:td;";
		}
		else if (n.name == "FlagExonerado") {
			if (n.checked) detalles += "S;char:td;";
			else detalles += "N;char:td;";
		}
		else if (n.name == "Total") {
			var Total = parseFloat(setNumero(n.value));
			if (isNaN(Total) || Total <= 0) { error = "Se encontraron <strong>Totales</strong> en la ficha de <strong>Items/Commodities</strong> incorrectos"; break; }
			else detalles += Total + ";char:td;";
		}
		else if (n.name == "detallesCategoriaProg") detalles += n.value + ";char:td;";
		else if (n.name == "detallesEjercicio") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCodPresupuesto") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCodFuente") detalles += n.value + ";char:td;";
		else if (n.name == "FechaEsperadaTermino") {
			if (!valFecha(n.value)) { error = "Se encontraron <strong>Fecha Plan.</strong> en la ficha de <strong>Items/Commodities</strong> incorrectas"; break; }
			else detalles += n.value + ";char:td;";
		}
		else if (n.name == "FechaTermino") {
			if (!valFecha(n.value)) { error = "Se encontraron <strong>Fecha Real</strong> en la ficha de <strong>Items/Commodities</strong> incorrectas"; break; }
			else detalles += n.value + ";char:td;";
		}
		else if (n.name == "CodCentroCosto") detalles += n.value + ";char:td;";
		else if (n.name == "NroActivo") detalles += n.value + ";char:td;";
		else if (n.name == "FlagTerminado") {
			if (n.checked) detalles += "S;char:td;";
			else detalles += "N;char:td;";
		}
		else if (n.name == "cod_partida") detalles += n.value + ";char:td;";
		else if (n.name == "CodCuenta") detalles += n.value + ";char:td;";
		else if (n.name == "CodCuentaPub20") detalles += n.value + ";char:td;";
		else if (n.name == "Comentarios") detalles += changeUrl(n.value) + ";char:td;";
		else if (n.name == "CodRequerimiento") detalles += n.value + ";char:td;";
		else if (n.name == "Secuencia") detalles += n.value + ";char:td;";
		else if (n.name == "CotizacionSecuencia") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadRequerimiento") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	//	detalles distribucion
	var detalles_partida = "";
	var frm_partidas = document.getElementById("frm_partidas");
	for(var i=0; n=frm_partidas.elements[i]; i++) {
		if (n.name == "cod_partida") {
			var _cod_partida = n.value;
			detalles_partida += n.value + ";char:td;";
		}
		else if (n.name == "CodCuenta") detalles_partida += n.value + ";char:td;";
		else if (n.name == "CodCuentaPub20") detalles_partida += n.value + ";char:td;";
		else if (n.name == "Monto") {
			var _Monto = new Number(n.value);
			detalles_partida += n.value + ";char:td;";
		}
		else if (n.name == "MontoDisponible") {
			var _MontoDisponible = new Number(n.value);
			if (_Monto > _MontoDisponible) { error = "Se encontro la partida <strong>"+_cod_partida+"</strong> sin Disponibilidad Presupuestaria"; break; }
			else detalles_partida += n.value + ";char:tr;";
		}
	}
	var len = detalles_partida.length; len-=9;
	detalles_partida = detalles_partida.substr(0, len);
	//	valido errores
	if (detalles == "") error = "Debe ingresar por lo menos un articulo en la ficha de <strong>Items/Commodities</strong>";
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		$.ajax({
			type: "POST",
			url: "lg_orden_servicio_ajax.php",
			data: "modulo=orden_servicio&accion="+accion+"&"+post+"&detalles="+detalles+"&detalles_partida="+detalles_partida,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");				
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					if (accion == "revisar") {
						var funct = "document.getElementById('frmentrada').submit();";
						cajaModal(partes[1], "exito", 400, funct);
					}
					else form.submit();
				}
			}
		});
	}
	return false;
}