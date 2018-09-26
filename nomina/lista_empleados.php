<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css1.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="fscript.js"></script>
</head>

<body>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Lista de Empleados</td>
		<td align="right"><a class="cerrar"; href="javascript:window.close();">[Cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<?php
include("fphp.php");
connect();
$MAXLIMIT=30;
//	CONSULTO LA TABLA PARA SABER EL TOTAL DE REGISTROS SOLAMENTE............
if ($filtro!="") $sql="SELECT * FROM mastempleado, mastpersonas WHERE (mastempleado.CodPersona LIKE '%".$filtro."%' OR mastpersonas.Busqueda LIKE '%".$filtro."%' OR mastpersonas.Ndocumento LIKE '%".$filtro."%' OR mastpersonas.DocFiscal LIKE '%".$filtro."%') AND (mastpersonas.CodPersona=mastempleado.CodPersona) AND (mastpersonas.EsEmpleado='S')";
else $sql="SELECT * FROM mastempleado, mastpersonas WHERE (mastpersonas.CodPersona=mastempleado.CodPersona) AND (mastpersonas.EsEmpleado='S')";
$query=mysql_query($sql) or die ($sql.mysql_error());
$registros=mysql_num_rows($query);
?>
<form name="frmlista" id="frmlista" method="post" action="lista_empleados.php?limit=0&campo=<?=$campo?>">
<table width="700" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
		<td align="right">
			<input name="filtro" type="text" id="filtro" size="30" value="<?=$filtro?>" /><input type="submit" value="Buscar" />
		</td>
	</tr>
</table>
<input type="hidden" name="registro" id="registro" />
<table width="700" class="tblLista">
	<tr class="trListaHead">
		<th width="70" scope="col">Empleado</th>
		<th scope="col">B&uacute;squeda</th>
		<th width="25" scope="col">Cli</th>
		<th width="25" scope="col">Pro</th>
		<th width="25" scope="col">Emp</th>
		<th width="25" scope="col">Otr</th>
		<th width="90" scope="col">Nro. Documento</th>
		<th width="90" scope="col">Documento Fiscal</th>
	</tr>
	<?php 
	if ($registros!=0) {
		//	CONSULTO LA TABLA
		if ($filtro!="") $sql="SELECT mastempleado.CodEmpleado, mastempleado.CodPersona, mastpersonas.Busqueda, mastpersonas.EsCliente, mastpersonas.EsProveedor, mastpersonas.EsEmpleado, mastpersonas.EsOtros, mastpersonas.Ndocumento, mastpersonas.DocFiscal FROM mastempleado, mastpersonas WHERE (mastempleado.CodPersona LIKE '%".$filtro."%' OR mastpersonas.Busqueda LIKE '%".$filtro."%' OR mastpersonas.Ndocumento LIKE '%".$filtro."%' OR mastpersonas.DocFiscal LIKE '%".$filtro."%') AND (mastpersonas.CodPersona=mastempleado.CodPersona) AND (mastpersonas.EsEmpleado='S') ORDER BY mastempleado.CodPersona";
		else $sql="SELECT mastempleado.CodEmpleado, mastempleado.CodPersona, mastpersonas.Busqueda, mastpersonas.EsCliente, mastpersonas.EsProveedor, mastpersonas.EsEmpleado, mastpersonas.EsOtros, mastpersonas.Ndocumento, mastpersonas.DocFiscal FROM mastempleado, mastpersonas WHERE (mastpersonas.CodPersona=mastempleado.CodPersona) AND (mastpersonas.EsEmpleado='S') ORDER BY mastempleado.CodPersona";
		$query=mysql_query($sql) or die ($sql.mysql_error());
		$rows=mysql_num_rows($query);
		//	MUESTRO LA TABLA
		for ($i=0; $i<$rows; $i++) {
			$field=mysql_fetch_array($query);
			if ($field['EsCliente']=="S") $escliente="checked"; else $escliente="";
			if ($field['EsProveedor']=="S") $esproveedor="checked"; else $esproveedor="";
			if ($field['EsEmpleado']=="S") $esempleado="checked"; else $esempleado="";
			if ($field['EsOtros']=="S") $esotros="checked"; else $esotros="";
			echo "
			<tr class='trListaBody' onclick='mClk(this, \"registro\"); selEmpleado(\"".$field['Busqueda']."\", \"".$campo."\");' onmouseover='mOvr(this);' onmouseout='mOut(this);' id='".$field['CodPersona']."'>
				<td align='center'>".$field['CodEmpleado']."</td>
				<td align='left'>".($field['Busqueda'])."</td>
				<td align='center'><input type='checkbox' $escliente disabled /></td>
				<td align='center'><input type='checkbox' $esproveedor disabled /></td>
				<td align='center'><input type='checkbox' $esempleado disabled /></td>
				<td align='center'><input type='checkbox' $esotros disabled /></td>
				<td align='left'>".$field['Ndocumento']."</td>
				<td align='left'>".$field['DocFiscal']."</td>
			</tr>";
		}
	}
	$rows=(int)$rows;
	echo "
	<script type='text/javascript' language='javascript'>
		totalLista($registros);
	</script>";				
	?>
</table>
</form>
</body>
</html>