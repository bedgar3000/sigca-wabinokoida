// JavaScript Document
//// ---------------------------------------------------------------------------------------------
//// 			FUNCION QUE PERMITE ACTIVAR RADIOS
//// ---------------------------------------------------------------------------------------------
function chekeador(form,id){ 
  if(id=="radio2"){ 
     form.radio1.checked=false;
	 document.getElementById("radio").value = "I"; 
  }
  if(id=="radio1"){ 
    form.radio2.checked=false;
	document.getElementById("radio").value = "A"; 
  }
}
//// ---------------------------------------------------------------------------------------------
//// 	FUNCION QUE PERMITE ACTIVAR CAMPOS DE AF_ACTIVOSMENORES
//// ---------------------------------------------------------------------------------------------
function enabledUbicacionActivosMenores(form){
 if(form.checkUbicacion.checked){ 
    form.fubicacion2.disabled=false; form.btUbicacion.disabled = false; document.getElementById('ubicacionactivo').style.visibility='visible';
}else{ 
    form.fubicacion2.disabled=true; form.fubicacion2.value=''; form.btUbicacion.disabled = true; form.fubicacion.value=''; 
	document.getElementById('ubicacionactivo').style.visibility='hidden';}
}
//// FUNCION QUE PERMITE ACTIVAR Y DESCTIVAR CAMPOS EN RP INVENTARIO A LA FECHA
function enabledRpAFecha(form){
 if(form.chkfecha.checked){
    form.fecha_desde.disabled=false; form.fecha_hasta.disabled=false;
 }else{
   form.fecha_desde.disabled=true; form.fecha_hasta.disabled=true; form.fecha_desde.value=''; form.fecha_hasta.value='';
 }
}
//// ---------------------------------------------------------------------------------------------
//// ----------		FUNCION QUE PERMITE GUARDAR REGISTRO TRANSACCION BAJA PARA UN ACTIVO
//// ---------------------------------------------------------------------------------------------
function guardarTransaccionBaja(form, Modulo){ 
   //alert(Modulo);

  var Activo= document.getElementById("nro_activo").value;
  var Organismo = document.getElementById("codorganismo").value;
  if(Modulo!='Nuevo') var CodTransaccionBaja = document.getElementById("codtransaccionbaja").value;

if(Modulo!='Anular'){
  var TipoTransaccion = document.getElementById("tipobaja").value;
  var Dependencia = document.getElementById("coddependencia").value;
  var Fecha = document.getElementById("f_actual").value;
  var FechaBaja = document.getElementById("f_baja").value;
  var CentroCosto = document.getElementById("codcentrocosto").value;
  var Responsable = document.getElementById("codresponsable").value;
  var ConceptoMovimiento = document.getElementById("conceptoMovimiento").value;
  var CodigoInterno = document.getElementById("codigo_interno").value;
  var Categoria = document.getElementById("codcategoria").value;
  var Ubicacion = document.getElementById("codubicacion").value;
  var Comentario = document.getElementById("comentario").value;
  var MontoLocal = setNumero(document.getElementById("monto_local").value); 
  var Resolucion = document.getElementById("nro_documento").value; 
  var FacturaNumero = document.getElementById("nrofactura").value;
  //var FechaIngreso = document.getElementById("FechaIngreso").value;
 // var NumeroOrden = document.getElementById("NumeroOrden").value;
  //var CodTransaccionBaja = document.getElementById("CodTransaccionBaja").value;
  
  var MotivoTraslado = document.getElementById("motivoTrasladoExterno").value;
  if(Modulo=='Nuevo')var PreparadoPor = document.getElementById("prepor").value;
  var regresar = document.getElementById("regresar").value; //alert('regresa=='+regresar)
  
  
  if(form.radio2.checked) var FlagExterno='S';
  if(form.flagContabilizado.checked) var ContabilizadoFlag = 'S'; else var ContabilizadoFlag = 'N';
}
  if(Modulo!='Nuevo') var Usuario = document.getElementById("usuario_actual").value;
  if(Modulo=='Anular'){
	  var motivo_anular = document.getElementById("motivo_anular").value;
	  var estado = document.getElementById("estado").value;
  }
   
 var ajax=nuevoAjax();
	ajax.open("POST", "gmactivofijo.php", true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("accion=guardarTransaccionBaja&Activo="+Activo+"&Organismo="+Organismo+"&Dependencia="+Dependencia+
		      "&TipoTransaccion="+TipoTransaccion+"&Fecha="+Fecha+"&CentroCosto="+CentroCosto+"&Responsable="+Responsable+
		      "&ConceptoMovimiento="+ConceptoMovimiento+"&CodigoInterno="+CodigoInterno+"&Categoria="+Categoria+"&Ubicacion="+Ubicacion+
		      "&Comentario="+Comentario+"&MontoLocal="+MontoLocal+"&Resolucion="+Resolucion+"&FacturaNumero="+FacturaNumero+
		      "&ContabilizadoFlag="+ContabilizadoFlag+"&Modulo="+Modulo+"&CodTransaccionBaja="+CodTransaccionBaja+
		      "&FlagExterno="+FlagExterno+"&MotivoTraslado="+MotivoTraslado+"&PreparadoPor="+PreparadoPor+"&Usuario="+Usuario+
		      "&motivo_anular="+motivo_anular+"&estado="+estado+"&FechaBaja="+FechaBaja);
  
  ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText.trim();
			if (resp != "")alert(resp); 
            else if((Modulo=='Nuevo')||(Modulo=='Modificar'))cargarPagina(form, document.getElementById("regresar").value+".php?limit=0");
				 else{ opener.document.getElementById("frmentrada").submit(); window.close();}
		}
	}
	return false;
}
//// -------------------------------------------------------------------------------------- ////
//// 			UTILIZADA PARA SELECCION MULTIPLE EN APROBACION BAJA DE ACTIVO
//// -------------------------------------------------------------------------------------- ////	
function cargarMasivoBajaActivo(form, Modulo) {
	
    //var Activo = document.getElementById("nro_activo").value; 
    var CodOrganismo = document.getElementById("fOrganismo").value;
   
	var detalles = "";
	 	
	for(i=0; n=form.elements[i]; i++) {
		if (n.type=="checkbox" && n.checked) detalles += n.value + ";";
	}
	var len = detalles.length; len--; 
	detalles = detalles.substr(0, len); 
	
	var contador=1;
	
	// Recorre la cadena string y verifico cuantos separadores(;) existen
    for(y=0; y<detalles.length; y++){
      if(detalles.charAt(y)==";"){
	     contador++; //alert("contador= "+contador);      
	  }
    }
	
	/*var an = 1;  var dep="";  var cont = 1; var inc = 0; var paso="";
	var a1 = detalles.split(';');
	
	for(x=0; x<contador; x++){
		
	  if((a1[inc]!="")&&(a1[inc]!="undefined")){ 
	    var a2 = a1[inc].split('|'); 
	    var a3 = a2[3]; //alert('a3= '+a3);
		inc++;
	  }
	  if((dep!=a3)&&(cont==1)) dep = a3;
	  else 
	    if((dep==a3)&&(cont!=1)) dep = a3;
        else{ alert('¡ Debe Seleccionar registros para una misma Dependencia ¡'); paso=1;}
	
	   if((a1[inc]!="")&&(a1[inc]!="undefined")){   
		 cont++; //alert('cont= '+cont);
	   }
	
	} */
	
	if (detalles == "") alert("¡Debe seleccionar por lo menos un registro!");
	else {
		var ajax=nuevoAjax();
		ajax.open("POST", "gmactivofijo.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("accion=guardarTransaccionBaja&detalles="+detalles+"&CodOrganismo="+CodOrganismo+"&Modulo="+Modulo);
		ajax.onreadystatechange=function() {
		if(ajax.readyState==4)	{
		  var resp = ajax.responseText;
		  if(resp != ""){
			  //alert("resp= "+resp);
			  var veces = resp.split(';'); 
			  var NroActa = veces[0];
			  var CodOrganismo = veces[1];
			  var Anio = veces[2];
			  
			  document.getElementById("frmentrada").submit();
			  
			  // Levanta ventana ACTA DE ENTREGA DE BIENES MUEBLES
			window.open('af_actabajabm.php?NroActa='+NroActa+'&CodOrganismo='+CodOrganismo+"&Anio="+Anio,'','height=500, width=870, left=200, top=100, resizable=yes');
									 
		  }else document.getElementById("frmentrada").submit();
		}
	   }
	 }
	
	window.close();
 //return false;
}
//// -------------------------------------------------------------------------------------- ////
//// 			UTILIZADA PARA SELECCION MULTIPLE EN ACTA RESPONSABILIDAD USO
//// -------------------------------------------------------------------------------------- ////	
function cargarMasivoActaRespUsoActivo(form, Modulo) {
	
  var deseaContinuar = confirm('!Esta seguro de realizar la operación.!');	
  if (deseaContinuar == true) {
 
	   //var Activo = document.getElementById("nro_activo").value; 
	   var CodOrganismo = document.getElementById("fOrganismo").value;
	   var Cod_Dependencia = document.getElementById("fDependencia").value;
	   
		var detalles = "";
			
		for(i=0; n=form.elements[i]; i++) {
			if (n.type=="checkbox" && n.checked) detalles += n.value + ";";
		}
		var len = detalles.length; len--; 
		detalles = detalles.substr(0, len); 
		
		var contador=1;
		
		// Recorre la cadena string y verifico cuantos separadores(;) existen
		for(y=0; y<detalles.length; y++){
		  if(detalles.charAt(y)==";"){
			 contador++;     
		  }
		}
		
		var an = 1;  var dep="";  var cont = 1; var inc = 0; var paso="";
		var a1 = detalles.split(';');
		
		for(x=0; x<contador; x++){
			
		  if((a1[inc]!="")&&(a1[inc]!="undefined")){ 
			var a2 = a1[inc].split('|'); 
			var a3 = a2[3]; 
			inc++;
		  }
		  if((dep!=a3)&&(cont==1)) dep = a3;
		  else 
			if((dep==a3)&&(cont!=1)) dep = a3;
			else{ alert('¡ Debe Seleccionar Activos para un mismo Empleado Usuario ¡'); paso=1;}
		
		   if((a1[inc]!="")&&(a1[inc]!="undefined")){   
			 cont++; 
		   }
		
		}
		
		
		if (detalles == "") alert("¡Debe seleccionar por lo menos un registro!");
		else if(paso!=1){
			var ajax=nuevoAjax();
			ajax.open("POST", "gmactivofijo.php", true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send("accion=cargarMasivoActaRespUsoActivo&detalles="+detalles+"&CodOrganismo="+CodOrganismo+"&Cod_Dependencia="+Cod_Dependencia+"&Modulo="+Modulo);
			ajax.onreadystatechange=function() {
			if(ajax.readyState==4)	{
			  var resp = ajax.responseText;
			  if(resp != ""){
				  
				  var veces = resp.split(';'); 
				  var NroActa = veces[0];
				  var CodOrganismo = veces[1];
				  var Anio = veces[2];
				  var CodDependencia = veces[3];
				  
				  document.getElementById("frmentrada").submit();
				  
				  // Levanta ventana ACTA DE ENTREGA DE BIENES MUEBLES
				window.open('af_actaresponusopdf.php?NroActa='+NroActa+'&CodOrganismo='+CodOrganismo+"&Anio="+Anio+"&CodDependencia="+CodDependencia,'','height=500, width=870, left=200, top=100, resizable=yes');
										 
			  }else document.getElementById("frmentrada").submit();
			}
		   }
		 }
  }
	window.close();
 //return false;
}
//// -------------------------------------------------------------------------------------- ////
////          APROBACION BAJA DE ACTIVO - LEVANTA PAGINA PREVIA VISUALIZACION
//// -------------------------------------------------------------------------------------- ////	
function cargarBajaActivo(form, pagina, target, param) {
 var detalles = ""; 
 var cont = "";
	 
	
	for(i=0; n=form.elements[i]; i++) { 
		if (n.type=="checkbox" && n.checked){ detalles += n.value + ";"; cont++;}
	}
	var len = detalles.length; len--;
	detalles = detalles.substr(0, len);
	
	if(detalles=="" || cont>1) alert("¡Debe seleccionar un registro!");
	else{
		if (target == "SELF") cargarPagina(form, pagina);
		else if (target == "BLANK") {
			var ajax=nuevoAjax();
				ajax.open("POST", "gmactivofijo.php", true);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send("accion=cargarBajaActivo&detalles="+detalles);
				ajax.onreadystatechange=function() {
					if (ajax.readyState==4)	{
						var resp = ajax.responseText;
						if(resp != ""){ //alert('resp= '+resp);
						  pagina = pagina + "&registro="+ detalles; //alert("Pagina= "+ pagina);
						  window.open(pagina, pagina, "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
						}
                		
				   }
			}
		}
} }
//// -------------------------------------------------------------------------------------- ////
////          GENERAR ACTA DE BAJA DE ACTIVO - LEVANTA PAGINA PREVIA VISUALIZACION
//// -------------------------------------------------------------------------------------- ////	
function cargarBajaActivoGenerarActa(form, Modulo) {
 
 var detalles = ""; 
 var cont = "";
	 
	
	for(i=0; n=form.elements[i]; i++) { 
		if (n.type=="checkbox" && n.checked){ detalles += n.value + ";"; cont++;}
	}
	var len = detalles.length; len--;
	detalles = detalles.substr(0, len);
	
	if(detalles=="" || cont>1) alert("¡Debe seleccionar un registro!");
	else{
		var ajax=nuevoAjax();
		ajax.open("POST", "gmactivofijo.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("accion=guardarTransaccionBaja&detalles="+detalles+'&Modulo='+Modulo);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				var resp = ajax.responseText;
				if(resp != ""){ //alert('resp= '+resp);
				  var veces = resp.split(';'); 
			  	  var NroActa = veces[0];
			 	  var CodOrganismo = veces[1];
			 	  var Anio = veces[2];
				  document.getElementById("frmentrada").submit();
				  window.open('af_actabajabm.php?NroActa='+NroActa+'&CodOrganismo='+CodOrganismo+"&Anio="+Anio,'','height=500, width=870, left=200, top=100, resizable=yes');
				  
				}
		    }
		 }
	  }
	return false;
} 
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE INVENTARIO X DEPENDENCIA
//// ---------------------------------------------------------------------------------------------
function cargarInventarioxDependencia(form){
 
	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.chkCatClasf.checked) filtro+=" and a.Clasificacion LIKE *"+form.fClasificacion.value+"%"+"*";
	if(form.checkUbicacion.checked) filtro+=" and a.Ubicacion LIKE *"+form.fubicacion.value+"%"+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*";
	if(form.chkclasificacionpub20.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fCodclasficacionPub20.value+"%"+"*";
	if(form.checkSituacionActivo.checked) filtro+=" and a.SituacionActivo=*"+form.fSituacionActivo.value+"*";
	if(form.chekcCosto.checked) filtro+=" and a.CentroCosto=*"+form.centro_costos.value+"*";
	if(form.chkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkBienes.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fBienes.value+"%"+"*";
	
    var pagina_mostrar="af_rpinventarioxdependenciapdf.php?filtro="+filtro;
        form.target = "af_rpinventarioxdependenciapdf";				
				cargarPagina(form, pagina_mostrar);
}
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE INVENTARIO ACTIVOS COSTO
//// ---------------------------------------------------------------------------------------------
function cargarInventarioActivosLista(form){

	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.chkCatClasf.checked) filtro+=" and a.Clasificacion LIKE *"+form.fClasificacion.value+"%"+"*";
	if(form.checkUbicacion.checked) filtro+=" and a.Ubicacion LIKE *"+form.ubicacion.value+"%"+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*";
	if(form.chkclasificacionpub20.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fCodclasficacionPub20.value+"%"+"*";
	if(form.checkSituacionActivo.checked) filtro+=" and a.SituacionActivo=*"+form.fSituacionActivo.value+"*";
	if(form.chekcCosto.checked) filtro+=" and a.CentroCosto=*"+form.centro_costos.value+"*";
	if(form.chkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkBienes.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fBienes.value+"%"+"*";
	
    var pagina_mostrar="af_rpinventarioactivoscostopdf.php?filtro="+filtro;
        form.target = "af_rpinventarioactivoscostopdf";				
				cargarPagina(form, pagina_mostrar);
}
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE INVENTARIO ACTIVOS COSTO GENERAL
//// ---------------------------------------------------------------------------------------------
function cargarInventarioActivosListaGen(form){

	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.chkCatClasf.checked) filtro+=" and a.Clasificacion LIKE *"+form.fClasificacion.value+"%"+"*";
	if(form.checkUbicacion.checked) filtro+=" and a.Ubicacion LIKE *"+form.ubicacion.value+"%"+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*";
	if(form.chkclasificacionpub20.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fCodclasficacionPub20.value+"%"+"*";
	if(form.checkSituacionActivo.checked) filtro+=" and a.SituacionActivo=*"+form.fSituacionActivo.value+"*";
	if(form.chekcCosto.checked) filtro+=" and a.CentroCosto=*"+form.centro_costos.value+"*";
	if(form.chkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkBienes.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fBienes.value+"%"+"*";
	
    var pagina_mostrar="af_rpinventarioactivoscostogenpdf.php?filtro="+filtro;
        form.target = "af_rpinventarioactivoscostogenpdf";				
				cargarPagina(form, pagina_mostrar);
}
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE INVENTARIO ACTIVOS COSTO
//// ---------------------------------------------------------------------------------------------
function cargarFormularioBM_1(form){

	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.chkCatClasf.checked) filtro+=" and a.Clasificacion LIKE *"+form.fClasificacion.value+"%"+"*";
	if(form.checkUbicacion.checked) filtro+=" and a.Ubicacion LIKE *"+form.fubicacion.value+"%"+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*";
	if(form.chkclasificacionpub20.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fClasificacionPub20.value+"%"+"*";
	if(form.checkSituacionActivo.checked) filtro+=" and a.SituacionActivo=*"+form.fSituacionActivo.value+"*";
	if(form.chekcCosto.checked) filtro+=" and a.CentroCosto=*"+form.centro_costos.value+"*";
	if(form.chkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkBienes.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fBienes.value+"%"+"*";
	
    var pagina_mostrar="af_rpformulariobm_1pdf.php?filtro="+filtro;
        form.target = "af_rpformulariobm_1pdf";				
				cargarPagina(form, pagina_mostrar);
}
//// ---------------------------------------------------------------------------------------------
function cargarFormularioBM_2(form){

	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.chkTipoMov.checked) filtro+=" and a.TipoMovimientos=*"+form.fTipoMov.value+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*";
	if(form.checkSituacionActivo.checked) filtro+=" and a.SituacionActivo=*"+form.fSituacionActivo.value+"*";
	if(form.chekcCosto.checked) filtro+=" and a.CentroCosto=*"+form.centro_costos.value+"*";
	if(form.checkUbicacion.checked) filtro+=" and a.Ubicacion=*"+form.fubicacion.value+"*";
	if(form.chkPeriodo.checked) filtro+=" and a.PeriodoTransaccion=*"+form.fPeriodo.value+"*";	
	
    var pagina_mostrar="af_rpformulariobm_2pdf.php?filtro="+filtro;
        form.target = "af_rpformulariobm_2pdf";				
				cargarPagina(form, pagina_mostrar);
}

//// ---------------------------------------------------------------------------------------------
//// ----------					MOSTRAR REPORTE INVENTARIO A LA FECHA
//// ---------------------------------------------------------------------------------------------
function cargarInventarioAFecha(form){
 
	var filtro="";
	var filtro2="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.chkCatClasf.checked) filtro+=" and a.Clasificacion LIKE *"+form.fClasificacion.value+"%"+"*";
	if(form.checkUbicacion.checked) filtro+=" and a.Ubicacion LIKE *"+form.fubicacion.value+"%"+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*"; else var dep="1";
	if(form.chkclasificacionpub20.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fCodclasficacionPub20.value+"%"+"*";
	if(form.checkSituacionActivo.checked) filtro+=" and a.SituacionActivo=*"+form.fSituacionActivo.value+"*";
	if(form.chekcCosto.checked) filtro+=" and a.CentroCosto=*"+form.centro_costos.value+"*";
	if(form.chkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkBienes.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fBienes.value+"%"+"*";
	
	
	if(form.chkfecha.checked){ 
	   if (form.fSituacionActivo.value=="DE") {
	   		var fecha_desde = document.getElementById("fecha_desde").value;
	       fecha_desde = fecha_desde.split('-');
		   fecha_desde = fecha_desde[2]+"-"+fecha_desde[1]+"-"+fecha_desde[0];
	
		   var fecha_hasta = document.getElementById("fecha_hasta").value;
		       fecha_hasta = fecha_hasta.split('-');
	       var fecha_hasta_mostrar = fecha_hasta[0]+"/"+fecha_hasta[1]+"/"+fecha_hasta[2]; 
			   fecha_hasta = fecha_hasta[2]+"-"+fecha_hasta[1]+"-"+fecha_hasta[0]; 
		   
		   filtro2+=" d.FechaBaja>=*"+fecha_desde+"*";
		   filtro2+=" and d.FechaBaja<=*"+fecha_hasta+"*";

	   }else{
	   		var fecha_desde = document.getElementById("fecha_desde").value;
	       fecha_desde = fecha_desde.split('-');
		   fecha_desde = fecha_desde[2]+"-"+fecha_desde[1]+"-"+fecha_desde[0];
	
		   var fecha_hasta = document.getElementById("fecha_hasta").value;
		       fecha_hasta = fecha_hasta.split('-');
	       var fecha_hasta_mostrar = fecha_hasta[0]+"/"+fecha_hasta[1]+"/"+fecha_hasta[2]; 
			   fecha_hasta = fecha_hasta[2]+"-"+fecha_hasta[1]+"-"+fecha_hasta[0]; 
		   
		   filtro+=" and a.FechaIngreso>=*"+fecha_desde+"*";
		   filtro+=" and a.FechaIngreso<=*"+fecha_hasta+"*";

	   }
	   
	   
	}else var fecha_hasta_mostrar ="";
	
    var pagina_mostrar="af_rpinventarioalafechapdf.php?filtro="+filtro+"&filtro2="+filtro2+"&dep="+dep+"&fecha_hasta_mostrar="+
                        fecha_hasta_mostrar+"&fSituacionActivo="+form.fSituacionActivo.value;
        form.target = "af_rpinventarioalafechapdf";				
				cargarPagina(form, pagina_mostrar);
}
//// ---------------------------------------------------------------------------------------------
//// ----------					MOSTRAR REPORTE INVENTARIO A LA FECHA
//// ---------------------------------------------------------------------------------------------
function cargarInventarioAFGeneral(form){
 
	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.chkCatClasf.checked) filtro+=" and a.Clasificacion LIKE *"+form.fClasificacion.value+"%"+"*";
	if(form.checkUbicacion.checked) filtro+=" and a.Ubicacion LIKE *"+form.fubicacion.value+"%"+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*"; else var dep="1";
	if(form.chkclasificacionpub20.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fCodclasficacionPub20.value+"%"+"*";
	if(form.checkSituacionActivo.checked) filtro+=" and a.SituacionActivo=*"+form.fSituacionActivo.value+"*";
	if(form.chekcCosto.checked) filtro+=" and a.CentroCosto=*"+form.centro_costos.value+"*";
	if(form.chkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkBienes.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fBienes.value+"%"+"*";
	
	
	//if(form.chkfecha.checked) filtro+=" and a.FechaIngreso=*"+form.fBienes.value+"%"+"*";
	
    var pagina_mostrar="af_rpinventariogeneralpdf.php?filtro="+filtro+"&dep="+dep;
        form.target = "af_rpinventariogeneralpdf";				
				cargarPagina(form, pagina_mostrar);
}
//// ---------------------------------------------------------------
///           
//// ---------------------------------------------------------------
/*function Distribucion(form,id){
	var distribucion = document.getElementById(id).value;
	    document.getElementById("distribucion").value= distribucion;
	if(distribucion!=""){
		//document.getElementById("mostrar").style.visibility= "visible";
		var visible = 'style="visibility:visible"';
		var tipobaja = document.getElementById("tipobaja").value;
		cargarPagina(form,"af_bajactivosnuevo.php?distribucion="+distribucion+"&visible="+visible+"&tipobaja="+tipobaja);
		
	}else document.getElementById("mostrar").style.visibility= "hidden";	
		
}*/
//// ---------------------------------------------------------------------------------------------
//// ----------		ACTIVAR TABLA DE FORMULARIO
//// ---------------------------------------------------------------------------------------------
function ActivarTable(form,valor){
	var valor = document.getElementById(valor).value; //alert(valor);
if(valor!=""){
  document.getElementById("mostrar").style.visibility = 'visible';
  document.getElementById("scrool").style.display = 'block';
}else{ 
   document.getElementById("mostrar").style.visibility = 'hidden'; 
   document.getElementById("scrool").style.display = 'none';
}
}
//// --------------------------------------------------------------------------------------------
function formatoMoneda(fld, milSep,decSep, e){
    var sep = 0; 
    var key = ''; 
    var i = j = 0; 
    var len = len2 = 0; 
    var strCheck = '0123456789'; 
    var aux = aux2 = ''; 
    var whichCode = (window.Event) ? e.which : e.keyCode; 
   // alert(whichCode);
    //if (whichCode == 13) return true; // Enter 
    
    //key = String.fromCharCode(whichCode); // Get key value from key code
    //alert(whichCode);
    
    if(whichCode!=8) //PARA QUE PERMITA ACEPTAR LA TECHA <- (BORRAR)
    {
    	key = String.fromCharCode(whichCode); // Get key value from key code
    	//alert(strCheck.indexOf(key));
    	if (strCheck.indexOf(key) == -1) return false; // Not a valid key
    	len = fld.value.length;    	
   		// alert(len);
    }
    
    else len = fld.value.length-1; //PARA QUE PERMITA BORRAR
   // alert(len);
    for(i = 0; i < len; i++) 
     if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != 44)) break; 
    aux = ''; 
    for(; i < len; i++) 
     if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i); 
    aux += key;
    len = aux.length;
    if (len == 0) fld.value = '0,00'; 
    if (len == 1) fld.value = '0'+ decSep + '0' + aux; 
    if (len == 2) fld.value = '0'+ decSep + aux; 
    if (len > 2) 
    { 
	     aux2 = ''; 
	     for (j = 0, i = len - 3; i >= 0; i--) 
	     { 
		      if (j == 3) 
		      { 
			       aux2 += milSep; 
			       j = 0; 
		      } 
		      aux2 += aux.charAt(i); 
		      j++; 
	     } 
	     fld.value = ''; 
	     len2 = aux2.length; 
	     for (i = len2 - 1; i >= 0; i--) 
	      	fld.value += aux2.charAt(i); 
	     fld.value += decSep + aux.substr(len - 2, len);
    } //decSep +
    return false;
}
//// ---------------------------------------------------------------------------------------------
//// ----------					ANULAR REGISTROS BAJA ACTIVOS 
//// ---------------------------------------------------------------------------------------------
function anularReg(form, pagina, accion){
 var codigo=form.registro.value;
 var Estado = document.getElementById("estado").value;
 
	if (codigo=="") msjError(1000);
	else if(Estado=='AP')alert("¡Este registro no puede ser anulado por estar en estado AProbado!");
	else{
	   var anular=confirm("¡Esta seguro de anular este registro!");
	     if(anular){
			 
			 cargarOpcion(form, pagina+"&Estado="+Estado,'BLANK', 'height=300, width=850, left=250, top=50, resizable=no');
		 }
	}
}
//// ---------------------------------------------------------------------------------------------
//// ----------							ANULAR REGISTROS 
//// ---------------------------------------------------------------------------------------------
function anularRegist(form, pagina, accion){
 var codigo=form.registro.value;
	if (codigo=="") msjError(1000);
	else{
	   var anular=confirm("¡Esta seguro de anular este registro!");
	     if(anular){
	       var ajax=nuevoAjax();
			ajax.open("POST", "gmactivofijo.php", true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send("accion=accion&codigo="+codigo);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4)	{
					var error=ajax.responseText;
					if (error!=0) alert ("¡"+error+"!");
					else cargarPagina(form, pagina+"&limit="+limit);
				}
			}
	    
		 }
	}
}
//// ---------------------------------------------------------------------------------------------							 
//// ---------------------------------------------------------------------------------------------
function muestra_detalle(form, valor){
  //alert(form);
  var cont = document.getElementById("cont").value; //alert(cont);
  
  var ajax=nuevoAjax();
			ajax.open("POST", "gmactivofijo.php", true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send("accion=tab2"+"&valor="+valor+"&cont="+cont);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4)	{
					//alert(ajax.responseText);
					 document.getElementById("tab2_cargar").innerHTML=ajax.responseText;
				}
			}
}
//// ---------------------------------------------------------------------------------------------								 
////	 -------------------		FUNCION PARA CARGAR UNA NUEVA PAGINA 
//// ---------------------------------------------------------------------------------------------
function cargarOpcionEditarActMenor(form, pagina, target, param){ 
	var codigo=form.registro.value;
	
	//if (codigo=="") msjError(1000);
	if(codigo=="") alert("¡Debe seleccionar un registro!");
	else 
		if (target=="SELF") cargarPaginaAF(form, pagina+"&registro="+codigo);
		else { pagina=pagina+"?limit=0&accion=VER&registro="+codigo; alert('pagina='+pagina); cargarVentana(form, pagina, param); }
}
//// ---------------------------------------------------------------------------------------------	
////                  FUNCION PARA CARGAR MODIFICAR BAJA ACTIVO - VALIDANDO EL ESTADO							 
//// ---------------------------------------------------------------------------------------------
function cargarOpcionListaBajaActivo(form, pagina, target, param) { 
	
	var codigo=form.registro.value;
	var estado = document.getElementById("estado_activo").value;
	
	
	if (codigo=="") msjError(1000);
	else 
	   
	  if((estado=='AP')||(estado=='RV')){ 
	     if(estado=='AP') var ST = 'Aprobado'; else if(estado=='RV') var ST = 'Revisado';
	     alert('¡ El Registro no puede ser modificado por estar en Estado '+ ST+ '¡');
      }else {
		  
		if(target=="SELF"){cargarPaginaAF(form, pagina+"&registro="+codigo);
		}else{ 
		    pagina= pagina+"?limit=0&accion=VER&registro="+codigo; 
			cargarVentana(form, pagina, param); 
	    }
	}
}
//// ---------------------------------------------------------------------------------------------	
////                  FUNCION PARA CAPTURAR ESTADO DE ACTIVO							 
//// ---------------------------------------------------------------------------------------------
function capturarEstadoActivo(form, estadoActivo){
 //alert(estadoActivo); 
 document.getElementById("estado_activo").value = estadoActivo; //alert('Estado= '+document.getElementById("estadoMov").value);
}
//// ---------------------------------------------------------------------------------------------
///              			IMPRIMIR REPORTE ACTA DE RESPONSABILIDAD DE USO
//// ---------------------------------------------------------------------------------------------
function imprimirActaResponsabilidadUso(form, modulo){
    
	var codigo = form.registro.value; //alert(codigo);
		
	if(codigo == "") msjError(1000);
	else{
		var valor = codigo.split('-');
		var NroActa = valor[0];
		var CodOrganismo = valor[1];
		var Anio = valor[2];
		var CodDependencia = valor[3]; 
	   
	   if(modulo == 'incorpporacion_bienes'){
		  window.open('af_actaincorporacion.php?nroActaIncorp='+NroActa+'&CodOrganismo='+CodOrganismo+"&Anio="+Anio,'','height=500, width=870, left=200, top=100, resizable=yes');
	   }else 
	    if(modulo == 'entrega_bienes'){
			
			var valor = codigo.split('-');
			var NroActa = valor[0];
			var CodOrganismo = valor[1];
			var Anio = valor[2];
			var TipoActa = valor[3]; 			
			
		  if(TipoActa=='AA'){	
		    window.open('af_actaentregabm.php?nroActaEntrega='+NroActa+'&CodOrganismo='+CodOrganismo+"&Anio="+Anio,'','height=500, width=870, left=200, top=100, resizable=yes');
		  }else
		    if(TipoActa=='AE'){
				var Pase = 'P';
				window.open('af_actaentregabmmovimiento.php?nroActaEntrega='+NroActa+'&CodOrganismo='+CodOrganismo+"&Anio="+Anio+"&TipoActa="+TipoActa+'&Pase='+Pase,'','height=500, width=870, left=200, top=100, resizable=yes');}
		  
		}else
		 if(modulo == 'desincorporacion_bienes'){
		  window.open('af_actabajabm.php?NroActa='+NroActa+'&CodOrganismo='+CodOrganismo+"&Anio="+Anio,'','height=500, width=870, left=200, top=100, resizable=yes');
		}else
		 window.open('af_actaresponusopdf.php?NroActa='+NroActa+'&CodOrganismo='+CodOrganismo+"&Anio="+Anio+"&CodDependencia="+CodDependencia,'','height=500, width=870, left=200, top=100, resizable=yes');
	}
 return false;		
}
//// ---------------------------------------------------------------------------------------------
///              ACTIVANDO CAMPOS :  
//// ---------------------------------------------------------------------------------------------
function enabledFechaDesdeHasta(form){
	if (form.chkFecha.checked) {form.fdesde.disabled=false; form.fhasta.disabled=false;} 
	else{ form.fdesde.disabled=true; form.fhasta.disabled=true; form.fhasta.value=""; form.fdesde.value="";} 
}
function enabledAnio(form){
	if (form.chkAnio.checked){form.fanio.disabled=false;} 
	else{ form.fanio.disabled=true; form.fanio.value="";} 
}
//// ---------------------------------------------------------------------------------------------
////     	             MOSTRAR REPORTE INVENTARIO X DEPENDENCIA
//// ---------------------------------------------------------------------------------------------
function cargarInventxDepUsuarioResp(form){
 
	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.chkCatClasf.checked) filtro+=" and a.Clasificacion LIKE *"+form.fClasificacion.value+"%"+"*";
	if(form.checkUbicacion.checked) filtro+=" and a.Ubicacion LIKE *"+form.fubicacion.value+"%"+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*";
	if(form.chkclasificacionpub20.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fCodclasficacionPub20.value+"%"+"*";
	if(form.checkSituacionActivo.checked) filtro+=" and a.SituacionActivo=*"+form.fSituacionActivo.value+"*";
	if(form.chekcCosto.checked) filtro+=" and a.CentroCosto=*"+form.centro_costos.value+"*";
	if(form.chkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkBienes.checked) filtro+=" and a.ClasificacionPublic20 LIKE *"+form.fBienes.value+"%"+"*";
	
    var pagina_mostrar="af_rpinventxdepusuarioresppdf.php?filtro="+filtro;
        form.target = "af_rpinventxdepusuarioresppdf";				
				cargarPagina(form, pagina_mostrar);
}
/// ---------------------------------------------------- #### 08-04-2015 Comienzo de Modificaciones
/// funcion para seleccionar de una lista un registro y colocar su valor en la ventana que lo llamo
function selListadoOpcion(codvalor, nomvalor, campo, cod, nom ) {
	if(campo==1){
	   parent.$("#"+cod).val(codvalor);
	   parent.$("#"+nom).val(nomvalor);
	}else 
	 if(campo==2){
	   opener.document.getElementById("cod").value = "codvalor"; 
	   opener.document.getElementById("nom").value = "nomvalor";
	 }
	
	parent.$.prettyPhoto.close();
}
/// ---------------------------------------------------- ####
/// Mostrar detalle de movimiento seleccionado
function MostrarDetalleMovimiento(form, valor) { 
	 
	var controlador= document.getElementById('controlador').value; 
	var ultimoTR= document.getElementById("ultimoTR").value; 
	var cantidad= document.getElementById('cant_veces').value;
	
	var candetalle = document.getElementById("can_detalle").value; candetalle++;
	document.getElementById('controlador').value= valor;
    document.getElementById("ultimoTR").value= 'det_'+candetalle;

	$.ajax({
		
		type: "POST",
		url: "gmactivofijo.php",
		data: "accion=MostrarDetalleMovimiento&candetalle="+candetalle+"&valor="+valor,
		async: false,
		success: function(datos){ 
		       
		  var veces = datos.split('|'); 
		  var datosTR = veces[0];
		  var datosCont = veces[1];
		  $('#can_detalle').val(candetalle);
		  $('#listaDetalles').append(datosTR); 
		  $('#cant_veces').val(datosCont); 
			
		}
	});
	
	if(controlador!=""){ 
	  var i=0;

       for(i=0;i<cantidad;i++ ){ 
		   var listaDetalles= document.getElementById("listaDetalles");
		   var tr= document.getElementById(ultimoTR); 
		   listaDetalles.removeChild(tr);
		   
	   }
     }
}
/// ---------------------------------------------------- ####
/// Quitar linea de movimiento seleccionados
/*function MostrarDetalleMovimiento(seldetalle, valor) {
	
	var controlador= document.getElementById('controlador').value; alert('controlador='+controlador);
	var ultimoTR= document.getElementById("ultimoTR").value; alert('ultimoTR='+ultimoTR);
	
	if(controlador!=""){
	    var listaDetalles = document.getElementById("listaDetalles");
		//var tr = document.getElementById(ultimoTR);
		listaDetalles.removeChild(ultimoTR);
		document.getElementById("seldetalle").value = "";
	
	}
	
	/*var listaDetalles = document.getElementById("listaDetalles");
	var tr = document.getElementById(valor);
	listaDetalles.removeChild(tr);
	document.getElementById("seldetalle").value = "";*/
/*}*/