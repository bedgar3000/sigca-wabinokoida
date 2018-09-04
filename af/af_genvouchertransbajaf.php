<?php 
// ------------------------------------- ####
include("../lib/fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
/// ------------------------
include("fphp.php");
connect();
extract($_POST);
extract($_GET);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link href="css1.css" rel="stylesheet" type="text/css" />-->
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript01.js"></script>
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
		<td class="titulo">Generaci&oacute;n de Vouchers</td>
		<td align="right"><a class="cerrar" href="javascript:" onclick="javascript:window.close();">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<? 

if($cont=='F')$hacer = "generarVoucherBajaPub20";
elseif($cont=='T')$hacer = "generarVoucherBajaOnco";




list($organismo, $activo, $codtransaccionbaja) = split("[|]", $registro);
 
  

$sql_a = "select * from af_activo where Activo='$activo' and CodOrganismo='$organismo'"; ///echo $sql_a;
$qry_a = mysql_query($sql_a) or die ($sql_a.mysql_error());
$row_a = mysql_num_rows($qry_a);
if($row_a!=0) $field_a = mysql_fetch_array($qry_a);

/// consulto tabla af_activodistribcontable y ac_contabilidades
$sql_b = "select PrefVoucherPD FROM mastaplicaciones WHERE CodAplicacion='AF'"; //echo $sql_b;
$qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error());
$row_b = mysql_num_rows($qry_b);
if($row_b!=0)$field_b = mysql_fetch_array($qry_b);

$sql_g = "select ObligacionTipoDocumento, ObligacionNroDocumento from lg_activofijo where Activo='$activo'";
$qry_g = mysql_query($sql_g) or die ($sql_g.mysql_error());
$row_g = mysql_num_rows($qry_g);
if($row_g!=0)$field_g = mysql_fetch_array($qry_g);

?>

<form name="frmentrada" id="frmentrada" method="POST" onsubmit="return generarVoucherBaja(this, '<?=$hacer;?>');">
<input type='text' id='hacer' name='hacer' value='<?=$hacer;?>'/>
<input type="hidden" id="activo"  name="activo"  value="<?=$activo;?>" />
<input type="hidden" id="CodOrganismo" name="CodOrganismo"  value="<?=$organismo;?>" />
<input type="hidden" id="codtransaccionbaja"  name="codtransaccionbaja" value="<?=$codtransaccionbaja;?>" />

<input type="hidden" id="CodVoucher" name="CodVoucher" value="FA" />
<input type="hidden" id="CodContabilidad" name="CodContabilidad" value="F" />
<input type="hidden" id="Periodo" name="Periodo" value="<?=date("Y-m");?>" />
<input type="hidden" id="centro_costo" name="centro_costo" value="<?=$field_a['CentroCosto'];?>" />



<table align="center">
<tr>
<td valign="top">
<table width="400" class="tblBotones">
    <tr><td align="right">&nbsp;</td></tr>
</table>

<table><tr><td><div style="overflow:scroll; width:400px; height:125px;">
<table width="400" class="tblLista">
    <thead>
    <tr>
        <th width="75" scope="col">Periodo</th>
        <th width="75" scope="col">Voucher</th>
        <th width="75" scope="col">Fecha</th>
        <th width="75" scope="col">Status</th>
        <th scope="col">Organismo</th>
    </tr>
    </thead>
    
    <tbody id="lista1">
    </tbody>
    
    <tfoot id="foot1">
    <tr><td colspan="5">&nbsp;</td></tr>
    <tr>
        <th scope="col" colspan="4">&nbsp;</th>
        <th scope="col" align="right">&nbsp;</th>
    </tr>
    </tfoot>
</table>
</div></td></tr></table>
</td>

<td valign="top">
<table width="550" class="tblBotones">
    <tr><td align="right">&nbsp;</td></tr>
</table>

<table><tr><td><div style="overflow:scroll; width:550px; height:125px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th width="50" scope="col">Linea</th>
        <th scope="col">Errores Encontrados</th>
        <th width="75" scope="col">Periodo</th>
        <th width="75" scope="col">Voucher</th>
        <th width="75" scope="col">Organismo</th>
    </tr>
    </thead>
    
    <!--<tbody id="lista_errores">-->
     <tbody>
    </tbody>
    
    <tfoot id="foot_errores">
        <tr><td colspan="5">&nbsp;</td></tr>
        <tr>
            <th scope="col" colspan="4">&nbsp;</th>
            <th scope="col">&nbsp;</th>
        </tr>
    </tfoot>
</table>
</div></td></tr></table>
</td>
</tr>
<?
 $s_trans = "select * from af_transaccionbaja where Organismo='$organismo' and Activo='$activo' and  CodTransaccionBaja='$codtransaccionbaja'";
 $q_trans = mysql_query($s_trans) or die ($s_trans.mysql_error());
 $r_trans = mysql_num_rows($q_trans);
 if($r_trans != 0) $f_trans = mysql_fetch_array($q_trans);
 

