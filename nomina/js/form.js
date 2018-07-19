// JavaScript Document

//	perfil de conceptos
function conceptos_perfil(form, accion) {
	$(".div-progressbar").css("display", "block");
	
	//	valido
	var error = "";
	if ($("#Descripcion").val().trim() == "") error = "Debe llenar los campos obligatorios";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=conceptos_perfil&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	perfil de conceptos (detalle)
function conceptos_perfil_detalle(form) {
	$(".div-progressbar").css("display", "block");
	
	//	valido
	var error = "";
	
	//	conceptos
	var detalles_conceptos = "";
	var frm_conceptos = document.getElementById("frm_conceptos");
	for(var i=0; n=frm_conceptos.elements[i]; i++) {
		if (n.name == "CodTipoProceso") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "CodConcepto") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "cod_partida") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "CuentaDebe") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "CuentaDebePub20") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "FlagDebeCC") {
			if (n.checked) detalles_conceptos += "S;char:td;";
			else detalles_conceptos += "N;char:td;";
		}
		else if (n.name == "CuentaHaber") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "CuentaHaberPub20") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "FlagHaberCC") {
			if (n.checked) detalles_conceptos += "S;char:td;";
			else detalles_conceptos += "N;char:td;";
		}
		else if (n.name == "FlagCategoriaProg") {
			if (n.checked) detalles_conceptos += "S;char:td;";
			else detalles_conceptos += "N;char:td;";
		}
		else if (n.name == "CategoriaProg") detalles_conceptos += n.value + ";char:tr;";
	}
	var len = detalles_conceptos.length; len-=9;
	detalles_conceptos = detalles_conceptos.substr(0, len);
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=conceptos_perfil&accion=conceptos&"+post+"&detalles_conceptos="+detalles_conceptos,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	control de procesos
function procesos_control(form, accion) {
	bloqueo(true);
	
	//	valido
	var error = "";
	if ($("#CodOrganismo").val() == "" || $("#CodTipoNom").val() == "" || $("#Periodo").val() == "" || $("#CodTipoProceso").val() == "" || $("#FechaDesde").val().trim() == "" || $("#FechaHasta").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if (!valFecha($("#FechaDesde").val())) error = "Formato de Fecha Desde incorrecta";
	else if (!valFecha($("#FechaHasta").val())) error = "Formato de Fecha Hasta incorrecta";
	else if ($("#PeriodoNomina").val().trim() == "" || !valPeriodo($("#PeriodoNomina").val())) error = "Periodo N&oacute;mina Incorrecto";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=procesos_control&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	ejecucion de procesos
function procesos_control_ejecucion() {
	bloqueo(true);
	
	//	personas
	var error = "";
	var detalles_personas = "";
	var chk = false;
	var frm_personas = document.getElementById("frm_aprobados");
	for(var i=0; n=frm_personas.elements[i]; i++) {
		if (n.name == "personas" && n.checked) {
			chk = true;
			detalles_personas += n.value + ";char:tr;";
		}
		else if (n.name == "EstadoPago" && chk) {
			chk = false;
			if (n.value == "TR") { error = "No puede Generar Empleados que ya fueron Transferidos a Obligaciones x Pagar"; break; }
			else if (n.value == "PA") { error = "No puede Generar Empleados que ya se le generaron Pagos"; break; }
		}
	}
	var len = detalles_personas.length; len-=9;
	detalles_personas = detalles_personas.substr(0, len);
	if (detalles_personas == "") error = "Debe seleccionar los empleados a Procesar";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(document.getElementById('frmentrada'));
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=procesos_control_ejecucion&accion=ejecutar&"+post+"&detalles_personas="+detalles_personas,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else document.getElementById('frmentrada').submit();
			}
		});
	}
	return false;
}

/*// 	calculo de fideicomiso
function fideicomiso_procesar_calculo() {
	bloqueo(true);
	
	//	formulario
	var post = getForm(document.getElementById("frmentrada"));
	
	//	lista de calculo
	var detalles_periodos = "";
	var frm_periodos = document.getElementById("frm_periodos");
	for(var i=0; n=frm_periodos.elements[i]; i++) {
		if (n.name == "Periodo") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "SueldoMensual") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "Bonificaciones") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "AliVac") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "AliFin") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "SueldoDiario") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "SueldoDiarioAli") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "Dias") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "PrestAntiguedad") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "DiasComplemento") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "PrestComplemento") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "PrestAcumulada") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "Tasa") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "DiasMes") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "InteresMensual") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "InteresAcumulado") detalles_periodos += n.value + ";char:td;";
		else if (n.name == "Anticipo") detalles_periodos += n.value + ";char:tr;";
	}
	var len = detalles_periodos.length; len-=9;
	detalles_periodos = detalles_periodos.substr(0, len);
	
	//	ajax
	$.ajax({
		type: "POST",
		url: "lib/form_ajax.php",
		data: "modulo=fideicomiso&accion=procesar&"+post+"&detalles_periodos="+detalles_periodos,
		async: false,
		success: function(resp) {
			if (resp.trim() != "") cajaModal(resp, "error", 400);
			else cajaModal("Se procesaron los datos exitosamente", "exito", 400);
		}
	});
}*/

