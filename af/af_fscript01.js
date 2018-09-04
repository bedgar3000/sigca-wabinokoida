/// --------------------------------------------------------------------------------
/// FUNCION QUE PERMITE ACTIVAR TD EN CLASIFICACION ACTIVO 20 NUEVO
function activarVisible(form, idSelectOrigen, idSelectDestino){
    var nivel = document.getElementById("nivel").value; 
    var selectOrigen=document.getElementById(idSelectOrigen); 
	var optSelectOrigen=selectOrigen.options[selectOrigen.selectedIndex].value;
	var selectDestino=document.getElementById(idSelectDestino); 
	
   if(nivel>'1'){
	    document.getElementById('cod2').style.display = 'block';
		document.getElementById('cod1').style.display = 'none';
		
	if (optSelectOrigen=="") {
		selectDestino.length=0;
		nuevaOpcion=document.createElement("option");
		nuevaOpcion.value="";
		nuevaOpcion.innerHTML="";
		selectDestino.appendChild(nuevaOpcion);
		selectDestino.disabled=true;
	} else {
		//	CREO UN OBJETO AJAX PARA VERIFICAR QUE EL NUEVO REGISTRO NO EXISTA EN LA BASE DE DATOS
		var ajax=nuevoAjax();
		ajax.open("POST", "af_fphp.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("accion=activarVisible&tabla="+idSelectDestino+"&opcion="+optSelectOrigen+"&nivel="+nivel);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==1) {
				// Mientras carga elimino la opcion "" y pongo una que dice "Cargando..."
				selectDestino.length=0;
				var nuevaOpcion=document.createElement("option");
				nuevaOpcion.value="";
				nuevaOpcion.innerHTML="Cargando...";
				selectDestino.appendChild(nuevaOpcion);
				selectDestino.disabled=true;	
			}
			if (ajax.readyState==4)	{
				selectDestino.parentNode.innerHTML=ajax.responseText;
			}
		}
	}
		
   }else{
	    document.getElementById('cod2').style.display = 'none';
		document.getElementById('cod1').style.display = 'block';
	}
}
/// --------------------------------------------------------------------------------
/// -------------------		FUNCION CARGAR CLASIFICACION 20
/// --------------------------------------------------------------------------------
function cargarSeelctCodClasificacion20(form){
	var valor = document.getElementById("valorNivel").value; 
  var ajax=nuevoAjax();
	  ajax.open("POST", "af_fphp.php", true);
	  ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	  ajax.send("accion=cargarSeelctCodClasificacion20&valor="+valor);
	  ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText.trim();
			if (resp != "")alert(resp); 
            else cargarPagina(form, form.action);
		}
	 }
}
///	--------------------------------------------------------------------------------
///	-------------------	GUARDAR	TIPO MOVIMIENTO DE ACTIVOS
///	--------------------------------------------------------------------------------
function TipoMovimientoActivos(form, accion, Modulo) {  
	var codigo = document.getElementById("codigo").value;
	var descripcion = document.getElementById("descripcion").value;
	var t_movimiento = document.getElementById("t_movimiento").value;
	
	if (document.getElementById("activo").checked) var status = "A"; else var status = "I";
	if (document.getElementById("flag_movimiento").checked) var FlagMovimiento="S";
	if (document.getElementById("flag_incorporacion").checked) var FlagIncorporacion="S";
	if (document.getElementById("flag_desincorporacion").checked) var FlagDesincorporacion="S";
	
		
	if (descripcion == "" || descripcion.length<2) alert("¡DEBE LLENAR LOS CAMPOS OBLIGATORIOS!");
	else if (!valNumerico(codigo)) alert("¡El código debe ser númerico!");
	else if (!valAlfanumerico(descripcion)) alert("¡No se permiten caracteres especiales en el campo descripción!");
	else {
		//	CREO UN OBJETO AJAX PARA VERIFICAR QUE EL NUEVO REGISTRO NO EXISTA EN LA BASE DE DATOS
		
		var ajax=nuevoAjax();
		ajax.open("POST", "af_fphp_ajax.php", true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send("accion="+accion+"&codigo="+codigo+"&descripcion="+descripcion+"&t_movimiento="+t_movimiento+"&status="+status+"&Modulo="+Modulo+"&FlagMovimiento="+FlagMovimiento+"&FlagIncorporacion="+FlagIncorporacion+"&FlagDesincorporacion="+FlagDesincorporacion);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4)	{
				var resp = ajax.responseText.trim();
				if (resp != "") alert(resp);
				else cargarPagina(form, "af_tipomovimientoactivo.php");
			}
		}
	}
	return false;
}
///	--------------------------------------------------------------------------------
/// -------------------		Filtro lista de Activos "af_lisactivos"
///	--------------------------------------------------------------------------------
function enabledClasificacionPub20(form){ 
  if(form.chkclasificacionpub20.checked){ 
     form.fClasificacionPub20.disabled = false; 
	 form.btClasifPub20.disabled = false; document.getElementById("clasificacion20").style.visibility = 'visible';
  }else{ 
     form.fClasificacionPub20.disabled=true; 
	 form.fClasificacionPub20.value=''; 
	 form.fCodclasficacionPub20.value='';
	 form.btClasifPub20.disabled = true; document.getElementById("clasificacion20").style.visibility = 'hidden';
  }
}
///	--------------------------------------------------------------------------------
/// -------------------	FILTRO MOVIMIENTOS DE ACTIVOS "af_selectoractivos"
///	--------------------------------------------------------------------------------
function enabledCentroCostosSelectorActivos(form){
  if(form.checkCentroCosto.checked){ 
     form.btCentroCosto.disabled=false; form.fCentroCosto2.disabled= false;
  }else{
	 form.btCentroCosto.disabled=true; form.fCentroCosto.value=""; form.fCentroCosto2.value="";  form.fCentroCosto2.disabled=true;
  }
}
///	--------------------------------------------------------------------------------
function enabledPersonaSelecActivo(form){
  if(form.checkPersona.checked){
	  form.btpersona.disabled=false; form.NombPersona.disabled=false;
  }else{ 
     form.btpersona.disabled=true; form.fPersona.value=""; form.NombPersona.value=""; form.NombPersona.disabled=true;
  }
}
///	--------------------------------------------------------------------------------
function enabledUbicacionSelectorActivo(form){
  if(form.checkUbicacion.checked){
	  form.btUbicacion.disabled=false; form.DescpUbicacion.disabled=false;
  }else{
	  form.btUbicacion.disabled=true; form.DescpUbicacion.disabled=true; form.fUbicacion.value=""; form.DescpUbicacion.value="";  
  }
}
///	--------------------------------------------------------------------------------
function enabledNaturalezaSelectorActivo(form){
 if(form.checkNaturaleza.checked) form.fNaturaleza.disabled=false;
 else{form.fNaturaleza.disabled=true; form.fNaturaleza.value="";}
}
///	--------------------------------------------------------------------------------
function enabledConsolidadoSelectorActivo(form){
 if(form.checkConsolidado.checked){
	 form.fNomConsolidado.disabled=false; form.btConsolidado.disabled=false;
 }else{
    form.fNomConsolidado.disabled=true; form.btConsolidado.disabled=true; form.fConsolidado.value="";form.fNomConsolidado.value="";
 }
}
//// -------------------------------------------------------------------------------
//// -------------------	GUARDAR APROBAR MOVIMIENTO DE ACTIVOS
//// -------------------------------------------------------------------------------
function guardarAprobarMovimientoActivo(form){
 
 var Organismo = document.getElementById("fOrganismo").value;
 var PreparadoPor = document.getElementById("preparado_por").value; 
 var MovimientoNumero = document.getElementById("fmovimiento").value;
 var Anio = document.getElementById("anio").value;

 var Pase = 'P';
 	   
     var ajax=nuevoAjax();
	 ajax.open("POST", "gmactivofijo.php", true);
	 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
     ajax.send("accion=guardarAprobarMovimientoActivo&Organismo="+Organismo+"&MovimientoNumero="+MovimientoNumero+"&PreparadoPor="+PreparadoPor+"&Anio="+Anio);	 
     ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var resp = ajax.responseText.trim();
			if (resp != ""){ 
				var respuesta = resp.split('|');
			    if(respuesta[0]==1){alert("EL USUARIO QUE PREPARO EL MOVIMIENTO DEBE SER DISTINTO AL MOMENTO DE APROBAR"); window.close();}
	            if(respuesta[0]==2){alert("MOVIMIENTO APROBADO");
				  opener.document.getElementById("frmentrada").submit();
				  var registro = Organismo+"|"+resp;
				  window.open('af_rptransferenciaactivofijomovimiento.php?registro='+registro,'', 'height=500, width=870, left=200, top=100, resizable=yes');
			      window.open('af_actaentregabmmovimiento.php?CodOrganismo='+Organismo+'&Pase='+Pase+"&nroActaEntrega="+respuesta[1]+"&Anio="+respuesta[2]+"&MovimientoNumero="+respuesta[3]+"&TipoActa="+respuesta[4],'', 'height=500, width=870, left=200, top=100, resizable=yes');
				  window.close();
				}
			}
		}
	}
	 return false; 
}
//// -------------------------------------------------------------------------------
//// -------------------	CARGAR LINEA
//// -------------------------------------------------------------------------------
function enabledContabilidadBajaActivo(form){ 
  if(form.checkContabilidad.checked) form.fContabilidad.disabled = false;
  else{ form.fContabilidad.disabled=true; form.fContabilidad.value='';}
}
//// -------------------------------------------------------------------------------
function enabledActivo(form){
  if(form.checkActivo.checked) form.fActivo.disabled = false;
  else{ form.fActivo.disabled=true; form.fActivo.value='';}
}
//// -------------------------------------------------------------------------------
function enabledPeriodo(form){
  if(form.checkPeriodo.checked) form.fPeriodo.disabled= false;
  else{ form.fPeriodo.disabled=true; form.fPeriodo.value='';}
}
//// -------------------------------------------------------------------------------
function enabledFecha(form){
  if(form.checkFecha.checked) form.fFecha.disabled=false;
  else{ form.fFecha.disabled=true; form.fFecha.value='';}
}
//// -------------------------------------------------------------------------------
function enabledBienes(form){
  if (form.chkBienes.checked) form.fBienes.disabled=false;
  else{ form.fBienes.disabled=true; form.fBienes.value='';} 
}
//// -------------------------------------------------------------------------------
//// -------------------	ANULAR REGISTRO DE TRANSACCION DE ACTIVOS
//// -------------------------------------------------------------------------------
function anularRegistro(form, pagina,accion){
 var codigo=form.registro.value;
	if (codigo=="") msjError(1000);
	else{
	   var anular=confirm("¡Esta seguro de anular este permiso?");
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
//// -------------------------------------------------------------------------------
function enabledRPNaturaleza(form){
 if(form.chkNaturaleza.checked) form.fNaturaleza.disabled = false; 
 else{ form.fNaturaleza.disabled= true; form.fNaturaleza.value='';}
}
//// -------------------------------------------------------------------------------
function enabledRPActivo(form){
 if(form.chkActivo.checked){ 
     form.fActivo.disabled = false; 
	 form.fDescpActivo.disabled = false; 
	 document.getElementById("activo").style.visibility='visible';}
 else{ 
 	form.fActivo.disabled= true; 
	form.fDescpActivo.disabled = true; 
	form.fActivo.value=''; 
	form.fDescpActivo.value=''; 
	document.getElementById("activo").style.visibility='hidden';
	}
}
//// -------------------------------------------------------------------------------
function enabledRPFechaAprobacion(form){
 if(form.chkFAprobacion.checked){form.fFechaAprobacionDesde.disabled=false; form.fFechaAprobacionHasta.disabled=false;}
 else{form.fFechaAprobacionDesde.disabled=true; form.fFechaAprobacionHasta.disabled=true; form.fFechaAprobacionHasta.value=''; form.fFechaAprobacionDesde.value='';}
}
//// -------------------------------------------------------------------------------
function enabledRPFechaPreparacion(form){
 if(form.chkFPreparacion.checked){form.fFechaPreparacionDesde.disabled=false; form.fFechaPreparacionHasta.disabled=false;}
 else{form.fFechaPreparacionDesde.disabled=true; form.fFechaPreparacionHasta.disabled=true; form.fFechaPreparacionHasta.value=''; form.fFechaPreparacionDesde.value='';}	
}
//// -------------------------------------------------------------------------------
function enabledRPFechaFacturacion(form){
 if(form.chkFactura.checked){form.fFechaFacturaDesde.disabled=false; form.fFechaFacturaHasta.disabled=false;}
 else{form.fFechaFacturaDesde.disabled=true; form.fFechaFacturaHasta.disabled=true; form.fFechaFacturaHasta.value=''; form.fFechaFacturaDesde.value='';}	
}
//// -------------------------------------------------------------------------------
function enabledRPUbicacionActual(form){
if(form.chkubicacionActual.checked){ 
   form.fub_actual_descp.disabled=false; 
   form.y_o.disabled=false; 
}else{
   form.fub_actual_descp.disabled=true; 
   form.y_o.disabled=true;
   form.fub_actual.value='';
   form.y_o.value='';
   form.fub_actual_descp.value='';
 }
}
//// -------------------------------------------------------------------------------
//// -------------------------------------------------------------------------------
function enabledRPUbicacionAnterior(form){
 if(form.chkubicacionAnterior.checked){ 
   form.fub_anterior_descp.disabled=false; 
   form.y_o.disabled=false; 
 }else{
   form.fub_anterior_descp.disabled=true;
   form.y_o.disabled=true;
   form.fub_anterior.value='';
   form.y_o.value='';
   form.fub_anterior_descp.value=''; 
 }
}
//// -------------------------------------------------------------------------------
//// -------------------------------------------------------------------------------
function valor_asignado(id){
 if(id=='fub_actual_descp') document.getElementById("asignado").value = '29';
 else document.getElementById("asignado").value = '30';
}
//// -------------------------------------------------------------------------------
//// FUNCION QUE PERMITE ACTIVAR SELECTOR JQUERY UTILIZANDO VALOR DE CAMPOS
//// -------------------------------------------------------------------------------
function rpmovlista() {
	var asignado = $("#asignado").val();
	var href = "af_listaubicacionesactivo.php?filtrar=default&limit=0&campo="+asignado+"&iframe=true&width=80%&height=100%"; 
	$("#a_lista").attr("href", href);
	document.getElementById("a_lista").click();
}
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE MOVIMIENTOS ACTIVOS
//// ---------------------------------------------------------------------------------------------
function CargarRPMovimientosActivo(form){

	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and Organismo=*"+form.forganismo.value+"*";
	if(form.chkActivo.checked) filtro+=" and Activo=*"+form.fActivo.value+"*";
	if(form.checkDependencia.checked) filtro+=" and Dependencia=*"+form.fDependencia.value+"*";
	if(form.chkFAprobacion.checked){ 
	   filtro+=" and FechaAprobacion>=*"+form.fFechaAprobacionDesde.value+"*";
	   filtro+=" and FechaAprobacion<=*"+form.fFechaAprobacionHasta.value+"*";
	}
	if(form.checkcCosto.checked) filtro+=" and CentroCosto=*"+form.centro_costos.value+"*";
	if(form.checkcCosto.checked) filtro+=" and CentroCosto=*"+form.centro_costos.value+"*";
	if(form.chkFPreparacion.checked){ 
	   filtro+=" and FechaPreparacion>=*"+form.fFechaPreparacionDesde.value+"*";
	   filtro+=" and FechaPreparacion<=*"+form.fFechaPreparacionHasta.value+"*";
	}
	if(form.chkubicacionActual.checked) filtro+=" and Ubicacion=*"+form.fub_actual.value+"*";
	if(form.chkubicacionAnterior.checked) filtro+=" and UbicacionAnterior=*"+form.fub_anterior.value+"*";
	
    var pagina_mostrar_1="af_rptabmovimientoactivospdf.php?filtro="+filtro;
	var pagina_mostrar_2="af_rptabmovimientootroformatopdf.php?filtro="+filtro;
        form.target = "af_rptabmovimientos";				
				cargarPagina(form, pagina_mostrar_1);
				cargarPagina(form, pagina_mostrar_2);
}
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE MOVIMIENTOS ACTIVOS
//// ---------------------------------------------------------------------------------------------
function af_rptabmovimiento_activos_movimientos(form,tab){
   var filtro="";
	
	var form = document.getElementById("frmentrada");
	if (tab == "movimiento") form.action= "af_rptabmovimientoactivospdf.php";
	else if(tab == "movimiento") form.action= "af_rptabmovimientoactivosotroformatopdf.php";	
	else form.action = "af_rptabmovimientoactivosotroformato2pdf.php";	
	form.submit();
}
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE INGRESO DE ACTIVOS
//// ---------------------------------------------------------------------------------------------
function af_rptab_ingreso_activos(form,tab){
    
	var form = document.getElementById("frmentrada");
	if(tab=="activo_activado") form.action = "af_rptabactivoactivadopdf.php";
	else if(tab=="no_asignado_pend_activar") form.action="af_rptabactivonoasignadospdf.php";
	else  form.action = "af_rptabactivosnoasignadosrecepcionpdf.php"; 	
	form.submit();
}
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE CATALOGO ACTIVOS
//// ---------------------------------------------------------------------------------------------
function af_rptabcatalogo_activos(form,tab){
   var filtro="";
		
	var form = document.getElementById("frmentrada");
	if (tab == "catalogo") form.action = "af_rptabcatalogoactivospdf.php";
	else form.action = "af_rptabcatalogoactivosotroformatopdf.php";	
	form.submit();
}
//// ---------------------------------------------------------------------------------------------
//// ----------		ACTIVAR Y DESACTIVAR CAMPOS
//// ---------------------------------------------------------------------------------------------
function enabledRPTABSituacion(form){
if(form.chksituacion.checked) form.fsituacion.disabled = false; 
else{ form.fsituacion.disabled=true; form.fsituacion.value='';} 
}
//// ----------		ACTIVAR Y DESACTIVAR CAMPOS
function enabledRPTABRango(form){
if(form.chkRango.checked){ 
  form.frango_desde.disabled=false;
  form.frango_hasta.disabled=false;
}else{
  form.frango_desde.disabled=true;
  form.frango_hasta.disabled=true;
  form.frango_desde.value='';
  form.frango_hasta.value='';
}
}
//// --------  ACTIVAR Y DESACTIVAR CAMPOS
function enabledContabilidad(form){
if(form.chkContabilidad.checked) form.fContabilidad.disabled=false;
else{ form.fContabilidad.disabled=false; form.fContabilidad.value='';}
}
//// --------  ACTIVAR Y DESACTIVAR CAMPOS
function enabledPeriodos(form){
if(form.chkPeriodo.checked){ form.fperiodo_desde.disabled=false; form.fperiodo_hasta.disabled=false;}
else{ form.fperiodo_desde.disabled=true; form.fperiodo_hasta.disabled=true; form.fperiodo_desde.value=''; form.fperiodo_hasta.value='';}
}
//// --------  ACTIVAR Y DESACTIVAR CAMPOS
function enabledVoucher(form){
if(form.chkVoucher.checked){ form.fvoucher_desde.disabled=false; form.fvoucher_hasta.disabled=false;}
else{ form.fvoucher_desde.disabled=true; form.fvoucher_hasta.disabled=true; form.fvoucher_desde.value=''; form.fvoucher_hasta.value='';}
}
//// --------  ACTIVAR Y DESACTIVAR CAMPOS
function enabledCuenta(form){
if(form.chkCuenta.checked){ form.fcuenta.disabled=false; form.fcuenta_descp.disabled=false; document.getElementById("cuenta").style.visibility='visible';}
else{ form.fcuenta.disabled=true; form.fcuenta_descp.disabled=true; form.fcuenta.value=''; form.fcuenta_descp.value=''; document.getElementById("cuenta").style.visibility='hidden';}
}
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE CATALOGO ACTIVOS
//// ---------------------------------------------------------------------------------------------
function af_rptabingresoegreso_activos(form,tab){
   var filtro="";
		
	var form = document.getElementById("frmentrada");
	if (tab == "adiciones_periodo") form.action = "af_rptabingresoegresoadicionesperiodopdf.php";
	else if(tab == "transacciones_baja") form.action = "af_rptabingresoegresotransaccionesbajapdf.php";
	else if(tab == "adiciones_x_voucher") form.action = "af_rptabingresoegresoadicionesxvoucherpdf.php"; 
	else form.action = "af_rptabingresoegresoadicionesanualespdf.php";	
	form.submit();
}
//// ---------------------------------------------------------------------------------------------
//// -------- ACTIVAR Y DESACTIVAR CAMPOS
function enabledResponsable(form){
 if(form.chkResponsable.checked){form.empleado_responsable.disabled=false;	document.getElementById("responsable").style.visibility='visible';}
 else{form.empleado_responsable.disabled=true;	form.empleado_responsable.value=''; form.cod_empresponsable.value=''; 
      document.getElementById("responsable").style.visibility='hidden';}
}    
//// ---------------------------------------------------------------------------------------------
//// ----------		MOSTRAR REPORTE INVENTARIO X DEPENDENCIA
//// ---------------------------------------------------------------------------------------------
function cargarActivosAsignadosPersona(form){
 
	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and a.CodOrganismo=*"+form.forganismo.value+"*";
	if(form.checkDependencia.checked) filtro+=" and a.CodDependencia=*"+form.fDependencia.value+"*";
	if(form.checkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkNaturaleza.checked) filtro+=" and a.Naturaleza=*"+form.fNaturaleza.value+"*";
	if(form.chkResponsable.checked) filtro+=" and a.EmpleadoUsuario=*"+form.cod_empresponsable.value+"*";
	if(form.chkPeriodo.checked){ 
	  filtro+=" and a.PeriodoIngreso>=*"+form.fperiodo_desde.value+"*";
	  filtro+=" and a.PeriodoIngreso<=*"+form.fperiodo_hasta.value+"*";
	}
		
    var pagina_mostrar="af_rptabactivosxempleadopdf.php?filtro="+filtro;
        form.target = "af_rptabactivosxempleadopdf";				
				cargarPagina(form, pagina_mostrar);
}
//// -----------------------------------------------------------------------------------------------
////      FUNCION QUE PERMITE GENERAR VOUCHER INGRESO ACTIVO
//// -----------------------------------------------------------------------------------------------
function generarVoucherIngActivo(form, valor){
	
   var activo = document.getElementById("activo").value;
   var codorganismo = document.getElementById("codorganismo").value;
   var CodVoucher = document.getElementById("CodVoucher").value; 
   var periodoingreso = document.getElementById("periodoingreso").value;
   var periodo = document.getElementById("periodo").value; 
   var registro = document.getElementById("registro").value; 
   var ContActivo = document.getElementById("ContActivo").value;
   var CodPersona = document.getElementById("CodPersona").value;
   var dependencia = document.getElementById("dependencia").value;
   var ComentariosVoucher = document.getElementById("ComentariosVoucher").value;
   var CodContabilidad = document.getElementById("Contabilidad").value; 
   var CodLibroCont = document.getElementById("CodLibroCont").value;
   
   var FechaIngreso = document.getElementById('FechaIngreso').value;
   var CentroCosto = document.getElementById("CentroCosto").value;
   var ObligacionTipoDocumento = document.getElementById("ObligacionTipoDocumento").value;
   var ObligacionNroDocumento  = document.getElementById("ObligacionNroDocumento").value;
   var FacturaFecha = document.getElementById("facturafecha").value;
   var FechaVoucher = document.getElementById("FechaVoucher").value;
   var dep_usuario = document.getElementById("dep_usuario").value;
   
  
   if(registro=="") msjError(1000);
   else{ 
     var ajax=nuevoAjax();
	 ajax.open("POST","gmactivofijo.php", true);
	 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	 ajax.send("accion=generarVoucherIngActivo&Activo="+activo+'&CodOrganismo='+codorganismo+'&CodVoucher='+CodVoucher+'&PeriodoIngreso='+periodoingreso+'&ContActivo='+ContActivo+'&CodPersona='+CodPersona+'&FechaIngreso='+FechaIngreso+'&CentroCosto='+CentroCosto+'&ObligacionTipoDocumento='+ObligacionTipoDocumento+'&ObligacionNroDocumento='+ObligacionNroDocumento+'&dependencia='+dependencia+"&ComentariosVoucher="+ComentariosVoucher+"&Periodo="+periodo+"&CodContabilidad="+CodContabilidad+"&CodLibroCont="+CodLibroCont+'&FacturaFecha='+FacturaFecha+'&FechaVoucher='+FechaVoucher+'&DependenciaUsuario='+dep_usuario);
	 ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var error=ajax.responseText;
			if (error!=0) alert(error); //alert ("¡"+error+"!");
			else{ 
			opener.document.getElementById("frmentrada").submit();
			
			 //window.open('af_voucher_pdf.php?activo='+activo+'&codorganismo='+codorganismo+'&CodVoucher='+CodVoucher+'&periodoingreso='+periodoingreso,'', 'height=500, width=870, left=200, top=100, resizable=yes'); 
			 
			 
			 
			//cargarPagina(form, "af_voucher_pdf.php?activo="+activo+"&codorganismo="+codorganismo+"&CodVoucher="+CodVoucher+"&periodoingreso="+periodoingreso);
			  //window.close();
			}
		}
      }
   }
   alert("PROCESO EXITOSO");
   window.close();
   return false;
}
//// -----------------------------------------------------------------------------------------------
////      FUNCION QUE PERMITE GENERAR VOUCHER BAJA PUB 20
//// -----------------------------------------------------------------------------------------------
function generarVoucherBaja(form, valor){ 
   
   var CodTransaccionBaja = document.getElementById("codtransaccionbaja").value; 
   var Activo = document.getElementById("activo").value;
   var CodOrganismo =document.getElementById("CodOrganismo").value;
   var CodVoucher = document.getElementById("CodVoucher").value;
   var CodContabilidad = document.getElementById("Contabilidad").value; 
   var Periodo = document.getElementById("Periodo").value;
   var CodLibroCont = document.getElementById("CodLibroCont").value;
   var CentroCosto = document.getElementById("centro_costo").value;
   var RefNroDocumento = document.getElementById("Documento").value;
   	  
   var ajax=nuevoAjax();
	 ajax.open("POST","gmactivofijo.php", true);
	 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	 ajax.send("accion="+valor+"&Activo="+Activo+"&CodTransaccionBaja="+CodTransaccionBaja+"&CodVoucher="+CodVoucher+"&CodContabilidad="+CodContabilidad+
	 	       "&Periodo="+Periodo+"&CodOrganismo="+CodOrganismo+"&CodLibroCont="+CodLibroCont+"&CentroCosto="+CentroCosto+"&RefNroDocumento="+RefNroDocumento);
	 ajax.onreadystatechange=function() {
		if (ajax.readyState==4)	{
			var error=ajax.responseText;
			if (error!=0) alert ("¡"+error+"!");
			else{ 
			  cargarPagina(form, "af_voucher_pdf.php?Activo="+Activo+"&CodOrganismo="+CodOrganismo+"&CodVoucher="+CodVoucher+"&Periodo="+Periodo);
			  //window.close();
			}
		}
      }
   return false;
}
//// -------------------------------------------------------------------------------------
////  				PERMITE INSERTAR VARIOS ACTIVOS PARA MOVIMIENTO
//// -------------------------------------------------------------------------------------
function insertarActivoMovimiento(valor) {
	 
	 if(valor>=1){
	    var candetalle = parseInt(valor) + 1;
	 }else{
	 	var candetalle = document.getElementById("can_detalle").value; candetalle++; 
	 }
	 
	$.ajax({
		
		type: "POST",
		url: "gmactivofijo.php",
		data: "accion=insertarActivoMovimiento&candetalle="+candetalle,
		async: false,
		success: function(datos) { 
		    $('#can_detalle').val(candetalle);
			$('#listaDetalles').append(datos);
		}
	});
}
////  ------------------------------------------------------------------------------------
////               					 SELECCIONADOR GENERAL
////  ------------------------------------------------------------------------------------
function selCentralActivoFijo(busqueda, campo, variable, otro1, otro2, otro3, otro4,otro5,otro6,otro7,otro8,otro9,otro10,otro11,otro12,otro13) {
  var registro=document.getElementById("registro").value;
  var seldetalle = document.getElementById("sel_detalle").value; 
  
  if(campo==1){
	  	
	  opener.document.getElementById("c_costosActual_"+seldetalle).value = registro;
	  opener.document.getElementById("c_costosActual2_"+seldetalle).value = variable; 
  }else
  if(campo==2){
	  	
	  opener.document.getElementById("ubicacion_Actual_"+seldetalle).value= registro;
	  opener.document.getElementById("ubicacion_Actual2_"+seldetalle).value= variable;
  }else
  if(campo==3){
	  	
	  opener.document.getElementById("dependenciaActual_"+seldetalle).value = registro ;
	  opener.document.getElementById("dependenciaActual2_"+seldetalle).value = variable;
  }else
  if(campo==4){
	  	
	  opener.document.getElementById("e_usuarioActual_"+seldetalle).value=registro;
	  opener.document.getElementById("e_usuarioActual2_"+seldetalle).value=variable;
  }else
  if(campo==5){
  
      opener.document.getElementById("e_responsableActual_"+seldetalle).value = registro ;
	  opener.document.getElementById("e_responsableActual2_"+seldetalle).value = variable;
  }else
  if(campo==6){
        opener.document.getElementById("activo_"+seldetalle).value=registro;
		opener.document.getElementById("descripcion_"+seldetalle).value=variable;
		opener.document.getElementById("cod_bar_"+seldetalle).value=otro1; 
		opener.document.getElementById("c_costos_"+seldetalle).value=otro2; 
		opener.document.getElementById("c_costosActual_"+seldetalle).value=otro2; 
		opener.document.getElementById("c_costos2_"+seldetalle).value=otro3; 
		opener.document.getElementById("c_costosActual2_"+seldetalle).value=otro3;
		
		opener.document.getElementById("ubicacion_"+seldetalle).value=otro4; 
		opener.document.getElementById("ubicacion_Actual_"+seldetalle).value= otro4;
		opener.document.getElementById("ubicacion2_"+seldetalle).value=otro5;
		opener.document.getElementById("ubicacion_Actual2_"+seldetalle).value= otro5;
		
		opener.document.getElementById("dependencia_"+seldetalle).value=otro6; 
		opener.document.getElementById("dependenciaActual_"+seldetalle).value=otro6;
		opener.document.getElementById("dependencia2_"+seldetalle).value=otro7;
		opener.document.getElementById("dependenciaActual2_"+seldetalle).value=otro7;
		
		opener.document.getElementById("e_usuario_"+seldetalle).value=otro8; 
		opener.document.getElementById("e_usuario2_"+seldetalle).value = otro9;
		opener.document.getElementById("e_usuarioActual_"+seldetalle).value = otro8;
		opener.document.getElementById("e_usuarioActual2_"+seldetalle).value= otro9;
		
		opener.document.getElementById("e_responsable_"+seldetalle).value=otro10; 
		opener.document.getElementById("e_responsable2_"+seldetalle).value = otro11;
		opener.document.getElementById("e_responsableActual_"+seldetalle).value = otro10;
		opener.document.getElementById("e_responsableActual2_"+seldetalle).value= otro11;
		
		opener.document.getElementById("organismo_"+seldetalle).value= otro12;
		opener.document.getElementById("organismo2_"+seldetalle).value= otro13;
		opener.document.getElementById("organismoActual_"+seldetalle).value= otro12;
		opener.document.getElementById("organismoActual2_"+seldetalle).value= otro13;
		opener.document.getElementById("valorguardar").value = 1;
  }else
  if(campo==7){
       parent.document.frmentrada.fusuario.value=registro;
	   parent.document.frmentrada.fnombusuario.value=variable;
  }else
  if(campo==8){
       parent.document.frmentrada.fprimario.value=registro;
	   parent.document.frmentrada.fnombprimario.value=variable;
  }
  
  if(campo>=1 && campo<=6)window.close();
  else parent.$.prettyPhoto.close();
}
//// -------------------------------------------------------------------------------------
////  						     GUARDAR NUEVO MOVIMIENTO DE ACTIVO
//// -------------------------------------------------------------------------------------
function guardarNuevoMovimientoActivo(form){
 if(document.getElementById("valorguardar").value==1){ 
   if(document.getElementById("motivoTrasladoInterno").value!="") var MotivoTraslado = document.getElementById("motivoTrasladoInterno").value; 
   else var MotivoTraslado = document.getElementById("motivoTrasladoExterno").value; 
   
   var Organismo = document.getElementById("CodOrganismo").value;
   var PreparadoPor = document.getElementById("preparado_por").value;
   var Comentario = document.getElementById("comentario").value;
   var InternoExternoFlag = document.getElementById("radioEstado").value;
   //var CodTipoMoviDes = document.getElementById("mot_trasl_des").value;
   //var CodTipoMoviInc = document.getElementById("mot_trasl_incorp").value;
   var FechaPreparacion = document.getElementById("fecha_prepa").value;
   
  var detalles = "";
  var error_detalles = "";
	
  // obtengo los valoes de las lineas insertadas
  var frmdetalles = document.getElementById("frmentrada");
  for(i=0; n=frmdetalles.elements[i]; i++) {
	 	
		if (n.name == "activo" && n.value=="") { error_detalles = "¡Debe Insertar por lo menos un Activo¡"; break; }
		else if (n.name == "activo") detalles += n.value + "|";
		else if (n.name == "organismo") detalles += n.value + "|";
		else if (n.name == "organismoActual") detalles += n.value + "|";
		else if (n.name == "c_costosActual") detalles += n.value + "|";
		else if (n.name == "c_costos") detalles += n.value + "|";
		else if (n.name == "ubicacion_Actual") detalles += n.value + "|";
		else if (n.name == "ubicacion") detalles += n.value + "|";
		else if (n.name == "dependenciaActual") detalles += n.value + "|";
		else if (n.name == "dependencia") detalles += n.value + "|";
		else if (n.name == "e_responsableActual") detalles += n.value + "|";
		else if (n.name == "e_responsable") detalles += n.value + "|";
		else if (n.name == "e_usuarioActual") detalles += n.value + "|";
		else if (n.name == "e_usuario") detalles += n.value + "|";
		else if (n.name == "mot_trasl_des") detalles += n.value + "|";
		else if (n.name == "mot_trasl_incorp") detalles += n.value + ";";
  }
   var len = detalles.length; len--;
   detalles = detalles.substr(0, len); 

   if (error_detalles != "") alert(error_detalles);
   else { 
     $.ajax({
	 
	    type: "post",
		url: "gmactivofijo.php",
		data: "accion=guardarNuevoMovimientoActivo&detalles="+detalles+"&MotivoTraslado="+MotivoTraslado+"&PreparadoPor="+PreparadoPor+"&Comentario="+Comentario+"&InternoExternoFlag="+InternoExternoFlag+"&Organismo="+Organismo+"&FechaPreparacion="+FechaPreparacion,
		async: false,
		succes: function(resp){
		     alert(resp);
			 //$('#numeroMovimientoGenerado').val(resp) 
		}
	 });
	 
	 } 
 }
  //return false;
 }
