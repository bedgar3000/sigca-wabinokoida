<?php
// ------------------------------------- ####
include("../lib/fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
//	------------------------------------
include ("fphp.php");
connect();
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('02', $concepto);
//	------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link href="css1.css" rel="stylesheet" type="text/css" />-->
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript01.js"></script>
<script type="text/javascript" src="../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/fscript.js" charset="utf-8"></script>
<style type="text/css">
<!--
UNKNOWN {FONT-SIZE: small}
#header {FONT-SIZE: 93%; BACKGROUND: url(imagenes/bg.gif) #dae0d2 repeat-x 50% bottom; FLOAT: left; WIDTH: 100%; LINE-HEIGHT: normal}
#header UL {PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-TOP: 10px; LIST-STYLE-TYPE: none}
#header LI {
        PADDING-RIGHT: 0px; PADDING-LEFT: 9px; BACKGROUND: url(imagenes/left.gif) no-repeat left top; FLOAT: left; PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-TOP: 0px}
#header A {
        PADDING-RIGHT: 15px; DISPLAY: block; PADDING-LEFT: 6px; FONT-WEIGHT: bold; BACKGROUND: url(imagenes/right.gif) no-repeat right top; FLOAT: left; PADDING-BOTTOM: 4px; COLOR: #765; PADDING-TOP: 5px; TEXT-DECORATION: none}
#header A { FLOAT: none}
#header A:hover {  COLOR: #333 }
#header #current { BACKGROUND-IMAGE: url(imagenes/left_on.gif)}
#header #current A { BACKGROUND-IMAGE: url(imagenes/right_on.gif); PADDING-BOTTOM: 5px; COLOR: #333 }
-->
</style>
</head>
<body>
<?
list($num_orden,$secuencia)= split('[|]',$_GET['registro']);
//// CONSULTA PRINCIPAL
$sa = "select * from lg_activofijo where NroOrden = '$num_orden' and Secuencia='$secuencia'";
$qa = mysql_query($sa) or die ($sa.mysql_error());
$ra = mysql_num_rows($qa); //echo $ra;

if($ra!='0'){$fa=mysql_fetch_array($qa);}

?>

<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Movimientos de Activos | Nuevo</td>
		<td align="right"><a class="cerrar" href="<?=$regresar?>.php" >[cerrar]</a></td>
	</tr>
</table>
<hr width="100%" color="#333333" />
<? 

/// Consulta Usuario Actual
$s_con = "select
               mp.NomCompleto,
			   mp.CodPersona
		   from 
			   usuarios u
			   inner join mastpersonas mp on (u.CodPersona=mp.CodPersona)
		  where
		       Usuario = '".$_SESSION['USUARIO_ACTUAL']."'";
$q_con = mysql_query($s_con) or die ($s_con.mysql_error());
$f_con = mysql_fetch_array($q_con);
?>

<form id="frmentrada" name="frmentrada" action="af_movimientoactivonuevo.php" method="POST" onsubmit="return guardarNuevoMovimientoActivo(this)">
<? echo "<input type='hidden' id='regresar' name='regresar' value='".$regresar."'/>";?>
<table class='tblForm' width='1050' height='50'>
<? echo "
     <input type='hidden' name='numeroMovimientoGenerado' id='numeroMovimientoGenerado'/> 
	 <input type='hidden' name='fOrganismo' id='fOrganismo' value='".$fOrganismo."'/> 
	 <input type='hidden' name='fmovimiento' id='fmovimiento' value='".$fmovimiento."'/>
	 <input type='hidden' name='fEstado' id='fEstado' value='".$fEstado."'/>
	 <input type='hidden' name='valorguardar' id='valorguardar' /> 
	  

