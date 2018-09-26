// JavaScript Document

//	funcion para cargar un ajax e imprimir la respuesta en otro objeto
function mostrarDocumentosObligacion() {
	var CodProveedor = $("#fCodProveedor").val();
	var DocumentoClasificacion = $("#fDocumentoClasificacion").val();
	//	detalles documento
	var detalles_documento = "";
	var frm_documento = document.getElementById("frm_documentos");
	for(var i=0; n=frm_documento.elements[i]; i++) {
		if (n.name == "documento" && n.checked) detalles_documento += n.value + ";";
	}
	var len = detalles_documento.length; len--;
	detalles_documento = detalles_documento.substr(0, len);
	
	//	envio los datos por ajax
	$.ajax({
		type: "POST",
		url: "lib/fphp_funciones_ajax.php",
		data: "accion=mostrarDocumentosObligacion&detalles_documento="+detalles_documento+"&CodProveedor="+CodProveedor+"&DocumentoClasificacion="+DocumentoClasificacion,
		async: false,
		success: function(resp) {
			$("#lista_detalles").html(resp);
		}
	});
}

//	muestro el tab de distribucion de la obligacion
function mostrarTabDistribucionObligacion() {
	var Anio = $("#Anio").val();
	var CodPresupuesto = $("#CodPresupuesto").val();
	var CodOrganismo = $("#CodOrganismo").val();
	var CodFuente = $("#CodFuente").val();
	var Ejercicio = $("#Ejercicio").val();
	var CategoriaProg = $("#CategoriaProg").val();
	var MontoImpuesto = new Number(setNumero($("#MontoImpuesto").val()));
	if (document.getElementById("FlagPresupuesto").checked) var FlagPresupuesto = "S"; else var FlagPresupuesto = "N";
	if (document.getElementById("FlagCompromiso").checked) var FlagCompromiso = "S"; else var FlagCompromiso = "N";
	if (document.getElementById("FlagAgruparIgv").checked) var FlagAgruparIgv = "S"; else var FlagAgruparIgv = "N";
	//	detalles
	var detalles = "";
	var frm_detalles = document.getElementById("frm_distribucion");
	for(var i=0; n=frm_detalles.elements[i]; i++) {
		if (n.name == "cod_partida") detalles += n.value + ";char:td;";
		else if (n.name == "CodCuenta") detalles += n.value + ";char:td;";
		else if (n.name == "CodCuentaPub20") detalles += n.value + ";char:td;";
		else if (n.name == "Monto") {
			var Monto = parseFloat(setNumero(n.value));
			if (isNaN(Monto) || Monto <= 0) Monto = 0;
			detalles += Monto + ";char:td;";
		}
		else if (n.name == "detallesCategoriaProg") detalles += n.value + ";char:td;";
		else if (n.name == "detallesEjercicio") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCodPresupuesto") detalles += n.value + ";char:td;";
		else if (n.name == "detallesCodFuente") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	if (detalles != "") {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/fphp_funciones_ajax.php",
			data: "accion=mostrarTabDistribucionObligacion&detalles="+detalles+"&MontoImpuesto="+MontoImpuesto+"&FlagPresupuesto="+FlagPresupuesto+"&FlagCompromiso="+FlagCompromiso+"&CodOrganismo="+CodOrganismo+"&CodPresupuesto="+CodPresupuesto+"&Anio="+Anio+"&CodFuente="+CodFuente+"&Ejercicio="+Ejercicio+"&CategoriaProg="+CategoriaProg+"&Estado="+$('#Estado').val()+"&NroDocumento="+$('#NroDocumento').val()+"&FlagAgruparIgv="+FlagAgruparIgv,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				$("#lista_cuentas").html(partes[0]);
				$("#lista_cuentas20").html(partes[1]);
				$("#lista_partidas").html(partes[2]);
				mostrarTab("tab", 4, 5);
			}
		});
	} else mostrarTab("tab", 4, 5);
}

//	exportar registro a txt
function registro_compra_seniat_txt(form, nombre_archivo) {
	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + n.value.trim() + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
	}
	
	//	ajax
	$.ajax({
		type: "POST",
		url: "ap_registro_compra_seniat_txt.php",
		data: post+"&nombre_archivo="+nombre_archivo,
		async: false,
		success: function(resp) {
			window.open("../lib/descargar_txt.php?nombre_archivo="+nombre_archivo, "ap_registro_compra_seniat_txt", "toolbar=no, menubar=no, location=no, scrollbars=yes, height=100, width=100, left=500, top=500, resizable=yes");
		}
	});
}

//	exportar registro a txt
function registro_compra_seniat_excel(form, nombre_archivo) {
	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + n.value.trim() + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
	}
	//	abrir archivo
	window.open("ap_registro_compra_seniat_excel.php?nombre_archivo="+nombre_archivo+"&"+post, "ap_registro_compra_seniat_excel", "toolbar=no, menubar=no, location=no, scrollbars=yes, height=100, width=100, left=500, top=500, resizable=yes");
}

//	exportar registro a txt
function registro_compra_retencion_islr_excel(form, nombre_archivo) {
	//	formulario
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + n.value.trim() + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
	}
	//	abrir archivo
	window.open("ap_registro_compra_retencion_islr_excel.php?nombre_archivo="+nombre_archivo+"&"+post, "ap_registro_compra_retencion_islr_excel", "toolbar=no, menubar=no, location=no, scrollbars=yes, height=100, width=100, left=500, top=500, resizable=yes");
}

function setFlagIngresos(TipoTransaccion) {
	$("#FlagDeposito").prop("checked", false);
	$("#FlagNotaCredito").prop("checked", false);
	$("#FlagOtroIngreso").prop("checked", false);
	$("#FlagOtroAjuste").prop("checked", false);
	if (TipoTransaccion == 'I') {
		$("#flagingresos").css("visibility", "visible");
		$("#flagotro").css("visibility", "visible");
	}
	else if (TipoTransaccion == 'E') {
		$("#flagingresos").css("visibility", "hidden");
		$("#flagotro").css("visibility", "visible");
	}
	else {
		$("#flagingresos").css("visibility", "hidden");
		$("#flagotro").css("visibility", "hidden");
	}
}

function setFlagTipo(chk) {
	$("#FlagDeposito").prop("checked", false);
	$("#FlagNotaCredito").prop("checked", false);
	$("#FlagOtroIngreso").prop("checked", false);
	chk.prop("checked", true);
}

function getPeriodoConciliacion(NroCuenta) {
	$.ajax({
		type: "POST",
		url: "lib/fphp_funciones_ajax.php",
		data: "accion=getPeriodoConciliacion&NroCuenta="+NroCuenta,
		async: false,
		success: function(resp) {
			$("#fFechaSaldoInicial").val(resp);
		}
	});
}

//	seleccionar nro de cuenta por default del organismo y tipo de pago
function ctabancariadefault(CodOrganismo, CodTipoPago, iNroCuenta) {
	$.ajax({
		type: "POST",
		url: "lib/fphp_funciones_ajax.php",
		data: "accion=ctabancariadefault&CodOrganismo="+CodOrganismo+"&CodTipoPago="+CodTipoPago,
		async: false,
		success: function(NroCuenta) {
			iNroCuenta.val(NroCuenta);
		}
	});
}