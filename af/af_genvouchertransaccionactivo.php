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
<script type="text/javascript" language="javascript" src="af_fscript_02.js"></script>
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
		<td class="titulo">Generaci&oacute;n Voucher de Transacciones de Baja | Nuevo Registro</td>
		<td align="right"><a class="cerrar" href="framemain.php">[cerrar]</a></td>
	</tr>
</table>
<hr width="100%" color="#333333" />
<?php
/// -------------------------
if(!$_POST) $fOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"]; else $cOrganismo = "checked"; 
if((!$_POST)or($_POST)){ 
	if($cont=="F"){
	  $fContabilidad="F";
	  $flag = "ContabilizadoFlagPub20";
	}elseif($cont=="T"){
		   $fContabilidad="T";
		   $flag = "ContabilizadoFlag";
		   }
}
if(!$_POST){ $fperiodo = date("Y-m"); $cPeriodo='checked';}

$filtro = "";
/// -------------------------
if($fOrganismo!=''){$filtro.=" AND (CodOrganismo='".$fOrganismo."')"; $cOrganismo = "checked";} else $dOrganismo = "disabled";
if($fContabilidad!=''){$filtro.=" AND (Contabilidad='".$fContabilidad."')"; $cContabilidad = "checked"; $dContabilidad = "disabled";} else $dContabilidad = "disabled";
if($fperiodo!=''){$filtro.=" AND (PeriodoIngreso='".$fperiodo."')"; $cPeriodo = "checked";}else $dPeriodo = "disabled";

echo"<form name='frmentrada' id='frmentrada' action='af_genvouchertransaccionactivo.php?limit=0&amp;campo=$campo' method='POST'>
<input type='hidden' name='tabla' id='tabla' value='$tabla'/>
<input type='hidden' name='cont' id='cont' value='".$cont."'/>
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
     <td align='right' width='100'>Contabilidad:</td>
	 <td>
	<input type='checkbox' name='chkContabilidad' id='chkContabilidad' value='1' $cContabilidad onclick='enabledRPContabilidad(this.form);' />
	<select id='fContabilidad' name='fContabilidad' class='selectMed' $dContabilidad>";
	 getContabilidad($fContabilidad, 0);
	echo"</select></td>
	 <td align='right'>Per&iacute;odo:</td>
     <td align='left'>
    <input type='checkbox' name='chkperiodo' id='chkperiodo' value='1' $cPeriodo onclick='enabledPeriodo(this.form);' />
    <input type='text' name='fperiodo' id='fperiodo' size='8' maxlength='7' $dPeriodo value='$fperiodo' style='text-align:center;'/>
  </td>
   </tr>
  </table>
</td>
</tr>
</table>
<center><input type='submit' name='btBuscar' value='Buscar'></center>
<form/><br />";

  /// CONSULTA PARA OBTENER DATOS DE LA TABLA A MOSTRAR
  $sa= "select 
  			  a.Fecha, 
			  a.Activo,
			  a.Organismo,
			  a.CodTransaccionBaja,
			  b.CodigoInterno,
			  b.Descripcion as DescpActivo,
			  c.Descripcion as DescpTipoTrans,
			  a.Comentario
			  
		  from 
              af_transaccionbaja a
			  inner join af_activo b on (a.Activo = b.Activo)
			  inner join af_tipotransaccion c on (a.TipoTransaccion = c.TipoTransaccion)
         where 
			  a.Organismo='$fOrganismo' and
			  a.$flag='N' and 
			  a.Estado='AP'"; //echo $sa;
  $qa= mysql_query($sa) or die ($sa.mysql_error()); 
  $ra= mysql_num_rows($qa);
  
//// ----------------------------------------------------------------------------------------
$MAXLIMIT=30;
?>
<table width="1000" align="center">
<tr>
  <td>
	<div id="header">
	<ul>
	<!-- CSS Tabs PESTA�AS OPCIONES -->
	<li><a onClick="document.getElementById('tab1').style.display='block';
    				document.getElementById('tab2').style.display='none'; " href="#">Transacciones Pendientes</a></li>
    <li><a onClick="document.getElementById('tab1').style.display='none';
    				document.getElementById('tab2').style.display='block'; " href="#">Detalle de la Transacciones</a></li>
  
	</ul>
	</div>
  </td>
