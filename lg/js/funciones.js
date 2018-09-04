// JavaScript Document

//	calculos los montos de la orden a partir de los detalles
function setMontosOrdenCompra(frm_detalles) {
	//	datos generales
	var FactorImpuesto = $("#FactorImpuesto").val();
	
	//	detalles
	var MontoAfecto = new Number(0.00);
	var MontoNoAfecto = new Number(0.00);
	var MontoIGV = new Number(0.00);
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CantidadPedida") var CantidadPedida = new Number(setNumero(n.value));
		else if (n.name == "PrecioUnit") var PrecioUnit = new Number(setNumero(n.value));
		else if (n.name == "DescuentoPorcentaje") var DescuentoPorcentaje = new Number(setNumero(n.value));
		else if (n.name == "DescuentoFijo") var DescuentoFijo = new Number(setNumero(n.value));
		else if (n.name == "FlagExonerado") {
			if (n.checked) var FlagExonerado = "S"; else var FlagExonerado = "N";
		}
		else if (n.name == "PrecioUnitTotal") {
			var MontoDescuentoPorcentaje = PrecioUnit * DescuentoPorcentaje / 100;
			var MontoDescuentoFijo = DescuentoFijo;
			var PrecioUnitTotal = PrecioUnit - MontoDescuentoPorcentaje - MontoDescuentoFijo;
			if (FlagExonerado == "S") {
				var Impuesto = new Number(0.00);
				MontoNoAfecto += (PrecioUnitTotal  * CantidadPedida);
			}
			else {
				var Impuesto = new Number(PrecioUnitTotal * FactorImpuesto / 100);
				MontoAfecto += (PrecioUnitTotal * CantidadPedida);
			}
			PrecioUnitTotal = PrecioUnitTotal + Impuesto;
			n.value = setNumeroFormato(PrecioUnitTotal, 2, '.', ',');
		}
		else if (n.name == "Total") {
			var Total = new Number(PrecioUnitTotal * CantidadPedida);
			n.value = setNumeroFormato(Total, 2, '.', ',');
		}
	}
	var MontoIGV = MontoAfecto * FactorImpuesto / 100;
	var MontoBruto = new Number(MontoAfecto + MontoNoAfecto);
	var MontoTotal = MontoBruto + MontoIGV;
	
	//	totales
	$("#MontoAfecto").val(setNumeroFormato(MontoAfecto, 2, '.', ','));
	$("#MontoNoAfecto").val(setNumeroFormato(MontoNoAfecto, 2, '.', ','));
	$("#MontoBruto").val(setNumeroFormato(MontoBruto, 2, '.', ','));
	$("#MontoIGV").val(setNumeroFormato(MontoIGV, 2, '.', ','));
	$("#MontoTotal").val(setNumeroFormato(MontoTotal, 2, '.', ','));
	$("#MontoPendiente").val(setNumeroFormato(MontoTotal, 2, '.', ','));
	mostrarTabDistribucionOrden();
}

//	FUNCION PARA ELIMINAR UNA LINEA DE UNA LISTA (TR EN TABLE)
function quitarLineaOrdenCompra(boton, detalle, form) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- detalle	-> sufijo de los campos de la lista
	*/
	boton.disabled = true;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;	
	var lista = "lista_" + detalle;
	if (document.getElementById(sel).value == "") cajaModal("Debe seleccionar una linea", "error", 400);
	else {
		var candetalle = new Number(document.getElementById(can).value); candetalle--;
		document.getElementById(can).value = candetalle;
		var seldetalle = document.getElementById(sel).value;
		var listaDetalles = document.getElementById(lista);
		var tr = document.getElementById(seldetalle);
		listaDetalles.removeChild(tr);
		document.getElementById(sel).value = "";
		setMontosOrdenCompra(form);
		$("#TipoClasificacion").val("");
	}
	boton.disabled = false;
}

