<?php 
// ------------------------------------- ####
include("../lib/fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
/// ------------------------
include("fphp.php");
connect();
extract($_POST);
extract($_GET);
if ($opcion == "ver") {
	$disabled_ver = "disabled";
	$display_ver = "display:none;";
}else{
 if ($opcion == "generar") {
	$disabled_ver = "disabled";
	$display_ver = "display:none;";
}
}
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
//	consulto datos generales de la transaccion
/*list($tb, $otro)=split('[?,=]', $tb);	*/
 //echo "TB=".$tab;



if($tab!='tb'){
 list($organismo, $activo) = split("[-]", $registro);
 $tabla_01 = 'af_activodistribcontable';
}else{
  list($organismo, $activo, $codtransaccionbaja) = split("[|]", $registro);
  $tabla_01 = 'af_transaccionbajacuenta';
  $valor_01 = "and CodTransaccionBaja='$codtransaccionbaja'";
}

$sql_a = "select * from af_activo where Activo='$activo' and CodOrganismo='$organismo'"; ///echo $sql_a;
$qry_a = mysql_query($sql_a) or die ($sql_a.mysql_error());
$row_a = mysql_num_rows($qry_a);
if($row_a!=0) $field_a = mysql_fetch_array($qry_a);

/// consulto tabla af_activodistribcontable y ac_contabilidades
$sql_b = "select 
                 a.*,
				 b.Contabilidad as ContActivo,
				 (SELECT PrefVoucherPD FROM mastaplicaciones WHERE CodAplicacion='AF') AS PrefVoucherPD,
				 (SELECT CodPersona FROM usuarios WHERE Usuario='".$_SESSION['USUARIO_ACTUAL']."') AS CodPersonaUsuario
			from
			     af_activo a
			     inner join af_activodistribcontable b on (b.Activo = a.Activo)
				 inner join ac_contabilidades c on (c.CodContabilidad = b.Contabilidad) 
		   where 
		         a.Activo='$activo' and 
				 a.CodOrganismo='$organismo'"; //echo $sql_b;
$qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error());
$row_b = mysql_num_rows($qry_b);
if($row_b!=0)$field_b = mysql_fetch_array($qry_b);

$sql_g = "select ObligacionTipoDocumento, ObligacionNroDocumento from lg_activofijo where Activo='$activo'";
$qry_g = mysql_query($sql_g) or die ($sql_g.mysql_error());
$row_g = mysql_num_rows($qry_g);
if($row_g!=0)$field_g = mysql_fetch_array($qry_g);
?>
<form name="frmentrada" id="frmentrada" method="POST" onsubmit="return generarVoucherIngActivo(this, 'generarVoucherIngActivo');">
<input type="hidden" id="NroProceso" name="NroProceso" value="<?=$NroProceso?>" />
<input type="hidden" id="activo" name="activo" value="<?=$field_b['Activo'];?>" />
<input type="hidden" id="codorganismo"  name="codorganismo" value="<?=$field_b['CodOrganismo'];?>" />
<input type="hidden" id="periodoingreso"  name="periodoingreso" value="<?=$field_b['PeriodoIngreso'];?>" />
<input type="hidden" id="periodo"  name="periodo" value="<?=date("Y-m");?>" />
<input type="hidden" id="registro" name="registro" value="<?=$registro;?>" />
<input type="hidden" id="CodPersona"  name="CodPersona" value="<?=$field_b['CodPersona'];?>" />
<input type="hidden" id="FechaIngreso" name="FechaIngreso" value="<?=$field_b['FechaIngreso'];?>" />
<input type="hidden" id="CentroCosto" name="CentroCosto" value="<?=$field_b['CentroCosto'];?>"/>
<input type="hidden" id="ObligacionTipoDocumento"  name="ObligacionTipoDocumento" value="<?=$field_g['ObligacionTipoDocumento'];?>" />
<input type="hidden" id="ObligacionNroDocumento" name="ObligacionNroDocumento" value="<?=$field_g['ObligacionNroDocumento'];?>"/>
<input type="hidden" id="dependencia" name="dependencia" value="<?=$field_b['CodDependencia'];?>"/>
<input type="hidden" id="ContActivo" name="ContActivo" value="<?=$field_b['ContActivo'];?>"/>
<input type="hidden" id="facturafecha" name="facturafecha" value="<?=$field_b['FacturaFecha'];?>"/>

<?
  $s_usu = "select * from usuarios where Usuario='".$_SESSION['USUARIO_ACTUAL']."'";
  $q_usu = mysql_query($s_usu) or die ($s_usu.mysql_error());
  $r_usu = mysql_num_rows($q_usu); if($q_usu!=0) $f_usu=mysql_fetch_array($q_usu);
  
  $s_dep = "select 
  				   CodDependencia 
			  from 
			       rh_empleadonivelacion 
			 where 
			       Secuencia = (select max(Secuencia) from rh_empleadonivelacion where CodPersona='".$f_usu['CodPersona']."') and  
			       CodPersona = '".$f_usu['CodPersona']."'";
  $q_dep = mysql_query($s_dep) or die ($s_dep.mysql_error());
  $r_dep = mysql_num_rows($q_dep); if($r_dep!=0)$f_dep= mysql_fetch_array($q_dep);

  echo" <input type='hidden' id='dep_usuario' name='dep_usuario' value='".$f_dep['CodDependencia']."'/>";  