</tr>
</table>

<div id="tab1" style="display: block;">
<div style="width:990px; height=15px; text-align:left" class="divFormCaption">Seleccione las Transacciones que dese generar</div>

<table width="990" class="tblBotones">
<tr>
<td><div id="rows"></div></td>
<td width="250"></td>
<td align="right">
<input type="button" id="btGenerar" value="Generar Voucher" onclick="generar_vouchers_abrir(this.form.registro.value, 'af_genvouchertransbajaf.php', 'generar', '<?=$cont;?>');" /></td>
	</tr>
</table>
<input type="hidden" name="registro" id="registro"/>
<input type="hidden" name="ventana" id="ventana" value="<?=$_GET['ventana']?>"/>
<table align="center" cellpadding="0" cellspacing="0"><tr><td valign="top" style="height:100px; width:150px;">
<table align="center" width="400px"><tr><td align="center"><div style="overflow:scroll; height:300px; width:990px;">
<table width="1600" class="tblLista">
<thead>
	<tr class="trListaHead">
		<th width="60">Fecha Ingreso</th>
        <th width="25">Activo</th>
        <th width="60">Cod. Interno</th>
		<th width="250">Descripcion</th>
         <th width="200">TipoTransacci&oacute;n</th>
        <th width="200">Comentario</th>
        <th width="50">Dependencia</th>
        <th width="50">Responsable</th>
        
	</tr>
  </thead>
<?php 
if ($ra!=0) {
 for($i=0; $i<$ra; $i++){		
	$field = mysql_fetch_array($qa);	
	list($ano, $mes, $dia) = split('[-]', $field['Fecha']);
	$fechaIngreso = $dia.'-'.$mes.'-'.$ano;
	$id=$field['Organismo'].'|'.$field['Activo'].'|'.$field['CodTransaccionBaja'];  //echo $id;
	$montoLocal = number_format($field['MontoLocal'],2,',','.');
	
	$sql_b = "select * from ac_mastcentrocosto where CodCentroCosto='".$field['CentroCosto']."'";
	$qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error());
	$row_b = mysql_num_rows($qry_b);
    if($row_b!=0) $field_b = mysql_fetch_array($qry_b);
	
	
    echo "
	 <tr class='trListaBody' onclick='mClk(this,\"registro\")| muestra_detalle(this,\"$id\");'  id='$id'>
	    <td align='center'>$fechaIngreso</td>
		<td align='center'>".$field['Activo']."</td>
		<td align='center'>".$field['CodigoInterno']."</td>
		<td align='left'>".$field['DescpActivo']."</td>
		<td align='left'>".$field['DescpTipoTrans']."</td>
		<td align='left'>".$field['Comentario']."</td>
		<td align='left'>".$field['Comentario']."</td>
		<td align='left'>".$field['Comentario']."</td>
		
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
</div>

<div id="tab2" style="display: none;">
<div style="width:990px; height=15px; text-align:left" class="divFormCaption">Seleccione las Transacciones que dese generar</div>

<table width="990" class="tblBotones">
<tr>
<td><div id="rows"></div></td>
<td width="250"></td>
<td align="right">
<input type="button" id="btGenerar" value="Generar Voucher" onclick="generar_vouchers_abrir(this.form.registro.value, 'af_genvouchertransbajaf.php','generar','<?=$cont;?>');" />
    </td>
	</tr>
</table>

<input type="hidden" name="ventana" id="ventana" value="<?=$_GET['ventana']?>"/>
<table align="center" cellpadding="0" cellspacing="0"><tr><td valign="top" style="height:100px; width:150px;">
<table align="center" width="400px"><tr><td align="center"><div style="overflow:scroll; height:300px; width:990px;">
<table width="1000" id="tab2_cargar" class="tblLista">


</table></div></td></tr></table></td></tr></table>
</div>
</body>
</html>
