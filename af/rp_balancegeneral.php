<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location:index.php");
//	------------------------------------
include("fphp.php");
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
<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="rp_fscript.js"></script>
</head>
<body>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Reporte | Balance General</td>
		<td align="right"><a class="cerrar" href="framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />
<?php

if(!$_POST){ $forganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"]; $fContabilidad.= 'F'; }
//if(!$_POST) $cLibro = "checked";
if(!$_POST) $fPeriodo  = date("Y-m");
$MAXLIMIT=30;

$filtro = "";
if ($forganismo != "")$corganismo = "checked";else $dorganismo = "disabled";
if ($fPeriodo != "")$cPeriodo = "checked"; else $dPeriodo = "disabled";
//if ($fLibro != "" ) $cLibro = "checked"; else $dLibro = "disabled";
if (($fCuentaDesde != "")and($fCuentaHasta != "")) $cCuenta = "checked"; else $dCuenta = "disabled";
if ($fContabilidad != "")$cContabilidad = "checked"; else $dContabilidad = "disabled";
if ($fVoucher !="")	$cVoucher = "checked"; else $dVoucher = "disabled";
//	-------------------------------------------------------------------------------
//// ------------------------------------------------------------------------------
echo "
<form name='frmentrada' id='frmentrada' method='POST' action='rp_libromayorgeneralpdf.php'  target='reporte'>
      <input type='hidden' name='limit' id='limit' value='".$limit."'/>
      <input type='hidden' name='registros' id='registros' value='".$registros."'/>

<div class='divBorder' style='width:900px;'>
<table width='900' class='tblFiltro'>
<tr>
 <td align='right'>Organismo:</td>
 <td>
  <input type='checkbox' name='chkorganismo' id='chkorganismo' value='1' $corganismo onclick='this.checked=true' />
  <select name='forganismo' id='forganismo' class='selectBig' $dorganismo onchange='getFOptions_2(this.id, \"fnanteproyecto\", \"chknanteproyecto\");'>";
		getOrganismos($forganismo, 3, $_SESSION[ORGANISMO_ACTUAL]);
		echo "
   </select>
 </td>
 <td align='right'>Per&iacute;odo:</td>
  <td align='left'>
    <input type='checkbox' name='chkPeriodo' id='chkPeriodo' value='1' $cPeriodo onclick='enabledRPPeriodo(this.form);' />
    <input type='text' name='fPeriodo' id='fPeriodo' size='6' maxlength='7' $dPeriodo value='$fPeriodo' style='text-align:center'/>
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
  <td align='right'>Cuenta:</td>
  <td><input type='checkbox' id='chkCuenta' name='chkCuenta' value='1' $cCuenta onclick='enabledRPCuenta(this.form);'/>
      Desde <input type='text' id='fCuentaDesde' name='fCuentaDesde' size='15' maxlength='13' value='$fCuentaDesde' $dCuenta/>
	  Hasta <input type='text' id='fCuentaHasta' name='fCuentaHasta' size='15' maxlength='13' value='$fCuentaHasta' $dCuenta/></td>
</tr>


<tr><td height='1'></td></tr>
</table>
</div>
<center><input type='submit' name='btBuscar' value='Buscar' onclick='filtroReporteLibroMayorGeneral(this.form, 0);'></center>
<br />";
//// ------------------------------------------------------------------------------
?>
<div style="width:900px" class="divFormCaption"></div>
<center>
<iframe name="reporte" id="reporte" style="border:solid 1px #CDCDCD; width:900px; height:300px;"></iframe>
</center>
</form>
</body>
</html>
