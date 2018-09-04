// CREO ESTE METODO PARA PODER ELIMINAR LOS ESPACIOS EN BLANCOS AL PRINCIPIO Y AL FINAL DE CADA CAMPO 
String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g,'') }

//	Me permite redondear a un numero decimales especificos
Number.prototype.decimal = function (num) {
    pot = Math.pow(10,num);
    return parseInt(this * pot) / pot;
}

//	obtenr las dimensiones y las coordenadas de un elemento html
getDimensions = function(oElement) {
	var x, y, w, h;
	x = y = w = h = 0;
	if (document.getBoxObjectFor) { // Mozilla
		var oBox = document.getBoxObjectFor(oElement);
		x = oBox.x-1;
		w = oBox.width;
		y = oBox.y-1;
		h = oBox.height;
	}
	else if (oElement.getBoundingClientRect) { // IE
		var oRect = oElement.getBoundingClientRect();
		x = oRect.left-2;
		w = oElement.clientWidth;
		y = oRect.top-2;
		h = oElement.clientHeight;
	}
	return {x: x, y: y, w: w, h: h};
}

function redondeo(numero, decimales) {
	var original = parseFloat(numero);
	return original.toFixed(decimales);
}

//	FUNCION QUE ME PERMITE CREAR UN NUEVO OBJETO AJAX
function nuevoAjax() { 
	/* Crea el objeto AJAX. Esta funcion es generica para cualquier utilidad de este tipo, por	lo que se puede copiar tal como esta aqui */
	var xmlhttp=false; 
	try 
	{ 
		// Creacion del objeto AJAX para navegadores no IE
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
	}
	catch(e)
	{ 
		try
		{
			// Creacion del objeto AJAX para IE 
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
		} 
		catch(E) { xmlhttp=false; }
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); } 
	return xmlhttp;
}

//	FUNCION QUE CAMBIA DE COLOR LA FILA DE UNA TABLA AL HACER CLICK EL PUNTERO DEL MOUSE SOBRE ELLA
function mClk(src, registro) {
	var seleccionado=document.getElementsByTagName("tr");
	for (var i=0; i<seleccionado.length; i++) {
		if (seleccionado[i].getAttribute((document.all ? 'className' : 'class')) ==	'trListaBodySel') {
			seleccionado[i].setAttribute((document.all ? 'className' : 'class'), "trListaBody");
			break;
		}
	}
	//
	var row=document.getElementById(src.id);	//	OBTENGO LA FILA DEL CLICK
	row.className="trListaBodySel";	//	CAMBIO EL COLOR DE LA FILA
	
	var registro=document.getElementById(registro);
	registro.value=src.id;
}

//	FUNCION QUE CAMBIA DE COLOR LA FILA DE UNA TABLA AL HACER CLICK EL PUNTERO DEL MOUSE SOBRE ELLA (MULTI SELECCION)
function mClkMulti(src) {
	//	Obtengo el id para seleccionar el check
	var fila = src.id.split("_");
	var id = fila[1];
	
	//	Obtengo la fila y le cambio el color
	var row = document.getElementById(src.id);
	if (row.className == "trListaBodySel") {
		row.className="trListaBody";
		document.getElementById(id).checked = false;
	} else {
		row.className="trListaBodySel";
		document.getElementById(id).checked = true;
	}
}

//	FUNCION QUE CAMBIA DE COLOR LA FILA DE UNA TABLA AL HACER CLICK EL PUNTERO DEL MOUSE SOBRE ELLA (MULTI SELECCION)
function clkMulti(src, id) {
	if ($('#'+id).prop('checked')) {
		src.removeClass('trListaBodySel');
		src.addClass('trListaBody');
		$('#'+id).removeAttr('checked');
	} else {
		src.removeClass('trListaBody');
		src.addClass('trListaBodySel');
		$('#'+id).attr('checked', 'checked');
	}
}

//	FUNCION QUE CAMBIA DE COLOR LA FILA DE UNA TABLA AL HACER CLICK EL PUNTERO DEL MOUSE SOBRE ELLA
function clk(src, id, valor) {
	var input = $("#sel_"+id);
	var lista = $("#lista_"+id+" > .trListaBodySel");
	lista.removeClass('trListaBodySel');
	lista.addClass('trListaBody');
	src.removeClass('trListaBody');
	src.addClass('trListaBodySel');
	input.val(valor);
}

//	FUNCION PARA SELECCIONAR TODO
function listaMultiSelTodo(form, nombre, row, check) {
	if (check) var clase = "trListaBodySel"; else var clase = "trListaBody";
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == nombre) {
			var id = n.id;
			var row_id = row + "_" + id;
			var tr = document.getElementById(row_id);
			tr.className = clase;
			document.getElementById(id).checked = check;
		}
	}
}

//	FUNCION QUE IMPRIME EL NUMERO DE REGISTROS Y BLOQUEA/DESBLOQUEA LAS OPCIONES
function totalRegistros(rows, admin, insert, update, del) {
	if (document.getElementById("rows")) {
		var numreg = document.getElementById("rows");
		numreg.innerHTML = "Registros: "+rows;
	}
	if (!rows) {
		$(".ver").attr("disabled","disabled");
		//
		$("#btVer").attr("disabled","disabled");
		$("#btCopiar").attr("disabled","disabled");
		$("#btImprimir").attr("disabled","disabled");
		$("#btCuenta").attr("disabled","disabled");
		$("#btCCosto").attr("disabled","disabled");
		$("#btPersona").attr("disabled","disabled");
		$("#btCuadro").attr("disabled","disabled");
		$("#btVerAutorizaciones").attr("disabled","disabled");
		$("#btVerAlterna").attr("disabled","disabled");
		$("#btImprimirSustento").attr("disabled","disabled");
		$("#btVerVoucher").attr("disabled","disabled");
		$("#btVerPago").attr("disabled","disabled");
		$("#btVerDoc").attr("disabled","disabled");
	}
	if (insert == "N") {
		$(".insert").attr("disabled","disabled");
		//
		$("#btNuevo").attr("disabled","disabled");
		$("#btAgregar").attr("disabled","disabled");
	}
	if (update == "N" || !rows) {
		$(".update").attr("disabled","disabled");
		//
		$("#btEditar").attr("disabled","disabled");
		$("#btModificar").attr("disabled","disabled");
		$("#btAbrir").attr("disabled","disabled");
		$("#btCerrar").attr("disabled","disabled");
		$("#btAnular").attr("disabled","disabled");
		$("#btAprobar").attr("disabled","disabled");
		$("#btMasiva").attr("disabled","disabled");
		$("#btConformar").attr("disabled","disabled");
		$("#btRevisar").attr("disabled","disabled");
		$("#btGenerar").attr("disabled","disabled");
		$("#btIniciar").attr("disabled","disabled");
		$("#btTerminar").attr("disabled","disabled");
		$("#btAutorizaciones").attr("disabled","disabled");
		$("#btAlterna").attr("disabled","disabled");
		$("#btPrepararFactura").attr("disabled","disabled");
		$("#btPrepago").attr("disabled","disabled");
		$("#btTransferir").attr("disabled","disabled");
		$("#btDirigirCompras").attr("disabled","disabled");
		$("#btDespachar").attr("disabled","disabled");
		$("#btPasarCompras").attr("disabled","disabled");
		$("#btRecepcionar").attr("disabled","disabled");
		$("#btEjecutar").attr("disabled","disabled");
		$("#btCopiar").attr("disabled","disabled");
		$("#btReversa").attr("disabled","disabled");
		$("#btDevolucion").attr("disabled","disabled");
		$("#btActualizar").attr("disabled","disabled");
		$("#btActivar").attr("disabled","disabled");
		$("#btInterrumpir").attr("disabled","disabled");
	}
	if (del == "N" || !rows) {
		$(".delete").attr("disabled","disabled");
		//
		$("#btEliminar").attr("disabled","disabled");
		$("#btBorrar").attr("disabled","disabled");
	}}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA 
function cargarPagina(form, pagina) {
	form.action = pagina;
	form.submit();
}

