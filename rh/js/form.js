// JavaScript Document

//	desarrollo de carreras y sucesion
function desarrollo_carreras(form, accion) {
	//	valido
	var error = "";
	if ($("#CodPersona").val().trim() == "" || $("#CodOrganismo").val() == "" || $("#CodDependencia").val() == "" || $("#Secuencia").val() == "" || $("#TipoEvaluacion").val() == "" || $("#TipoEvaluacion").val() == "" || $("#TipoEvaluacion").val() == "" || $("#TipoEvaluacion").val() == "" || $("#TipoEvaluacion").val() == "") error = "Debe ingresar los campos obligatorios";
	
	//	formulario
	var DescripCargo = $("#DescripCargo option:selected").text();
	var post = "";
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
	
	//	capacitacion tecnica
	var detalles_captecnica = "";
	var frm = document.getElementById("frm_captecnica");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "Descripcion") detalles_captecnica += n.value + ";char:tr;";
	}
	var len = detalles_captecnica.length; len-=9;
	detalles_captecnica = detalles_captecnica.substr(0, len);
	
	//	habilidades
	var detalles_habilidad = "";
	var frm = document.getElementById("frm_habilidad");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "Tipo") detalles_habilidad += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles_habilidad += n.value + ";char:tr;";
	}
	var len = detalles_habilidad.length; len-=9;
	detalles_habilidad = detalles_habilidad.substr(0, len);
	
	//	evaluaciones
	var detalles_evaluacion = "";
	var frm = document.getElementById("frm_evaluacion");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "Descripcion") detalles_evaluacion += n.value + ";char:tr;";
	}
	var len = detalles_evaluacion.length; len-=9;
	detalles_evaluacion = detalles_evaluacion.substr(0, len);
	
	//	metas
	var detalles_metas = "";
	var frm = document.getElementById("frm_metas");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "Descripcion") detalles_metas += n.value + ";char:tr;";
	}
	var len = detalles_metas.length; len-=9;
	detalles_metas = detalles_metas.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=desarrollo_carreras&accion="+accion+"&detalles_captecnica="+detalles_captecnica+"&detalles_habilidad="+detalles_habilidad+"&detalles_evaluacion="+detalles_evaluacion+"&detalles_metas="+detalles_metas+"&"+post,
			async: false,
			success: function(resp) {
				var datos = resp.split("|");
				if (datos[0].trim() != "") cajaModal(datos[0], "error", 400);
				else {
					if (accion == "nuevo") {
						var funct = "document.getElementById('frmentrada').submit();";
						cajaModal("Plan de Carreras <strong>Nro. "+datos[1]+"</strong> se cre&oacute; con &eacute;xito", "exito", 400, funct);
					}
					else if (accion == "terminar") {
						$("#imprimir").val("si")
						form.submit();
					}
					else form.submit();
				}
			}
		});
	}
	return false;
}

//	solicitud de vacaciones
function vacaciones(form, accion) {
	//	valido
	var error = "";
	if ($("#CodPersona").val().trim() == "" || $("#CodOrganismo").val().trim() == "" || $("#CodDependencia").val().trim() == "" || $("#Tipo").val().trim() == "" || $("#FechaSalida").val().trim() == "" || $("#NroDias").val().trim() == "" || $("#FechaTermino").val().trim() == "" || $("#FechaIncorporacion").val().trim() == "") error = "Debe ingresar los campos obligatorios";
	else if (!valFecha($("#FechaSalida").val())) error = "Formato de Fecha de Salida Incorrecta";
	else if (!valFecha($("#FechaTermino").val())) error = "Formato de Fecha de Termino Incorrecto";
	else if (isNaN(setNumero($("#NroDias").val()))) error = "Nro. de Dias Incorrecto";
	
	//	formulario
	var post = "";
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
	
	//	detalles
	var detalles = "";
	var frm = document.getElementById("frm_detalles");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "NroPeriodo") detalles += n.value + ";char:td;";
		else if (n.name == "FlagUtlizarPeriodo") {
			if (n.checked) detalles += "S;char:td;"; else detalles += "N;char:td;";
		}
		else if (n.name == "NroDias") detalles += n.value + ";char:td;";
		else if (n.name == "FechaInicio") detalles += n.value + ";char:td;";
		else if (n.name == "FechaFin") detalles += n.value + ";char:td;";
		else if (n.name == "Derecho") detalles += n.value + ";char:td;";
		else if (n.name == "TotalUtilizados") detalles += n.value + ";char:td;";
		else if (n.name == "Pendientes") detalles += n.value + ";char:td;";
		else if (n.name == "Observaciones") detalles += changeUrl(n.value) + ";char:td;";
		else if (n.name == "Secuencia") detalles += n.value + ";char:tr;";
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=vacaciones&accion="+accion+"&detalles="+detalles+"&"+post,
			async: false,
			success: function(resp){
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					if (accion == "nuevo") {
						var funct = "document.getElementById('frmentrada').submit();";
						cajaModal("Solicitud de Vacaci&oacute;n <strong>Nro. "+partes[1]+"</strong> se cre&oacute; con &eacute;xito", "exito", 400, funct);
					}
					else if (accion == "aprobar") {
						var registro = $("#Anio").val() + "." + $("#CodSolicitud").val();
						cargarOpcion2(form, "rh_vacaciones_pdf.php?", "BLANK", "height=800, width=750, left=200, top=200, resizable=no", registro);
						form.submit();
					}
					else form.submit();
				}
			}
		});
	}
	return false;
}