//	
function verDisponibilidadPresupuestaria() {
	var Ejercicio = $("#Ejercicio").val();
	var CodPresupuesto = $("#CodPresupuesto").val();
	var CodFuente = $("#CodFuente").val();
	var CodOrganismo = $("#CodOrganismo").val();
	var CodProveedor = $("#CodProveedor").val();
	var Anio = $("#Anio").val();
	var NroOrden = $("#NroOrden").val();
	var Estado = $("#Estado").val();
	var FactorImpuesto = $("#FactorImpuesto").val();
	//	detalles
	var detalles = "";
	var frm_detalles = document.getElementById("frm_detalles");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CodItem") detalles += n.value + ";char:td;";
		else if (n.name == "CommoditySub") detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPedida") {
			var CantidadPedida = parseFloat(setNumero(n.value));
			if (isNaN(CantidadPedida) || CantidadPedida <= 0) CantidadPedida = 0;
			detalles += CantidadPedida + ";char:td;";
		}
		else if (n.name == "PrecioUnit") {
			var PrecioUnit = parseFloat(setNumero(n.value));
			if (isNaN(PrecioUnit) || PrecioUnit <= 0) PrecioUnit = 0;
			detalles += PrecioUnit + ";char:td;";
		}
		else if (n.name == "FlagExonerado") {
			if (n.checked) detalles += "S;char:td;"; else detalles += "N;char:td;";
		}
		else if (n.name == "detallesEjercicio") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCodPresupuesto") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCodFuente") detalles += n.value + ";char:td;";
		else if (n.name == "cod_partida") detalles += n.value + ";char:tr;";
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
		else if (n.name == "Monto") detalles_partida += n.value + ";char:td;";
		else if (n.name == "MontoAjustado") detalles_partida += n.value + ";char:td;";
		else if (n.name == "MontoCompromiso") detalles_partida += n.value + ";char:td;";
		else if (n.name == "PreCompromiso") detalles_partida += n.value + ";char:td;";
		else if (n.name == "CotizacionesAsignadas") detalles_partida += n.value + ";char:td;";
		else if (n.name == "MontoDisponible") detalles_partida += n.value + ";char:td;";
		else if (n.name == "MontoDisponibleReal") detalles_partida += n.value + ";char:td;";
		else if (n.name == "MontoPendiente") detalles_partida += n.value + ";char:td;";
		else if (n.name == "partidasCodFuente") detalles_partida += n.value + ";char:td;";
		else if (n.name == "partidasCategoriaProg") detalles_partida += n.value + ";char:tr;";
	}
	var len = detalles_partida.length; len-=9;
	detalles_partida = detalles_partida.substr(0, len);
	//	
	var href = "gehen.php?anz=lg_orden_disponibilidad_presupuestaria&detalles="+detalles+"&detalles_partida="+detalles_partida+"&CodOrganismo="+CodOrganismo+"&CodProveedor="+CodProveedor+"&Anio="+Anio+"&NroOrden="+NroOrden+"&FactorImpuesto="+FactorImpuesto+"&Estado="+Estado+"&CodFuente="+CodFuente+"&CodPresupuesto="+CodPresupuesto+"&Ejercicio="+Ejercicio+"&CodPresupuesto="+$('#CodPresupuesto').val()+"&iframe=true&width=100%&height=430";
	$('#a_disponibilidad').attr('href', href);
	$('#a_disponibilidad').click();
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function generarRequerimientoPendiente(frm_detalle) {
	//	detalles documento
	var error = "";
	var detalles = "";
	var sel = false;
	for(var i=0; n=frm_detalle.elements[i]; i++) {
		if (n.name == "Secuencia" && n.checked) {
			sel = true;
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CodItem" && sel) {
			detalles += n.value + ";char:td;";
			var CodItem = n.value;
		}
		else if (n.name == "CodInterno" && sel) detalles += n.value + ";char:td;";
		else if (n.name == "Descripcion" && sel) detalles += n.value + ";char:td;";
		else if (n.name == "CodUnidad" && sel) detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPedida" && sel) detalles += n.value + ";char:td;";
		else if (n.name == "CantidadPendiente" && sel) detalles += n.value + ";char:td;";
		else if (n.name == "StockActual" && sel) {
			var StockActual = new Number(n.value);
			if (StockActual == 0) { error = "No puede Despachar Items con <strong>Stock en Cero</strong>"; break; }
			detalles += n.value + ";char:td;";
		}
		else if (n.name == "CodCentroCosto" && sel) detalles += n.value + ";char:td;";
		else if (n.name == "EnTransito" && sel) detalles += n.value + ";char:td;";
		else if (n.name == "FlagCompraAlmacen" && sel) {
			detalles += n.value + ";char:tr;";
			sel = false;
		}
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	if (detalles == "") cajaModal("Debe seleccionar por lo menos un registro", "error", 400);
	else if (error != "") {
		cajaModal(error, "error", 400);
	}
	else {
		$("#registro").val(detalles);
		$("#frmentrada").submit();
	}
}

//	FUNCION QUE IMPRIME EL NUMERO DE REGISTROS Y BLOQUEA/DESBLOQUEA LAS OPCIONES
function totalRegistrosGenerarOrden(rows_oc, rows_os, admin, insert, update, del) {
	//	
	var numreg_oc = document.getElementById("rows_oc");
	numreg_oc.innerHTML = "Registros: "+rows_oc;
	
	var numreg_os = document.getElementById("rows_os");
	numreg_os.innerHTML = "Registros: "+rows_os;
	//	
	if (insert == "N" || update == "N" || rows_oc == '0') {
		$("#btGenerarOC").attr("disabled","disabled");
	}
	if (insert == "N" || update == "N" || rows_os == '0') {
		$("#btGenerarOS").attr("disabled","disabled");
	}
}

//	calculos los montos an transaccion en almacen a partir de los detalles
function setMontosAlmacen(frm_detalles) {
	//	detalles
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CantidadPedida") var CantidadPedida = parseFloat(n.value);
		else if (n.name == "CantidadRecibida") var CantidadRecibida = parseFloat(setNumero(n.value));
		else if (n.name == "PrecioUnit") var PrecioUnit = parseFloat(setNumero(n.value));
		else if (n.name == "Total") {
			var Total = CantidadRecibida * PrecioUnit;
			$("#"+n.id).val(Total).formatCurrency();
		}
		else if (n.name == "CantidadPedidaCompra") var CantidadPedidaCompra = parseFloat(n.value);
		else if (n.name == "CantidadCompra") {
			var CantidadCompra = parseFloat(setNumero(n.value));
			var NuevaCantidadCompra = CantidadRecibida * CantidadPedidaCompra / CantidadPedida;
			$("#"+n.id).val(NuevaCantidadCompra).formatCurrency();
		}
	}
}