//	FUNCION PARA MOSTRAR/OCULTAR E MENSAJE DE CARGANDO
function cargando(display) {
	/*
	$("#bloqueo").css("display", display);
	$("#cargando").css("display", display);
	*/
	document.getElementById("bloqueo").style.display = display;
	document.getElementById("cargando").style.display = display;
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA xxx
function cargarOpcion(form, pagina, target, param) {
	var codigo = form.registro.value;
	if (codigo == "") alert("¡Debe seleccionar un registro!");
	else {
		if (target == "SELF") cargarPagina(form, pagina);
		else if (target == "BLANK") {
			pagina = pagina + "&registro=" + codigo;
			window.open(pagina, pagina, "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
		}
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA 
function cargarOpcion2(form, pagina, target, param, registro, funct) {
	if (registro == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		if (target == "SELF") cargarPagina(form, pagina);
		else if (target == "BLANK") {
			pagina = pagina + "&registro=" + registro;
			window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
		}
		else if (target == "FUNCTION") {
			eval(funct);
		}
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function cargarOpcionMultiple(form, pagina, target, param, nombre, idregistro, multi) {
	/*
	.-	form	-> referencia al formulario de la lista que se esta seleccionando (objeto)
	.-	pagina	-> ruta de la pagina que se abrira (string)
	.-	target	-> indica si la pagina se abrira en la misma ventana o en una ventana nueva (SELF, BLANK) (string)
	.-	nombre	-> nombre del check que se activa al seleccionar un registro (string)
	.-	multi	-> indica si se puede seleccionar mas de un registro (true, false) (boolean)
	*/
	
	//	lineas
	var error = "";
	var registro = "";
	var lineas = new Number(0);
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == nombre && n.checked) { registro += n.value + ";"; lineas++; }
	}
	var len = registro.length; len--;
	registro = registro.substr(0, len);
	document.getElementById(idregistro).value = registro;
	
	if (lineas == 0) alert("¡Debe seleccionar por lo menos un registro!");
	else if (!multi && lineas > 1) alert("¡No puede seleccionar más de un registro!");
	else {
		if (target == "SELF") cargarPagina(form, pagina);
		else if (target == "BLANK") {
			pagina = pagina + "&registro=" + registro;
			window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
		}
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function cargarOpcionMultiple2(form, pagina, target, param, nombre, idregistro, multi, frmentrada) {
	/*
	.-	form	-> referencia al formulario de la lista que se esta seleccionando (objeto)
	.-	pagina	-> ruta de la pagina que se abrira (string)
	.-	target	-> indica si la pagina se abrira en la misma ventana o en una ventana nueva (SELF, BLANK) (string)
	.-	nombre	-> nombre del check que se activa al seleccionar un registro (string)
	.-	multi	-> indica si se puede seleccionar mas de un registro (true, false) (boolean)
	*/
	
	//	lineas
	var error = "";
	var registro = "";
	var lineas = new Number(0);
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == nombre && n.checked) { registro += n.value + ";"; lineas++; }
	}
	var len = registro.length; len--;
	registro = registro.substr(0, len);
	document.getElementById(idregistro).value = registro;
	
	if (!frmentrada) frmentrada = form;
	
	if (lineas == 0) cajaModal("Debe seleccionar por lo menos un registro", "error", 400);
	else if (!multi && lineas > 1) cajaModal("No puede seleccionar más de un registro", "error", 400);
	else {
		if (target == "SELF") cargarPagina(frmentrada, pagina);
		else if (target == "BLANK") {
			pagina = pagina + "&registro=" + registro;
			window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
		}
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function cargarOpcionValidar(form, pagina, target, param, accion, php) {
	var codigo = form.registro.value;
	if (codigo == "") alert("¡Debe seleccionar un registro!");
	else {
		//	CREO UN OBJETO AJAX
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/"+php+"_fphp_funciones_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("accion="+accion+"&codigo="+codigo);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				var resp = ajax.responseText;
				if (resp.trim() != "") alert(resp.trim());
				else {
					if (target == "SELF") cargarPagina(form, pagina);
					else if (target == "BLANK") {
						pagina = pagina + "&registro=" + codigo;
						window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
					}
				}
			}
		}
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function cargarOpcionValidar2(form, codigo, varpost, pagina, target, param) {
	if (codigo == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		$.ajax({
			type: "POST",
			url: "lib/fphp_funciones_ajax.php",
			data: varpost+"&codigo="+codigo,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp.trim(), "error", 400);
				else {
					if (target == "SELF") cargarPagina(form, pagina);
					else if (target == "BLANK") {
						pagina = pagina + "&registro=" + codigo;
						window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
					}
				}
			}
		});
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function cargarOpcionValidar3(form, codigo, url, varpost, pagina, target, param) {
	if (codigo == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		$.ajax({
			type: "POST",
			url: url,
			data: varpost+"&codigo="+codigo,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp.trim(), "error", 400);
				else {
					if (target == "SELF" || !target) cargarPagina(form, pagina);
					else if (target == "BLANK") {
						pagina = pagina + "&registro=" + codigo;
						window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
					}
				}
			}
		});
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function cargarOpcionValidar4(form, codigo, url, varpost, pagina, target, a) {
	if (codigo == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		$.ajax({
			type: "POST",
			url: url,
			data: varpost+"&codigo="+codigo,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp.trim(), "error", 400);
				else {
					if (target == "SELF" || !target) cargarPagina(form, pagina);
					else if (target == "BLANK") {
						var url = pagina + "&" + $('#'+form.id).serialize() + "&iframe=true&width=100%&height=100%";
						$("#"+a).attr("href", url);
						document.getElementById(a).click();
					}
				}
			}
		});
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function cargarOpcionMultipleValidar(form, pagina, target, param, accion, nombre, idregistro, php, multi) {
	//	lineas
	var error = "";
	var registro = "";
	var lineas = new Number(0);
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == nombre && n.checked) { registro += n.value + ";"; lineas++; }
	}
	var len = registro.length; len--;
	registro = registro.substr(0, len);
	document.getElementById(idregistro).value = registro;
	
	if (lineas == 0) alert("¡Debe seleccionar por lo menos un registro!");
	else if (!multi && lineas > 1) alert("¡No puede seleccionar más de un registro!");
	else {
		//	CREO UN OBJETO AJAX
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/"+php+"_fphp_funciones_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(accion+"&codigo="+registro);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				var resp = ajax.responseText;
				if (resp.trim() != "") alert(resp.trim());
				else {
					if (target == "SELF") cargarPagina(form, pagina);
					else if (target == "BLANK") {
						pagina = pagina + "&registro=" + registro;
						window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
					}
				}
			}
		}
	}
}

//	FUNCION PARA CARGAR UNA NUEVA PAGINA mmm
function cargarOpcionMultipleValidar2(form, pagina, target, param, varpost, nombre, idregistro, multi) {
	//	lineas
	var error = "";
	var registro = "";
	var lineas = new Number(0);
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == nombre && n.checked) { registro += n.value + ";"; lineas++; }
	}
	var len = registro.length; len--;
	registro = registro.substr(0, len);
	$("#"+idregistro).val(registro);
	
	if (lineas == 0) cajaModal("Debe seleccionar por lo menos un registro", "error", 400);
	else if (!multi && lineas > 1) cajaModal("No puede seleccionar más de un registro", "error", 400);
	else {
		$.ajax({
			type: "POST",
			url: "lib/fphp_funciones_ajax.php",
			data: varpost+"&registro="+registro,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp.trim(), "error", 400);
				else {
					if (target == "SELF") cargarPagina(form, pagina);
					else if (target == "BLANK") {
						pagina = pagina + "&registro=" + codigo;
						window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
					}
				}
			}
		});
	}
}

//	FUNCION PARA EJECUTAR OPCION DE MENU DE REGISTROS
function opcionRegistro(form, registro, modulo, accion, pagina) {
	cargando("block");
	//	si selecciono un registro
	if (registro == "") alert("¡Debe seleccionar un registro!");
	else {
		//	confirmar la accion
		if (accion == "eliminar") var confirmar = confirm("¿Esta seguro de eliminar este registro?");
		else var confirmar = confirm("¿Esta seguro de realizar esta acción?");
		
		//	valido y ejecuto
		if (confirmar) {
			pagina = "../lib/" + pagina + "_fphp_ajax.php";
			//	CREO UN OBJETO AJAX
			var ajax=nuevoAjax();
			ajax.open("POST", pagina, true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send("modulo="+modulo+"&accion="+accion+"&registro="+registro);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4)	{
					cargando("none");
					var resp = ajax.responseText;
					if (resp != 0) alert (resp);
					else cargarPagina(form, form.action);
				}
			}
		} else cargando("none");
	}
}

//	FUNCION PARA EJECUTAR OPCION DE MENU DE REGISTROS
function opcionRegistro2(form, registro, modulo, accion) {
	//	si selecciono un registro
	if (registro == "") {
		cajaModal("Debe seleccionar un registro", "error", 400);
	} else {
		$("#cajaModal").dialog({
			buttons: {
				"Aceptar": function() {
					$(this).dialog("close");
					$.ajax({
						type: "POST",
						url: "lib/form_ajax.php",
						data: "modulo="+modulo+"&accion="+accion+"&registro="+registro,
						async: false,
						success: function(resp) {
							if (resp.trim() != "") cajaModal(resp, "error", 400);
							else form.submit();
						}
					});
				},
				"Cancelar": function() {
					$(this).dialog("close");
				}
			}
		});
		cajaModalConfirm("Está seguro de realizar esta acción", 400);
	}
}

//	FUNCION PARA EJECUTAR OPCION DE MENU DE REGISTROS
function opcionRegistro3(form, registro, modulo, accion, url) {
	//	si selecciono un registro
	if (registro == "") {
		cajaModal("Debe seleccionar un registro", "error", 400);
	} else {
		$("#cajaModal").dialog({
			buttons: {
				"Aceptar": function() {
					$(this).dialog("close");
					$.ajax({
						type: "POST",
						url: url,
						data: "modulo="+modulo+"&accion="+accion+"&registro="+registro+"&"+$('#'+form.id).serialize(),
						async: false,
						success: function(resp) {
							if (resp.trim() != "") cajaModal(resp, "error", 400);
							else form.submit();
						}
					});
				},
				"Cancelar": function() {
					$(this).dialog("close");
				}
			}
		});
		cajaModalConfirm("Está seguro de realizar esta acción", 400);
	}
}

//	FUNCION PARA EJECUTAR OPCION DE MENU DE REGISTROS
function opcionRegistroParent(form, registro, modulo, accion) {
	//	si selecciono un registro
	if (registro == "") {
		cajaModalParent("Debe seleccionar un registro", "error", 400);
	} else {
		parent.$("#cajaModal").dialog({
			buttons: {
				"Aceptar": function() {
					//	formulario
					var post = getForm(form);
					$.ajax({
						type: "POST",
						url: "lib/form_ajax.php",
						data: "modulo="+modulo+"&accion="+accion+"&registro="+registro+"&"+post,
						async: false,
						success: function(resp) {
							parent.$("#cajaModal").dialog("close");
							if (resp.trim() != "") cajaModalParent(resp, "error", 400);
							else form.submit();
						}
					});
				},
				"Cancelar": function() {
					parent.$(this).dialog("close");
				}
			}
		});
		cajaModalConfirmParent("Está seguro de realizar esta acción", 400);
	}
}

//	FUNCION PARA EJECUTAR OPCION DE MENU DE REGISTROS
function opcionRegistroMultiple(form, nombre, modulo, accion, multi) {
	//	lineas
	var error = "";
	var detalles = "";
	var lineas = new Number(0);
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == nombre && n.checked) { detalles += n.value + ";char:tr;"; lineas++; }
	}
	var len = detalles.length; len-=9;
	detalles = detalles.substr(0, len);
	if (lineas == 0) cajaModal("Debe seleccionar por lo menos un registro", "error", 400);
	else if (!multi && lineas > 1) cajaModal("No puede seleccionar más de un registro", "error", 400);
	else {
		$("#cajaModal").dialog({
			buttons: {
				"Aceptar": function() {
					$(this).dialog("close");
					bloqueo(true);
					setTimeout(function(){
						$.ajax({
							type: "POST",
							url: "lib/form_ajax.php",
							data: "modulo="+modulo+"&accion="+accion+"&registro="+detalles+"&"+$('#'+form.id).serialize(),
							async: false,
							success: function(resp) {
								if (resp.trim() != "") cajaModal(resp, "error", 400);
								else form.submit();
							}
						});
					},1000);
				},
				"Cancelar": function() {
					$(this).dialog("close");
				}
			}
		});
		cajaModalConfirm("Está seguro de realizar esta acción", 400);
	}
}