//	acumulado de fideicomiso
function fideicomiso_acumulado(form) {
	bloqueo(true);
	
	//	valido
	var error = "";
	if ($("#PeriodoInicial").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if (!valPeriodo($("#PeriodoInicial").val())) error = "Formato de Periodo Inicial incorrecto";
	else if (isNaN(setNumero($("#AcumuladoInicialDias").val())) && $("#AcumuladoInicialDias").val() != "") error = "Formato de Dias Inicial incorrecto";
	else if (isNaN(setNumero($("#AcumuladoDiasAdicionalInicial").val())) && $("#AcumuladoDiasAdicionalInicial").val() != "") error = "Formato de Dias Adicional Inicial incorrecto";
	else if (isNaN(setNumero($("#AcumuladoInicialProv").val())) && $("#AcumuladoInicialProv").val() != "") error = "Formato de Antiguedad Inicial incorrecto";
	else if (isNaN(setNumero($("#AcumuladoInicialFide").val())) && $("#AcumuladoInicialFide").val() != "") error = "Formato de Fideicomiso Inicial incorrecto";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=fideicomiso&accion=acumulado&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	tipos de proceso
function tipo_proceso(form, accion) {
	bloqueo(true);
	
	//	valido
	var error = "";
	if ($("#CodTipoProceso").val().trim() == "" || $("#Descripcion").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if (!valCodigo($("#CodTipoProceso").val())) error = "Formato para el C&oacute;digo Incorrecto";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=tipo_proceso&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	conceptos asignacion
function conceptos_asignacion() {
	bloqueo(true);
	
	//	valido
	var TipoAplicacion = $("#TipoAplicacion").val();
	var error = "";
	if ($("#CodConcepto").val()) error = "Debe seleccionar el Concepto";
	
	//	empleados
	var error = "";
	var detalles_empleados = "";
	var chk = false;
	var frm_empleados = document.getElementById("frm_empleados");
	for(var i=0; n=frm_empleados.elements[i]; i++) {
		if (n.name == "empleados" && n.checked) {
			detalles_empleados += n.value + ";char:td;";
			chk = true;
		}
		else if (n.name == "PeriodoDesde" && chk) {
			var PeriodoDesde = n.value;
			detalles_empleados += PeriodoDesde + ";char:td;";
		}
		else if (n.name == "PeriodoHasta" && chk) {
			var PeriodoHasta = n.value;
			if (PeriodoDesde > PeriodoHasta && TipoAplicacion == "T") { error = "El Periodo Inicio no puede ser mayor al Periodo Fin"; break; }
			else detalles_empleados += PeriodoHasta + ";char:td;";
		}
		else if (n.name == "FlagManual" && chk) {
			if (n.checked) detalles_empleados += "S;char:td;";
			else detalles_empleados += "N;char:td;";
		}
		else if (n.name == "Monto" && chk) detalles_empleados += n.value + ";char:td;";
		else if (n.name == "Cantidad" && chk) detalles_empleados += n.value + ";char:td;";
		else if (n.name == "Procesos" && chk) detalles_empleados += n.value + ";char:td;";
		else if (n.name == "Estado" && chk) {
			detalles_empleados += n.value + ";char:tr;";
			chk = false;
		}
	}
	var len = detalles_empleados.length; len-=9;
	detalles_empleados = detalles_empleados.substr(0, len);
	if (detalles_empleados == "") error = "Debe seleccionar los empleados a Procesar";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(document.getElementById('frmentrada'));
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=conceptos_asignacion&"+post+"&detalles_empleados="+detalles_empleados,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else cajaModal("Concepto asignado exitosamente", "exito", 400, "document.getElementById('frmentrada').submit();");
			}
		});
	}
	return false;
}

