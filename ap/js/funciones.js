// JavaScript Document

//	ABRIR GENERAR VOUCHERS
function generar_vouchers_abrir(registro, pagina, imprimir) {
	window.open("gehen.php?anz="+pagina+"&registro="+registro+"&imprimir="+imprimir, pagina, "toolbar=no, menubar=no, location=no, scrollbars=yes, width=1000, height=600");
}

//	ABRIR GENERAR VOUCHERS
function vouchers_abrir(registro, pagina, imprimir) {
	var url = "gehen.php?anz="+pagina+"&registro="+registro+"&iframe=true&width=100%&height=100%";
	$("#aVoucher").attr("href", url);
	document.getElementById("aVoucher").click();
}

//	ACTUALIZO LOS TOTALES
function actualizarMontoImpuestoObligacion() {
	//	impuestos
	var frm_impuesto = document.getElementById("frm_impuesto");
	for(var i=0; n=frm_impuesto.elements[i]; i++) {
		if (n.name == "Signo") var _Signo = n.value;
		else if (n.name == "MontoAfecto") var _MontoAfecto = new Number(setNumero(n.value));
		else if (n.name == "FactorPorcentaje") var _FactorPorcentaje = new Number(setNumero(n.value));
		else if (n.name == "MontoImpuesto") {
			var _MontoImpuesto = new Number(_MontoAfecto * _FactorPorcentaje / 100);
			if (_Signo == "N") _MontoImpuesto *= -1;
			n.value = setNumeroFormato(_MontoImpuesto, 2, ".", ",");
		}
	}
	actualizarMontosObligacion();
}

//	ACTUALIZO LOS MONTOS DE LA OBLIGACION (IMPUESTOS)
function actualizarMontosObligacionImpuesto() {
	//	distribucion
	var MontoAfecto = new Number(0);
	var MontoNoAfecto = new Number(0);
	var frm_distribucion = document.getElementById("frm_distribucion");
	for(var i=0; n=frm_distribucion.elements[i]; i++) {
		if (n.name == "FlagNoAfectoIGV") {
			if (n.checked) var FlagNoAfectoIGV = "S";
			else var FlagNoAfectoIGV = "N";
		}
		if (n.name == "Monto") {
			var monto = new Number(setNumero(n.value));
			if (FlagNoAfectoIGV == "S") MontoNoAfecto += monto;
			else MontoAfecto += monto;
		}
	}
	
	//	calculo montos
	var FactorImpuesto = new Number($("#FactorImpuesto").val());
	var MontoImpuesto = new Number(MontoAfecto * FactorImpuesto / 100);
	
	//	impuestos
	var frm_impuesto = document.getElementById("frm_impuesto");
	for(var i=0; n=frm_impuesto.elements[i]; i++) {
		if (n.name == "Signo") var _Signo = n.value;
		else if (n.name == "FlagImponible") var _FlagImponible = n.value;
		else if (n.name == "MontoAfecto") {
			if (_FlagImponible == "I") var _MontoAfecto = new Number(MontoImpuesto);
			else if (_FlagImponible == "N") var _MontoAfecto = new Number(MontoAfecto);
			else if (_FlagImponible == "B") var _MontoAfecto = new Number(MontoAfecto+MontoNoAfecto);
			else if (_FlagImponible == "T") var _MontoAfecto = new Number(MontoAfecto+MontoImpuesto);
			n.value = setNumeroFormato(_MontoAfecto, 2, ".", ",");
		}
		else if (n.name == "FactorPorcentaje") var _FactorPorcentaje = new Number(setNumero(n.value));
		else if (n.name == "MontoImpuesto") {
			var _MontoImpuesto = new Number(_MontoAfecto * _FactorPorcentaje / 100);
			if (_Signo == "N") _MontoImpuesto *= -1;
			n.value = setNumeroFormato(_MontoImpuesto, 2, ".", ",");
		}
	}
	actualizarMontosObligacion();
}