//	FUNCION PARA EJECUTAR OPCION DE MENU DE REGISTROS
function opcionRegistroMultiple2(form, nombre, modulo, accion, url, multi) {
	//	lineas
	nombre = nombre + '[]';
	var error = "";
	var lineas = new Number(0);
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == nombre && n.checked) lineas++;
	}
	if (lineas == 0) cajaModal("Debe seleccionar por lo menos un registro", "error", 400);
	else if (!multi && lineas > 1) cajaModal("No puede seleccionar más de un registro", "error", 400);
	else {
		$("#cajaModal").dialog({
			buttons: {
				"Aceptar": function() {
					$(this).dialog("close");
					bloqueo(true);
					setTimeout(function() {
						$.ajax({
							type: "POST",
							url: url,
							data: "modulo="+modulo+"&accion="+accion+"&"+$('#'+form.id).serialize(),
							async: false,
							success: function(resp) {
								if (resp.trim() != "") cajaModal(resp, "error", 400);
								else form.submit();
							}
						});
					},1000);
				},
				"Cancelar": function() {
					$(this).dialog("close");
				}
			}
		});
		cajaModalConfirm("Está seguro de realizar esta acción", 400);
	}
}

//	FUNCION PARA EJECUTAR OPCION DE MENU DE REGISTROS
function opcionValidar(form, pagina, data) {
	$.ajax({
		type: "POST",
		url: "lib/fphp_funciones_ajax.php",
		data: data,
		async: false,
		success: function(resp) {
			if (resp.trim() != "") cajaModal(resp, "error", 400);
			else cargarPagina(form, pagina);
		}
	});
}

//	FUNCION PARA CARGAR UNA NUEVA VENTANA
function cargarVentana(form, pagina, param) {
	window.open(pagina, "wPrincipal", "toolbar=no, menubar=no, location=no, scrollbars=yes, "+param);
}

/*
.- pagina -> * url de la pagina a cargar
.- name	  -> nombre del checkbox
.- multi  -> indica si permite la selección multiple (0/1)
.- form	  -> formulario
.- target -> indica si se recarga la pagina o se abre un popup
.- param  -> indica si se recarga la pagina o se abre un popup
.- 
*/
function loadOpcion(pagina, name, mult, form, target, param) {
	if (!name) var name = "registro";
	if (!form) var form = $('form');
	if (!mult) var mult = 0;
	if (!target) var target = "SELF";

	if (!$("input[name='"+name+"[]']:checked").length) cajaModal('Debe seleccionar por lo menos un registro','error');
	else if ($("input[name='"+name+"[]']:checked").length > 1 && !mult) cajaModal('Debe seleccionar solamente un registro','error');
	else {
		if (target == "SELF") cargarPagina(document.getElementById(form.attr('id')), pagina);
		else if (target == "BLANK") {
			pagina = pagina + "&" + form.serialize();
			window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
		}
	}
}

/*
.- url    -> * url de la pagina a cargar
.- data   -> * data a enviar
.- name	  -> nombre del checkbox
.- multi  -> indica si permite la selección multiple (0/1)
.- form	  -> formulario
.- 
*/
function executeOpcion(url, data, name, mult, form) {
	if (!name) var name = "registro";
	if (!form) var form = $('form');
	if (!mult) var mult = 0;
	if (!target) var target = "SELF";

	if (!$("input[name='"+name+"[]']:checked").length) cajaModal('Debe seleccionar por lo menos un registro','error');
	else if ($("input[name='"+name+"[]']:checked").length > 1 && !mult) cajaModal('Debe seleccionar solamente un registro','error');
	else {
		$.ajax({
			type: "POST",
			url: url,
			data: data+"&"+form.serialize(),
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else document.getElementById(form.attr('id')).submit();
			}
		});
	}
}

/*
.- pagina -> * url de la pagina a cargar
.- link	  -> nombre del link
.- name	  -> nombre del checkbox
.- multi  -> indica si permite la selección multiple (0/1)
.- form	  -> formulario
.- 
*/
function loadPopup(pagina, link, name, mult, form, w, h) {
	if (!link) var link = $("#a_imprimir");
	if (!name) var name = "registro";
	if (!form) var form = $('form');
	if (!mult) var mult = 0;
	if (!w) var w = '100%';
	if (!h) var h = '100%';

	if (!$("input[name='"+name+"[]']:checked").length) cajaModal('Debe seleccionar por lo menos un registro','error');
	else if ($("input[name='"+name+"[]']:checked").length > 1 && !mult) cajaModal('Debe seleccionar solamente un registro','error');
	else {
		var href = pagina + "&" + form.serialize() + "&iframe=true&width="+w+"&height="+h;
		link.attr('href', href);
		document.getElementById(link.attr('id')).click();
	}
}

//	funcion para cargar un ajax e imprimir la respuesta en otro objeto
function imprimirAjaxResponse(parametros, objeto, php) {
	/*
	.- parametros	-> variables enviadas por el post via ajax
	.- objeto		-> nombre del objeto jtml donde imprimire la respuesta del ajax
	.- php			-> indica si tomo el archivo con las funciones generales o si tomo el archivo con las funciones del modulo
	.- 
	*/
	if (php == "") var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "../lib/"+php+"_fphp_funciones_ajax.php";
	
	//	CREO UN OBJETO AJAX
	var ajax=nuevoAjax();
	ajax.open("POST", php_ajax, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(parametros);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText;
			document.getElementById(objeto).innerHTML = resp;
		}
	}
}

//	VALIDO CARACTERES NUMERICOS EN UN CADENA DE TEXTO
function contarNumericoEntero(texto) {
	var checkOK = "0123456789";
	var checkStr = texto;
	var n = 0;
	for(var i=0; i < checkStr.length; i++) {
		ch = checkStr.charAt(i);
		for (var j=0; j<checkOK.length; j++) {
			if (ch == checkOK.charAt(j)) ++n;
		}
	}
	return n;
}

//	VALIDO CARACTERES NUMERICOS EN UN CADENA DE TEXTO
function contarLetras(texto) {
	var checkOK = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚ" + "abcdefghijklmnñopqrstuvwxyzáéíóú";
	var checkStr = texto;
	var n = 0;
	for(var i=0; i < checkStr.length; i++) {
		ch = checkStr.charAt(i);
		for (var j=0; j<checkOK.length; j++) {
			if (ch == checkOK.charAt(j)) ++n;
		}
	}
	return n;
}

//	VALIDO CARACTERES ALFANUMERICOS EN UN CADENA DE TEXTO
function valAlfaNumerico(texto) {
	var checkOK = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZÁÉÍÓÚ" + "abcdefghijklmnñopqrstuvwxyzáéíóú" + "@|.,_/-*#$%()=:;¿?¡! " + "0123456789";
	var checkStr = texto;
	var allValid = true;	
	for(var i=0; i < checkStr.length; i++) {
		ch = checkStr.charAt(i);
		for (j=0; j<checkOK.length; j++)
			if (ch == checkOK.charAt(j)) break;
		if (j == checkOK.length) {
			allValid = false;
			break;
		}
	}
	return allValid;
}

//	VALIDO CARACTERES NUMERICOS EN UN CADENA DE TEXTO
function valNumericoEntero(texto) {
	var checkOK = "0123456789";
	var checkStr = texto;
	var allValid = true;
	for(var i=0; i < checkStr.length; i++) {
		ch = checkStr.charAt(i);
		for (var j=0; j<checkOK.length; j++)
			if (ch == checkOK.charAt(j)) break;
		if (j == checkOK.length) {
			allValid = false;
			break;
		}
	}
	return allValid;
}

//	VALIDO CARACTERES ALFABETICOS EN UN CADENA DE TEXTO
function valLetras(texto) {
	var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZ" + "abcdefghijklmnopqrstuvwxyz";
	var checkStr = texto;
	var allValid = true;
	for(var i=0; i < checkStr.length; i++) {
		ch = checkStr.charAt(i);
		for (var j=0; j<checkOK.length; j++)
			if (ch == checkOK.charAt(j)) break;
		if (j == checkOK.length) {
			allValid = false;
			break;
		}
	}
	return allValid;
}

//	VALIDO CARACTERES NUMERICOS EN UN CADENA DE TEXTO
function valCodigo(texto) {
	var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZ" + "abcdefghijklmnopqrstuvwxyz" + "0123456789";
	var checkStr = texto;
	var allValid = true;
	for(var i=0; i < checkStr.length; i++) {
		ch = checkStr.charAt(i);
		for (var j=0; j<checkOK.length; j++)
			if (ch == checkOK.charAt(j)) break;
		if (j == checkOK.length) {
			allValid = false;
			break;
		}
	}
	return allValid;
}

//	VALIDO CARACTERES NUMERICOS EN UN CADENA DE TEXTO
function valNumericoFloat(texto) {
	var checkOK = "0123456789.,";
	var checkStr = texto;
	var allValid = true;
	for(var i=0; i < checkStr.length; i++) {
		ch = checkStr.charAt(i);
		for (j=0; j<checkOK.length; j++)
			if (ch == checkOK.charAt(j)) break;
		if (j == checkOK.length) {
			allValid = false;
			break;
		}
	}
	return allValid;
}