//	requerimientos de personal
function requerimientos(form, accion) {
	//	valido
	var error = "";
	if ($("#CodOrganismo").val().trim() == "" || $("#CodDependencia").val().trim() == "" || $("#CodPersona").val().trim() == "" || $("#CodCargo").val().trim() == "" || $("#Motivo").val().trim() == "" || $("#VigenciaInicio").val().trim() == "" || $("#VigenciaFin").val().trim() == "" || $("#TipoContrato").val().trim() == "" || $("#FechaDesde").val().trim() == "" || $("#Modalidad").val().trim() == "" || $("#NumeroSolicitado").val().trim() == "") error = "Debe ingresar los campos obligatorios";
	else if (!valFecha($("#VigenciaInicio").val()) || !valFecha($("#VigenciaFin").val()) || formatFechaAMD($("#VigenciaInicio").val()) > formatFechaAMD($("#VigenciaFin").val())) error = "Formato de Fecha de Vigencia Incorrecta";
	else if (!valFecha($("#FechaDesde").val()) || !valFecha($("#FechaHasta").val()) || ($("#FechaHasta").val().trim() != "" && formatFechaAMD($("#FechaDesde").val()) > formatFechaAMD($("#FechaHasta").val()))) error = "Formato de Fecha de Salida Incorrecta";
	else if (isNaN(setNumero($("#NumeroSolicitado").val())) || parseInt(setNumero($("#NumeroSolicitado").val())) <= 0) error = "Nro. de Vacantes Incorrecto";
	
	//	formulario
	var post = "";
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
	
	//	evaluacion
	var evaluacion = "";
	var frm = document.getElementById("frm_evaluacion");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "Secuencia") evaluacion += n.value + ";char:td;";
		else if (n.name == "Evaluacion") evaluacion += n.value + ";char:td;";
		else if (n.name == "Etapa") evaluacion += n.value + ";char:td;";
		else if (n.name == "PlantillaEvaluacion") evaluacion += n.value + ";char:tr;";
	}
	var len = evaluacion.length; len-=9;
	evaluacion = evaluacion.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=requerimientos&accion="+accion+"&evaluacion="+evaluacion+"&"+post,
			async: false,
			success: function(resp){
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					if (accion == "nuevo") {
						var funct = "document.getElementById('frmentrada').submit();";
						cajaModal("Requerimiento de Personal <strong>Nro. "+partes[1]+"</strong> se cre&oacute; con &eacute;xito", "exito", 400, funct);
					}
					else form.submit();
				}
			}
		});
	}
	return false;
}

//	requerimientos de personal (registrar)
function requerimientos_registrar() {
	//	valores
	var error = "";
	var CodOrganismo = $("#CodOrganismo").val();
	var Requerimiento = $("#Requerimiento").val();
	var TipoPostulante = $("#TipoPostulante").val();
	var Postulante = $("#Postulante").val();
	var Evaluacion = $("#Evaluacion").val();
	var Secuencia = $("#Secuencia").val();
	
	//	competencias
	var competencias = "";
	var frm = document.getElementById("frm_competencias");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.type == "radio" && n.checked) competencias += n.id + ";char:td;" + n.value + ";char:tr;";
	}
	var len = competencias.length; len-=9;
	competencias = competencias.substr(0, len);
	
	//	ajax
	$.ajax({
		type: "POST",
		url: "lib/form_ajax.php",
		data: "modulo=requerimientos&accion=registrar-puntaje&competencias="+competencias+"&CodOrganismo="+CodOrganismo+"&Requerimiento="+Requerimiento+"&TipoPostulante="+TipoPostulante+"&Evaluacion="+Evaluacion+"&Postulante="+Postulante+"&Secuencia="+Secuencia,
		async: false,
		success: function(resp) {
			var partes = resp.split("|");
			if (partes[0].trim() != "") cajaModalParent(partes[0], "error", 400);
			else {
				actualizar_postulantes_evaluaciones();
				cajaModalParent("Se registro con exito el Puntaje de las Competencias", "exito", 400);
			}
		}
	});
}

//	requerimientos de personal (aprobar/descalificar)
function requerimientos_postulantes_aprobar(accion) {
	//	valores
	var error = "";
	var sel_candidato = $("#sel_candidato").val();
	if (sel_candidato == "") {
		cajaModalParent("Debe seleccionar un Candidato", "error", 400);
	} else {
		var CodOrganismo = $("#CodOrganismo").val();
		var Requerimiento = $("#Requerimiento").val();
		var TipoPostulante = sel_candidato.substr(10, 1);
		var Postulante = sel_candidato.substr(11);
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=requerimientos&accion="+accion+"&CodOrganismo="+CodOrganismo+"&Requerimiento="+Requerimiento+"&TipoPostulante="+TipoPostulante+"&Postulante="+Postulante,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModalParent(resp, "error", 400);
				else {
					actualizar_postulantes();
				}
			}
		});
	}
}

//	requerimientos de personal (contratar)
function requerimientos_postulantes_contratar() {
	//	valores
	var error = "";
	var sel_candidato = $("#sel_candidato").val();
	var NumeroPendiente = parseInt($("#NumeroPendiente").val());
	if (sel_candidato == "") cajaModalParent("Debe seleccionar un Candidato", "error", 400);
	else {
		var CodOrganismo = $("#CodOrganismo").val();
		var Requerimiento = $("#Requerimiento").val();
		var TipoPostulante = sel_candidato.substr(10, 1);
		var Postulante = sel_candidato.substr(11);
		if (TipoPostulante == "E") {
			var href = "../empleados/gehen.php?anz=empleados_form&opcion=contratar&CodOrganismoReq="+CodOrganismo+"&Requerimiento="+Requerimiento+"&TipoPostulante="+TipoPostulante+"&Postulante="+Postulante+"&iframe=true&width=775&height=105%";
		} else {
			var href = "../empleados/gehen.php?anz=empleados_nivelaciones_form&opcion=contratar&CodOrganismoReq="+CodOrganismo+"&Requerimiento="+Requerimiento+"&TipoPostulante="+TipoPostulante+"&Postulante="+Postulante+"&iframe=true&width=925&height=450";
		}
		parent.$("#a_contratar").attr("href", href);
		parent.document.getElementById("a_contratar").click();
	}
}