//	ACTUALIZO LOS MONTOS DE LA OBLIGACION
function actualizarMontosObligacion() {
	//	impuestos
	var impuesto_total = new Number(0);
	var frm_impuesto = document.getElementById("frm_impuesto");
	for(var i=0; n=frm_impuesto.elements[i]; i++) {
		if (n.name == "MontoImpuesto") {
			var monto = new Number(setNumero(n.value));
			impuesto_total += monto;
		}
	}
	
	//	documentos
	var documento_total = new Number(0);
	var documento_afecto = new Number(0);
	var documento_impuesto = new Number(0);
	var documento_noafecto = new Number(0);
	var frm_documento = document.getElementById("frm_documento");
	for(var i=0; n=frm_documento.elements[i]; i++) {
		if (n.name == "MontoAfecto") {
			var monto = new Number(setNumero(n.value));
			documento_afecto += monto;
		}
		else if (n.name == "MontoNoAfecto") {
			var monto = new Number(setNumero(n.value));
			documento_noafecto += monto;
		}
	}
	
	//	distribucion
	var distribucion_total = new Number(0);
	var MontoAfecto = new Number(0);
	var MontoNoAfecto = new Number(0);
	var frm_distribucion = document.getElementById("frm_distribucion");
	for(var i=0; n=frm_distribucion.elements[i]; i++) {
		if (n.name == "FlagNoAfectoIGV") {
			if (n.checked) var FlagNoAfectoIGV = "S";
			else var FlagNoAfectoIGV = "N";
		}
		if (n.name == "Monto") {
			var monto = new Number(setNumero(n.value));
			distribucion_total += monto;
			if (FlagNoAfectoIGV == "S") MontoNoAfecto += monto;
			else MontoAfecto += monto;
		}
	}
	
	//	adelantos
	var MontoAdelanto = 0;
	$('input[name="adelantos_CodAdelanto[]"]').each(function(idx) {
		var adelantos_MontoTotal = new Number($('input[name="adelantos_MontoTotal[]"]:eq('+idx+')').val());
		MontoAdelanto += adelantos_MontoTotal;
	});
	
	//	calculo montos
	var FactorImpuesto = new Number($("#FactorImpuesto").val());
	var documento_impuesto = new Number(documento_afecto * FactorImpuesto / 100);
	var documento_total = new Number(documento_afecto + documento_noafecto + documento_impuesto);
	var MontoImpuesto = new Number(MontoAfecto * FactorImpuesto / 100);
	var MontoObligacion = new Number(MontoAfecto + MontoNoAfecto + MontoImpuesto + impuesto_total);
	var MontoPagar = new Number(MontoObligacion - MontoAdelanto);
	var MontoPagoParcial = new Number(setNumero($('#MontoPagoParcial').val()));
	var MontoPendiente = new Number(MontoPagar - MontoPagoParcial);
	
	//	asigno montos a la lista
	$("#impuesto_total").val(setNumeroFormato(impuesto_total, 2, ".", ","));
	$("#documento_total").val(setNumeroFormato(documento_total, 2, ".", ","));
	$("#documento_afecto").val(setNumeroFormato(documento_afecto, 2, ".", ","));
	$("#documento_impuesto").val(setNumeroFormato(documento_impuesto, 2, ".", ","));
	$("#documento_noafecto").val(setNumeroFormato(documento_noafecto, 2, ".", ","));	
	$("#distribucion_total").val(setNumeroFormato(distribucion_total, 2, ".", ","));
	
	//	asigno montos generales
	$("#MontoAfecto").val(setNumeroFormato(MontoAfecto, 2, ".", ","));
	$("#MontoNoAfecto").val(setNumeroFormato(MontoNoAfecto, 2, ".", ","));
	$("#MontoImpuesto").val(setNumeroFormato(MontoImpuesto, 2, ".", ","));
	$("#MontoImpuestoOtros").val(setNumeroFormato(impuesto_total, 2, ".", ","));
	$("#MontoObligacion").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
	$('#MontoAdelanto').val(MontoAdelanto).formatCurrency();
	$("#MontoPagar").val(setNumeroFormato(MontoPagar, 2, ".", ","));
	$("#MontoPagoParcial").val(setNumeroFormato(MontoPagoParcial, 2, ".", ","));
	$("#MontoPendiente").val(setNumeroFormato(MontoPendiente, 2, ".", ","));
}