//	VALIDO CARACTERES
function charIncorrecto(texto) {
	var checkOK = "&+";
	var checkStr = texto;
	var allValid = true;	
	for(var i=0; i < checkStr.length; i++) {
		ch = checkStr.charAt(i);
		for (j=0; j<checkOK.length; j++)
			if (ch == checkOK.charAt(j)) break;
		if (j == checkOK.length) {
			allValid = false;
			break;
		}
	}
	return allValid;
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function chkListado(boo, idboton, id1, id2) {
	$("#"+id1).val("");
	$("#"+id2).val("");
	if (boo) $("#"+idboton).css("visibility", "visible"); else $("#"+idboton).css("visibility", "hidden");
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function chkCampos(boo, id1, id2) {
	if (boo) {
		$("#"+id1).val("").removeAttr("disabled");
		$("#"+id2).val("").removeAttr("disabled");
	} else {
		$("#"+id1).val("").attr("disabled", "disabled");
		$("#"+id2).val("").attr("disabled", "disabled");
	}
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function chkCampos2(boo, inputs) {
	for(var i=0; i<inputs.length; i++) {
		var id = "#" + inputs[i];
		if ($(id).length > 0) {
			if (boo) $(id).val("").removeAttr("disabled");
			else $(id).val("").attr("disabled", "disabled");
		}
	}
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function chkFiltro(boo, id) {
	document.getElementById(id).value = "";
	document.getElementById(id).disabled = !boo;
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function chkFiltro_2(boo, id1, id2) {
	document.getElementById(id1).value = "";
	document.getElementById(id2).value = "";
	document.getElementById(id1).disabled = !boo;
	document.getElementById(id2).disabled = !boo;
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function chkFiltroLista(boo, id1, id2, idboton) {
	document.getElementById(id1).value = "";
	document.getElementById(id2).value = "";
	document.getElementById(idboton).disabled = !boo;
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function chkFiltroLista_2(boo, id1, id2, id3, id4, idboton) {
	document.getElementById(id1).value = "";
	document.getElementById(id2).value = "";
	document.getElementById(id3).value = "";
	document.getElementById(id4).value = "";
	if (!boo) document.getElementById(idboton).style.visibility = "hidden";
	else document.getElementById(idboton).style.visibility = "visible";
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function chkFiltroLista_3(boo, id1, id2, id3, idboton) {
	document.getElementById(id1).value = "";
	document.getElementById(id2).value = "";
	if (document.getElementById(id3)) document.getElementById(id3).value = "";
	if (!boo) document.getElementById(idboton).style.visibility = "hidden";
	else document.getElementById(idboton).style.visibility = "visible";
}

//	FUNCION PARA BLOQUEAR/DESBLOQUEAR INPUTS DEL FILTRO
function ckLista(boo, inputs, botones) {
	if (boo) var visibility = "visible"; else var visibility = "hidden";
	if (inputs) {
		for(var i=0; i<inputs.length; i++) {
			if ($("#"+inputs[i]).length > 0) $("#"+inputs[i]).val("");
		}
	}
	if (botones) {
		for(var i=0; i<botones.length; i++) {
			if ($("#"+botones[i]).length > 0) $("#"+botones[i]).css("visibility", visibility);
		}
	}
}

//	FUNCION PARA CARGAR EN UN SELECT LO SELECCIONADO EN OTRO SELECT
function getOptions(optSelectOrigen, tabla, idSelectDestino, w) {
	var nivel = new Number(1);
	var selectDestino = document.getElementById(idSelectDestino);
	if (document.getElementById(idSelectDestino).disabled) var disableDestino = "disabled"; else var disableDestino = "";
	nuevaOpcion = document.createElement("option");
	nuevaOpcion.value = "";
	
	if (optSelectOrigen == "") {
		nuevaOpcion.innerHTML = "&nbsp;";
		selectDestino.length = 0;
		selectDestino.appendChild(nuevaOpcion);
	} else {
		var accion = "nivel" + nivel;
		//	CREO UN OBJETO AJAX
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/fphp_selects.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("accion="+accion+"&tabla="+tabla+"&idSelectDestino="+idSelectDestino+"&opcion="+optSelectOrigen+"&w="+w+"&disableDestino="+disableDestino);
		ajax.onreadystatechange=function() {
			if (ajax.readyState == 1) {
				selectDestino.length = 0;
				nuevaOpcion.innerHTML = "Cargando...";
				selectDestino.appendChild(nuevaOpcion);
				selectDestino.disabled = true;
			}
			if (ajax.readyState==4)	{
				selectDestino.parentNode.innerHTML = ajax.responseText;
			}
		}
	}
}

//	FUNCION PARA CARGAR EN UN SELECT LO SELECCIONADO EN OTRO SELECT
function getOptions_2(optSelectOrigen1, optSelectOrigen2, tabla, idSelectDestino, w) {
	var nivel = new Number(1);
	var selectDestino = document.getElementById(idSelectDestino);
	if (document.getElementById(idSelectDestino).disabled) var disableDestino = "disabled"; else var disableDestino = "";
	nuevaOpcion = document.createElement("option");
	nuevaOpcion.value = "";
	
	if (optSelectOrigen1 == "" || optSelectOrigen2 == "") {
		nuevaOpcion.innerHTML = "&nbsp;";
		selectDestino.length = 0;
		selectDestino.appendChild(nuevaOpcion);
	} else {
		var accion = "nivel" + nivel;
		//	CREO UN OBJETO AJAX
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/fphp_selects.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("accion="+accion+"&tabla="+tabla+"&idSelectDestino="+idSelectDestino+"&opcion1="+optSelectOrigen1+"&opcion2="+optSelectOrigen2+"&w="+w+"&disableDestino="+disableDestino);
		ajax.onreadystatechange=function() {
			if (ajax.readyState == 1) {
				selectDestino.length = 0;
				nuevaOpcion.innerHTML = "Cargando...";
				selectDestino.appendChild(nuevaOpcion);
				selectDestino.disabled = true;
			}
			if (ajax.readyState==4)	{
				selectDestino.parentNode.innerHTML = ajax.responseText;
			}
		}
	}
}

//	FUNCION PARA INSERTAR UNA LINEA EN UNA LISTA (TR EN TABLE) (OJO ELIMINAR)
function insertarLinea(boton, accion, detalle, php) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- accion	-> modulo
	.- detalle	-> sufijo de los campos de la lista
	.- php		-> indica si tomo el archivo con las funciones generales o si tomo el archivo con las funciones del modulo
	.- 
	*/
	boton.disabled = true;
	var nro = "nro_" + detalle;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	var nrodetalle = new Number(document.getElementById(nro).value); nrodetalle++;
	var candetalle = new Number(document.getElementById(can).value); candetalle++;
	
	if (php == "") var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "../lib/"+php+"_fphp_funciones_ajax.php";
	//	CREO UN OBJETO AJAX
	var ajax=nuevoAjax();
	ajax.open("POST", php_ajax, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("accion="+accion+"&nrodetalle="+nrodetalle+"&candetalle="+candetalle);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText;
			document.getElementById(nro).value = nrodetalle;
			document.getElementById(can).value = candetalle;
			idtr = detalle + "_" + nrodetalle;			
			var newTr = document.createElement("tr");
			newTr.className = "trListaBody";
			newTr.setAttribute("onclick", "mClk(this, '"+sel+"');");
			newTr.id = idtr
			document.getElementById(lista).appendChild(newTr);
			document.getElementById(idtr).innerHTML = resp;
			boton.disabled = false;
		}
	}
}

//	FUNCION PARA INSERTAR UNA LINEA EN UNA LISTA (TR EN TABLE)
function insertarLinea2(boton, accion, detalle, php, valores) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- accion	-> modulo
	.- detalle	-> sufijo de los campos de la lista
	.- php		-> indica si tomo el archivo con las funciones generales o si tomo el archivo con las funciones del modulo
	.- 
	*/
	boton.disabled = true;
	var nro = "nro_" + detalle;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	var nrodetalle = new Number($("#"+nro).val()); nrodetalle++;
	var candetalle = new Number($("#"+can).val()); candetalle++;
	//	defino el path
	if (!php) var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "lib/fphp_funciones_ajax.php";
	//	ajax
	$.ajax({
		type: "POST",
		url: php_ajax,
		data: "accion="+accion+"&nrodetalle="+nrodetalle+"&candetalle="+candetalle+"&"+valores,
		async: true,
		success: function(resp) {
			$("#"+nro).val(nrodetalle);
			$("#"+can).val(candetalle);
			idtr = detalle + "_" + nrodetalle;
			var newTr = document.createElement("tr");
			newTr.className = "trListaBody";
			newTr.setAttribute("onclick", "mClk(this, '"+sel+"');");
			newTr.id = idtr
			document.getElementById(lista).appendChild(newTr);
			document.getElementById(idtr).innerHTML = resp;
			boton.disabled = false;
		}
	});
}

//	FUNCION PARA INSERTAR UNA LINEA EN UNA LISTA (TR EN TABLE)
function insertarLinea3(boton, accion, detalle, php_ajax, valores) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- accion	-> modulo
	.- detalle	-> sufijo de los campos de la lista
	.- php		-> indica si tomo el archivo con las funciones generales o si tomo el archivo con las funciones del modulo
	.- 
	*/
	boton.disabled = true;
	var nro = "nro_" + detalle;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	var nrodetalle = new Number($("#"+nro).val()); nrodetalle++;
	var candetalle = new Number($("#"+can).val()); candetalle++;
	//	ajax
	$.ajax({
		type: "POST",
		url: php_ajax,
		data: "modulo=ajax&accion="+accion+"&nrodetalle="+nrodetalle+"&candetalle="+candetalle+"&"+valores,
		async: true,
		success: function(resp) {
			$("#"+nro).val(nrodetalle);
			$("#"+can).val(candetalle);
			idtr = detalle + "_" + nrodetalle;
			var newTr = document.createElement("tr");
			newTr.className = "trListaBody";
			newTr.setAttribute("onclick", "mClk(this, '"+sel+"');");
			newTr.id = idtr
			document.getElementById(lista).appendChild(newTr);
			document.getElementById(idtr).innerHTML = resp;
			boton.disabled = false;
			inicializar();
		}
	});
}

//	FUNCION PARA INSERTAR UNA LINEA EN UNA LISTA (TR EN TABLE)
function insertar(boton, detalle, php, valores) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- detalle	-> sufijo de los campos de la lista
	.- php		-> indica si tomo el archivo con las funciones generales o si tomo el archivo con las funciones del modulo
	.- valores	-> valores enviados por post al ajax
	*/
	boton.disabled = true;
	var nro = "#nro_" + detalle;
	var can = "#can_" + detalle;
	var sel = "#sel_" + detalle;
	var lista = "#lista_" + detalle;
	var nro_detalle = new Number($(nro).val()); nro_detalle++;
	var can_detalle = new Number($(can).val()); can_detalle++;
	//	defino el path
	if (!php) var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "lib/fphp_funciones_ajax.php";
	//	ajax
	$.ajax({
		type: "POST",
		url: php_ajax,
		data: "nro_detalle="+nro_detalle+"&can_detalle="+can_detalle+"&"+valores,
		async: true,
		success: function(resp) {
			$(nro).val(nro_detalle);
			$(can).val(can_detalle);
			$(lista).append(resp);
			boton.disabled = false;
			inicializar();
		}
	});
}

//	FUNCION PARA INSERTAR UNA LINEA EN UNA LISTA (TR EN TABLE)
function insertar2(boton, detalle, valores, url) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- detalle	-> sufijo de los campos de la lista
	.- valores	-> valores enviados por post al ajax
	.- url	-> ruta del archivo ajax .php
	*/
	boton.disabled = true;
	var nro = "#nro_" + detalle;
	var can = "#can_" + detalle;
	var sel = "#sel_" + detalle;
	var lista = "#lista_" + detalle;
	var nro_detalle = new Number($(nro).val()); nro_detalle++;
	var can_detalle = new Number($(can).val()); can_detalle++;
	//	ajax
	$.ajax({
		type: "POST",
		url: url,
		data: "nro_detalle="+nro_detalle+"&can_detalle="+can_detalle+"&"+valores,
		async: true,
		success: function(resp) {
			$(nro).val(nro_detalle);
			$(can).val(can_detalle);
			$(lista).append(resp);
			boton.disabled = false;
			inicializar();
		}
	});
}

//	abrir selector para listas
function listaSelector(a, lista, ventana, inputs, w, h) {
	if (!w) var w = 960;
	if (!h) var h = 430;
	//	
	var sel = $('#sel_'+a).val();
	if (sel == '') cajaModal('Debe seleccionar un registro', 'error');
	else {
		var partes = sel.split('_');
		var id = partes[1];
		var campos = "";
		for(var i=0; i<inputs.length; i++) {
			var j = i + 1;
			campos += "&campo" + j + "=" + a + "_" + inputs[i] + id;
		}
		var href = "../lib/listas/gehen.php?anz="+lista+"&filtrar=default&"+campos+"&ventana="+ventana+"&iframe=true&width="+w+"&height="+h;
		$('#a_'+a).attr('href', href);
		$('#a_'+a).click();
	}
}

//	FUNCION PARA ELIMINAR UNA LINEA DE UNA LISTA (TR EN TABLE)
function quitarLinea(boton, detalle) {
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
}

//	FUNCION PARA ELIMINAR UNA LINEA DE UNA LISTA (TR EN TABLE)
function quitar(boton, detalle) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- detalle	-> sufijo de los campos de la lista
	*/
	boton.disabled = true;
	var can = "#can_" + detalle;
	var sel = "#sel_" + detalle;	
	var lista = "#lista_" + detalle;
	if ($(sel).val() == "") cajaModal("Debe seleccionar una linea", "error", 400);
	else {
		var candetalle = parseInt($(can).val()); candetalle--;
		$(can).val(candetalle);
		$(sel).val("");
		$(lista+" .trListaBodySel").remove();
	}
	boton.disabled = false;
}

//	FUNCION PARA ELIMINAR UNA LINEA DE UNA LISTA (TR EN TABLE)
function caja_chica_conceptos_quitar(boton, detalle) {
	/*
	.- boton	-> referencia del boton (objeto)
	.- detalle	-> sufijo de los campos de la lista
	*/
	boton.disabled = true;
	//var can = "#can_" + detalle;
	var sel = "#sel_" + detalle;	
	var lista = "#lista_" + detalle;
	if ($(sel).val() == "") cajaModal("Debe seleccionar una linea", "error", 400);
	else {
		//var candetalle = parseInt($(can).val()); candetalle--;
		//$(can).val(candetalle);
		$(sel).val("");
		var id = $(lista+" .trListaBodySel").attr("id").split("_");
		$(lista+" .trListaBodySel").remove();
		$("#distribucion"+id[1]).remove();
		
	}
	boton.disabled = false;
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
		var candetalle = new Number(document.getElementById(can).value); candetalle--;
		document.getElementById(can).value = candetalle;
		var seldetalle = document.getElementById(sel).value;
		var partes = seldetalle.split("_");
		var Codigo = document.getElementById("Codigo_"+partes[1]).value;
		if (Codigo != "") alert("No se puede eliminar");
		var listaDetalles = document.getElementById(lista);
		var tr = document.getElementById(seldetalle);
		listaDetalles.removeChild(tr);
		document.getElementById(sel).value = "";
	}
	boton.disabled = false;
}