//	bloqueo/desbloqueo preio unitario en transaccion en almacen
function setFlagManualAlmacen(boo) {
	if (boo) $(".cell.PrecioUnit").removeAttr("disabled");
	else $(".cell.PrecioUnit").attr("disabled", "disabled");
	$(".cell.PrecioUnit").val("0,00");
	setMontosAlmacen(document.getElementById("frm_detalles"));
}

//	si al conformar el requerimiento tildo para caja chica selecciono compras/almacen
function setDirigidoACC(boo) {
	if (boo) $("#FlagCompras").attr("checked", "checked");
	else {
		var TipoClasificacion = $("#TipoClasificacion").val();
		if (TipoClasificacion == "C") $("#FlagCompras").attr("checked", "checked");
		else $("#FlagAlmacen").attr("checked", "checked");
	}
}

//	funcion para abrir el reporte de transaccion de almacen (recepcion/despacho)
function imprimir_transaccion_almacen(registro) {
	if (!registro) var registro = $('#registro').val();
	var partes = registro.split(".");
	if (partes[3] == "I") var pdf = "lg_transaccion_almacen_recepcion_pdf.php?";
	else if (partes[3] == "E") var pdf = "lg_transaccion_almacen_despacho_pdf.php?";
	var href = pdf + "registro="+ registro +"&iframe=true&height=100%&width=100%";
	$("#a_imprimir").attr("href", href);
	document.getElementById("a_imprimir").click();
}