?>
<tr>
<td colspan="2">
<table width="960" class="tblForm">
    <input type="text" id="Documento" name="Documento" value="<?=$f_trans['TipoTransaccion'].'-'.$f_trans['CodTransaccionBaja'];?>">
    <tr>
        <td class="tagForm" width="125">* Organismo:</td>
        <td> <? //echo"Organismo=".$organismo; ?>
            <select id="CodOrganismo" style="width:300px;" disabled>
                <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $organismo, 1)?>
            </select>
        </td>
        <td class="tagForm">Descripci&oacute;n:</td>
        <td><input type="text" id="ComentariosVoucher" style="width:297px;" value="<?=($f_trans['Comentario'])?>" disabled /></td>
    </tr>
    <tr>
        <td class="tagForm">* Fecha:</td>
        <td><!--<input type="text" id="FechaVoucher" value="<?=formatFechaDMA($field_mast['FechaPago'])?>" style="width:75px;" disabled />--><input type="text" id="FechaVoucher" value="<?=date("d-m-Y");?>" style="width:75px;" disabled /></td>
        <td class="tagForm">Preparado Por:</td>
        <td>
            <input type="hidden" id="PreparadoPor" value="<?=$_SESSION['CODPERSONA_ACTUAL']?>" />
            <input type="text" style="width:297px;" value="<?=$_SESSION['NOMBRE_USUARIO_ACTUAL']?>" disabled />
        </td>
    </tr>
    <tr>
        <td class="tagForm">Voucher:</td>
        <td> <? echo $field_b['CodVoucher'];?>
            <select id="CodVoucher" <?=$disabled_ver?>>
                <?=loadSelect("mastaplicaciones", "PrefVoucherPD", "PrefVoucherPD", $field_b['PrefVoucherPD'], 1)?>
            </select>
            <input type="text" id="NroVoucher" style="width:50px;" disabled="disabled" />
        </td>
        <td class="tagForm">Aprobado Por:</td>
        <td>
            <input type="hidden" id="AprobadoPor" value="<?=$_SESSION['CODPERSONA_ACTUAL']?>" />
            <input type="text" style="width:297px;" value="<?=$_SESSION['NOMBRE_USUARIO_ACTUAL']?>" disabled />
        </td>
    </tr>
    <tr>
         <td class="tagForm">* Libro Contable:</td>
        <td>
            <select id="CodLibroCont" style="width:150px;" disabled>
                <?=loadSelect("ac_librocontable", "CodLibroCont", "Descripcion", "", 0)?>
            </select>
        </td>
      <td class="tagForm">* Contabilidad:</td>
        <td>
          <select id='Contabilidad' name='Contabilidad' style="width:150px;" disabled>
             <?=getContabilidad( $cont, 0);?>
          </select>
      </td>
    </tr>
    <tr>
       
    </tr>
    
</table>
</td>
</tr>

<tr>
<td valign="top" colspan="2">
<table width="960" class="tblBotones">
    <tr>
        <td align="right">
            <input type="submit" class="btLista" value="Generar" id="btGenerar" />
            <input type="button" class="btLista" value="Rechazar" onclick="javascript:window.close();" />
        </td>
    </tr>
</table>

<table><tr><td><div style="overflow:scroll; width:960px; height:225px;">
<table width="1100" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="30">#</th>
        <th scope="col" width="110">Cuenta</th>
        <th scope="col">Descripci&oacute;n</th>
        <th scope="col" width="125">Monto</th>
        <th scope="col" width="200">Persona</th>
        <th scope="col" width="100">Documento</th>
        <th scope="col" width="90">C.Costo</th>
        <th scope="col" width="75">Fecha</th>
    </tr>
    </thead>
    
    <tbody>
<?php
$sql_c = "select 
				a.*,
				b.TipoTransaccion,
				b.Responsable,
				c.Descripcion as DescpCentroCosto,
				d.NomCompleto
		    from 
			    af_transaccionbajacuenta a
				inner join af_transaccionbaja b on (b.Activo=a.Activo) and (b.CodTransaccionBaja=a.CodTransaccionBaja)
				inner join ac_mastcentrocosto c on (c.CodCentroCosto=b.CentroCosto) 
				inner join mastpersonas d on (d.CodPersona=b.Responsable)
		   where 
		        a.Activo = '$activo' and 
				a.CodTransaccionBaja='$codtransaccionbaja' and 
				a.Contabilidad='F'";  //echo $sql_c;
$qry_c = mysql_query($sql_c) or die ($sql_c.mysql_error());
$row_c = mysql_num_rows($qry_c);

