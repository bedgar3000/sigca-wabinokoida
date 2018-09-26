
<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
//	------------------------------------
include ("fphp.php");
include ("controlActivoFijo.php");
connect();
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('03', $concepto);
//	------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link href="css1.css" rel="stylesheet" type="text/css" />-->
<link href="css2.css" rel="stylesheet" type="text/css" />
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript_02.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript2.js"></script>
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
<?
$s = "select * from usuarios where Usuario='".$_SESSION['USUARIO_ACTUAL']."'";
$q = mysql_query($s) or die ($s.mysql_error());
$f = mysql_fetch_array($q);
echo "<input type='hidden' id='usuario_actual' name='usuario_actual' value='".$f['CodPersona']."'/>";

list($Activo, $Organismo, $CodTransaccionBaja) = split('[|]', $registro); 

echo "<input type='hidden' name='nro_activo' id='nro_activo' value='$Activo'/>
<input type='hidden' name='codorganismo' id='codorganismo' value='$Organismo'/>
<input type='hidden' name='codtransaccionbaja' id='codtransaccionbaja' value='$CodTransaccionBaja'/> 
<input type='hidden' name='estado' id='estado' value='".$Estado."'/> 
";

?>
<form id="frmentrada" name="frmentrada" action="af_bajactivoslistar.php?limit=0"  onsubmit="return guardarTransaccionBaja(this,'Anular');">
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Baja de Activos | Anular</td>
		<td align="right"><a class="cerrar" href="<?=$regresar?>.php" onclick="window.close();">[cerrar]</a></td>
	</tr>
</table>
<hr width="100%" color="#333333" /><br />
<table align="center" width="700" class="tblForm">
<tr>
  <td class="tagForm">Motivo Anulaci&oacute;n:</td>
  <td><textarea id="motivo_anular" name="motivo_anular" style="width:500px; height:100px"></textarea></td>
</tr>
</table>
<center><input type="submit" name="btAnular" id="btAnular" value="Anular"/>
<input type="button" name="btCancelar" id="btCancelar" value="Cancelar" onclick="window.close();" /></center>
</form>
</body></html>