//// -------------------------------------------------------------------------------------
////					  QUITAR LINEA  NUEVO MOVIMIENTO DE ACTIVO
//// -------------------------------------------------------------------------------------
function quitarLineaActivoMovimiento(seldetalle, valor) {
	var listaDetalles = document.getElementById("listaDetalles");
	var tr = document.getElementById(valor);
	listaDetalles.removeChild(tr);
	document.getElementById("seldetalle").value = "";
}
//// -------------------------------------------------------------------------------------
//// 						GUARDAR EDITAR MOVIMIENTO DE ACTIVO
//// -------------------------------------------------------------------------------------
function guardarEditarMovimientoActivo(form){		
   
   var editar=confirm("¡Esta seguro de guardar  los cambios realizados a este Movimiento de Activos¡");
   
   if (editar== true) {
   
	   var FechaPreparacion = document.getElementById("fecha_prepa").value;
	   
	   if(document.getElementById("motivoTrasladoInterno").value!=""){ 
		  var MotivoTraslado = document.getElementById("motivoTrasladoInterno").value;
	   }else{ 
		  var MotivoTraslado = document.getElementById("motivoTrasladoExterno").value;
	   }
	   
	   var MovimientoNumero = document.getElementById("fmovimiento").value; 
	   var Organismo = document.getElementById("CodOrganismo").value;
	   var InternoExternoFlag = document.getElementById("radioEstado").value;
	   var Comentario = document.getElementById("comentario").value;
	   var Anio = document.getElementById("anio").value;
	   
	  var detalles = "";
	  var error_detalles = "";
		
	  // obtengo los valoes de las lineas insertadas
	  var frmdetalles = document.getElementById("frmentrada");
	  for(i=0; n=frmdetalles.elements[i]; i++) {
			
			if (n.name == "activo" && n.value=="") { error_detalles = "¡Debe Insertar por lo menos un Activo¡"; break; }
			else if (n.name == "activo") detalles += n.value + "|";
			else if (n.name == "organismo") detalles += n.value + "|";
			else if (n.name == "organismoActual") detalles += n.value + "|";
			else if (n.name == "c_costosActual") detalles += n.value + "|";
			else if (n.name == "c_costos") detalles += n.value + "|";
			else if (n.name == "ubicacion_Actual") detalles += n.value + "|";
			else if (n.name == "ubicacion") detalles += n.value + "|";
			else if (n.name == "dependenciaActual") detalles += n.value + "|";
			else if (n.name == "dependencia") detalles += n.value + "|";
			else if (n.name == "e_responsableActual") detalles += n.value + "|";
			else if (n.name == "e_responsable") detalles += n.value + "|";
			else if (n.name == "e_usuarioActual") detalles += n.value + "|";
			else if (n.name == "e_usuario") detalles += n.value + "|";
			else if (n.name == "mot_trasl_des") detalles += n.value + "|";
			else if (n.name == "mot_trasl_incorp") detalles += n.value + ";";
	  }
	   var len = detalles.length; len--;
	   detalles = detalles.substr(0, len);
	   if (error_detalles != "") alert(error_detalles);
	   else { 
		 $.ajax({
			type: "post",
			url: "gmactivofijo.php",
			data: "accion=guardarEditarMovimientoActivo&detalles="+detalles+"&MotivoTraslado="+MotivoTraslado+"&Comentario="+Comentario+
			      "&InternoExternoFlag="+InternoExternoFlag+"&Organismo="+Organismo+"&FechaPreparacion="+FechaPreparacion+
			      "&MovimientoNumero="+MovimientoNumero+"&Anio="+Anio,
			async: false,
			succes: function(resp){
				 alert(resp);
				 //$('#numeroMovimientoGenerado').val(resp) 
			}
		 });
		 
		 } 
   }else return false;
}
//// -------------------------------------------------------------------------------------
function enabledPeriodoFormBM2(form){
  if(form.chkPeriodo.checked) form.fPeriodo.disabled= false;
  else{ form.fPeriodo.disabled=true; form.fPeriodo.value='';}
}
function enabledTipoMov(form){
  if(form.chkTipoMov.checked) form.fTipoMov.disabled = false;
  else{ form.fTipoMov.disabled = true; form.fTipoMov.value='';}
}
//// -------------------------------------------------------------------------------------
function enabledEstadoConservacion(form){
  if(form.checkEstadoConserv.checked) form.fEstadoConservacion.disabled=false;
  else{ form.fEstadoConservacion.disabled=true; form.fEstadoConservacion.value="";}
}
/// -------------------------------------------------------------- ####
/// Activar campo reporte de lista de activos
function enabledTipoActa(form){
  if(form.chktipoacta.checked) form.ftipoacta.disabled= false;
  else{ form.ftipoacta.disabled=true; form.ftipoacta.value="";}
}
/// -------------------------------------------------------------- ####
/// Cargar listado de actas
function cargarListadoActas(form){
 
	var filtro="";
	if(form.chkorganismo.checked) filtro+=" and CodOrganismo=*"+form.forganismo.value+"*";
	//if(form.chkEstado.checked) filtro+=" and a.Estado=*"+form.fEstado.value+"*";
	if(form.chkfecha.checked){ 
	   
	   var fecha_desde = document.getElementById("fecha_desde").value;
	       fecha_desde = fecha_desde.split('-');
		   fecha_desde = fecha_desde[2]+"-"+fecha_desde[1]+"-"+fecha_desde[0];
	
	   var fecha_hasta = document.getElementById("fecha_hasta").value;
	       fecha_hasta = fecha_hasta.split('-');
       var fecha_hasta_mostrar = fecha_hasta[0]+"/"+fecha_hasta[1]+"/"+fecha_hasta[2]; 
		   fecha_hasta = fecha_hasta[2]+"-"+fecha_hasta[1]+"-"+fecha_hasta[0]; 
	   
	   filtro+=" and FechaActa>=*"+fecha_desde+"*";
	   filtro+=" and FechaActa<=*"+fecha_hasta+"*";
	   
	   
	}

	if(form.chktipoacta.checked) var tipoActa= $("#ftipoacta").val();
	
    var pagina_mostrar="af_rplistadoactaspdf.php?filtro="+filtro+"&tipoActa="+tipoActa;
        form.target = "af_rplistadoactaspdf";				
				cargarPagina(form, pagina_mostrar);
}