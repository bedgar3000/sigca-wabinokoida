<?php
// ------------------------------------- ####
include("../lib/fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
/// ------------------------
include("fphp.php");
connect();
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('07', $concepto);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link href="css1.css" rel="stylesheet" type="text/css" />-->
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
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
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Generaci&oacute;n de Voucher | Nuevo Registro</td>
		<td align="right"><a class="cerrar" href="framemain.php">[cerrar]</a></td>
	</tr>
</table>
<hr width="100%" color="#333333" />
<?php
/*if($fSituacion=='v_A')$fSituacion="VoucherIngPub20=''";
elseif($fSituacion=='v_B')$fSituacion="VoucherIngPub20<>''";*/
/// -------------------------
if(!$_POST) $fOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"]; else $cOrganismo = "checked";
if(!$_POST){ $fPeriodo = date("Y-m"); $cPeriodo='checked';}
if(!$_POST) $fSituacion = "is";

$filtro = "";
/// -------------------------
if($fOrganismo!=''){$filtro.=" AND (CodOrganismo='".$fOrganismo."')"; $cOrganismo = "checked";} else $dOrganismo = "disabled";
//if($fDependencia!=''){$filtro.=" AND (CodDependencia='".$fDependencia."')"; $cDependencia="checked";} else $dDependencia = "disabled";
if($fContabilidad!=''){$filtro.=" AND (Contabilidad='".$fContabilidad."')"; $cContabilidad = "checked";} else $dContabilidad = "disabled";
/// -------------------------
if($fActivo!=''){$filtro.=" AND (Activo='".$fActivo."')"; $cActivo = "checked";}else $dActivo = "disabled";
if($fPeriodo!=''){$filtro.=" AND (PeriodoIngreso='".$fPeriodo."')"; $cPeriodo = "checked";}else $dPeriodo = "disabled";
if($fSituacion!='')$filtro.=" AND ((VoucherIngPub20 = '') OR (VoucherIngPub20 IS NULL)) ";
/// -------------------------
if ($fdesde != "" and $fhasta != "") { // FECHA DE REGISTRO DEL DOCUMENTO

  list($d, $m, $a)=SPLIT('[/.-]', $_POST['fdesde']); $fechadesde=$a.'-'.$m.'-'.$d;
  list($d, $m, $a)=SPLIT('[/.-]', $_POST['fhasta']); $fechahasta=$a.'-'.$m.'-'.$d;

	if ($fdesde != "") $filtro .= " AND (Fecha >= '$fechadesde')";
	if ($fhasta != "") $filtro .= " AND (Fecha <= '$fechahasta')";
	$cFecha = "checked";

	list($a, $m, $d)=SPLIT('[/.-]', $fechadesde); $fechadesde=$d.'-'.$m.'-'.$a;
    list($a, $m, $d)=SPLIT('[/.-]', $fechahasta); $fechahasta=$d.'-'.$m.'-'.$a;

} else $dFecha = "disabled";
if($fEstado!=''){$filtro.=" AND (Estado='".$fEstado."')"; $cEstado = "checked";}else $dEstado="disabled";

echo"<form name='frmentrada' id='frmentrada' action='af_genvouchernuevoactivo.php?limit=0&amp;campo=$campo' method='POST'>
<input type='hidden' name='tabla' id='tabla' value='$tabla'/>
<table class='tblForm' width='1000' height='50'>
<tr>
   <td>
   <table>
   <tr>
     <td align='right' width='100'>Organismo:</td>
   <td align='left' width='335'>
	   <input type='checkbox' id='checkOrganismo' name='checkOrganismo' value='1' $cOrganismo onclick='this.checked=true;'/>
	   <select name='fOrganismo' id='fOrganismo' class='selectBig' $dOrganismo>";
	   getOrganismos($fOrganismo,3);
	   echo"
	   </select>
   </td>
     <td align='right' width='100'>Departamento:</td>";
	 $sql = "select Descripcion from mastaplicaciones where CodAplicacion ='".$_SESSION["APLICACION_ACTUAL"]."'";
	 $qry = mysql_query($sql) or die ($sql.mysql_error());
	 $field = mysql_fetch_array($qry);
     echo"<td width='80'><input type='text' name='fDepartamento' id='fDepartamento' size='16' value='".$field['Descripcion']."' disabled/></td>
      <td align='right' width='150'>Situaci&oacute;n Generaci&oacute;n:</td>
      <td><select id='fSituacion' name='fSituacion' class='selectMed'>";
	   SituacionGeneracion($fSituacion,0);
	   echo"
		  </select></td>
   </tr>

  <tr>
   <td align='right'>Aplicacion:</td>
   <td align='left'><input type='text' name='aplicacion' id='aplicacion' size='4' value='".$_SESSION["APLICACION_ACTUAL"]."' disabled/></td>
   <td align='right'>Sistema Fuente:</td>
   <td><input type='text' id='sistFuente' name='sistFuente' size='10' value='AUTOCONT' disabled/></td>
  </tr>

  <tr>
   <td align='right'>Periodo Ingreso:</td>
   <td><input type='checkbox' id='chkPeriodo' name='chkPeriodo' value='1' $cPeriodo onclick='enabledPeriodo(this.form);'/><input type='text' id='fPeriodo' name='fPeriodo' size='5' value='".$fPeriodo."' $dPeriodo/></td>
   <td></td>
   <td></td>
   <td align='right'><input type='checkbox' id='sel_todo' name='sel_todo'/>Seleccionar Todo</td>
   <td></td>
  </tr>
   </table>
   </td>
</tr>
</table>
<center>";?>
<input type='submit' name='btBuscar' value='Buscar'/>
<? echo"</center>
</form>";

  /// CONSULTA PARA OBTENER DATOS DE LA TABLA A MOSTRAR
  $sa= "select * from
                      af_activo
                where
                      CodOrganismo<>'' and
					            GenerarVoucherIngresoFlag='S' and Estado='AP'  $filtro "; //echo $sa;
  $qa= mysql_query($sa) or die ($sa.mysql_error());
  $ra= mysql_num_rows($qa);

//// ----------------------------------------------------------------------------------------
$MAXLIMIT=30;
?>
<form id="frmentrada2" name="frmentrada2">
<table width="1000" class="tblBotones">
<tr>
<td><div id="rows"></div></td>
<td width="250"></td>
<td align="right">
<input type="button" id="btGenerar" value="Generar Voucher" onclick="generar_vouchers_abrir(this.form.registro.value, 'af_genvouchernuevoactivo_mostrar.php','generar');" />
        <!--<input type="button" id="btNuevo" name="btNuevo" value="Nuevo" class="btLista" onclick="nuevaTransaccion();"/><input type="button" id="btNuevo" name="btVer" value="Ver" class="btLista" onclick="verTransaccion();"/><input type="button" id="btModificar" name="btModificar" value="Modificar" class="btLista" onclick="modificarTransaccion();"/><input type="button" id="btEliminar" name="btEliminar" value="Eliminar" class="btLista" onclick="eliminarTransaccion();"/><input type="button" id="btAprobar" name="btAprobar" value="Aprobar" class="btLista" onclick="aprobarTransaccion();"/>--></td>
	</tr>
</table>
<input type="hidden" name="registro" id="registro"/>
<input type="hidden" name="ventana" id="ventana" value="<?=$_GET['ventana']?>"/>
<table align="center" cellpadding="0" cellspacing="0"><tr><td valign="top" style="height:100px; width:150px;">
<table align="center" width="400px"><tr><td align="center"><div style="overflow:scroll; height:300px; width:1000px;">
<table width="1400" class="tblLista">
<thead>
	<tr class="trListaHead">
		<th width="90" scope="col">Fecha Ingreso</th>
        <th scope="col" width="90">Activo</th>
        <th scope="col" width="90">Cod. Interno</th>
		<th scope="col" width="350">Descripcion</th>
        <th scope="col" width="90">Monto Local</th>
        <th scope="col" width="350">Centro Costo</th>
	</tr>
  </thead>
<?php
if ($ra!=0) {
 for($i=0; $i<$ra; $i++){
	$field = mysql_fetch_array($qa);
	list($ano, $mes, $dia) = split('[-]', $field['FechaIngreso']);
	$fechaIngreso = $dia.'-'.$mes.'-'.$ano;
	$id=$field['CodOrganismo'].'-'.$field['Activo'];
	$montoLocal = number_format($field['MontoLocal'],2,',','.');

	$sql_b = "select * from ac_mastcentrocosto where CodCentroCosto='".$field['CentroCosto']."'";
	$qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error());
	$row_b = mysql_num_rows($qry_b);
    if($row_b!=0) $field_b = mysql_fetch_array($qry_b);


    echo "
	 <tr class='trListaBody' onclick='mClk(this,\"registro\");'  id='$id'>
	    <td align='center'>$fechaIngreso</td>
		<td align='center'>".$field['Activo']."</td>
		<td align='center'>".$field['CodigoInterno']."</td>
		<td align='left'>".$field['Descripcion']."</td>
		<td align='right'>$montoLocal</td>
		<td align='left'>".$field_b['Descripcion']."</td>

	 </tr>";
	}}
	$rows=(int)$rows;
	echo "
	<script type='text/javascript' language='javascript'>
		totalLista($ra);
		totalLotes($registro, $rows, ".$_GET['limit'].");
	</script>";
	?>
</table></div></td></tr></table></td></tr></table>
</form>
</body>
</html>