//	ACTUALIZO LOS TOTALES DE LA OBLIGACION
function actualizarTotalesObligacion(campo) {
	//	valores actuales
	var FactorImpuesto = new Number($("#FactorImpuesto").val());
	var Afecto = new Number(setNumero($("#MontoAfecto").val()));
	var NoAfecto = new Number(setNumero($("#MontoNoAfecto").val()));
	var Impuesto = new Number(setNumero($("#MontoImpuesto").val()));
	var ImpuestoOtros = new Number(setNumero($("#MontoImpuestoOtros").val()));
	var Obligacion = new Number(setNumero($("#MontoObligacion").val()));
	var MontoAdelanto = new Number(setNumero($("#MontoAdelanto").val()));
	var MontoPagoParcial = new Number(setNumero($("#MontoPagoParcial").val()));	
	//	calculo
	var MontoImpuesto = new Number(Afecto * FactorImpuesto / 100);
	if (campo == "MontoAfecto" || campo == "MontoNoAfecto") {
		MontoAfecto = Afecto;
		MontoNoAfecto = NoAfecto;
		MontoImpuesto = MontoAfecto * FactorImpuesto / 100;
		MontoImpuestoOtros = ImpuestoOtros;
		MontoObligacion = MontoAfecto + MontoNoAfecto + MontoImpuesto - MontoImpuestoOtros;
	}
	else if (campo == "MontoImpuesto") {
		MontoAfecto = Impuesto / FactorImpuesto * 100;
		MontoNoAfecto = NoAfecto;
		MontoImpuesto = Impuesto;
		MontoImpuestoOtros = ImpuestoOtros;
		MontoObligacion = MontoAfecto + MontoNoAfecto + MontoImpuesto - MontoImpuestoOtros;
	}
	//	asigno montos generales
	MontoPagar = MontoObligacion - MontoAdelanto;
	MontoPendiente = MontoPagar - MontoPagoParcial;
	$("#MontoAfecto").val(setNumeroFormato(MontoAfecto, 2, ".", ","));
	$("#MontoNoAfecto").val(setNumeroFormato(MontoNoAfecto, 2, ".", ","));
	$("#MontoImpuesto").val(setNumeroFormato(MontoImpuesto, 2, ".", ","));
	$("#MontoImpuestoOtros").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
	$("#MontoObligacion").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
	$("#MontoPagar").val(setNumeroFormato(MontoPagar, 2, ".", ","));
	$("#MontoPendiente").val(setNumeroFormato(MontoPendiente, 2, ".", ","));
}

//	cargo documentos para preparar factura
function cargarOpcionPrepararFactura(frm_documento) {
	//	datos generales
	var CodOrganismo = $("#fCodOrganismo").val();
	var CodProveedor = $("#fCodProveedor").val();
	var DocumentoClasificacion = $("#fDocumentoClasificacion").val();
	
	//	detalles documento
	var detalles_documento = "";
	for(var i=0; n=frm_documento.elements[i]; i++) {
		if (n.name == "documento" && n.checked) detalles_documento += n.value + ";";
	}
	var len = detalles_documento.length; len--;
	detalles_documento = detalles_documento.substr(0, len);
	
	$("#registro").val(detalles_documento);
	$("#frmentrada").attr("action", "gehen.php?anz=ap_facturacion_form");
	$("#frmentrada").submit();
}

//	bloqueo/desbloqueo campos de provision en el maestro de tipo de doc. cxp
function setFlagProvision(chk) {
	$("#CodCuentaProv").val("");
	$("#CodCuentaProvPub20").val("");
	if (chk) { $("#a_CodCuentaProv").css("visibility", "visible"); $("#a_CodCuentaProvPub20").css("visibility", "visible"); }
	else { $("#a_CodCuentaProv").css("visibility", "hidden"); $("#a_CodCuentaProvPub20").css("visibility", "hidden"); }
}

//	bloqueo/desbloqueo campos de adelanto en el maestro de tipo de doc. cxp
function setFlagAdelanto(chk) {
	$("#CodCuentaAde").val("");
	$("#CodCuentaAdePub20").val("");
	if (chk) { $("#a_CodCuentaAde").css("visibility", "visible"); $("#a_CodCuentaAdePub20").css("visibility", "visible"); }
	else { $("#a_CodCuentaAde").css("visibility", "hidden"); $("#a_CodCuentaAdePub20").css("visibility", "hidden"); }
}

//	bloqueo/desbloqueo campos de adelanto en el maestro de tipo de doc. cxp
function setFlagVoucher(chk) {
	$("#CodCuenta").val("");
	$("#CodCuentaPub20").val("");
	$("#CodVoucher").val("").prop("disabled", !chk);
	if (chk) {
		$("#a_CodCuenta").css("visibility", "visible");
		$("#a_CodCuentaPub20").css("visibility", "visible");
	} else {
		$("#a_CodCuenta").css("visibility", "hidden");
		$("#a_CodCuentaPub20").css("visibility", "hidden");
	}
}

//	
function cerrarVoucher(li) {
	parent.$("#"+li).remove();
	if (li == "li1") {
		if (parent.$("#li2").length) {
			current(parent.$("#li2"));
			cargarPagina(parent.document.getElementById('frmentrada'), 'gehen.php?anz=ap_generar_vouchers_ordenacion');
		} else parent.parent.$.prettyPhoto.close();
	} else {
		if (parent.$("#li1").length) {
			current(parent.$("#li1"));
			cargarPagina(parent.document.getElementById('frmentrada'), 'gehen.php?anz=ap_generar_vouchers_provision_voucher');
		} else parent.parent.$.prettyPhoto.close();
	}
}