//	ajuste salarial (grado salarial)
function ajuste_salarial(form, accion) {
	bloqueo(true);
	
	//	valido
	var error = "";
	if ($("#CodOrganismo").val() == "" || $("#NroResolucion").val().trim() == "" || $("#Descripcion").val().trim() == "") error = "Debe llenar los campos obligatorios";
	
	//	ajustes
	var detalles_ajustes = "";
	var frm_ajustes = document.getElementById("frm_ajustes");
	for(var i=0; n=frm_ajustes.elements[i]; i++) {
		if (n.name == "CodNivel") {
			var chk = n.checked;
			if (chk) detalles_ajustes += n.value + ";char:td;";
		}
		else if (n.name == "SueldoBasico" && chk) detalles_ajustes += setNumero(n.value) + ";char:td;";
		else if (n.name == "Porcentaje" && chk) {
			var Porcentaje = setNumero(n.value);
			if (isNaN(Porcentaje)) { error = "Se encontraron Porcentajes incorrectos"; break; }
			else detalles_ajustes += Porcentaje + ";char:td;";
		}
		else if (n.name == "Monto" && chk) {
			var Monto = setNumero(n.value);
			if (isNaN(Monto)) { error = "Se encontraron Montos Incorrectos"; break; }
			else detalles_ajustes += Monto + ";char:td;";
		}
		else if (n.name == "SueldoNuevo" && chk) {
			detalles_ajustes += setNumero(n.value) + ";char:tr;";
			chk = false;
		}
	}
	var len = detalles_ajustes.length; len-=9;
	detalles_ajustes = detalles_ajustes.substr(0, len);
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=ajuste_salarial&accion="+accion+"&"+post+"&detalles_ajustes="+detalles_ajustes,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	ajuste salarial (empleados)
function ajuste_salarial_emp(form, accion) {
	bloqueo(true);
	
	//	valido
	var error = "";
	if ($("#CodOrganismo").val() == "" || $("#NroResolucion").val().trim() == "" || $("#Descripcion").val().trim() == "") error = "Debe llenar los campos obligatorios";
	
	//	ajustes
	var detalles_ajustes = "";
	var frm_ajustes = document.getElementById("frm_ajustes");
	for(var i=0; n=frm_ajustes.elements[i]; i++) {
		if (n.name == "CodPersona") {
			var chk = n.checked;
			if (chk) detalles_ajustes += n.value + ";char:td;";
		}
		else if (n.name == "SueldoBasico" && chk) detalles_ajustes += setNumero(n.value) + ";char:td;";
		else if (n.name == "Porcentaje" && chk) {
			var Porcentaje = setNumero(n.value);
			if (isNaN(Porcentaje)) { error = "Se encontraron Porcentajes incorrectos"; break; }
			else detalles_ajustes += Porcentaje + ";char:td;";
		}
		else if (n.name == "Monto" && chk) {
			var Monto = setNumero(n.value);
			if (isNaN(Monto)) { error = "Se encontraron Montos Incorrectos"; break; }
			else detalles_ajustes += Monto + ";char:td;";
		}
		else if (n.name == "SueldoNuevo" && chk) {
			detalles_ajustes += setNumero(n.value) + ";char:tr;";
			chk = false;
		}
	}
	var len = detalles_ajustes.length; len-=9;
	detalles_ajustes = detalles_ajustes.substr(0, len);
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=ajuste_salarial_emp&accion="+accion+"&"+post+"&detalles_ajustes="+detalles_ajustes,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	control de procesos (prenomina)
function prenomina_procesos(form, accion) {
	bloqueo(true);
	
	//	valido
	var error = "";
	if ($("#CodOrganismo").val() == "" || $("#CodTipoNom").val() == "" || $("#Periodo").val() == "" || $("#CodTipoProceso").val() == "" || $("#FechaDesde").val().trim() == "" || $("#FechaHasta").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if (!valFecha($("#FechaDesde").val())) error = "Formato de Fecha Desde incorrecta";
	else if (!valFecha($("#FechaHasta").val())) error = "Formato de Fecha Hasta incorrecta";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=prenomina_procesos&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	control de liquidaciones
function prestaciones_control(form, accion) {
	bloqueo(true);
	//	formulario
	var post = getForm(form);
	//	intereses
	//	ajustes
	var Flag = false;
	var detalles_intereses = "";
	var frm_intereses = document.getElementById("frm_intereses");
	for(var i=0; n=frm_intereses.elements[i]; i++) {
		if (n.name == "Flag" && n.checked) Flag = true;
		else if (n.name == "Periodo" && Flag) detalles_intereses += n.value + "|";
		else if (n.name == "Porcentaje" && Flag) detalles_intereses += n.value + "|";
		else if (n.name == "MontoBase" && Flag) detalles_intereses += n.value + "|";
		else if (n.name == "Monto" && Flag) {
			detalles_intereses += n.value + ";";
			Flag = false;
		}
	}
	var len = detalles_intereses.length; len-=1;
	detalles_intereses = detalles_intereses.substr(0, len);
	
	//	ajax
	$.ajax({
		type: "POST",
		url: "lib/form_ajax.php",
		data: "modulo=prestaciones_control&accion="+accion+"&"+post+"&detalles_intereses="+detalles_intereses,
		async: false,
		success: function(resp) {
			if (resp.trim() != "") cajaModal(resp, "error", 400);
			else form.submit();
		}
	});
	return false;
}