//	funcion para abrir el reporte de transaccion de commodities (recepcion/despacho)
function imprimir_transaccion_commodity(registro) {
	if (!registro) var registro = $('#registro').val();
	var partes = registro.split(".");
	var pdf = "gehen.php?anz=lg_transaccion_commodity_reportes&";
	var href = pdf + "registro="+ registro +"&iframe=true&height=100%&width=100%";
	$("#a_imprimir").attr("href", href);
	document.getElementById("a_imprimir").click();
}

//	calculo los valores generales si modifico el iva manualmente solamente
function setNuevoMontoIvaOC() {
	var MontoAfecto = new Number(parseFloat(setNumero($("#MontoAfecto").val())));
	var MontoNoAfecto = new Number(parseFloat(setNumero($("#MontoNoAfecto").val())));
	var MontoBruto = new Number(parseFloat(setNumero($("#MontoBruto").val())));
	var MontoIGV = new Number(parseFloat(setNumero($("#MontoIGV").val())));
	var MontoTotal = MontoBruto + MontoIGV;
	$("#MontoTotal").val(setNumeroFormato(MontoTotal, 2, '.', ','));
	$("#MontoPendiente").val(setNumeroFormato(MontoTotal, 2, '.', ','));
	mostrarTabDistribucionOrden()
}

//	calculo los valores generales si modifico el iva manualmente solamente
function setNuevoMontoIvaOS() {
	var MontoAfecto = new Number(parseFloat(setNumero($("#MontoOriginal").val())));
	var MontoNoAfecto = new Number(parseFloat(setNumero($("#MontoNoAfecto").val())));
	var MontoIGV = new Number(parseFloat(setNumero($("#MontoIva").val())));
	var MontoTotal = MontoAfecto + MontoNoAfecto + MontoIGV;
	$("#TotalMontoIva").val(setNumeroFormato(MontoTotal, 2, '.', ','));
	$("#MontoPendiente").val(setNumeroFormato(MontoTotal, 2, '.', ','));
	mostrarTabDistribucionOrden()
}

//
function setFlagAsignado(chk) {
	$(".FlagAsignado").prop("checked", false);
	chk.prop("checked", true);
}

//
function cotizaciones_items_totales(id) {
	//	valores
	var CantidadCompra = setNumero($("#CantidadCompra_"+id).val());
	var PrecioUnitInicio = setNumero($("#PrecioUnitInicio_"+id).val());
	var DescuentoPorcentaje = setNumero($("#DescuentoPorcentaje_"+id).val());
	var DescuentoFijo = setNumero($("#DescuentoFijo_"+id).val());
	if ($("#FlagExonerado_"+id).prop("checked")) var FactorPorcentaje = 0;
	else var FactorPorcentaje = $("#FlagExonerado_"+id).val();
	var MontoDescuento = 0;
	//	calculos
	var ImpuestoUnit = PrecioUnitInicio * FactorPorcentaje / 100;
	var PrecioUnitInicioIva = PrecioUnitInicio + ImpuestoUnit;
	if (DescuentoPorcentaje > 0) MontoDescuento = PrecioUnitInicioIva * DescuentoPorcentaje / 100;
	if (DescuentoFijo > 0) MontoDescuento = MontoDescuento + DescuentoFijo;
	var PrecioUnitIva = PrecioUnitInicioIva - MontoDescuento;
    PrecioUnitIva = redondeo(PrecioUnitIva, 2);
	var Total = PrecioUnitIva * CantidadCompra;
	//	totales
	$("#PrecioUnitInicioIva_"+id).val(PrecioUnitInicioIva).formatCurrency();
	$("#PrecioUnitIva_"+id).val(PrecioUnitIva).formatCurrency();
	$("#Total_"+id).val(Total).formatCurrency();
	$("#PrecioUnitFinal_"+id).val(PrecioUnitIva).formatCurrency();
	setFlagMejorPrecio();
}