//	
function cerrarVoucherPago(li) {
	parent.$("#"+li).remove();
	if (li == "li1") {
		if (parent.$("#li2").length) {
			current(parent.$("#li2"));
			cargarPagina(parent.document.getElementById('frmentrada'), 'gehen.php?anz=ap_generar_vouchers_pagos_voucher_pub20');
		} else parent.parent.$.prettyPhoto.close();
	} else {
		if (parent.$("#li1").length) {
			current(parent.$("#li1"));
			cargarPagina(parent.document.getElementById('frmentrada'), 'gehen.php?anz=ap_generar_vouchers_pagos_voucher');
		} else parent.parent.$.prettyPhoto.close();
	}
}

//	VOUCHERS
function verVoucher(origen) {
	var registro = $("#registro").val();
	var url = "gehen.php?anz=ap_vouchers_tab&registro="+registro+"&accion=ver&origen="+origen+"&iframe=true&width=1050&height=575";
	$("#aVoucher").attr("href", url);
	document.getElementById("aVoucher").click();
}

//
function listado_clasificador_presupuestario_disponible_abrir() {
	var CodPresupuesto = $("#CodPresupuesto").val();
	var CodOrganismo = $("#CodOrganismo").val();	
	var url = "../lib/listas/listado_clasificador_presupuestario_disponible.php?filtrar=default&cod=CodPartida&CodPresupuesto="+CodPresupuesto+"&CodOrganismo="+CodOrganismo+"&ventana=selListadoLista&seldetalle=sel_transacciones&iframe=true&width=850&height=500";
	$("#aSelPartida").attr("href", url);
	validarAbrirLista('sel_transacciones', 'aSelPartida');
}

//
function flagPresupuesto(boo) {
	if (boo) {
		$(".btpartida").prop("disabled", false);
		$(".partida").prop("disabled", false).val("");
	} else {
		$(".btpartida").prop("disabled", true);
		$(".partida").prop("disabled", true).val("");
	}
}