//	FUNCION PARA ABRIR UNA VENTANA DE LISTA DE OPCIONES PARA INSERTARLO EN LINEA DE OTRA LISTA
function abrirListado(detalle, cod, nom, listado, param, variables) {
	var sel = "sel_" + detalle;
	var seldetalle = document.getElementById(sel).value;
	var partes = seldetalle.split("_");
	var campocod = cod + "_" + partes[1];
	var camponom = nom + "_" + partes[1];
	
	if (seldetalle == "") alert("¡Debe seleccionar una linea!");
	else window.open("../lib/"+ listado +".php?cod="+campocod+"&nom="+camponom+"&"+variables, "wlista", "toolbar=no, menubar=no, location=no, scrollbars=yes, resizable=no, " + param);
}

//	FUNCION PARA ABRIR UNA LISTA PARA DESPUES INSERTAR EN LA LISTA QUE LO LLAMO
function abrirListadoInsertar(url, detalle, opcion, accion, php, param) {
	/*
	.- url		-> (string) direccion de la pagina
	.- detalle	-> (string)	sufijo de los campos de la lista (OJO: DEBEN TENER LOS CAMPOS EL MISMO PREFIO)
	.- opcion	-> (string)	nombre de la funcion a utilizar despues de seleccionar de la lista
	.- accion	-> (string)	bandera para la funcion ...ajax.php de donde obtengo la informacion a insertar en la tabla origen
	.- php		-> (string)	prefjo que indica el modulo donde se aloja el archivo ...ajax - si esta vacio tomo el de las funciones generales
	.- param	-> (string)	parametros de la funcion window.open (tamaño, posicion, etc)
	*/
	window.open("../lib/"+url+"&detalle="+detalle+"&opcion="+opcion+"&accion="+accion+"&php="+php, "wlista", "toolbar=no, menubar=no, location=no, scrollbars=yes, resizable=no, " + param);
}

//	FUNCION PARA INSERTAR DE UNA LISTA A OTRA LISTA
function insertarLineaListado(accion, detalle, php, codigo) {
	/*
	.- accion	-> (string)	bandera para la funcion ...ajax.php de donde obtengo la informacion a insertar en la tabla origen
	.- detalle	-> (string)	sufijo de los campos de la lista (OJO: DEBEN TENER LOS CAMPOS EL MISMO PREFIO)
	.- php		-> (string)	prefjo que indica el modulo donde se aloja el archivo ...ajax - si esta vacio tomo el de las funciones generales
	.- codigo	-> (string)	id del registro que se selecciono
	*/
	
	//	excepciones de campos adicionales
	var variables_adicionales = "";
	if (accion == "cotizaciones_invitar_cotizar_insertar") {
		variables_adicionales = "&cantidad="+opener.document.getElementById("cantidad").value;
	}
	
	//	obtengo todos los campos necesarios y ejecuto el ajax para insertar la linea
	var nro = "nro_" + detalle;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	var nrodetalle = new Number(opener.document.getElementById(nro).value); nrodetalle++;
	var candetalle = new Number(opener.document.getElementById(can).value); candetalle++;
	
	if (php == "") var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "../lib/"+php+"_fphp_funciones_ajax.php";
	//	CREO UN OBJETO AJAX
	var ajax=nuevoAjax();
	ajax.open("POST", php_ajax, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("accion="+accion+"&codigo="+codigo+"&nrodetalle="+nrodetalle+"&candetalle="+candetalle+variables_adicionales);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText;
			var datos = resp.split("|");
			if (datos[1]) idtr = detalle + "_" + datos[1];
			else idtr = detalle + "_" + nrodetalle;
			if (opener.document.getElementById(idtr)) {
				alert("¡ERROR: No puede insertar dos veces el mismo registro!");
			} else {
				opener.document.getElementById(nro).value = nrodetalle;
				opener.document.getElementById(can).value = candetalle;
				var newTr = document.createElement("tr");
				newTr.className = "trListaBody";
				newTr.setAttribute("onclick", "mClk(this, '"+sel+"');");
				newTr.id = idtr
				opener.document.getElementById(lista).appendChild(newTr);
				opener.document.getElementById(idtr).innerHTML = datos[0];
				window.close();
			}
		}
	}
}

//	FUNCION PARA INSERTAR DE UNA LISTA A OTRA LISTA
function insertarLineaListadoItem(accion, detalle, php, codigo, tipo) {
	//	obtengo los campos
	var nro = "nro_" + detalle;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	if (opener.document.getElementById("flagcompras").checked) var flagdirigido = "C"; else var flagdirigido = "A";
	var ccosto = opener.document.getElementById("ccosto").value;
	var nrodetalle = new Number(opener.document.getElementById(nro).value); nrodetalle++;
	var candetalle = new Number(opener.document.getElementById(can).value); candetalle++;
	
	if (php == "") var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "../lib/"+php+"_fphp_funciones_ajax.php";
	//	CREO UN OBJETO AJAX
	var ajax=nuevoAjax();
	ajax.open("POST", php_ajax, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("accion="+accion+"&codigo="+codigo+"&nrodetalle="+nrodetalle+"&candetalle="+candetalle+"&ccosto="+ccosto+"&tipo="+tipo+"&flagdirigido="+flagdirigido);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText;
			var datos = resp.split("|");
			idtr = detalle + "_" + datos[1];
			if (opener.document.getElementById(idtr)) {
				alert("¡ERROR: No puede insertar dos veces la misma linea!");
			} else {
				opener.document.getElementById(nro).value = nrodetalle;
				opener.document.getElementById(can).value = candetalle;
				if (tipo == "item") opener.document.getElementById("btCommodity").disabled = "true";
				else opener.document.getElementById("btItem").disabled = "true";
				var newTr = document.createElement("tr");
				newTr.className = "trListaBody";
				newTr.setAttribute("onclick", "mClk(this, '"+sel+"');");
				newTr.id = idtr;
				opener.document.getElementById(lista).appendChild(newTr);
				opener.document.getElementById(idtr).innerHTML = datos[0];
				window.close();
			}
		}
	}
}

