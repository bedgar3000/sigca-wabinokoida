<?php
// ------------------------------------- ####
include("../lib/fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
//	------------------------------------
//include ("fphp.php");
include ("af_php.php");
connect();
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('04', $concepto);
//	------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
<link type="text/css" rel="stylesheet" href="../css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../css/estilo.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../css/fancytree/skin-win8/ui.fancytree.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" src="../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.fancytree.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.formatCurrency-1.4.0.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.formatCurrency.all.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.timeentry.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.numeric.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.mask.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" src="js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" src="js/form.js" charset="utf-8"></script>

<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript_02.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript01.js"></script>
</head>
<body>
<div id="cajaModal"></div>
<!-- pretty -->
<span class="gallery clearfix"></span>
<!--////////////////////@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@///////////////////////////-->
<table width="100%" cellspacing="0" cellpadding="0">
 <tr>
  <td class="titulo">Reporte | Listado de Actas</td>
  <td align="right">
   <a class="cerrar" href="framemain.php">[cerrar]</a>
  </td>
 </tr>
</table>
<hr width="100%" color="#333333" />
<!--////////////////////@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@///////////////////////////-->
<?php 
if(!$_POST) $forganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
if(!$_POST){ $fSituacionActivo = 'IF'; $cSituacionActivo = "checked";}
//if(!$_POST){ $fEstado = 'AP'; $cEstado = "checked";}
if(!$_POST){ $FechaActual= formatFechaDMA($FechaActual);  $fecha_desde= "01-".date("m-Y"); $fecha_hasta= $FechaActual; $cFechaRecibido="checked"; }
if(!$_POST){ $ftipoacta="AI"; $cTipoActa="checked"; }

$MAXLIMIT=30;
$filtro = "";
if($forganismo!=""){$filtro.= " AND (CodOrganismo = '".$forganismo."')"; $corganismo = "checked"; } else $dorganismo = "disabled";
//if($fDependencia!=""){$filtro.="AND (CodDependencia='".$fDependencia."')"; $cDependencia="checked";}else $dDependencia="disabled";
//if($fEstado!=""){$filtro.="AND (Estado ='".$fEstado."')"; $cEstado = "checked";} else $dEstado = "disabled";
if($ftipoacta!="") $cTipoActa="checked"; else $dTipoActa= "disabled";
if(($fecha_desde!="")and($fecha_hasta!=""))$cFecha="checked"; else $dFecha="disabled";

?>
<? echo"
<form name='frmentrada' action='af_rplistadoactas.php?limit=0' method='POST'>
<input type='hidden' name='limit' id='limit' value='".$limit."'>
<input type='hidden' name='registros' id='registros' value='".$registros."'/>
<input type='hidden' name='usuarioActual' id='usuarioActual' value='".$_SESSION['USUARIO_ACTUAL']."'/>

<div class='divBorder' style='width:900px;'>
<table width='900' class='tblFiltro'>
 <tr>
   <td class='tagForm'>Organismo:</td>
   <td>
    <input type='checkbox' name='chkorganismo' id='chkorganismo' value='1' $corganismo onclick='this.checked=true' />
    <select name='forganismo' id='forganismo' class='selectBig' $dorganismo onchange='getFOptions_2(this.id, \"fanteproyecto\", \"chknanteproyecto\");'>";
  	 //getOrganismos($obj[2], 3, $_SESSION[ORGANISMO_ACTUAL]);
     getOrganismos($_SESSION[ORGANISMO_ACTUAL],3);
	   echo "
    </select>
  </td>

  <td align='right'>Fecha:</td>
   <td><input type='checkbox' id='chkfecha' name='chkfecha' value='1' $cFecha onclick='enabledRpAFecha(this.form);'/>
       <input type='text' id='fecha_desde' name='fecha_desde' value='$fecha_desde' size='10' maxlength='10' $dFecha style='text-align:center' class='datepicker'/> al
     <input type='text' id='fecha_hasta' name='fecha_hasta' value='$fecha_hasta' size='10' maxlength='10' $dFecha style='text-align:center' class='datepicker'/></td>
</tr>

<tr>
  <td align='right'>Tipo Acta:</td>
	<td ><input type='checkbox' name='chktipoacta' id='chktipoacta' $cTipoActa onclick='enabledTipoActa(this.form);'/>
		   <select id='ftipoacta' name='ftipoacta' $dTipoActa>
         <option value=''></option>";
           getSeleccionTipoActa($ftipoacta,0);
       echo"</select> 
  </td>

  <td align='right'></td> 
  <td></td>
</tr>

<tr>
  <td height='5'></td>
</tr>

</table>
</div>
<center><input type='submit' name='btBuscar' value='Buscar' onclick='cargarListadoActas(this.form);'></center>
<br /><div style='width:900;' class='divDivision'>Listado de Actas</div>
<form/><br />";
?>
<table width="900" class="tblBotones">
<tr>
<td><div id="rows"></div></td>
<td width="250">
<?php 
//echo"<input type='hidden' name='regresar' id='regresar' value='cpi_docinternoslista'/>";
?>
		</td>
		<td align="right">
<!--<input type="button" id="btEjecutar" name="btejecutar"  value="Ejecutar Cierre" onclick="ProcesoEjecutarCierre(this.form);"/>-->
		</td>
	</tr>
</table>
<input type="hidden" name="registro" id="registro" />
<div style="width:900px" class="divFormCaption"></div>
<center>
<iframe name="af_rplistadoactaspdf" id="af_rplistadoactaspdf" style="border:solid 1px #CDCDCD; width:900px; height:300px; visibility:false; display:false;" ></iframe>
</center>
<form/>
</body>
</html>
