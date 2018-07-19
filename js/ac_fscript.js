// JavaScript Document
//// ---------------------------------------------------------
//	agregar periodo de control contable
function control_cierres_mensuales(form, accion) {
	//	formulario
	var forganismo = document.getElementById("forganismo").value.trim();
	var ftipo_registro = document.getElementById("ftipo_registro").value.trim();
	var flibro_contable = document.getElementById("flibro_contable").value.trim();
	var periodo = document.getElementById("periodo").value.trim();
	
	//	periodos seleccionados	
	var seleccion = "";
	if (accion == "abrir" || accion == "cerrar") {
		for(i=0; n=form.elements[i]; i++) {
			if (n.name == "periodos" && n.checked) seleccion += n.value + ";";
		}
		var len = seleccion.length; len--;
		seleccion = seleccion.substr(0, len);
	}
	
	//	valido
	if (accion == "agregar" && (!valPeriodo(periodo) || periodo == "")) alert("¡ERROR: Periodo incorrecto!");
	else if ((accion == "abrir" || accion == "cerrar") && seleccion == "") alert("¡ERROR: Debe seleccionar el/los periodo(s) a actualizar!");
	else {
		cargando("block");
		//	CREO UN OBJETO AJAX
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/ac_fphp_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("modulo=control_cierres_mensuales&accion="+accion+"&periodo="+periodo+"&ftipo_registro="+ftipo_registro+"&flibro_contable="+flibro_contable+"&forganismo="+forganismo+"&seleccion="+seleccion);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				cargando("none");
				var resp = ajax.responseText;
				if (resp.trim() != "") alert(resp);
				else form.submit();
			}
		}
	}
	return false;
}
//// ---------------------------------------------------------
//	voucher
function voucher(form, accion) {
	cargando("block");
	//	formulario
	var periodo = document.getElementById("periodo").value.trim();
	var codvoucher = document.getElementById("codvoucher").value.trim();
	var nrovoucher = document.getElementById("nrovoucher").value.trim();
	var organismo = document.getElementById("organismo").value.trim();
	var codingresado = document.getElementById("codingresado").value.trim();
	var dependencia = document.getElementById("dependencia").value.trim();
	var codaprobado = document.getElementById("codaprobado").value.trim();
	var libro_contable = document.getElementById("libro_contable").value.trim();
	var descripcion = document.getElementById("descripcion").value.trim();
	var fecha = document.getElementById("fecha").value.trim();
	var nrointerno = document.getElementById("nrointerno").value.trim();
	var estado = document.getElementById("estado").value.trim();
	var total_creditos = new Number(setNumero(document.getElementById("total_creditos").innerHTML));
	var total_debitos = new Number(setNumero(document.getElementById("total_debitos").innerHTML));
	var balance = total_creditos + total_debitos;
	var contabilidad = document.getElementById("contabilidad").value; //alert('contabilidad='+contabilidad);
	
	//	detalle
	var error = "";
	var detalles = "";
	var frm_detalle = document.getElementById("frm_detalle");
	for(i=0; n=frm_detalle.elements[i]; i++) {
		if (n.name == "codcuenta") {
			if (n.value == "") { error = "¡ERROR: Debe seleccionar una cuenta contable en la línea del voucher!"; break; }
			else detalles += n.value + "|";
		}
		else if (n.name == "codpersona") {
			if (n.value == "") error = "¡ERROR: Debe seleccionar una persona en la línea del voucher!";
			else detalles += n.value + "|";
		}
		else if (n.name == "documento") detalles += n.value + "|";
		else if (n.name == "fecha") {
			if (!valFecha(n.value)) { error = "¡ERROR: Se encontró una fecha incorrecta en las líneas del voucher!"; break; }
			else detalles += n.value + "|";
		}
		else if (n.name == "monto") {
			var monto = new Number(setNumero(n.value));
			if (monto == 0) { error = "¡ERROR: El monto no puede ser cero en las líneas del voucher!"; break; }
			else if (isNaN(monto)) { error = "¡ERROR: Se encontró un monto incorrecto en las líneas del voucher!"; break; }
			else detalles += monto + "|";
		}
		else if (n.name == "codccosto") {
			if (n.value == "") { error = "¡ERROR: Debe seleccionar un centro de costo en la línea del voucher!"; break; }
			else detalles += n.value + "|";
		}
		else if (n.name == "descripcion") detalles += n.value + ";";
	}
	var len = detalles.length; len--;
	detalles = detalles.substr(0, len);
	
	//	busco errores
	if (organismo == "" || libro_contable == "") error = "¡ERROR: Los campos marcados con (*) son obligatorios!";
	else if (!valAlfaNumerico(descripcion) || !valAlfaNumerico(nrointerno)) error = "¡ERROR: No se permiten carácteres especiales en los campos!";
	else if (!valPeriodo(periodo)) error = "¡ERROR: Periodo incorrecto!";
	else if (detalles == "") error = "¡ERROR: Debe ingresar por lo menos una línea en el voucher!";
	else if (!valAlfaNumerico(detalles)) error = "¡ERROR: No se permiten carácteres especiales en los campos!";
	else if (balance != 0) error = "¡ERROR: El balance de créditos y débitos es diferente!";
	
	//	valido
	if (error != "") { alert(error); cargando("none"); }
	else {
		//	CREO UN OBJETO AJAX
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/ac_fphp_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("modulo=voucher&accion="+accion+"&periodo="+periodo+"&codvoucher="+codvoucher+"&nrovoucher="+nrovoucher+"&organismo="+organismo+"&codingresado="+codingresado+"&dependencia="+dependencia+"&codaprobado="+codaprobado+"&libro_contable="+libro_contable+"&descripcion="+descripcion+"&fecha="+fecha+"&nrointerno="+nrointerno+"&estado="+estado+"&detalles="+detalles+"&contabilidad="+contabilidad);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				cargando("none");
				var resp = ajax.responseText;
				if (resp.trim() != "") alert(resp);
				else form.submit();
			}
		}
	}
	return false;
}