//	FUNCION PARA INSERTAR DE UNA LISTA A OTRA LISTA
function insertarLineaListadoItemOC(accion, detalle, php, codigo, tipo) {
	//	obtengo los campos
	var nro = "nro_" + detalle;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	var nrodetalle = new Number(opener.document.getElementById(nro).value); nrodetalle++;
	var candetalle = new Number(opener.document.getElementById(can).value); candetalle++;
	var impuesto = new Number(opener.document.getElementById("impuesto").value);
	var comentarios = opener.document.getElementById("comentarios").value;
	
	if (php == "") var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "../lib/"+php+"_fphp_funciones_ajax.php";
	//	CREO UN OBJETO AJAX
	var ajax=nuevoAjax();
	ajax.open("POST", php_ajax, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("accion="+accion+"&codigo="+codigo+"&nrodetalle="+nrodetalle+"&candetalle="+candetalle+"&tipo="+tipo+"&impuesto="+impuesto+"&comentarios="+comentarios);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText;
			var datos = resp.split("|");
			idtr = detalle + "_" + datos[1];
			if (opener.document.getElementById(idtr)) {
				alert("¡ERROR: No puede insertar dos veces la misma linea!");
			} else {
				opener.document.getElementById("tipoclasificacion").value = datos[2];
				opener.document.getElementById(nro).value = nrodetalle;
				opener.document.getElementById(can).value = candetalle;
				if (tipo == "item") opener.document.getElementById("btCommodity").disabled = "true";
				else opener.document.getElementById("btItem").disabled = "true";
				var newTr = document.createElement("tr");
				newTr.className = "trListaBody";
				newTr.setAttribute("onclick", "mClk(this, '"+sel+"');");
				newTr.id = idtr;
				opener.document.getElementById(lista).appendChild(newTr);
				opener.document.getElementById(idtr).innerHTML = datos[0];
				window.close();
			}
		}
	}
}

//	FUNCION PARA INSERTAR DE UNA LISTA A OTRA LISTA
function insertarLineaListadoItemOS(accion, detalle, php, codigo) {
	//	obtengo los campos
	var nro = "nro_" + detalle;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	var nrodetalle = new Number(opener.document.getElementById(nro).value); nrodetalle++;
	var candetalle = new Number(opener.document.getElementById(can).value); candetalle++;
	var impuesto = new Number(opener.document.getElementById("impuesto").value);
	var comentarios = opener.document.getElementById("descripcion").value;
	var fentrega = opener.document.getElementById("fentrega").value;
	var ccosto = opener.document.getElementById("ccosto").value;
	var nomccosto = opener.document.getElementById("nomccosto").value;
	
	if (php == "") var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "../lib/"+php+"_fphp_funciones_ajax.php";
	//	CREO UN OBJETO AJAX
	var ajax=nuevoAjax();
	ajax.open("POST", php_ajax, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("accion="+accion+"&codigo="+codigo+"&nrodetalle="+nrodetalle+"&candetalle="+candetalle+"&impuesto="+impuesto+"&comentarios="+comentarios+"&fentrega="+fentrega+"&ccosto="+ccosto+"&nomccosto="+nomccosto);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText;
			var datos = resp.split("|");
			idtr = detalle + "_" + datos[1];
			if (opener.document.getElementById(idtr)) {
				alert("¡ERROR: No puede insertar dos veces la misma linea!");
			} else {
				opener.document.getElementById(nro).value = nrodetalle;
				opener.document.getElementById(can).value = candetalle;
				var newTr = document.createElement("tr");
				newTr.className = "trListaBody";
				newTr.setAttribute("onclick", "mClk(this, '"+sel+"');");
				newTr.id = idtr;
				opener.document.getElementById(lista).appendChild(newTr);
				opener.document.getElementById(idtr).innerHTML = datos[0];
				window.close();
			}
		}
	}
}

//	FUNCION PARA INSERTAR DE UNA LISTA A OTRA LISTA
function fideicomiso_calculo(codpersona, php) {
	var periodo = opener.document.getElementById("periodo").value;
	if (php == "") var php_ajax = "../lib/fphp_funciones_ajax.php";
	else var php_ajax = "../lib/"+php+"_fphp_funciones_ajax.php";
	//	CREO UN OBJETO AJAX
	var ajax=nuevoAjax();
	ajax.open("POST", php_ajax, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("accion=fideicomiso_calculo&codpersona="+codpersona+"&periodo="+periodo);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText;
			var datos = resp.split("|");
			opener.document.getElementById("codpersona").value = datos[0];
			opener.document.getElementById("codempleado").value = datos[1];
			opener.document.getElementById("nomempleado").value = datos[2];
			opener.document.getElementById("documento").value = datos[3];
			opener.document.getElementById("anios").value = datos[4];
			opener.document.getElementById("meses").value = datos[5];
			opener.document.getElementById("dias").value = datos[6];
			opener.document.getElementById("fingreso").value = datos[7];
			opener.document.getElementById("listaCalculo").innerHTML = datos[8];
			window.close();
		}
	}
}

//	FUNCIONES PARA VALIDAR FECHA
// Valido que sean numeros 
function esDigito(sChr){
	var sCod = sChr.charCodeAt(0);
	return ((sCod > 47) && (sCod < 58));
}

// Valido separadores de fecha...
function valSep(fecha){
	var bOk = false;
	bOk = bOk || ((fecha.charAt(2) == "-") && (fecha.charAt(5) == "-"));
	bOk = bOk || ((fecha.charAt(2) == "/") && (fecha.charAt(5) == "/"));
	return bOk;
}

// Valido dia fin de mes...
function finMes(fecha){
	var nAno = fecha.substr(6);
	var nMes = parseInt(fecha.substr(3, 2), 10);
	var nRes = 0;
	switch (nMes){
		case 1: nRes = 31; break;
		case 2: 
				if (nAno % 4 == 0) nRes = 29; 
				else nRes = 28;
				break;
		case 3: nRes = 31; break;
		case 4: nRes = 30; break;
		case 5: nRes = 31; break;
		case 6: nRes = 30; break;
		case 7: nRes = 31; break;
		case 8: nRes = 31; break;
		case 9: nRes = 30; break;
		case 10: nRes = 31; break;
		case 11: nRes = 30; break;
		case 12: nRes = 31; break;
	}
	return nRes;
}

// Valido dia...
function valDia(fecha){
	var nDia = parseInt(fecha.substr(0, 2), 10);
	var bOk = false;
	bOk = bOk || ((nDia >= 1) && (nDia <= finMes(fecha)));
	return bOk;
}

// Valido mes...
function valMes(nMes){
	var bOk = false;
	var nMesInt = parseInt(nMes, 10);
	bOk = bOk || ((nMesInt >= 1) && (nMesInt <= 12) && (nMes.length == 2));
	return bOk;
}

// Valido a�o...
function valAno(nAno){
	var bOk = true;
	var nAnoInt = parseInt(nAno, 10);
	bOk = bOk && ((nAno.length == 4) && (nAnoInt > 0));
	if (bOk){
		for (var i = 0; i < nAno.length; i++){
			bOk = bOk && esDigito(nAno.charAt(i));
		}
	}
	return bOk;
}

// Valido fecha completa...
function valFecha(fecha){
	var bOk = true;
	var nAno = fecha.substr(6);
	var nMes = fecha.substr(3, 2);
	if (fecha != "") {
		bOk = bOk && (valAno(nAno));
		bOk = bOk && (valMes(nMes));
		bOk = bOk && (valDia(fecha));
		bOk = bOk && (valSep(fecha));
	}
	return bOk;
}

//	valido el periodo de fecha
function valPeriodo(fecha) {
	var bOk = true;
	var nAno = fecha.substr(0, 4);
	var nMes = fecha.substr(5);
	if (fecha != "") {
		bOk = bOk && (valAno(nAno));
		bOk = bOk && (valMes(nMes));
	}
	return bOk;
}

//	valido hora
function valHora(time) {
	var bOk = true;
	if (time.length == 11) var seg = true; else var seg = false;
	var nHora = String(time.substr(0, 2));
	var nMin = String(time.substr(3, 2));
	if (seg) {
		var nSeg = String(time.substr(6, 2));
		var nM = String(time.substr(9));
	} else {
		var nM = String(time.substr(6));
	}
	if (time != "") {
		bOk = bOk && (nHora.length == 2);
		bOk = bOk && (nMin.length == 2);
		if (seg) bOk = bOk && (nSeg.length == 2);
		bOk = bOk && (nHora >= '01' && nHora <= '12');
		bOk = bOk && (nMin >= '00' && nMin <= '59');
		if (seg) bOk = bOk && (nSeg >= '00' && nSeg <= '59');
		bOk = bOk && (nM == "am" || nM == "pm");
	}
	return bOk;
}

//	funcion para los numeros
function oNumero(numero) {
	//Propiedades
	this.valor = numero || 0
	this.dec = -1;
	
	//Métodos
	this.formato = numFormat;
	this.ponValor = ponValor;
	
	//Definición de los métodos
	function ponValor(cad) {
		if (cad =='-' || cad=='+') return
		if (cad.length ==0) return
		if (cad.indexOf('.') >=0)
			this.valor = parseFloat(cad);
		else
			this.valor = parseInt(cad);
	}

	function numFormat(dec, miles) {
		var num = this.valor, signo=3, expr;
		var cad = ""+this.valor;
		var ceros = "", pos, pdec, i;
		for (i=0; i < dec; i++)
			ceros += '0';
		pos = cad.indexOf('.')
		if (pos < 0)
		    cad = cad+"."+ceros;
		else {
		    pdec = cad.length - pos -1;
		    if (pdec <= dec) {
		        for (i=0; i< (dec-pdec); i++)
		            cad += '0';
		    }
		    else {
		        num = num*Math.pow(10, dec);
		        num = Math.round(num);
		        num = num/Math.pow(10, dec);
		        cad = new String(num);
		    }
		}
		pos = cad.indexOf('.')
		if (pos < 0) pos = cad.lentgh
		if (cad.substr(0,1)=='-' || cad.substr(0,1) == '+')
			signo = 4;
		if (miles && pos > signo)
		    do {
		        expr = /([+-]?\d)(\d{3}[\.\,]\d*)/
		        cad.match(expr)
		        cad=cad.replace(expr, RegExp.$1+','+RegExp.$2)
			}
		while (cad.indexOf(',') > signo)
		    if (dec<0) cad = cad.replace(/\./,'')
		        return cad;
	}
}

