<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link href="css1.css" rel="stylesheet" type="text/css" />-->
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="../css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="../ac/af_fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript2.js"></script>
<script type="text/javascript" src="../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" language="javascript" src="../js/ac_fscript.js"></script>

</head>
<body>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Listado de Plan de Cuentas</td>
		<td align="right"><a class="cerrar" href="#" onclick="javascript:window.close();">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />
<?php
include("../fphp.php");
///include("fphp.php");
connect();
$MAXLIMIT=30;

//if(!$_POST){ $fBienes = '01'; $cBienes = "checked";} 

$filtro2="";
//if ($fBienes!=""){$filtro2 .=" AND (CodCuenta ='".$fBienes."')"; $cBienes = "checked"; }else $dBienes = "disabled";

//	CONSULTO LA TABLA PARA SABER EL TOTAL DE REGISTROS SOLAMENTE............
if ($_POST['filtro']!="") $sql="SELECT * FROM ac_mastplancuenta WHERE (CodCuenta LIKE '%".$_POST['filtro']."%' OR Descripcion LIKE '%".$_POST['filtro']."%')";
else $sql="SELECT * FROM ac_mastplancuenta";
$query=mysql_query($sql) or die ($sql.mysql_error());
$registros=mysql_num_rows($query);

?>
<form name="frmentrada" id="frmentrada" action="listado_cuentas_contables.php?limit=0" method="POST">
<input type="hidden" name="tabla" id="tabla" value="<?=$tabla?>" />

<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<!--<table width="800" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
		<td align="right">Filtro: <input type="text" name="filtro" id="filtro" value="<?=$filtro?>" size="30" /></td>
	</tr>
</table>-->
<table width="100%" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
		<td width="250">
			<?php 
			echo "
			<table align='center'>
				<tr>
					<td>
						<input name='btPrimero' type='button' id='btPrimero' value='&lt;&lt;' onclick='setLotes(this.form, \"P\", $registros, ".$_GET['limit'].");' />
						<input name='btAtras' type='button' id='btAtras' value='&lt;' onclick='setLotes(this.form, \"A\", $registros, ".$_GET['limit'].");' />
					</td>
					<td>Del</td><td><div id='desde'></div></td>
					<td>Al</td><td><div id='hasta'></div></td>
					<td>
						<input name='btSiguiente' type='button' id='btSiguiente' value='&gt;' onclick='setLotes(this.form, \"S\", $registros, ".$_GET['limit'].");' />
						<input name='btUltimo' type='button' id='btUltimo' value='&gt;&gt;' onclick='setLotes(this.form, \"U\", $registros, ".$_GET['limit'].");' />
					</td>
				</tr>
			</table>";
			?>
		</td>
		<td align="center">
			<input name="filtro" type="text" id="filtro" size="30" value="<?=$_POST['filtro']?>" /><input type="submit" value="Buscar" />
		</td>
	</tr>
</table>
<input type="hidden" name="registro" id="registro" />

<table align="center"><tr><td align="center"><div style="overflow:scroll; width:800px; height:700px;">
<table width="100%" class="tblLista">
<thead>
	<tr class="trListaHead">
		<th width="125" scope="col">Cuenta</th>
		<th scope="col">Descripci&oacute;n</th>
		<th width="125" scope="col">Tipo de Cuenta</th>
		<th width="75" scope="col">Naturaleza</th>
		<th width="75" scope="col">Estado</th>
	</tr>
</thead>
	<?php
	if ($registros!=0) {
	//$filtro = trim($filtro); 
	if ($_POST['filtro']!="") $sql="SELECT 
		                                    *
									  FROM 
										    ac_mastplancuenta
									 WHERE 
										    (CodCuenta LIKE '%".$_POST['filtro']."%' OR Descripcion LIKE '%".$_POST['filtro']."%')
								  ORDER BY 
									        CodCuenta LIMIT ".$_GET['limit'].", $MAXLIMIT";  
	else $sql="SELECT * FROM ac_mastplancuenta where CodCuenta like '$fBienes%' ORDER BY CodCuenta LIMIT ".$_GET['limit'].", $MAXLIMIT";
	
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows = mysql_num_rows($query);
	//	MUESTRO LA TABLA
	for ($i=0; $i<$rows; $i++) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] == "A") $status = "Activo"; else $status = "Inactivo";
		if ($field['Naturaleza'] == "D") $naturaleza = "Deudora"; else $naturaleza = "Acreeedora";
		
		$sql = "SELECT * FROM ac_mastplancuenta WHERE CodCuenta LIKE '".$field['CodCuenta']."%' AND CodCuenta <> '".$field['CodCuenta']."'";
		$query_sub = mysql_query($sql) or die ($sql.mysql_error());
		if (mysql_num_rows($query_sub) == 0 && strlen($field['CodCuenta']) >= 3) {
			?>
			<tr class="trListaBody" onclick="selListado('<?=$field['CodCuenta']?>', '<?=($field["Descripcion"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$field['CodCuenta']?>">
				<td align="center"><?=$field['CodCuenta']?></td>
				<td><?=($field['Descripcion'])?></td>
				<td align="center"><?=($field['NomTipoCuenta'])?></td>
				<td align="center"><?=($naturaleza)?></td>
				<td align="center"><?=($status)?></td>
			</tr>
			<?php
		}
	}
	}
	$rows=(int)$rows;
	echo "
	<script type='text/javascript' language='javascript'>
		totalLista($registros);
		totalLotes($registros, $rows, ".$_GET['limit'].");
	</script>";	
	?>
</table>
</div></td></tr></table>
</form>

</body>
</html>