//
function setFlagMejorPrecio() {
	var MejorPrecio = 0;
	//	detalles
	var frm_proveedores = document.getElementById("frm_proveedores");
	for(var i=0; n=frm_proveedores.elements[i]; i++) {
		if (n.name == "CantidadCompra") var CantidadCompra = setNumero(n.value);
		else if (n.name == "PrecioUnitInicio") var PrecioUnitInicio = setNumero(n.value);
		else if (n.name == "PrecioUnitFinal") {
			var PrecioUnitFinal = setNumero(n.value);
			if ((PrecioUnitFinal > 0 && PrecioUnitFinal < MejorPrecio) || (MejorPrecio == 0 && PrecioUnitFinal > 0)) {
				MejorPrecio = PrecioUnitFinal;
				var id = n.id.substr(16);
				$(".FlagMejorPrecio").prop("checked", false);
				$("#FlagMejorPrecio_"+id).prop("checked", true);
				$(".FlagAsignado").prop("checked", false);
				$("#FlagAsignado_"+id).prop("checked", true);
			}
		}
	}
}

//
function cotizaciones_items_cuadro_abrir(idsel) {
	//	obtengo lineas seleccionadas
	var registro = "";
	var lineas = new Number(0);
	var form = document.getElementById("frmentrada");
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == idsel && n.checked) { registro += n.value + ";"; lineas++; }
	}
	var len = registro.length; len--;
	registro = registro.substr(0, len);
	$("#sel_registros").val(registro);
	
	if (lineas == 0) cajaModal("Debe seleccionar por lo menos un registro", "error", 400);
	else {
		//	formulario
		var get = getForm(form);
		var url = "lg_cotizaciones_cuadro_comparativo_pdf.php?origen=cotizaciones_items_invitar&" + get + "&iframe=true&width=100%&height=100%";
		$("#a_cuadro").attr("href", url);
		document.getElementById("a_cuadro").click();
	}
}

//
function cotizaciones_proveedores_totales(id) {
	//	valores
	var CantidadCompra = setNumero($("#CantidadCompra_"+id).val());
	var PrecioUnitInicio = setNumero($("#PrecioUnitInicio_"+id).val());
	var DescuentoPorcentaje = setNumero($("#DescuentoPorcentaje_"+id).val());
	var DescuentoFijo = setNumero($("#DescuentoFijo_"+id).val());
	if ($("#FlagExonerado_"+id).prop("checked")) var FactorPorcentaje = 0; else var FactorPorcentaje = $("#FlagExonerado_"+id).val();
	var MontoDescuento = 0;
	//	calculos
	var ImpuestoUnit = PrecioUnitInicio * FactorPorcentaje / 100;
	var PrecioUnitInicioIva = PrecioUnitInicio + ImpuestoUnit;
	if (DescuentoPorcentaje > 0) MontoDescuento = PrecioUnitInicioIva * DescuentoPorcentaje / 100;
	if (DescuentoFijo > 0) MontoDescuento = MontoDescuento + DescuentoFijo;
	var PrecioUnit = PrecioUnitInicio - MontoDescuento;
	var PrecioUnitIva = PrecioUnitInicioIva - MontoDescuento;
    PrecioUnitIva = redondeo(PrecioUnitIva, 2);
	var Total = PrecioUnitIva * CantidadCompra;
	//	totales
	$("#PrecioUnitInicioIva_"+id).val(PrecioUnitInicioIva).formatCurrency();
	$("#PrecioUnit_"+id).val(PrecioUnit).formatCurrency();
	$("#PrecioUnitIva_"+id).val(PrecioUnitIva).formatCurrency();
	$("#Total_"+id).val(Total).formatCurrency();
}