//	mayorizar/desmayorizar voucher
/*function voucher_mayorizar(form, accion) {
	cargando("block");
	//	formulario
	var organismo = document.getElementById("forganismo").value.trim();
	var periodo = document.getElementById("fperiodo").value.trim();
	
	//	vouchers seleccionados	
	var seleccion = "";
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == "voucher" && n.checked) seleccion += n.value + ";";
	}
	var len = seleccion.length; len--;
	seleccion = seleccion.substr(0, len);
	
	//	errores
	var error = "";
	if (!valPeriodo(periodo)) error = "¡ERROR: Periodo incorrecto!";
	else if (seleccion == "") error = "¡ERROR: Debe seleccionar el/los voucher(s) a " + accion + "!";
	
	//	valido
	if (error != "") { alert(error); cargando("none"); }
	else {	alert("modulo=voucher_mayorizar&accion="+accion+"&periodo="+periodo+"&organismo="+organismo+"&seleccion="+seleccion);
		//	CREO UN OBJETO AJAX
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/ac_fphp_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("modulo=voucher_mayorizar&accion="+accion+"&periodo="+periodo+"&organismo="+organismo+"&seleccion="+seleccion);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				cargando("none");
				var resp = ajax.responseText;
				if (resp.trim() != "") alert(resp);
				else form.submit();
			}
		}
	}
}*/
//// ---------------------------------------------------------
//	mayorizar/desmayorizar voucher
function voucher_mayorizar(form, accion) {
	cargando("block");  //alert(accion);
		//	formulario
	var organismo = document.getElementById("forganismo").value.trim();
	var periodo = document.getElementById("fperiodo").value.trim();
	var codcontabilidad = document.getElementById("fContabilidad").value;
	//var voucher = document.getElementById("voucher").value;
	
	//	vouchers seleccionados	
	var seleccion = "";
	for(i=0; n=form.elements[i]; i++) {
		if (n.name == "voucher" && n.checked) seleccion += n.value + ";";
	}
	var len = seleccion.length; len--;
	seleccion = seleccion.substr(0, len);
	
	//	errores
	var error = "";
	if (!valPeriodo(periodo)) error = "¡ERROR: Periodo incorrecto!";
	else if (seleccion == "") error = "¡ERROR: Debe seleccionar el/los voucher(s) a " + accion + "!";
	
	//	valido
	if (error != "") { alert(error); cargando("none"); }
	else {	//alert("modulo=voucher_mayorizar&accion="+accion+"&periodo="+periodo+"&organismo="+organismo+"&seleccion="+seleccion);
		//	CREO UN OBJETO AJAX
		var ajax=nuevoAjax();
		ajax.open("POST", "../lib/ac_fphp_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("modulo=voucher_mayorizar&accion="+accion+"&periodo="+periodo+"&organismo="+organismo+"&seleccion="+seleccion+"&codcontabilidad="+codcontabilidad);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				cargando("none");
				var resp = ajax.responseText;
				if (resp.trim() != "") alert(resp);
				else form.submit();
			}
		}
	}
}
//// ---------------------------------------------------------
//	valido filtro
function consultas_saldo_determinado(form) {
	var fcodcuentad = document.getElementById("fcodcuentad").value.trim();
	var fcodcuentah = document.getElementById("fcodcuentah").value.trim();
	
	if (fcodcuentad == "" || fcodcuentah == "") {
		alert("¡ERROR: Debe seleccionar la cuenta desde y la cuenta hasta!");
		return false;
	} else return true;
}
//// ---------------------------------------------------------
function activarSelector(form, id){
	//alert('id='+id);
if(id == 'T'){ 
     form.btCuenta.disabled=false; form.btPub20.disabled=true; 
  }else{ 
     form.btCuenta.disabled=true;  form.btPub20.disabled=false;
  }
}
// -----------------------------------------------------------
function totalLotes(registros, rows, limit) {
	var desde = document.getElementById("desde");
	var hasta = document.getElementById("hasta");
	var btPrimero=document.getElementById("btPrimero");
	var btAtras=document.getElementById("btAtras");
	var btSiguiente=document.getElementById("btSiguiente");
	var btUltimo=document.getElementById("btUltimo");
	if (registros) {
		if (limit==0) { btPrimero.disabled=true; btAtras.disabled=true; } else { btPrimero.disabled=false; btAtras.disabled=false; }		
		if ((registros<=MAXLIMIT) || ((limit+rows)==registros) || (limit==registros)) { btSiguiente.disabled=true; btUltimo.disabled=true; }
		else { btSiguiente.disabled=false; btUltimo.disabled=false; }		
		desde.innerHTML=limit+1;
		hasta.innerHTML=limit+rows;
	} else {
		btPrimero.disabled=true; btAtras.disabled=true;
		btSiguiente.disabled=true; btUltimo.disabled=true;
		desde.innerHTML=0;
		hasta.innerHTML=0;
	}
}
// -----------------------------------------------------------
function totalLista(rows) {
	var numreg = document.getElementById("rows");
	numreg.innerHTML="Registros: "+rows;
}
// -----------------------------------------------------------
function setLotes(form, lote, registros, limit, ordenar) {
	//alert('Limit='+limit);
	//alert('Lote='+lote);
	switch (lote) {
		case "P":
			limit=0;
			break;
		case "A":
			limit=limit-MAXLIMIT; 
			break;
		case "S":
			limit=limit+MAXLIMIT; //alert('Limit2='+limit);
			break;
		case "U":
			var num=(registros/MAXLIMIT);
			num=parseInt(num);
			limit=num*MAXLIMIT;
			if (limit==registros) limit=limit-MAXLIMIT;
			break;
	}
	//document.getElementById("limit").value = limit;
	var pagina=form.action+"&limit="+limit+"&ordenar="+ordenar;
	cargarPagina(form, pagina);
}
//// ---------------------------------------------------------
function cambioPeriodo(id){
  var periodo = document.getElementById(id).value; //alert(periodo);
  var periodo = periodo.split("-");
  var m = periodo[1];
  var a = periodo[2];
  
  var am = a+'-'+m; //alert('am='+am);
  document.getElementById("periodo").value =  am;
}