<?php
/// -------------------------------------------------####
include("../lib/fphp.php");
//session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
//	------------------------------------
//include("fphp.php");
include("rp_fphp.php");
connect();
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('01', $concepto);
//	------------------------------------
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link href="css1.css" rel="stylesheet" type="text/css" />-->
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="rp_fscript.js"></script>
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
<div id="cajaModal"></div>
<!-- pretty -->
<span class="gallery clearfix"></span>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Reporte | Balance Comprobaci&oacute;n</td>
		<td align="right"><a class="cerrar" href="framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />
<?php

if(!$_POST){ $forganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"]; $fperiodo= date("Y-m"); $fContabilidad.= 'F';}
$MAXLIMIT=30;

$filtro = "";
if ($forganismo != ""){$filtro.= "and (CodOrganismo='".$forganismo."')";$corganismo = "checked";}else $dorganismo = "disabled";
if ($fperiodo != "")$cPeriodo = "checked"; else $dPeriodo = "disabled";
if ($fContabilidad != ""){$filtro.=" and (CodContabilidad='".$cContabilidad."')";$cContabilidad = "checked";}else $dContabilidad = "disabled";

//	-------------------------------------------------------------------------------
//// ------------------------------------------------------------------------------
echo "
<form name='frmentrada' id='frmentrada' action='rp_balancesaldoanterioracumuladopdf.php' method='POST' target='iReporte'>
      <input type='hidden' name='limit' id='limit' value='".$limit."'/>
      <input type='hidden' name='registros' id='registros' value='".$registros."'/>

<div class='divBorder' style='width:900px;'>
<table width='900' class='tblFiltro'>
<tr>
 <td align='right'>Organismo:</td>
 <td>
  <input type='checkbox' name='chkorganismo' id='chkorganismo' value='1' $corganismo onclick='this.checked=true' />
  <select name='forganismo' id='forganismo' class='selectBig' $dorganismo onchange='getFOptions_2(this.id, \"fnanteproyecto\", \"chknanteproyecto\");'>";
		//getOrganismos($forganismo, 3, $_SESSION[ORGANISMO_ACTUAL]);
    getOrganismos($_SESSION[ORGANISMO_ACTUAL],3);
		echo "
   </select>
 </td>
 <td align='right'>Per&iacute;odo:</td>
  <td align='left'>
    <input type='checkbox' name='chkperiodo' id='chkperiodo' value='1' $cPeriodo onclick='enabledPeriodo(this.form);' />
    <input type='text' name='fperiodo' id='fperiodo' size='8' maxlength='7' $dPeriodo value='$fperiodo' style='text-align:center;'/>
  </td>
</tr>

<tr>
  <td align='right'>Contabilidad:</td>
  <td>
	<input type='checkbox' name='chkContabilidad' id='chkContabilidad' value='1' $cContabilidad onclick='enabledRPContabilidad(this.form);' />
	<select id='fContabilidad' name='fContabilidad' class='selectMed' $dContabilidad>";
	 getContabilidad($fContabilidad, 0);
	echo"</select></td>
  <td align='right' colspan='2'>Cuenta Desde: <input type='text' id='cuenta_desde' name='cuenta_desde' value='000000000' size='13' style='text-align:right;'/> Hasta: <input type='text' id='cuenta_hasta' name='cuenta_hasta' value='999999999' size='13' style='text-align:right;'/></td>
</tr>

<tr><td height='2'></td></tr>
</table>
</div>
<center><input type='submit' name='btBuscar' value='Buscar' onclick='rp_balancecomprobacion(this.form, 0);'></center>
<br /><div class='divDivision' style='width:900px'>Resultados</div>
<form/>";
//// ------------------------------------------------------------------------------
?>
<table width="908" align="center">
<tr>
  <td>
	<div id="header">
	<ul>
	<!-- CSS Tabs PESTA�AS OPCIONES -->
    <li><a onClick="rp_balancecomprobacion('frmentrada','saldo_anterior_acumulado');" href="#">Saldo Anterior Acumulado</a></li>
	  <li><a onClick="rp_balancecomprobacion('frmentrada','debe_haber');" href="#">Debe - Haber</a></li>
    <li><a onClick="rp_balancecomprobacion('frmentrada','debe_haber_acumulado');" href="#">Debe - Haber Acumulado</a></li>
	  <li><a onClick="rp_balancecomprobacion('frmentrada','Acumulado');" href="#">Acumulado</a></li> 
	</ul>
	</div>
  </td>
</tr>
</table>

<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:900px; height:350px;"></iframe>
</center>
</body>
</html>