";?>
<tr>
   <td>
   <table>
   <tr>
       <td width='5'></td>
       <td align='right'>Organismo:</td>
       <td align='left'>
           <select name='CodOrganismo' id='CodOrganismo' class='selectBig' $dOrganismo>
           <? getOrganismos($_SESSION['ORGANISMO_ACTUAL'],3);?>
           </select>
       </td>
	   <td width='5'></td>
       <td align='right'>Movimiento #</td>
	   <td><input type='text' id='movimiento' name='movimiento' value='' disabled/></td>
	   <td width='5'>Comentario:</td>
       <td><textarea id="comentario" name="comentario" style=" text-justify:auto; width:250px; height:35px"></textarea></td>
	   <!--<td align='right'>Estado:</td>
       <td><input type="text" id="estado" name="estado" value="En Preparaci&oacute;n" size="20" disabled/></td>-->	   
   </tr>
   <tr>
     <td width='5'></td>
     <td align="right">Preparado Por:</td>
	 <td><input type="hidden" id="preparado_por" name="preparado_por" value="<?=$f_con['1'];?>" disabled/><input type='text' id='pre_por' name='pre_por' value="<?=$f_con[0];?>" size='51' disabled/>
	     <input type='text' id='fecha_prepa' name='fecha_prepa' value="<?=date("d-m-Y");?>" size='8' maxlength="10"/></td>
	 <td width='5'></td>
	 <td>Tipo Movimiento:</td>
	 <td><input type="hidden" name="radioEstado" id="radioEstado" value="I"/><input type='radio' id='radio1' name='radio1' onclick="estadosPosee02(this.form)|selectMotMovimiento(this.form);" checked/>Interno
	     <input type='radio' id='radio2' name='radio2' onclick="estadosPosee02(this.form)|selectMotMovimiento(this.form);"/>Externo</td>
    <td></td>
    <!--<td align="right">Motivo Traslado:</td>
    <td><input type="hidden" id="m_traslado" name="m_traslado" value=""/><input type="text" id="motivo_traslado" name="btMotivo" size="20"/><input type="button" id="btMotivo" name="cargar" value="..."/></td>-->
   </tr>
    
   <tr>
     <td width='5'></td>
     <td align="right">Aprobado Por:</td>
	 <td><input type='text' id='apro_por' name='apro_por' value="" size="51" disabled/>
	     <input type='text' id='fecha_apro' name='fecha_apro' value='' size='8' disabled/></td>
	 <td width='5'></td>
	 <td align='right'>Motivo Traslado:</td>
	 <td align='left' colspan='2'><select id="motivoTrasladoInterno" name="motivoTrasladoInterno" class="selectMed" style="display:block;">
                                  <option value=""></option>
                                  <?
                                    $s_movint = "select * from mastmiscelaneosdet where CodMaestro='MMOVINTER'";
									$q_movint = mysql_query($s_movint) or die ($s_movint.mysql_error());
									$r_movint = mysql_num_rows($q_movint);
									
									for($i=0; $i<$r_movint; $i++){
										$f_movint = mysql_fetch_array($q_movint);
										echo"<option value='".$f_movint['CodDetalle']."'>".$f_movint['Descripcion']."</option>";
									}
								  ?>
                                  </select>
                                  <select id="motivoTrasladoExterno" name="motivoTrasladoExterno" class="selectMed" style="display:none;">
                                  <option value=""></option>
                                  <?
                                    $s_movext = "select * from mastmiscelaneosdet where CodMaestro='MMOVEXTER'";
									$q_movext = mysql_query($s_movext) or die ($s_movext.mysql_error());
									$r_movext = mysql_num_rows($q_movext);
									
									for($i=0;$i<$r_movext; $i++){
										$f_movext = mysql_fetch_array($q_movext);
										echo"<option value='".$f_movext['CodDetalle']."'>".$f_movext['Descripcion']."</option>";
									}
								  ?>
                                  </select></td>
   </tr>	
	
   </table>
   </td>
