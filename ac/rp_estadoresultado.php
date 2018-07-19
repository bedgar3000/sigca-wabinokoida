<?php
include("../lib/fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location:index.php");
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
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="rp_fscript.js"></script>
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
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Reporte | Estado de Resultado</td>
		<td align="right"><a class="cerrar" href="framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />
<?php

if(!$_POST){ $forganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"]; $fContabilidad.= 'F'; }
//if(!$_POST) $cLibro = "checked";
if(!$_POST){ 
  $fPeriodoDesde = date("Y-")."01";
  $fPeriodoHasta = date("Y-m");
  $cPeriodo = "checked";
}
$MAXLIMIT=30;

$filtro= "";
if($forganismo!="") $corganismo= "checked";else $dorganismo= "disabled";
if($fPeriodoDesde!="" and $fPeriodoHasta!="")$cPeriodo= "checked"; else $dPeriodo= "disabled";
if(($fCuentaDesde!="")and($fCuentaHasta != "")) $cCuenta= "checked"; else $dCuenta= "disabled";
if($fContabilidad!="")$cContabilidad = "checked"; else $dContabilidad= "disabled";
//	-------------------------------------------------------------------------------
//// ------------------------------------------------------------------------------
echo "
<form name='frmentrada' id='frmentrada' method='POST' action='rp_estadoresultadopdf.php'  target='iReporte'>
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
		  getOrganismos($forganismo, 3);
		echo "
   </select>
 </td>
 <td align='right'>Per&iacute;odo:</td>
  <td align='left'>
    <input type='checkbox' name='chkPeriodo' id='chkPeriodo' value='1' $cPeriodo onclick='enabledRPPeriodoDesHas(this.form);' />
    Desde <input type='text' name='fPeriodoDesde' id='fPeriodoDesde' size='6' maxlength='7' $dPeriodo value='$fPeriodoDesde' style='text-align:center'/>
	Hasta <input type='text' name='fPeriodoHasta' id='fPeriodoHasta' size='6' maxlength='7' $dPeriodo value='$fPeriodoHasta' style='text-align:center'/>
  </td>
</tr>

<tr>
  <td align='right'>Contabilidad:</td>
  <td>
	<input type='checkbox' name='chkContabilidad' id='chkContabilidad' value='1' $cContabilidad onclick='enabledRPContabilidad(this.form);' />
	<select id='fContabilidad' name='fContabilidad' class='selectMed' $dContabilidad>
	<option value=''></option>";
	 getContabilidad($fContabilidad, 0);
	echo"</select></td>
  <td align='right'></td>
  <td></td>
</tr>


<tr><td height='1'></td></tr>
</table>
</div>
<center><input type='submit' name='btBuscar' value='Buscar' onclick='rp_balanceGeneral(this.form, 0);'></center>
<br /><div class='divDivision' style='width:900px'>Resultados</div>
<form/>";
//// ------------------------------------------------------------------------------
?>

<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:900px; height:350px;"></iframe>
</center>
</body>
</html>




<!--<div style="width:900px" class="divFormCaption"></div>
<center>
<iframe name="reporte" id="reporte" style="border:solid 1px #CDCDCD; width:900px; height:300px;"></iframe>
</center>
</form>
</body>
</html>-->