//	capacitaciones
function capacitaciones(form, accion) {
	//	valido
	var error = "";
	if ($("#CodOrganismo").val().trim() == "" || $("#Solicitante").val().trim() == "" || $("#CodCurso").val().trim() == "" || $("#CodCentroEstudio").val().trim() == "" || $("#TipoCurso").val().trim() == "" || $("#Modalidad").val().trim() == "" || $("#TipoCapacitacion").val().trim() == "" || $("#CodCiudad").val().trim() == "" || $("#Vacantes").val().trim() == "" || $("#FechaDesde").val().trim() == "" || $("#FechaHasta").val().trim() == "") error = "Debe ingresar los campos obligatorios";
	else if (!valFecha($("#FechaDesde").val()) || !valFecha($("#FechaHasta").val()) || formatFechaAMD($("#FechaDesde").val()) > formatFechaAMD($("#FechaHasta").val())) error = "Formato de Fechas Incorrecta";
	else if (isNaN(setNumero($("#Vacantes").val())) || parseInt(setNumero($("#Vacantes").val())) <= 0) error = "Nro. de Vacantes Incorrecto";
	else if ($("#FlagCostos").attr("checked") != "checked" && (isNaN(setNumero($("#CostoEstimado").val())) || parseFloat(setNumero($("#CostoEstimado").val())) <= 0)) error = "Monto del Costo Estimado Incorrecto";
	else if ($("#FlagCostos").attr("checked") != "checked" && (isNaN(setNumero($("#MontoAsumido").val())) || parseFloat(setNumero($("#MontoAsumido").val())) < 0)) error = "Monto Asumido Incorrecto";
	
	//	formulario
	var post = "";
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
	
	//	participantes
	var detalles_participantes = "";
	var frm = document.getElementById("frm_participantes");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "CodPersona") detalles_participantes += n.value + ";char:td;";
		else if (n.name == "CodDependencia") detalles_participantes += n.value + ";char:tr;";
	}
	var len = detalles_participantes.length; len-=9;
	detalles_participantes = detalles_participantes.substr(0, len);
	if (detalles_participantes == "") error = "Debe insertar alg&uacute;n participante";
	
	//	horario
	var detalles_horario = "";
	if (accion == "aprobar") {
		var frm = document.getElementById("frm_horario");
		for(var i=0; n=frm.elements[i]; i++) {
			if (n.name == "Estado") detalles_horario += n.value + ";char:td;";
			else if (n.name == "Lunes") {
				if (n.checked) detalles_horario += "S;char:td;"; else detalles_horario += "N;char:td;";
			}
			else if (n.name == "Martes") {
				if (n.checked) detalles_horario += "S;char:td;"; else detalles_horario += "N;char:td;";
			}
			else if (n.name == "Miercoles") {
				if (n.checked) detalles_horario += "S;char:td;"; else detalles_horario += "N;char:td;";
			}
			else if (n.name == "Jueves") {
				if (n.checked) detalles_horario += "S;char:td;"; else detalles_horario += "N;char:td;";
			}
			else if (n.name == "Viernes") {
				if (n.checked) detalles_horario += "S;char:td;"; else detalles_horario += "N;char:td;";
			}
			else if (n.name == "Sabado") {
				if (n.checked) detalles_horario += "S;char:td;"; else detalles_horario += "N;char:td;";
			}
			else if (n.name == "Domingo") {
				if (n.checked) detalles_horario += "S;char:td;"; else detalles_horario += "N;char:td;";
			}
			else if (n.name == "FechaDesde") {
				var FechaDesde = n.value;
				if (!valFecha(n.value)) { error = "Se encontraron Lapsos de Fechas Incorrectas en la Ficha de Horario"; break; }
				else detalles_horario += n.value + ";char:td;";
			}
			else if (n.name == "HoraInicioLunes") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraInicioMartes") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraInicioMiercoles") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraInicioJueves") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraInicioViernes") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraInicioSabado") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraInicioDomingo") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "TotalDias") {
				var TotalDias = parseInt(n.value);
				if (isNaN(TotalDias) || TotalDias <= 0) { error = "Se encontró un Total de Dias incorrecto"; break; }
				else detalles_horario += n.value + ";char:td;";
			}
			else if (n.name == "FechaHasta") {
				var FechaHasta = n.value;
				if (!valFecha(n.value)) { error = "Se encontraron Lapsos de Fechas Incorrectas en la Ficha de Horario"; break; }
				else if (formatFechaAMD(FechaDesde) > formatFechaAMD(FechaHasta)) { error = "Se encontraron Lapsos de Fechas Incorrectas en la Ficha de Horario"; break; }
				else detalles_horario += n.value + ";char:td;";
			}
			else if (n.name == "HoraFinLunes") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraFinMartes") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraFinMiercoles") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraFinJueves") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraFinViernes") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraFinSabado") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "HoraFinDomingo") detalles_horario += formatHora(n.value) + ";char:td;";
			else if (n.name == "TotalHoras")  detalles_horario += n.value + ";char:tr;";
		}
		var len = detalles_horario.length; len-=9;
		detalles_horario = detalles_horario.substr(0, len);
		if (detalles_horario == "") error = "Debe ingresar el Detalle de los Horarios";
	}
	
	//	gastos
	var detalles_gastos = "";
	if (accion == "iniciar") {
		var frm = document.getElementById("frm_gastos");
		for(var i=0; n=frm.elements[i]; i++) {
			if (n.name == "Numero") detalles_gastos += n.value + ";char:td;";
			else if (n.name == "Fecha") {
				if (!valFecha(n.value)) { error = "Se encontraron Fechas incorrectas en la ficha de Gastos"; break; }
				else detalles_gastos += formatFechaAMD(n.value) + ";char:td;";
			}
			else if (n.name == "SubTotal") {
				var SubTotal = parseFloat(setNumero(n.value));
				if (isNaN(SubTotal) || SubTotal < 0) { error = "Se encontraron Sub-Totales incorrectos en la ficha de Gastos"; break; }
				else detalles_gastos += SubTotal + ";char:td;";
			}
			else if (n.name == "Impuestos") {
				var Impuestos = parseFloat(setNumero(n.value));
				if (isNaN(Impuestos) || Impuestos < 0) { error = "Se encontraron Sub-Totales incorrectos en la ficha de Gastos"; break; }
				else detalles_gastos += Impuestos + ";char:td;";
			}
			else if (n.name == "Total") detalles_gastos += setNumero(n.value) + ";char:tr;";
		}
		var len = detalles_gastos.length; len-=9;
		detalles_gastos = detalles_gastos.substr(0, len);
		if (detalles_gastos == "") error = "Debe ingresar el Detalle de los Gastos";
	}
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=capacitaciones&accion="+accion+"&detalles_participantes="+detalles_participantes+"&detalles_horario="+detalles_horario+"&detalles_gastos="+detalles_gastos+"&"+post,
			async: false,
			success: function(resp){
				var partes = resp.split("|");
				if (partes[0].trim() != "") cajaModal(partes[0], "error", 400);
				else {
					if (accion == "nuevo") {
						var funct = "document.getElementById('frmentrada').submit();";
						cajaModal("Capacitaci&oacute;n <strong>Nro. "+partes[1]+"</strong> se cre&oacute; con &eacute;xito", "exito", 400, funct);
					}
					else form.submit();
				}
			}
		});
	}
	return false;
}