//
function cotizaciones_proveedores_descuento() {
	//	montos del descuento
	var DescuentoPorcentaje = setNumero($("#DescuentoPorcentaje").val());
	var DescuentoFijo = setNumero($("#DescuentoFijo").val());
	$(".DescuentoPorcentaje").val(DescuentoPorcentaje).formatCurrency();
	$(".DescuentoFijo").val(DescuentoFijo).formatCurrency();
	//	recorro
	var form = document.getElementById("frm_items");
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == "CotizacionSecuencia") {
			var id = "items_" + n.value;
			cotizaciones_proveedores_totales(id);
		}
	}
}

//	FUNCION PARA ELIMINAR UNA LINEA DE UNA LISTA (TR EN TABLE)
function quitarLineaCommoditySub(boton, detalle) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- detalle	-> sufijo de los campos de la lista
	*/
	boton.disabled = true;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	if (document.getElementById(sel).value == "") alert("¡Debe seleccionar una linea!");
	else {
		var seldetalle = document.getElementById(sel).value;
		var partes = seldetalle.split("_");
		var Codigo = document.getElementById("Codigo_"+partes[1]).value;
		if (Codigo != "") {
			$.ajax({
				type: "POST",
				url: "lib/fphp_funciones_ajax.php",
				data: "accion=commodity_eliminar_validar&Codigo="+Codigo,
				async: false,
				success: function(resp) {
					if (resp.trim() != "") cajaModal(resp, "error", 400);
					else {
						var eliminados_detalle = $("#eliminados_detalle").val() + Codigo + ";";
						$("#eliminados_detalle").val(eliminados_detalle);
						var candetalle = new Number(document.getElementById(can).value); candetalle--;
						document.getElementById(can).value = candetalle;
						var listaDetalles = document.getElementById(lista);
						var tr = document.getElementById(seldetalle);
						listaDetalles.removeChild(tr);
						document.getElementById(sel).value = "";
					}
				}
			});
		}
	}
	boton.disabled = false;
}

function setMontosOrdenServicio(frm_detalles) {
	//	datos generales
	var FactorImpuesto = $("#FactorImpuesto").val();
	//	detalles
	var MontoOriginal = new Number(0.00);
	var MontoNoAfecto = new Number(0.00);
	var MontoIGV = new Number(0.00);
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "CantidadPedida") var CantidadPedida = new Number(setNumero(n.value));
		else if (n.name == "PrecioUnit") var PrecioUnit = new Number(setNumero(n.value));
		else if (n.name == "FlagExonerado") {
			if (n.checked) var FlagExonerado = "S"; else var FlagExonerado = "N";
		}
		else if (n.name == "Total") {
			if (FlagExonerado == "N") {
				var Impuesto = PrecioUnit * FactorImpuesto / 100; 
				MontoOriginal += (PrecioUnit * CantidadPedida)
			} else {
				var Impuesto = 0;
				MontoNoAfecto += (PrecioUnit * CantidadPedida)
			}
			var PrecioUnitTotal = PrecioUnit + Impuesto;
			var Total = new Number(PrecioUnitTotal * CantidadPedida);
			n.value = setNumeroFormato(Total, 2, '.', ',');
		}
	}
	var MontoIva = MontoOriginal * FactorImpuesto / 100;
	var MontoBruto = new Number(MontoOriginal + MontoNoAfecto);
	var TotalMontoIva = MontoBruto + MontoIva;
	//	totales
	$("#MontoOriginal").val(setNumeroFormato(MontoOriginal, 2, '.', ','));
	$("#MontoNoAfecto").val(setNumeroFormato(MontoNoAfecto, 2, '.', ','));
	$("#MontoIva").val(setNumeroFormato(MontoIva, 2, '.', ','));
	$("#TotalMontoIva").val(setNumeroFormato(TotalMontoIva, 2, '.', ','));
	$("#MontoPendiente").val(setNumeroFormato(TotalMontoIva, 2, '.', ','));
	mostrarTabDistribucionOrden();
}