//	actualizo monto de la obligacion
function actualizar_montos_obligacion() {
	var MontoAfecto = obtener_obligacion_afecto(document.getElementById("frm_distribucion"));
	var MontoNoAfecto = obtener_obligacion_noafecto(document.getElementById("frm_distribucion"));
	var MontoImpuesto = obtener_obligacion_impuestos(document.getElementById("frm_documento"));
	actualizar_afecto_retenciones(MontoAfecto, MontoNoAfecto, MontoImpuesto, document.getElementById("frm_impuesto"));
	var MontoImpuestoOtros = obtener_obligacion_retenciones(document.getElementById("frm_impuesto"));
	var MontoObligacion = MontoAfecto + MontoNoAfecto + MontoImpuesto + MontoImpuestoOtros;
	$("#MontoAfecto").val(setNumeroFormato(MontoAfecto, 2, ".", ","));
	$("#MontoNoAfecto").val(setNumeroFormato(MontoNoAfecto, 2, ".", ","));
	$("#MontoImpuesto").val(setNumeroFormato(MontoImpuesto, 2, ".", ","));
	$("#MontoImpuestoOtros").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
	$("#MontoObligacion").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
	$("#MontoPagar").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
	$("#MontoPendiente").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
	$("#impuesto_total").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
}
//	obtener el monto afecto
function obtener_obligacion_afecto(frm_distribucion) {
	//	distribucion
	var MontoAfecto = new Number();
	for(var i=0; n=frm_distribucion.elements[i]; i++) {
		if (n.name == "FlagNoAfectoIGV") {
			if (n.checked) var FlagNoAfectoIGV = "S";
			else var FlagNoAfectoIGV = "N";
		}
		else if (n.name == "Monto") {
			var Monto = new Number(parseFloat(setNumero(n.value)));
			if (FlagNoAfectoIGV != "S") MontoAfecto += Monto;
		}
	}
	return MontoAfecto;
}
//	obtener el monto no afecto
function obtener_obligacion_noafecto(frm_distribucion) {
	//	distribucion
	var MontoNoAfecto = new Number();
	for(var i=0; n=frm_distribucion.elements[i]; i++) {
		if (n.name == "FlagNoAfectoIGV") {
			if (n.checked) var FlagNoAfectoIGV = "S";
			else var FlagNoAfectoIGV = "N";
		}
		else if (n.name == "Monto") {
			var Monto = new Number(parseFloat(setNumero(n.value)));
			if (FlagNoAfectoIGV == "S") MontoNoAfecto += Monto;
		}
	}
	return MontoNoAfecto;
}
//	obtener el monto de impuesto/retenciones
function obtener_obligacion_impuestos(frm_documento) {
	//	distribucion
	var documento_impuesto = new Number();
	for(var i=0; n=frm_documento.elements[i]; i++) {
		if (n.name == "MontoImpuestos") {
			var MontoImpuestos = new Number(parseFloat(setNumero(n.value)));
			documento_impuesto += MontoImpuestos;
		}
	}
	return documento_impuesto;
}
//	actualizo retenciones
function actualizar_afecto_retenciones(MontoAfecto, MontoNoAfecto, MontoImpuesto, frm_impuesto) {
	//	impuestos
	for(var i=0; n=frm_impuesto.elements[i]; i++) {
		if (n.name == "Signo") var _Signo = n.value;
		else if (n.name == "FlagImponible") var _FlagImponible = n.value;
		else if (n.name == "MontoAfecto") {
			if (_FlagImponible == "I") var _MontoAfecto = new Number(MontoImpuesto);
			else if (_FlagImponible == "N") var _MontoAfecto = new Number(MontoAfecto);
			else if (_FlagImponible == "B") var _MontoAfecto = new Number(MontoAfecto+MontoNoAfecto);
			else if (_FlagImponible == "T") var _MontoAfecto = new Number(MontoAfecto+MontoImpuesto);
			n.value = setNumeroFormato(_MontoAfecto, 2, ".", ",");
		}
		else if (n.name == "FactorPorcentaje") var _FactorPorcentaje = new Number(setNumero(n.value));
		else if (n.name == "MontoSustraendo") var _MontoSustraendo = new Number(setNumero(n.value));
		else if (n.name == "MontoAfectoSustraendo") var _MontoAfectoSustraendo = new Number(setNumero(n.value));
		else if (n.name == "MontoImpuesto") {
			var _MontoImpuesto = new Number(_MontoAfecto * _FactorPorcentaje / 100);
			if (_Signo == "N") var _Signo = "-";
			_MontoImpuesto = _MontoImpuesto - _MontoSustraendo;
			n.value = _Signo + setNumeroFormato(_MontoImpuesto, 2, ".", ",");
		}
	}
}
//	obtengo retenciones
function obtener_obligacion_retenciones(frm_impuesto) {
	var impuesto_total = new Number();
	//	impuestos
	for(var i=0; n=frm_impuesto.elements[i]; i++) {
		if (n.name == "MontoImpuesto") {
			var Impuesto = parseFloat(setNumero(n.value));
			impuesto_total += Impuesto;
		}
	}
	return impuesto_total;
}

//	quitar linea de impuesto
function quitarLineaImpuesto(boton, detalle) {
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
		var candetalle = new Number(document.getElementById(can).value); candetalle--;
		document.getElementById(can).value = candetalle;
		var seldetalle = document.getElementById(sel).value;
		var listaDetalles = document.getElementById(lista);
		var tr = document.getElementById(seldetalle);
		listaDetalles.removeChild(tr);
		document.getElementById(sel).value = "";
	}
	boton.disabled = false;
	//	actualizar montos de la obligacion
	var MontoAfecto = setNumero($("#MontoAfecto").val());
	var MontoNoAfecto = setNumero($("#MontoNoAfecto").val());
	var MontoImpuesto = setNumero($("#MontoImpuesto").val());
	actualizar_afecto_retenciones(MontoAfecto, MontoNoAfecto, MontoImpuesto, document.getElementById("frm_impuesto"));
	var MontoImpuestoOtros = obtener_obligacion_retenciones(document.getElementById("frm_impuesto"));
	var MontoObligacion = MontoAfecto + MontoNoAfecto + MontoImpuesto + MontoImpuestoOtros;
				var MontoAdelanto = obtener_obligacion_retenciones_adelanto(document.getElementById("frm_impuesto"));
				var MontoPagar = MontoObligacion + MontoAdelanto;
	$("#MontoImpuestoOtros").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
	$("#MontoObligacion").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
				$("#MontoAdelanto").val(setNumeroFormato(MontoAdelanto, 2, ".", ","));
	$("#MontoPagar").val(setNumeroFormato(MontoPagar, 2, ".", ","));
	$("#MontoPendiente").val(setNumeroFormato(MontoPagar, 2, ".", ","));
	$("#impuesto_total").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
}