$secuencia = 0;
if($row_c!=0){
	for($i=0; $i<$row_c; $i++){
	   $field_c = mysql_fetch_array($qry_c);
	   
	   $sql_d = "select Descripcion from ac_mastplancuenta20 where CodCuenta='".$field_c['CuentaContable']."'";
	   $qry_d = mysql_query($sql_d) or die ($sql_d.mysql_error());
	   $row_d = mysql_num_rows($qry_d);
	   if($row_d!=0)$field_d = mysql_fetch_array($qry_d);
	   
	   //if($field_c['Contabilidad']=='F') $sql_d = "select Descripcion from ac_mastplancuenta20 where CodCuenta='".$field_c['CuentaContable']."'";
	   //else $sql_d = "select Descripcion from ac_mastplancuenta where CodCuenta='".$field_c['CuentaContable']."'";
	   
	   //echo $sql_d;
	   	   
	   $sql_e = "select * from lg_activofijo where Activo='".$field_c['Activo']."'";
	   $qry_e = mysql_query($sql_e) or die ($sql_e.mysql_error());
	   $row_e = mysql_num_rows($qry_e);
	   if($row_e!=0) $field_e = mysql_fetch_array($qry_e);
	   
	   $sql_f = "select 
	                    Secuencia,TipoTransaccion, Contabilidad, SignoFlag 
				   from 
				        af_tipotranscuenta 
				  where 
				        TipoTransaccion = '".$field_c['TipoTransaccion']."' and 
						Secuencia > '$secuencia'"; //echo $sql_f;
	   $qry_f = mysql_query($sql_f)  or die ($sql_f.mysql_error());
	   $row_f = mysql_num_rows($qry_f);
	   if($row_f!=0){
		 $field_f = mysql_fetch_array($qry_f); //echo "Signo=".$field_f['SignoFlag'].'*';
		 if($field_f['SignoFlag']=="-"){
		    $Debitos+= $field_c['MontoLocal'];
			$style = 'color:red';
		 }else{ 
		    $Creditos+= $field_c['MontoLocal'];
			$style = 'color:black';
		 }
		 
		 $secuencia = $field_f['Secuencia'];
	   }					   
?>
<tr class="trListaBody">
<td><input type="text" name="Linea" value="<?=++$Linea?>" class="cell2" style="text-align:center;" readonly /></td>
<td><input type="text" name="CodCuenta" value="<?=$field_c['CuentaContable']?>" class="cell2" style="text-align:center;" readonly/></td>
<td><input type="hidden" name="Descripcion" value="<?=$field_d['Descripcion']?>" />
    <input type="text" value="<?=$field_d['Descripcion'];?>" class="cell2" readonly /></td>
<td><input type="text" name="MontoVoucher" value="<?=number_format($field_c['MontoLocal'], 2, ',', '.')?>" class="cell2" style="text-align:right; <?=$style?>" readonly/></td>
<td><input type="text" name="CodPersona" value="<?=$field_c['NomCompleto']?>" class="cell2" style="text-align:center;" readonly /></td>
<td>
    <input type="text" name="ReferenciaTipoDocumento" value="<?=$f_trans['TipoTransaccion'].'-'.$f_trans['CodTransaccionBaja'];?>" class="cell2" style="text-align:center;" readonly />
    <!--<input type="text" name="ReferenciaTipoDocumento" value="FP" class="cell2" style="width:20px;text-align:center;" readonly /> 
    <input type="text" name="ReferenciaNroDocumento" value="<?=$field_e['NroDocumento']?>" class="cell2" style="width:120px;text-align:center;" readonly/>-->
</td>
<td>
    <!--<input type="text" name="CodCentroCosto" value="<?=$_PARAMETRO['CCOSTOVOUCHER']?>" class="cell2" style="text-align:center;" readonly />-->
    <input type="text" name="CodCentroCosto" value="<?=$field_c['DescpCentroCosto']?>" class="cell2" style="text-align:center;" readonly />
</td>
<td>
    <input type="text" name="FechaVoucher" value="<?=formatFechaDMA($field_c['Fecha'])?>" class="cell2" style="width:75px; text-align:center;" readonly />
</td>
</tr>
    <?
}}
?>
</tbody>
</table>
</div></td></tr></table>

<table>
<tr>
	<th scope="col" width="140">Nro Lineas: <input type="text" id="Lineas" value="<?=$Linea?>" class="cell2" style="text-align:center; font-weight:bold; font-size:12px; width:20px;" readonly /></th>
	<th scope="col" width="75">&nbsp;</th>
	<th scope="col" width="150">&nbsp;</th>
	<th scope="col" width="75">Total:</th>
	<th scope="col" width="125">
		<input type="text" id="Creditos" value="<?=number_format($Creditos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px;" readonly />
	</th>
	<th scope="col" width="125">
		<input type="text" id="Debitos" value="<?=number_format($Debitos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px; color:red;" readonly />
	</th>
	<th scope="col" width="125">&nbsp;</th>
</tr>
</table>

</td>
</tr>
</table>
</form>

<?php
if ($opcion != "ver") {
	?>
    <!-- JS	-->
    <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        validarErroresVoucher();
    });
    </script>
    <?
}
?>