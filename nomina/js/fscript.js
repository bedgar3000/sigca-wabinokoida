// JavaScript Document

/*//	mostrar calculo del empleado de antiguedad/fideicomiso
function fideicomiso_procesar_calculo_mostrar(form) {
	bloqueo(true);
	
	//	valido
	var error = "";
	if ($("#Periodo").val().trim() == "" || isNaN($("#Periodo").val())) error = "Debe ingresar un periodo valido";
	else if ($("#CodPersona").val() == "") error = "Debe seleccionar un empleado";
	
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		//	formulario
		var post = getForm(form);
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/fphp_funciones_ajax.php",
			data: "accion=fideicomiso_procesar_calculo_mostrar&"+post,
			async: false,
			success: function(resp) {
				bloqueo(false);
				$("#listado_periodos").html(resp);
			}
		});
	}
}*/

//	FUNCION SELECT DEPENDIENTE
function loadSelectPeriodosNomina(opt) {
	var CodOrganismo = $("#fCodOrganismo").val();
	var CodTipoNom = $("#fCodTipoNom").val();
	//
	var option = "<option value=''>&nbsp;</option>";
	$("#fPeriodo").empty().append(option);
	$("#fCodTipoProceso").empty().append(option);
	//
	if (CodOrganismo != "" && CodTipoNom != "") {
		$.ajax({
			type: "POST",
			url: "../lib/fphp_selects.php",
			data: "tabla=loadSelectPeriodosNomina&CodOrganismo="+CodOrganismo+"&CodTipoNom="+CodTipoNom+"&opt="+opt,
			async: true,
			success: function(resp) {
				$("#fPeriodo").empty().append(resp);
			}
		});
	}
}

//	FUNCION SELECT DEPENDIENTE
function loadSelectPeriodosNominaProcesos(opt) {
	var CodOrganismo = $("#fCodOrganismo").val();
	var CodTipoNom = $("#fCodTipoNom").val();
	var Periodo = $("#fPeriodo").val();
	//
	var option = "<option value=''>&nbsp;</option>";
	$("#fCodTipoProceso").empty().append(option);
	//
	if (CodOrganismo != "" && CodTipoNom != "" && Periodo != "") {
		$.ajax({
			type: "POST",
			url: "../lib/fphp_selects.php",
			data: "tabla=loadSelectPeriodosNominaProcesos&CodOrganismo="+CodOrganismo+"&CodTipoNom="+CodTipoNom+"&Periodo="+Periodo+"&opt="+opt,
			async: true,
			success: function(resp) {
				$("#fCodTipoProceso").empty().append(resp);
			}
		});
	}
}

//	quitar
function control_quitar(boton) {
	bloqueo(true);
	
	//	personas
	var detalles_personas = "";
	var frm_personas = document.getElementById("frm_aprobados");
	if (boton == "<") {
		for(var i=0; n=frm_personas.elements[i]; i++) {
			if (n.name == "personas" && n.checked) detalles_personas += n.value + ";char:tr;";
		}
	} 
	else if (boton == "<<") {
		for(var i=0; n=frm_personas.elements[i]; i++) {
			if (n.name == "personas") detalles_personas += n.value + ";char:tr;";
		}
	}
	var len = detalles_personas.length; len-=9;
	detalles_personas = detalles_personas.substr(0, len);
	
	if (detalles_personas == "" && boton == "<") cajaModal("Debe seleccionar un Empleado", "error", 400);
	if (detalles_personas == "" && boton == "<<") cajaModal("Lista vacia", "error", 400);
	else {
		//	formulario
		var post = getForm(document.getElementById("frmentrada"));
		
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=procesos_control_ejecucion&accion=eliminar&detalles_personas="+detalles_personas+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else {
					bloqueo(false);
					if (boton == "<") {
						$("#lista_aprobados .trListaBodySel").clone(true).appendTo("#lista_disponibles");
						$("#lista_aprobados .trListaBodySel").remove();
					}
					else if (boton == "<<") {
						$("#lista_aprobados tr").clone(true).appendTo("#lista_disponibles");
						$("#lista_aprobados tr").remove();
					}
					$("#rows_disponibles").html($("#lista_disponibles tr").length);
					$("#rows_aprobados").html($("#lista_aprobados tr").length);
				}
			}
		});
	}
}

//	calcular totales acumulado fideicomiso
function fideicomiso_acumulado_totales() {
	var AcumuladoInicialDias = setNumero($("#AcumuladoInicialDias").val());
	var AcumuladoDiasAdicionalInicial = setNumero($("#AcumuladoDiasAdicionalInicial").val());
	var AcumuladoInicialProv = setNumero($("#AcumuladoInicialProv").val());
	var AcumuladoInicialFide = setNumero($("#AcumuladoInicialFide").val());
	
	var AcumuladoProvDias = setNumero($("#AcumuladoProvDias").val());
	var AcumuladoDiasAdicional = setNumero($("#AcumuladoDiasAdicional").val());
	var AcumuladoProv = setNumero($("#AcumuladoProv").val());
	var AcumuladoFide = setNumero($("#AcumuladoFide").val());
	
	var TotalDias = AcumuladoInicialDias + AcumuladoProvDias;
	var TotalDiasAdicional = AcumuladoDiasAdicionalInicial + AcumuladoDiasAdicional;
	var TotalAntiguedad = AcumuladoInicialProv + AcumuladoProv;
	var TotalFideicomiso = AcumuladoInicialFide + AcumuladoFide;
	
	$("#TotalDias").val(TotalDias);
	$("#TotalDiasAdicional").val(TotalDiasAdicional);
	$("#TotalAntiguedad").val(TotalAntiguedad);
	$("#TotalFideicomiso").val(TotalFideicomiso);
}

function setFlagRetroactivo(CodTipoProceso) {
	//	ajax
	$.ajax({
		type: "POST",
		url: "lib/fphp_funciones_ajax.php",
		data: "accion=setFlagRetroactivo&CodTipoProceso="+CodTipoProceso,
		async: false,
		success: function(resp) {
			var Periodo = $("#Periodo").val();
			$("#PeriodoNomina").val(Periodo);
			if (resp.trim() == "S") $(".FlagRetroactivo").css("display", "block");
			else $(".FlagRetroactivo").css("display", "none");
		}
	});
}