//	funcion para formtear un campo numerico cuando deja el campo
function numeroBlur(campo, decimales) {
	if (!decimales) decimales = 2;
	var numero = new Number(setNumero(campo.value));
	if (numero == 0) campo.value = "0,00";
	else campo.value = setNumeroFormato(numero, decimales, ".", ",");
}

//	funcion para formtear un campo numerico cuando deja el campo
function numeroFocus(campo, decimales) {
	if (!decimales) decimales = 2;
	var numero = new Number(setNumero(campo.value));
	if (numero == 0) campo.value = "";
	else { 
		valor = setNumeroFormato(numero, decimales, "", ",");
		var x = new String(valor);
		var sep = x.split(",");
		var dec = new Number(sep[1]);
		if (dec == 0) campo.value = sep[0];
		else if (dec <= 10) campo.value = setNumeroFormato(numero, 1, "", ",");
		else campo.value = setNumeroFormato(numero, decimales, "", ",");
	}
}

//	funcion cuando un campo numerico recibe el foco
//	->	num_formateado:(texto) numero a formatear
function nroFocus(campo) {
	var valor = campo.val();
	var num = valor.toString();
	num = num.replace(/[.]/gi, "");
	var numero = parseFloat(num);
	if (numero == 0) campo.val("");
	else campo.val(num);
}

function nroBlur(input) {
	if (input.val().trim() == "") input.val("0,00")
	else input.formatCurrency();
}

//	funcion para convertir un numero formateado en su valor real
function setNumero(num_formateado) {
	if (num_formateado) {
        var num = num_formateado.toString();
        num = num.replace(/[.]/gi, "");
        num = num.replace(/[,]/gi, ".");
        var numero = new Number(num);
        return parseFloat(numero);
    } else return 0;
}

//	dar formato a un numero
function setNumeroFormato(num, dec, sep_mil, sep_dec) {
	num = parseFloat(num);
	dec = parseInt(dec);
	if (num < 0) var Signo = "-"; else Signo = "";
	//var numero = String(redondear(num, dec));
	var numero = String(Math.abs(num.toFixed(dec)));
	var partes = numero.split(".");
	var entera = parseInt(partes[0]);
	if (partes[1]) {
		var nro_dec = parseInt(partes[1].length);
		var veces = dec - nro_dec;
		if (veces > 0) var ceros = str_repeat("0", veces); else var ceros = "";
		var decimal = String(partes[1]) + ceros;
	} else {
		var veces = dec;
		var ceros = str_repeat("0", veces);
		var decimal = ceros;
	}
	var cadena = "";
	var aux;
	var cont = 1, m, k;
	if (entera < 0) aux = 1; else aux = 0;
	entera = String(entera);
	for(m=entera.length-1; m>=0; m--) {
		cadena = entera.charAt(m) + cadena;
		if(cont%3 == 0 && m > aux) cadena = sep_mil + cadena; else cadena = cadena;
		if(cont == 3) cont = 1; else cont++;
	}
	cadena = Signo + cadena + sep_dec + String(decimal);
	return cadena;
}  

//	redondear decimales
function redondear(numero, decimales) {
	if (parseFloat(numero) < 0) var signo = -1; else var signo = 1;
	var num = Math.abs(parseFloat(numero));
	var decimales = parseInt(decimales);
	var partes = String(num).split(".");
	if (partes[1] && String(partes[1]).length > decimales) {
		var entera = parseInt(String(partes[0]) + String(partes[1]).substr(0, decimales));
		var redondear = parseInt(String(partes[1]).substr(decimales, 1));
		if (redondear >= 5) ++entera;
		var largo = parseInt(String(entera).length);
		var parte_entera = String(entera).substr(0, largo-decimales);
		var parte_decimal = String(entera).substr(-decimales);
		var resultado = String(parte_entera) + "." + String(parte_decimal);
		resultado = parseFloat(resultado) * signo;
		return parseFloat(resultado);
	} else {
		return parseFloat(numero);
	}
}

//	funcion para rellenar
function str_repeat(cad, veces) {
	var cadena = cad;
	for(var i=0; i<(veces-cad.length); i++){
		cadena += cad;
	}
	return cadena;
}

//	FUNCION PARA FORMATEAR LA FECHA A FORMATO DD-MM-AAAA
function formatFechaAMD(fecha) {
	var f = new String();
	var nDia = fecha.substr(0, 2);
	var nMes = fecha.substr(3, 2);
	var nAno = fecha.substr(6);
	f = nAno + "-" + nMes + "-" + nDia;
	return f;
}

//	FUNCION PARA FORMATEAR LA FECHA A FORMATO DD-MM-AAAA
function formatFechaDMA(fecha) {
	var f = new String();
	var nAno = fecha.substr(0, 4);
	var nMes = fecha.substr(5, 2);
	var nDia = fecha.substr(8, 2);
	f = nDia + "-" + nMes + "-" + nAno;
	return f;
}

//	FUNCION PARA FORMATEAR LA HORA A FORMATO 24
function formatHora(t) {
	if (t.length == 11) var seg = true; else var seg = false;
	var h = t.substr(0, 2);
	var m = t.substr(3, 2);
	if (seg) {
		var s = t.substr(6, 2);
		var md = t.substr(9);
	} else {
		var s = "00";
		var md = t.substr(6);
	}
	if (h >= '01' && h <= '12' && m >= '00' && m <= '59' && s >= '00' && s <= '59' && (md == "am" || md == "pm") && h.length == 2 && m.length == 2 && s.length == 2 && md.length == 2) {
		if (md == "am") {
			if (h == '12') var hora = String("00");
			else var hora = String(h);
		}
		else if (md = "pm") {
			if (h < '12') var hora = String(parseInt(h) + 12);
			else var hora = String(h);
		}
		var time = String(hora + ":" + m + ":" + s);
	} else var time = "";
	
	return time;
}

//	funcion para cambiar la clase current de un grupo de fichas
function currentTab(tab, li) {
	/*
	.- tab			-> (string)	prefijo del id del ul
	.- li			-> (string)	prefijo del id de los li pertenecientes al ul
	*/
	
	//	quito el current al tab seleccionado
	var objeto_ul = document.getElementById(tab);
	var numero_li = objeto_ul.childNodes.length;
	for (var i=0; i<numero_li; i++) {
		if (objeto_ul.childNodes[i].className == "current") objeto_ul.childNodes[i].className = "";
	}
	//	agrego el current al tab seleccionado
	li.className = "current";
}

//	funcion para cambiar la clase current de un grupo de fichas
function current(li) {
	var idtab = li.parent().attr("id");
	$("#"+idtab+" li").removeClass("current");
	li.addClass("current");
}

//	funcion para mostrar el div relacionado a un grupo de fichas
function mostrarTab(tab, li, numero_li) {
	/*
	.- tab			-> (string)	prefijo del id de los div
	.- li			-> (string)	prefijo del id de los li
	.- numero_li	-> (int)	numero de fichas (li, div)
	*/
	
	for (var i=1; i<=numero_li; i++) {
		var id = new String(tab + i);
		document.getElementById(id).style.display = "none";
	}
	var id = new String(tab + li);
	document.getElementById(id).style.display = "block";

	if ($('#ficha').length) $('#ficha').val(li);
}

//	FUNCION PARA OBTENER LOS DIAS HABILES ENTRES DOS FECHAS
function getDiasHabiles(desde, hasta, campo) {
	if (formatFechaAMD(desde) > formatFechaAMD(hasta)) {
		campo.value = 0;
	} else {
		//	CREO UN OBJETO AJAX PARA VERIFICAR QUE EL NUEVO REGISTRO NO EXISTA EN LA BASE DE DATOS
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/fphp_funciones_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("accion=getDiasHabiles&FechaInicio="+desde+"&FechaFin="+hasta);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				campo.value = ajax.responseText;
			}
		}
	}
}
function getDiasHabiles2(FechaInicio, FechaFin, iDias) {
	//	ajax
	$.ajax({
		type: "POST",
		url: "../lib/fphp_funciones_ajax.php",
		data: "accion=getDiasHabiles&FechaInicio="+FechaInicio+"&FechaFin="+FechaFin,
		async: false,
		success: function(resp) {
			iDias.val(resp.trim());
		}
	});
}

//	FUNCIONES PARA FORMATEAR LOS CAMPOS FECHA
function setFechaDMA(campo) {
	//	valor del campo
	var texto = new String(campo.value);
	
	//	separo los valores del dia, mes y año
	var nDia = texto.substr(0, 2);
	var nMes = texto.substr(3, 2);
	var nAno = texto.substr(6);
	
	if (!valNumericoEntero(nDia)) nDia = "00";
	if (!valNumericoEntero(nMes)) nMes = "00";
	if (!valNumericoEntero(nAno)) nAno = "0000";
	
	//	ultima letra ingresada
	var letra = texto.substr(-1);
	
	if (texto.length == 3) {
		if (letra != "-") var sep = "-" + letra; else var sep = letra;
		campo.value = nDia + sep;
	}
	
	else if (texto.length == 6) {
		if (letra != "-") var sep = "-" + letra; else var sep = letra;
		campo.value = nDia + "-" + nMes + sep;
	}
	
	else if (texto.length == 10) {
		campo.value = nDia + "-" + nMes + "-" + nAno;
	}
}