</tr>
<tr><td height='5'></td></tr>
<tr>
 <td>
 <table>
 <tr>
   <td>
      <input type="button" id="btInsertar" name="Insertar" value="Insertar" onclick="insertarActivoMovimiento('0');"/> 
      <input type="button" id="btEliminar" name="btEliminar" value="Eliminar" onclick="quitarLineaActivoMovimiento(this, document.getElementById('sel_detalle').value);"/></td>
 <td>Seleccionar <input type="button" id="btactivo" name="btactivo" value="Activo" onclick="cargarVentanaLista(this.form, 'af_selectoractivos.php?limit=0&campo=6&ventana=SelActivoMovimiento&cierre=1&can_detalle='+document.getElementById('can_detalle').value+'&sel_detalle='+document.getElementById('sel_detalle').value,'height=520, width=900, left=200, top=70, resizable=yes');"/>
 	             <input type="button" id="centro_costos" name="centro_costos" value="Centro de Costos" onclick="cargarVentanaLista(this.form, 'af_listacentroscostos.php?limit=0&ventana=selCentroCosto&campo=1&sel_detalle='+document.getElementById('sel_detalle').value,'height=500, width=870, left=200, top=100, resizable=yes');"/>
 	             <input type="button" id="btubicacion" name="btubicacion" value="Ubicaci&oacute;n" onclick="cargarVentanaLista(this.form, 'af_listaubicacionesactivo.php?limit=0&campo=2&ventana=SelUbicacionesActivo&sel_detalle='+document.getElementById('sel_detalle').value,'height=500, width=870, left=200, top=100, resizable=yes');"/>
 	             <input type="button" id="btDependencia" name="btDependencia" value="Dependencia" onclick="cargarVentanaLista(this.form, 'af_listadependencias.php?limit=0&campo=3&ventana=SelDependenciaActivo&sel_detalle='+document.getElementById('sel_detalle').value,'height=500, width=870, left=200, top=100, resizable=yes');"/> 
 	             <input type="button" id="btEmpleadoUsuario" name="btEmpleadoUsuario" value="Empl. Usuario" onclick="cargarVentanaLista(this.form, 'af_listaempleados.php?limit=0&campo=4&ventana=SelEmpleadoUsuario&sel_detalle='+document.getElementById('sel_detalle').value, 'height=500, width=870, left=200, top=100, resizable=yes');"/>
 	             <input type="button" id="btEmpleadoResponsable" name="btEmpleadoResponsable" value="Empl. Respons." onclick="cargarVentanaLista(this.form, 'af_listaempleados.php?limit=0&campo=5&ventana=SelEmpleadoUsuario&sel_detalle='+document.getElementById('sel_detalle').value, 'width=870, height=500, top=100, left=200, resizable=yes');"/></td>
 </tr>
</table>
</td>
</tr>
<tr>
  <td align='center'>Ultima Modif.:<input type='text' id='ultimo_usuario' name='ultimo_usuario' style="text-align=center;" readonly/> 
  	                               <input type='text' id='ultima_fecha' name="ultima_fecha" style="text-align=center;"  readonly/></td>
</tr>
</table>


<table width="1055" align="center">
<tr>
  <td>
	<div id="header">
	<ul>
    <li><a onClick="document.getElementById('tab1').style.display='block';" href="#">Movimientos</a></li>
	<!-- CSS Tabs PESTA�AS OPCIONES -->
    <!--
	<li><a onClick="document.getElementById('tab1').style.display='block'; document.getElementById('tab2').style.display='none';" href="#">Movimientos</a></li>
	<li><a onClick="document.getElementById('tab1').style.display='none'; document.getElementById('tab2').style.display='block';" href="#">Errores de Importaci&oacute;n</a></li> 
    -->
	</ul>
	</div>
  </td>
</tr>
</table>
<!-- ****************************************************** COMIENZO TAB1 ************************************************ -->
<input type="hidden" id="sel_detalle" />
<input type="hidden" id="can_detalle"/>

<div id="tab1" style="display: BLOCK;">
<div style="width:1050px; height=15px;" class="divFormCaption"></div>
<table class="tblForm" width="1050">
<tr>
 <td colspan="4" align="center">
  <center>
  <div style="overflow:scroll; width:999px; height:300px;">
 <table width="970" border="1" bgcolor="#CCCCCC" align="center">

 <tbody id="listaDetalles">
     
   <?
	 /// preguntamos según la acción 
	 if($accion=="nuevo"){}
   ?>
 
</tbody>
</table></div></center></td></tr></table></div>
<!-- ****************************************************** COMIENZO TAB2 ************************************************ -->
<center>
 <? echo "<input type='hidden' name='fEstado' id='fEstado' value='".$estado."'/>";?>
 <input type="submit" id="guardar" name="guardar" value="Guardar Registro"/>
 <input type="button" id="cancelar" name="cancelar" value="Cancelar" onClick="cargarPagina(this.form, '<?=$regresar;?>.php?filtro=<?=$fEstado;?>');" /> 
</center>
</form>
<div style="width:850px" class="divMsj">Campos Obligatorios *</div>
</body>
</html>