//	tipos de evaluacion
function evaluacion_tipo(form, accion) {
	//	valido
	var error = "";
	if ($("#TipoEvaluacion").val().trim() == "" || $("#Descripcion").val().trim() == "") error = "Debe ingresar los campos obligatorios";
	else if (!valCodigo($("#TipoEvaluacion").val())) error = "No se permiten caracteres especiales para el c&oacute;digo";
	
	//	formulario
	var post = "";
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
	
	//	grados
	var detalles_grados = "";
	var frm = document.getElementById("frm_grados");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "Grado") detalles_grados += n.value + ";char:td;";
		else if (n.name == "Descripcion") {
			if (n.value.trim() == "") { error = "La Descripci&oacute;n es Obligatoria para los Grados"; break; }
			else detalles_grados += changeUrl(n.value) + ";char:td;";
		}
		else if (n.name == "PuntajeMin") {
			var PuntajeMin = parseInt(setNumero(n.value));
			if (isNaN(PuntajeMin)) { error = "Formato de Puntuaci&oacute;n Incorrecta"; break; }
			else detalles_grados += n.value + ";char:td;";
		}
		else if (n.name == "PuntajeMax") {
			var PuntajeMax = parseInt(setNumero(n.value));
			if (isNaN(PuntajeMax)) { error = "Formato de Puntuaci&oacute;n Incorrecta"; break; }
			else if (PuntajeMin > PuntajeMax) { error = "Puntaje Min. no puede ser mayor que Puntaje Max."; break; }
			else detalles_grados += n.value + ";char:td;";
		}
		else if (n.name == "Estado") detalles_grados += n.value + ";char:tr;";
	}
	var len = detalles_grados.length; len-=9;
	detalles_grados = detalles_grados.substr(0, len);
	if (detalles_grados == "") error = "Debe insertar los Grados de Calificaci&oacute;n";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=evaluacion_tipo&accion="+accion+"&detalles_grados="+detalles_grados+"&"+post,
			async: false,
			success: function(resp){
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	evaluacion
function evaluacion(form, accion) {
	//	valido
	var error = "";
	if ($("#TipoEvaluacion").val().trim() == "" || $("#Descripcion").val().trim() == "") error = "Debe ingresar los campos obligatorios";
	else if (isNaN($("#PuntajeMin").val()) || isNaN($("#PuntajeMax").val()) || parseInt($("#PuntajeMin").val()) > parseInt($("#PuntajeMax").val()) || parseInt($("#PuntajeMin").val()) <= 0 || parseInt($("#PuntajeMax").val()) <= 0) error = "Puntaje incorrecto";
	
	//	formulario
	var post = "";
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
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=evaluacion&accion="+accion+"&"+post,
			async: false,
			success: function(resp){
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	items para evaluacion
function evaluacion_items(form, accion) {
	//	valido
	var error = "";
	if ($("#Evaluacion").val().trim() == "" || $("#Descripcion").val().trim() == "" || $("#PuntajeMin").val().trim() == "" || $("#PuntajeMax").val().trim() == "") error = "Debe ingresar los campos obligatorios";
	else if (isNaN($("#PuntajeMin").val()) || isNaN($("#PuntajeMax").val()) || parseInt($("#PuntajeMin").val()) > parseInt($("#PuntajeMax").val()) || parseInt($("#PuntajeMin").val()) <= 0 || parseInt($("#PuntajeMax").val()) <= 0) error = "Puntaje incorrecto";
	
	//	formulario
	var post = "";
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
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=evaluacion_items&accion="+accion+"&"+post,
			async: false,
			success: function(resp){
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	grupos de competencia
function competencias_grupo(form, accion) {
	//	valido
	var error = "";
	if ($("#Area").val().trim() == "" || $("#Descripcion").val().trim() == "" || $("#TipoEvaluacion").val().trim() == "") error = "Debe ingresar los campos obligatorios";
	else if (!valCodigo($("#Area").val())) error = "No se permiten caracteres especiales para el <strong>C&oacute;digo</strong>";
	
	//	formulario
	var post = "";
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
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=competencias_grupo&accion="+accion+"&"+post,
			async: false,
			success: function(resp){
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	competencias
function competencias(form, accion) {
	//	valido
	var error = "";
	if ($("#Descripcion").val().trim() == "" || $("#Area").val() == "" || $("#Nivel").val() == "" || $("#Calificacion").val() == "" || $("#TipoCompetencia").val() == "" || $("#ValorRequerido").val() == "" || $("#ValorMinimo").val() == "") error = "Debe ingresar los campos obligatorios";
	
	//	formulario
	var post = "";
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
	
	//	grados
	var detalles_grados = "";
	var frm = document.getElementById("frm_grados");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "TipoEvaluacion") detalles_grados += n.value + ";char:td;";
		else if (n.name == "Grado") detalles_grados += n.value + ";char:td;";
		else if (n.name == "Valor") {
			var Valor = parseInt(setNumero(n.value));
			if (isNaN(Valor)) { error = "Formato de Valor Incorrecto"; break; }
			else detalles_grados += Valor + ";char:td;";
		}
		else if (n.name == "Explicacion") detalles_grados += changeUrl(n.value) + ";char:td;";
		else if (n.name == "Explicacion2") detalles_grados += changeUrl(n.value) + ";char:td;";
		else if (n.name == "Estado") detalles_grados += n.value + ";char:tr;";
	}
	var len = detalles_grados.length; len-=9;
	detalles_grados = detalles_grados.substr(0, len);
	if (detalles_grados == "") error = "Debe insertar los Grados de Calificaci&oacute;n";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=competencias&accion="+accion+"&detalles_grados="+detalles_grados+"&"+post,
			async: false,
			success: function(resp){
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	plantillade competencias
function competencias_plantilla(form, accion) {
	//	valido
	var error = "";
	if ($("#Descripcion").val().trim() == "" || $("#TipoEvaluacion").val() == "") error = "Debe ingresar los campos obligatorios";
	
	//	formulario
	var post = "";
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
	
	//	competencias
	var detalles_competencias = "";
	var frm = document.getElementById("frm_competencias");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "Competencia") detalles_competencias += n.value + ";char:td;";
		else if (n.name == "Peso") detalles_competencias += n.value + ";char:td;";
		else if (n.name == "FactorParticipacion") detalles_competencias += n.value + ";char:td;";
		else if (n.name == "FlagPotencial") {
			if (n.checked) detalles_competencias += "S;char:td;"; else detalles_competencias += "N;char:td;";
		}
		else if (n.name == "FlagCompetencia") {
			if (n.checked) detalles_competencias += "S;char:td;"; else detalles_competencias += "N;char:td;";
		}
		else if (n.name == "FlagConceptual") {
			if (n.checked) detalles_competencias += "S;char:tr;"; else detalles_competencias += "N;char:tr;";
		}
	}
	var len = detalles_competencias.length; len-=9;
	detalles_competencias = detalles_competencias.substr(0, len);
	if (detalles_competencias == "") error = "Debe insertar las Competencias de la Plantilla";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=competencias_plantilla&accion="+accion+"&detalles_competencias="+detalles_competencias+"&"+post,
			async: false,
			success: function(resp){
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	grados de calificacion
function grados_calificacion(form, accion) {
	//	valido
	var error = "";
	if ($("#Descripcion").val().trim() == "") error = "Debe ingresar los campos obligatorios";
	else if (isNaN($("#PuntajeMin").val()) || isNaN($("#PuntajeMax").val()) || parseInt($("#PuntajeMin").val()) > parseInt($("#PuntajeMax").val()) || parseInt($("#PuntajeMin").val()) <= 0 || parseInt($("#PuntajeMax").val()) <= 0) error = "Puntaje incorrecto";
	
	//	formulario
	var post = "";
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
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=grados_calificacion&accion="+accion+"&"+post,
			async: false,
			success: function(resp){
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	registro de eventos
function bono_periodos_registrar_eventos_procesar(form) {
	//	eventos
	var error = "";
	var detalles_eventos = "";
	var frm = document.getElementById("frm_eventos");
	for(var i=0; n=frm.elements[i]; i++) {
		if (n.name == "Fecha") {
			var Fecha = n.value.trim();
			if (!valFecha(Fecha) || Fecha.trim() == "") { error = "Se encontraron lineas con Fechas Incorrectas"; break; }
			else detalles_eventos += formatFechaAMD(Fecha) + ";char:td;";
		}
		else if (n.name == "HoraSalida") {
			var HoraSalida = n.value.trim();
			if (!valHora(HoraSalida) && HoraSalida.trim() != "") { error = "Se encontraron lineas con Horas Incorrectas"; break; }
			else detalles_eventos += formatHora(HoraSalida) + ";char:td;";
		}
		else if (n.name == "HoraEntrada") {
			var HoraEntrada = n.value.trim();
			if (!valHora(HoraEntrada) && HoraEntrada.trim() != "") { error = "Se encontraron lineas con Horas Incorrectas"; break; }
			else detalles_eventos += formatHora(HoraEntrada) + ";char:td;";
		}
		else if (n.name == "TotalHoras") {
			var TotalHoras = n.value.trim();
			if (TotalHoras == "") { error = "Se encontraron lineas con Totales en Blanco"; break; }
			else if (TotalHoras == "0:0") { error = "Se encontraron lineas con Horas Fuera del Intervalo del Horario"; break; }
			else detalles_eventos += TotalHoras + ";char:td;";
		}
		else if (n.name == "TipoEvento") detalles_eventos += n.value + ";char:td;";
		else if (n.name == "Motivo") detalles_eventos += n.value + ";char:td;";
		else if (n.name == "Observaciones") detalles_eventos += changeUrl(n.value) + ";char:tr;";
	}
	var len = detalles_eventos.length; len-=9;
	detalles_eventos = detalles_eventos.substr(0, len);
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=bono_periodos_registrar_eventos_procesar&detalles_eventos="+detalles_eventos+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	proceso de jubilacion
function jubilaciones(accion) {
	$(".div-progressbar").css("display", "block");
	
	//	valores
	var NomDependencia = changeUrl($("#CodDependencia option:selected").text());
	var DescripCargo = changeUrl($("#CodCargo option:selected").text());
	var AniosServicio = $("#AniosServicio").val();
	var SueldoBase = parseFloat(setNumero($("#SueldoBase").val()));
	var MontoJubilacion = parseFloat(setNumero($("#MontoJubilacion").val()));
	var Coeficiente = parseFloat(setNumero($("#Coeficiente").val()));
	var TotalSueldo = parseFloat(setNumero($("#TotalSueldo").val()));
	var TotalPrimas = parseFloat(setNumero($("#TotalPrimas").val()));
		
	//	valido
	var error = "";
	if ($("#CodPersona").val() == "") error = "Debe seleccionar el Empleado a Procesar";
	else if ($("#FlagCumple").val() != "S") error = "El Empleado NO cumple con los requisitos para optar a la Jubilaci&oacute;n";
	else if (Coeficiente <= 0 || isNaN(Coeficiente)) error = "Coeficiente Incorrecto";
	else if (TotalSueldo <= 0 || isNaN(TotalSueldo)) error = "Total de Sueldos Incorrecto";
	else if (TotalPrimas <= 0 || isNaN(TotalPrimas)) error = "Total de Primas de Antiguedad Incorrecto";
	else if (MontoJubilacion <= 0 || isNaN(MontoJubilacion)) error = "Monto de Jubilaci&oacute;n Incorrecto";
	if (accion == "nuevo" || accion == "modificar") {
		if ($("#CodTipoNom").val() == "") error = "Debe seleccionar el Tipo de N&oacute;mina";
		else if ($("#CodTipoTrabajador").val() == "") error = "Debe seleccionar el Tipo de Trabajador";
		else if ($("#Fegreso").val() == "") error = "Debe ingresar la Fecha de Egreso";
		else if (!valFecha($("#Fegreso").val())) error = "Formato de Fecha de Egreso Incorrecta";
		else if ($("#CodMotivoCes").val() == "") error = "Debe seleccionar el Motivo del Cese";
	}
	
	//	detalles
	if (error == "") {
		//	antecedentes
		var detalles_antecedentes = "";
		var frm = document.getElementById("frm_antecedentes");
		for(var i=0; n=frm.elements[i]; i++) {
			if (n.name == "Organismo") detalles_antecedentes += changeUrl(n.value) + ";char:td;";
			else if (n.name == "FIngreso") detalles_antecedentes += formatFechaAMD(n.value) + ";char:td;";
			else if (n.name == "FEgreso") detalles_antecedentes += formatFechaAMD(n.value) + ";char:td;";
			else if (n.name == "Anios") detalles_antecedentes += n.value + ";char:td;";
			else if (n.name == "Meses") detalles_antecedentes += n.value + ";char:td;";
			else if (n.name == "Dias") detalles_antecedentes += n.value + ";char:tr;";
		}
		var len = detalles_antecedentes.length; len-=9;
		detalles_antecedentes = detalles_antecedentes.substr(0, len);
		
		//	sueldos
		var detalles_sueldos = "";
		var frm = document.getElementById("frm_sueldos");
		for(var i=0; n=frm.elements[i]; i++) {
			if (n.name == "Secuencia") detalles_sueldos += n.value + ";char:td;";
			else if (n.name == "Periodo") detalles_sueldos += n.value + ";char:td;";
			else if (n.name == "CodConcepto") detalles_sueldos += n.value + ";char:td;";
			else if (n.name == "Monto") detalles_sueldos += setNumero(n.value) + ";char:tr;";
		}
		var len = detalles_sueldos.length; len-=9;
		detalles_sueldos = detalles_sueldos.substr(0, len);
		if (detalles_sueldos == "") error = "El Empleado debe tener por lo menos 24 cotizaciones en la Ficha de Relaci&oacute;n de Sueldos";
	}
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	general
		var post_general = getForm(document.getElementById('frmgeneral'));
		
		//	jubilacion
		var post_jubilacion = getForm(document.getElementById('frmjubilacion'));
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=jubilaciones&accion="+accion+"&NomDependencia="+NomDependencia+"&DescripCargo="+DescripCargo+"&AniosServicio="+AniosServicio+"&detalles_antecedentes="+detalles_antecedentes+"&detalles_sueldos="+detalles_sueldos+"&Coeficiente="+Coeficiente+"&TotalSueldo="+TotalSueldo+"&TotalPrimas="+TotalPrimas+"&MontoJubilacion="+MontoJubilacion+"&SueldoBase="+SueldoBase+"&"+post_general+"&"+post_jubilacion,
			async: false,
			success: function(resp) {
				var datos = resp.split("|");
				if (datos[0].trim() != "") cajaModal(datos[0], "error", 400);
				else {
					if (accion == "nuevo") {
						var funct = "document.getElementById('frmgeneral').submit();";
						cajaModal("Proceso de Jubilaci&oacute;n <strong>Nro. "+datos[1]+"</strong> generado exitosamente", "exito", 400, funct);
					}
					else document.getElementById('frmgeneral').submit();;
				}
			}
		});
	}
	return false;
}

//	proceso de pension x invalidez
function pensiones_invalidez(form, accion) {
	$(".div-progressbar").css("display", "block");
	
	//	valores
	var NomDependencia = changeUrl($("#CodDependencia option:selected").text());
	var DescripCargo = changeUrl($("#CodCargo option:selected").text());
	var MontoPension = parseFloat(setNumero($("#MontoPension").val()));
		
	//	valido
	var error = "";
	if ($("#CodPersona").val() == "") error = "Debe seleccionar el Empleado a Procesar";
	else if ($("#FlagCumple").val() != "S") error = "El Empleado NO cumple con los requisitos para optar a la Pensi&oacute;n";
	else if (MontoPension <= 0 || isNaN(MontoPension)) error = "Monto de Pensi&oacute;n Incorrecto";
	if (accion == "aprobar") {
		if ($("#CodTipoNom").val() == "") error = "Debe seleccionar el Tipo de N&oacute;mina";
		else if ($("#CodTipoTrabajador").val() == "") error = "Debe seleccionar el Tipo de Trabajador";
		else if ($("#Fegreso").val() == "") error = "Debe ingresar la Fecha de Egreso";
		else if (!valFecha($("#Fegreso").val())) error = "Formato de Fecha de Egreso Incorrecta";
		else if ($("#CodMotivoCes").val() == "") error = "Debe seleccionar el Motivo del Cese";
	}
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	datos
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=pensiones_invalidez&accion="+accion+"&NomDependencia="+NomDependencia+"&DescripCargo="+DescripCargo+"&"+post,
			async: false,
			success: function(resp) {
				var datos = resp.split("|");
				if (datos[0].trim() != "") cajaModal(datos[0], "error", 400);
				else {
					if (accion == "nuevo") {
						var funct = "document.getElementById('frmentrada').submit();";
						cajaModal("Proceso de Pensi&oacute;n <strong>Nro. "+datos[1]+"</strong> generado exitosamente", "exito", 400, funct);
					}
					else document.getElementById('frmentrada').submit();;
				}
			}
		});
	}
	return false;
}

//	proceso de pension x sobreviviente
function pensiones_sobreviviente(accion) {
	$(".div-progressbar").css("display", "block");
	
	//	valores
	var NomDependencia = changeUrl($("#CodDependencia option:selected").text());
	var DescripCargo = changeUrl($("#CodCargo option:selected").text());
	var AniosServicio = $("#AniosServicio").val();
	var SueldoBase = parseFloat(setNumero($("#SueldoBase").val()));
	var MontoJubilacion = parseFloat(setNumero($("#MontoJubilacion").val()));
	var Coeficiente = parseFloat(setNumero($("#Coeficiente").val()));
	var TotalSueldo = parseFloat(setNumero($("#TotalSueldo").val()));
	var TotalPrimas = parseFloat(setNumero($("#TotalPrimas").val()));
		
	//	valido
	var error = "";
	if ($("#CodPersona").val() == "") error = "Debe seleccionar el Empleado a Procesar";
	else if ($("#FlagCumple").val() != "S") error = "El Empleado NO cumple con los requisitos para optar a la Jubilaci&oacute;n";
	else if (Coeficiente <= 0 || isNaN(Coeficiente)) error = "Coeficiente Incorrecto";
	else if (TotalSueldo <= 0 || isNaN(TotalSueldo)) error = "Total de Sueldos Incorrecto";
	else if (TotalPrimas <= 0 || isNaN(TotalPrimas)) error = "Total de Primas de Antiguedad Incorrecto";
	else if (MontoJubilacion <= 0 || isNaN(MontoJubilacion)) error = "Monto de Jubilaci&oacute;n Incorrecto";
	if (accion == "nuevo" || accion == "modificar") {
		if ($("#CodTipoNom").val() == "") error = "Debe seleccionar el Tipo de N&oacute;mina";
		else if ($("#CodTipoTrabajador").val() == "") error = "Debe seleccionar el Tipo de Trabajador";
		else if ($("#Fegreso").val() == "") error = "Debe ingresar la Fecha de Egreso";
		else if (!valFecha($("#Fegreso").val())) error = "Formato de Fecha de Egreso Incorrecta";
		else if ($("#CodMotivoCes").val() == "") error = "Debe seleccionar el Motivo del Cese";
	}
	
	//	detalles
	if (error == "") {
		//	antecedentes
		var detalles_antecedentes = "";
		var frm = document.getElementById("frm_antecedentes");
		for(var i=0; n=frm.elements[i]; i++) {
			if (n.name == "Organismo") detalles_antecedentes += changeUrl(n.value) + ";char:td;";
			else if (n.name == "FIngreso") detalles_antecedentes += formatFechaAMD(n.value) + ";char:td;";
			else if (n.name == "FEgreso") detalles_antecedentes += formatFechaAMD(n.value) + ";char:td;";
			else if (n.name == "Anios") detalles_antecedentes += n.value + ";char:td;";
			else if (n.name == "Meses") detalles_antecedentes += n.value + ";char:td;";
			else if (n.name == "Dias") detalles_antecedentes += n.value + ";char:tr;";
		}
		var len = detalles_antecedentes.length; len-=9;
		detalles_antecedentes = detalles_antecedentes.substr(0, len);
		
		//	sueldos
		var detalles_sueldos = "";
		var frm = document.getElementById("frm_sueldos");
		for(var i=0; n=frm.elements[i]; i++) {
			if (n.name == "Secuencia") detalles_sueldos += n.value + ";char:td;";
			else if (n.name == "Periodo") detalles_sueldos += n.value + ";char:td;";
			else if (n.name == "CodConcepto") detalles_sueldos += n.value + ";char:td;";
			else if (n.name == "Monto") detalles_sueldos += setNumero(n.value) + ";char:tr;";
		}
		var len = detalles_sueldos.length; len-=9;
		detalles_sueldos = detalles_sueldos.substr(0, len);
		if (detalles_sueldos == "") error = "El Empleado debe tener por lo menos 24 cotizaciones en la Ficha de Relaci&oacute;n de Sueldos";
		
		//	beneficiarios
		var detalles_beneficiarios = "";
		var frm = document.getElementById("frm_beneficiarios");
		for(var i=0; n=frm.elements[i]; i++) {
			if (n.name == "NroDocumento") detalles_beneficiarios += n.value + ";char:td;";
			else if (n.name == "NombreCompleto") detalles_beneficiarios += changeUrl(n.value) + ";char:td;";
			else if (n.name == "FlagPrincipal") {
				if (n.checked) detalles_beneficiarios += "S;char:td;";
				else detalles_beneficiarios += "N;char:td;";
			}
			else if (n.name == "Parentesco") detalles_beneficiarios += n.value + ";char:td;";
			else if (n.name == "FechaNacimiento") detalles_beneficiarios += formatFechaAMD(n.value) + ";char:td;";
			else if (n.name == "Sexo") detalles_beneficiarios += n.value + ";char:td;";
			else if (n.name == "FlagIncapacitados") detalles_beneficiarios += n.value + ";char:td;";
			else if (n.name == "FlagEstudia") detalles_beneficiarios += n.value + ";char:tr;";
		}
		var len = detalles_beneficiarios.length; len-=9;
		detalles_beneficiarios = detalles_beneficiarios.substr(0, len);
	}
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	general
		var post_general = getForm(document.getElementById('frmgeneral'));
		
		//	jubilacion
		var post_jubilacion = getForm(document.getElementById('frmjubilacion'));
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=pensiones_sobreviviente&accion="+accion+"&NomDependencia="+NomDependencia+"&DescripCargo="+DescripCargo+"&AniosServicio="+AniosServicio+"&detalles_antecedentes="+detalles_antecedentes+"&detalles_sueldos="+detalles_sueldos+"&detalles_beneficiarios="+detalles_beneficiarios+"&Coeficiente="+Coeficiente+"&TotalSueldo="+TotalSueldo+"&TotalPrimas="+TotalPrimas+"&MontoJubilacion="+MontoJubilacion+"&SueldoBase="+SueldoBase+"&"+post_general+"&"+post_jubilacion,
			async: false,
			success: function(resp) {
				var datos = resp.split("|");
				if (datos[0].trim() != "") cajaModal(datos[0], "error", 400);
				else {
					if (accion == "nuevo") {
						var funct = "document.getElementById('frmgeneral').submit();";
						cajaModal("Proceso de Jubilaci&oacute;n <strong>Nro. "+datos[1]+"</strong> generado exitosamente", "exito", 400, funct);
					}
					else document.getElementById('frmgeneral').submit();;
				}
			}
		});
	}
	return false;
}

//	reingreso/cese
function reingreso(form, accion) {
	$(".div-progressbar").css("display", "block");
	
	//	valido
	var error = "";
	if ($("#Tipo").val() == "C") {
		if ($("#CodMotivoCes").val() == "") error = "Debe seleccionar el Motivo de Cese";
		else if ($("#ObsCese").val().trim() == "") error = "Debe llenar el campo Explicaci&oacute;n";
		else if ($("#FechaEgreso").val().trim() == "") error = "Debe ingresar la Fecha de Egreso";
		else if (!valFecha($("#FechaEgreso").val())) error = "Fecha de Egreso incorrecta";
	}
	else if ($("#Tipo").val() == "R") {
		if ($("#CodPersona").val() == "") error = "Debe seleccionar un empleado";
		else if ($("#CodOrganismo").val() == "") error = "Debe seleccionar un Organismo";
		else if ($("#CodDependencia").val() == "") error = "Debe seleccionar una Dependencia";
		else if ($("#CodCargo").val() == "") error = "Debe seleccionar un Cargo";
		else if ($("#FechaIngreso").val().trim() == "") error = "Debe ingresar la Fecha de Ingreso";
		else if (!valFecha($("#FechaIngreso").val())) error = "Fecha de Ingreso incorrecta";
	}
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		var Dependencia = $("#CodDependencia option:selected").text();
		post += "&Dependencia="+Dependencia;
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=reingreso&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}

//	tabla de disfrutes vacacionales
function disfrute_vacacionales(form, accion) {
	bloqueo(true);
	
	//	valido
	var error = "";
	if ($("#CodTipoNom").val() == "" || $("#NroAnio").val().trim() == "" || $("#DiasDisfrutes").val().trim() == "" || $("#DiasAdicionales").val().trim() == "") error = "Debe llenar los campos obligatorios";
	if (isNaN(setNumero($("#NroAnio").val()))) error = "Formato para el A&ntilde;o Incorrecto";
	else if (isNaN(setNumero($("#DiasDisfrutes").val()))) error = "Formato para los Dias de Disfrute Incorrecto";
	else if (isNaN(setNumero($("#DiasAdicionales").val()))) error = "Formato para los Dias Adicionales Incorrecto";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=disfrute_vacacionales&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}