//	--
function getDescripcionLista(accion, campo, iddescripcion, campo2, iddescripcion2) {
	var codigo = campo.value;
	if (codigo.trim() != "") {
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/fphp_funciones_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(accion+"&codigo="+codigo);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				var resp = ajax.responseText;
				var datos = resp.split("|");
				if (datos[0].trim() != "") alert(datos[0]);
				else {
					if (datos[1]) campo.value = datos[1]; 
					else campo.value = "";
					if (datos[2]) document.getElementById(iddescripcion).value = datos[2]; 
					else document.getElementById(iddescripcion).value = "";
					if (datos[3] && document.getElementById(campo2)) document.getElementById(campo2).value = datos[3];
					else if (document.getElementById(campo2)) document.getElementById(campo2).value = "";
					if (datos[4] && document.getElementById(iddescripcion2)) document.getElementById(iddescripcion2).value = datos[4]; 
					else if (document.getElementById(iddescripcion2)) document.getElementById(iddescripcion2).value = "";
				}
			}
		}
	} else {
		campo.value = "";
		document.getElementById(iddescripcion).value = "";
		if (document.getElementById(campo2)) document.getElementById(campo2).value = "";
		if (document.getElementById(iddescripcion2)) document.getElementById(iddescripcion2).value = "";
	}
}

//	--
function getDescripcionLista2(accion, campo, descripcion, campo2, descripcion2) {
	var codigo = campo.val();
	if (codigo.trim() != "") {
		$.ajax({
			type: "POST",
			url: "../lib/fphp_funciones_ajax.php",
			data: accion+"&codigo="+codigo,
			async: false,
			success: function(resp) {
				var datos = resp.split("|");
				if (datos[1]) campo.val(datos[1]);
				else {
					campo.val("");
					cajaModal("No se encontró ningún registro", "error");
				}
				if (datos[2]) descripcion.val(datos[2]);
				else descripcion.val("");
				if (datos[3]) campo2.val(datos[3]);
				else if (campo2) campo2.val("");
				if (datos[4]) descripcion2.val(datos[4]);
				else if (descripcion2) descripcion2.val("");
			}
		});
	} else {
		campo.value = "";
		descripcion.val("");
		if (campo2) campo2.val("");
		if (descripcion2) descripcion2.val("");
	}
}

//	--
function stock_actual_item(coditem) {
	var form = opener.document.getElementById("frmentrada");
	var forganismo = opener.document.getElementById("forganismo").value;
	var action = form.action + "&item="+coditem + "&forganismo="+forganismo;
	opener.location.href = action;
	window.close();
}

//	funcion para cargar un ajax e imprimir la respuesta en otro objeto
function appendAjax(datos, objeto) {
	/*
	.- parametros	-> variables enviadas por el post via ajax
	.- objeto		-> nombre del objeto html donde imprimire la respuesta del ajax
	.- 
	*/
	objeto.html("Cargando...");
	$.ajax({
		type: "POST",
		url: "lib/fphp_funciones_ajax.php",
		data: datos,
		async: false,
		success: function(resp){
			objeto.html(resp);
		}
	});
}

//	funcion para sustituir caracteres especiales
function changeUrl(txt) {
	var texto = txt.toString();
	texto = texto.replace(/[&]/gi, "|char:ampersand|");
	texto = texto.replace(/[+]/gi, "|char:mas|");
	texto = texto.replace(/[']/gi, "|char:comillasimple|");
	texto = texto.replace(/["]/gi, "|char:comilladoble|");
	return texto;
	
}

//	abrir reporte de 
function abrirReporte(form, idlink, url , w, h) {
	if (!w) w = "100%";
	if (!h) h = "100%";
	//	formulario
	var get = getForm(form);
	url = url + ".php?" + get + "iframe=true&width=" + w + "&height=" + h;
	$("#"+idlink).attr("href", url);
	document.getElementById(idlink).click();
	return false;
}

//	abrir reporte de 
function abrirReporte2(form, idlink, url , w, h) {
	if (!w) w = "100%";
	if (!h) h = "100%";
	//	formulario
	var get = form.serialize();
	url = url + "?" + get + "&iframe=true&width=" + w + "&height=" + h;
	
	idlink.attr("href", url);
	idlink.click();
}

//	abrir reporte de 
function abrirReporteVal(idlink, url , w, h, iregistro, imprimir, form) {
	if (form) var get = getForm(form);
	else var get = "";
	if (!w) w = "100%";
	if (!h) h = "100%";
	var campo = iregistro.attr("id");
	if (!imprimir) var valor = iregistro.val(); else var valor = imprimir;
	if (valor == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		url = url + ".php?"+campo+"="+valor+"&"+get+"&iframe=true&width="+w+"&height="+h;
		$("#"+idlink).attr("href", url);
		document.getElementById(idlink).click();
	}
}

//	abrir reporte de 
function abrirReporteVal2(idlink, url , w, h, iregistro, imprimir, form) {
	if (form) var get = getForm(form);
	else var get = "";
	if (!w) w = "100%";
	if (!h) h = "100%";
	var campo = iregistro.attr("id");
	if (!imprimir) var valor = iregistro.val(); else var valor = imprimir;
	if (valor == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		url = url + "&"+campo+"="+valor+"&"+get+"&iframe=true&width="+w+"&height="+h;
		$("#"+idlink).attr("href", url);
		document.getElementById(idlink).click();
	}
}

//	abrir reporte de 
function abrirReporteVal3(idlink, url , w, h, iregistro, imprimir, form) {
	if (form) var get = getForm(form);
	else var get = "";
	if (!w) w = "100%";
	if (!h) h = "100%";
	var campo = iregistro.attr("id");
	if (!imprimir) var valor = iregistro.val(); else var valor = imprimir;
	if (valor == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		url = url + ".php?"+campo+"="+valor+"&iframe=true&width="+w+"&height="+h;
		$("#"+idlink).attr("href", url);
		document.getElementById(idlink).click();
	}
}

//
function abrirIFrame(form, idlink, url , w, h, valor) {
	if (!w) w = "100%";
	if (!h) h = "100%";
	//	formulario
	var get = getForm(form);
	if (valor == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		url = url + "&" + get + "iframe=true&width=" + w + "&height=" + h;
		$("#"+idlink).attr("href", url);
		document.getElementById(idlink).click();
	}
}

//
function abrirIFrame2(form, idlink, url, valor, w, h) {
	if (!w) w = "100%";
	if (!h) h = "100%";
	//	formulario
	var get = form.serialize();
	if (valor == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		url = url + "&" + get + "&iframe=true&width=" + w + "&height=" + h;
		idlink.attr("href", url);
		idlink.click();
	}
}

//
function abrirIFrameMulti(form, idlink, url , w, h, suf) {
	//	detalles
	var detalles = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.name == suf && n.checked) detalles += n.value + "|";
	}
	var len = detalles.length; len-=1;
	detalles = detalles.substr(0, len);
	$("#sel_"+suf).val(detalles);
	
	var valor = detalles;
	if (!w) w = "100%";
	if (!h) h = "100%";
	//	formulario
	var get = getForm(form);
	if (valor == "") cajaModal("Debe seleccionar un registro", "error", 400);
	else {
		url = url + "&" + get + "iframe=true&width=" + w + "&height=" + h;
		$("#"+idlink).attr("href", url);
		document.getElementById(idlink).click();
	}
}

//	funcion para recargar la pagina enviando el valor para ordenar una lista
function order(f, frm, iorder) {
	if (frm) var form = document.getElementById(frm); else var form = document.getElementById("frmentrada");
	if (iorder) $("#"+iorder).val(f); else $("#fOrderBy").val(f);
	form.submit();
}

//	obtengo los valiores del formulario para se enviado
function getForm(form) {
	var post = "";
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
	}
	return post;
}

//	
function bono_periodos_registrar_eventos_control(registro) {
	var detalle = "eventos";
	var secuencia = $("#secuencia").val();
	var FechaInicio = $("#FechaInicio").val();
	var FechaFin = $("#FechaFin").val();
	var nro = "nro_" + detalle + "_" + secuencia;
	var can = "can_" + detalle + "_" + secuencia;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle + "_" + secuencia;
	var nrodetalle = parseInt(parent.$("#"+nro).val()); nrodetalle++;
	var candetalle = parseInt(parent.$("#"+can).val()); candetalle++;
	
	//	ajax
	$.ajax({
		type: "POST",
		url: "../../rh/lib/fphp_funciones_ajax.php",
		data: "accion=bono_periodos_registrar_eventos_control&nrodetalle="+nrodetalle+"&candetalle="+candetalle+"&registro="+registro+"&secuencia="+secuencia+"&FechaInicio="+FechaInicio+"&FechaFin="+FechaFin,
		async: true,
		success: function(resp) {
			parent.$("#"+nro).val(nrodetalle);
			parent.$("#"+can).val(candetalle);
			parent.$("#"+lista).append(resp);
			parent.$.prettyPhoto.close();
		}
	});
}

function setPeriodoFromFecha(Fecha, iPeriodo) {
	var f = formatFechaAMD(Fecha);
	iPeriodo.val(f.substr(0, 7));
}

//	
function facturacion_activos_obligacion(CodTipoDocumento, NroDocumento, FechaRegistro) {
	parent.$("#CodTipoDocumento").val(CodTipoDocumento);
	parent.$("#NroDocumento").val(NroDocumento);
	parent.$("#FechaRegistro").val(FechaRegistro);
	parent.$.prettyPhoto.close();
}

//	funcion para activar bloqueo de formulario
function bloqueo(boo) {
	if (boo) {
		$(".div-progressbar").css("display", "block");
	} else {
		$(".div-progressbar").css("display", "none");
	}
}

//	habilito/desahabilito tipo de proceso en asignacion de conceptos x empleado
function setFlagTipoProceso(boo) {
	if (boo) {
		$('#Procesos').val('');
		$('.Procesos').val('');
		$('#btProcesos').css('visibility', 'visible');
	} else {
		$('#Procesos').val('[TODOS]');
		$('.Procesos').val('[TODOS]');
		$('#btProcesos').css('visibility', 'hidden');
	}
}

//	
function conceptos_tipo_proceso() {
	//	procesos seleccionados
	var error = "";
	var detalles_registros = "";
	var chk = false;
	var frm_registros = document.getElementById("frmentrada");
	for(var i=0; n=frm_registros.elements[i]; i++) {
		if (n.name == "registros" && n.checked) detalles_registros += n.value + " ";
	}
	if (detalles_registros == "") error = "Debe seleccionar los Tipos de Procesos";
	//	inserto
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		parent.$("#Procesos").val(detalles_registros);
		parent.$(".Procesos").val(detalles_registros);
		parent.$.prettyPhoto.close();
	}
}