?>

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
    
    <tr>
    	<td colspan="2">
            <table width="960" class="tblForm">
                <tr>
                    <td class="tagForm" width="125">* Organismo:</td>
                    <td> <? //echo"Organismo=".$organismo; ?>
                        <select id="CodOrganismo" style="width:300px;" <?=$disabled_ver;?>>
                            <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $organismo, 1)?>
                        </select>
                    </td>
                    <td class="tagForm">Descripci&oacute;n:</td>
                    <td><input type="text" id="ComentariosVoucher" style="width:297px;" value="<?=($field_b['Descripcion'])?>" <?=$disabled_ver?> /></td>
                </tr>
                <tr>
                    <td class="tagForm">* Fecha:</td>
                    <? list($a, $m, $d) = split('[-]', $field_b['FechaIngreso']);
					   $fecha = $d.'-'.$m.'-'.$a; 
					?>
                    <td><!--<input type="text" id="FechaVoucher" value="<?=formatFechaDMA($field_mast['FechaPago'])?>" style="width:75px;" disabled />--><input type="text" id="FechaVoucher" name="FechaVoucher" value="<?=$fecha;?>" style="width:75px;" disabled /></td>
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
                    <td><?
                         $sa = "select 
						 				a.*,
										b.NomCompleto
								  from 
								  		mastdependencias a 
										inner join mastpersonas b on (b.CodPersona=a.CodPersona) 
								 where 
								 	  a.CodDependencia='0009'" ;
						 $qa = mysql_query($sa) or die ($sa.mysql_error());
						 $ra = mysql_num_rows($qa); if($ra != 0) $fa = mysql_fetch_array($qa);
					
					    ?>
                        <input type="hidden" id="AprobadoPor" value="<?=$fa['CodPersona'];?>" />
                        <input type="text" style="width:297px;" value="<?=$fa['NomCompleto'];?>" disabled />
                    </td>
                </tr>
                <tr>
                     <td class="tagForm">* Libro Contable:</td>
                    <td>
                        <select id="CodLibroCont" style="width:150px;" <?=$disabled_ver?>>
                            <?=loadSelect("ac_librocontable", "CodLibroCont", "Descripcion", "", 0)?>
                        </select>
                    </td>
                  <td class="tagForm">* Contabilidad:</td>
                    <td>
                      <select id='Contabilidad' name='Contabilidad' style="width:150px;" <?=$disabled_ver?>>
	                     <?=getContabilidad('F',0);?>
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
                    <th scope="col" width="75">Persona</th>
                    <th scope="col" width="150">Documento</th>
                    <th scope="col" width="75">C.Costo</th>
                    <th scope="col" width="75">Fecha</th>
                </tr>
                </thead>
                
                <tbody>
<?php
$sql_c = "select * from $tabla_01 where Activo = '$activo' $valor_01"; 
$qry_c = mysql_query($sql_c) or die ($sql_c.mysql_error());
$row_c = mysql_num_rows($qry_c);

$secuencia = 0;
if($row_c!=0){
	for($i=0; $i<$row_c; $i++){
	   $field_c = mysql_fetch_array($qry_c);
	   if($field_c['Contabilidad']=='F') $sql_d = "select Descripcion from ac_mastplancuenta20 where CodCuenta='".$field_c['CuentaContable']."'";
	   else $sql_d = "select Descripcion from ac_mastplancuenta where CodCuenta='".$field_c['CuentaContable']."'";
	   
	   //echo $sql_d;
	   $qry_d = mysql_query($sql_d) or die ($sql_d.mysql_error());
	   $row_d = mysql_num_rows($qry_d);
	   if($row_d!=0)$field_d = mysql_fetch_array($qry_d);
	   
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
		    $Debitos+= $field_c['Monto'];
			$style = 'color:red';
		 }else{ 
		    $Creditos+= $field_c['Monto'];
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
<td><input type="text" name="MontoVoucher" value="<?=number_format($field_c['Monto'], 2, ',', '.')?>" class="cell2" style="text-align:right; <?=$style?>" readonly/></td>
<td><input type="text" name="CodPersona" value="<?=$field_b['CodProveedor']?>" class="cell2" style="text-align:center;" readonly /></td>
<td>
    <input type="text" name="ReferenciaTipoDocumento" value="FP" class="cell2" style="width:20px;" readonly /> 
    <input type="text" name="ReferenciaNroDocumento" value="<?=$field_e['NroDocumento']?>" class="cell2" style="width:120px;text-align:center;" readonly/>
</td>
<td>
    <!--<input type="text" name="CodCentroCosto" value="<?=$_PARAMETRO['CCOSTOVOUCHER']?>" class="cell2" style="text-align:center;" readonly />-->
    <input type="text" name="CodCentroCosto" value="<?=$field_b['CentroCosto']?>" class="cell2" style="text-align:center;" readonly />
</td>
<td>
    <input type="text" name="FechaVoucher" value="<?=formatFechaDMA($field_b['FechaIngreso'])?>" class="cell2" style="width:75px; text-align:center;" readonly />
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