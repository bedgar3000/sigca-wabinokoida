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
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
<script type="text/javascript" src="../js/funciones.js" charset="utf-8"></script>
</head>

<body onload="document.getElementById('codigo').focus();">
<?php
include("af_fphp.php");
connect();
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Tipo de Movimiento Activos | Ver Registro</td>
		<td align="right"><a class="cerrar" href="javascript:window.close();">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="af_clasificacion_activos.php" method="POST" onsubmit="return verificarClasificacionActivo(this, 'GUARDAR');">
<input type="hidden" name="filtro" id="filtro" value="<?=$filtro?>" />
<div style="width:700px" class="divFormCaption">Datos del Registro</div>
<?
 $sql = "select * from af_tipomovimientos where CodTipoMovimiento = '".$_GET['registro']."'";
 $qry = mysql_query($sql) or die ($sql.mysql_error());
 $row = mysql_num_rows($qry);
 if($row!=0) $field = mysql_fetch_array($qry);

 if($field['FlagMovimiento']=='S'){ $FlagMovimiento="onclick='this.checked=true;'"; $FlagMovimiento_01="checked";
 }else{ $FlagMovimiento="disabled";}
 
 if($field['FlagIncorporacion']=='S'){ $FlagIncorporacion="onclick='this.checked=true;'";  $FlagIncorporacion_01="checked";
 }else{ $FlagIncorporacion="disabled";}
 
 if($field['FlagDesincorporacion']=='S'){ $FlagDesincorporacion="onclick='this.checked=true;'"; $FlagDesincorporacion_01="checked";
 }else{ $FlagDesincorporacion="disabled";}

?>
<table width="700" class="tblForm">
<tr>
  <td width="117"class="tagForm">C&oacute;digo:</td>
  <td width="109"><input name="codigo" type="text" id="codigo" size="10" maxlength="2" value="<?=$field['CodTipoMovimiento'];?>" readonly/>*</td>
  <td width="142">Aplica Movimiento:<input type="checkbox" id="flag_movimiento" name="flag_movimiento" <?=$FlagMovimiento;?> <?=$FlagMovimiento_01;?> /></td>
  <td width="144">Aplica Incorporaci&oacute;n:<input type="checkbox" id="flag_incorporacion" name="flag_incorporacion" <?=$FlagIncorporacion;?> <?=$FlagIncorporacion_01;?>/></td>
  <td width="164">Aplica Desincorporaci&oacute;n:<input type="checkbox" id="flag_desincorporacion" name="flag_desincorporacion" <?=$FlagDesincorporacion;?> <?=$FlagDesincorporacion_01;?>/></td>
	</tr>
    <tr>
		<td class="tagForm">Tipo Movimiento:</td>
		<td colspan="4"><select name="t_movimiento" id="t_movimiento" class="selectMed">
            <? if($field['TipoMovimiento']=='IN'){ ?>
                 <option value="IN" selected="selected">Incorporaci&oacute;n</option>
            <? }else{ ?>
                 <option value="DE" selected="selected">Desincorporaci&oacute;n</option>
            <? }?>
            </select>*</td>
	</tr>
	<tr>
		<td class="tagForm">Descripci&oacute;n:</td>
		<td colspan="4"><input name="descripcion" type="text" id="descripcion" maxlength="255" value="<?=utf8_encode($field['DescpMovimiento']);?>" style="width:90%;" readonly/>*</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td colspan="4">
			<? if($field['Estado']=='A'){ ?>
				<input id="activo" name="status" type="radio" value="A" checked onclick="this.checked=true"/> Activo
			    <input id="inactivo" name="status" type="radio" value="I" onclick="this.checked=false" disabled/> Inactivo
            <? }else{ ?>
                <input id="activo" name="status" type="radio" value="A"  onclick="this.checked=false" disabled/> Activo
			    <input id="inactivo" name="status" type="radio" value="I" checked onclick="this.checked=true"/> Inactivo
            <? }?>
		</td>
	</tr>
	<tr>
	<td class="tagForm">&Uacute;ltima Modif.:</td>
	<td colspan="4">
		<input name="ult_usuario" type="text" id="ult_usuario" size="30"  disabled="disabled" value="<?=$field['UltimoUsuario'];?>"/>
		<input name="ult_fecha" type="text" id="ult_fecha" size="25"  disabled="disabled" style="text-align:right" value="<?=$field['UltimaFechaModif'];?>"/>
	</td>
	</tr>
</table>
<!--<center> 
<input type="submit" value="Guardar Registro" />
<input name="bt_cancelar" type="button" id="bt_cancelar" value="Cancelar" onClick="cargarPagina(this.form, 'af_tipomovimientoactivo.php');" />
</center><br />-->
</form>

<div style="width:700px" class="divMsj">Campos Obligatorios *</div>
</body>
</html>
