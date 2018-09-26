// JavaScript Document

//	
function selSpan(span) {
	$(".span-selected").removeClass("span-selected").addClass("span");
	span.addClass("span-selected");
}

//	agregar
function control_agregar(boton) {
	if (boton == ">") {
		if ($("#lista_disponibles .trListaBodySel").length > 0) {
			$("#lista_disponibles .trListaBodySel").clone(true).appendTo("#lista_aprobados");
			$("#lista_disponibles .trListaBodySel").remove();
		} else cajaModal("Debe seleccionar un Empleado", "error", 400);
	}
	else if (boton == ">>") {
		if ($("#lista_disponibles tr").length > 0) {
			$("#lista_disponibles tr").clone(true).appendTo("#lista_aprobados");
			$("#lista_disponibles tr").remove();
		} else cajaModal("Lista vacia", "error", 400);
	}
	$("#rows_disponibles").html($("#lista_disponibles tr").length);
	$("#rows_aprobados").html($("#lista_aprobados tr").length);
}

//	abrir reporte
function procesos_control_payroll(reporte) {
	//	personas
	var detalles_personas = "";
	var frm_personas = document.getElementById("frm_aprobados");
	for(var i=0; n=frm_personas.elements[i]; i++) {
		if (n.name == "personas" && n.checked) detalles_personas += n.value + "|";
	}
	var len = detalles_personas.length; len-=1;
	detalles_personas = detalles_personas.substr(0, len);
	
	//	valido
	if (detalles_personas == "") cajaModal("Debe seleccionar por lo menos un empleado", "error", 400);
	else {
		//	formulario
		var get = getForm(document.getElementById('frmentrada'));
		var NomProceso = $("#fCodTipoProceso option:selected").text();
		get = get + "&NomProceso="+NomProceso;
		var url = "pr_"+reporte+"_pdf.php?empleados=" + detalles_personas + "&" + get + "&iframe=true&width=100%&height=100%";
		$("#a_reporte").attr("href", url);
		document.getElementById("a_reporte").click();
	}
}

//	
function ajuste_salarial_check(id) {
	var boo = $("#CodNivel"+id).prop("checked");
	$("#Porcentaje"+id).val("0,00").prop("readonly", !boo);
	$("#Monto"+id).val("0,00").prop("readonly", !boo);
	$("#SueldoNuevo"+id).val("0,00");
}

//	
function ajuste_salarial_montos(id) {
	var SueldoBasico = setNumero($("#SueldoBasico"+id).val());
	var Porcentaje = setNumero($("#Porcentaje"+id).val());
	var Monto = setNumero($("#Monto"+id).val());
	if (Porcentaje > 0 || Monto > 0) var SueldoNuevo = (SueldoBasico * Porcentaje / 100) + Monto + SueldoBasico;
	else SueldoNuevo = 0;
	$("#SueldoNuevo"+id).val(SueldoNuevo).formatCurrency();
}

//	
function ajuste_salarial_emp_check(id) {
	var boo = $("#CodPersona"+id).prop("checked");
	$("#Porcentaje"+id).val("0,00").prop("readonly", !boo);
	$("#Monto"+id).val("0,00").prop("readonly", !boo);
	$("#SueldoNuevo"+id).val("0,00");
}

//	
function ajuste_salarial_emp_nomina_mostrar(CodTipoNom) {
	$("input[name=CodPersona]").prop("checked", false);	
	$("input[name=Porcentaje]").prop("readonly", true).val("0,00");
	$("input[name=Monto]").prop("readonly", true).val("0,00");
	$("input[name=SueldoNuevo]").val("0,00");
	$(".lista").css("display", "none");
	$(".lista."+CodTipoNom).css("display", "block");
}

//	
function ajuste_salarial_emp_nomina_mostrar_ver(CodTipoNom) {
	$(".lista").css("display", "none");
	$(".lista."+CodTipoNom).css("display", "block");
}