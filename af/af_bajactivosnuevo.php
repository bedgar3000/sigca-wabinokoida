<?php
// ------------------------------------- ####
include("../lib/fphp.php");
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
<!--<link href="css1.css" rel="stylesheet" type="text/css" />
<link href="css2.css" rel="stylesheet" type="text/css" />-->
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

<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Baja de Activos | Nueva Transacci&oacute;n</td>
		<td align="right"><a class="cerrar" href="<?=$regresar?>.php" onclick="window.close();">[cerrar]</a></td>
	</tr>
</table>
<hr width="100%" color="#333333" />
<form id="frmentrada" name="frmentrada" action="<?=$regresar?>.php?limit=0"  onsubmit="return guardarTransaccionBaja(this,'Nuevo');"> 
<? echo"
       <input type='hidden' id='registro' name='registro' value='".$registro."'/>
	   <input type='hidden' id='fOrganismo' name='fOrganismo' value='".$fOrganismo."'/>
	   <input type='hidden' id='fDependencia' name='fDependencia' value='".$fDependencia."'/>
	   <input type='hidden' id='fContabilidad' name='fContabilidad' value='".$fContabilidad."'/>
	   <input type='hidden' id='fActivo' name='fActivo' value='".$fActivo."'/>
	   <input type='hidden' id='fPeriodo' name='fPeriodo' value='".$fPeriodo."'/>
	   <input type='hidden' id='fFecha' name='fFecha' value='".$fFecha."'/>
	   <input type='hidden' id='fEstado' name='fEstado' value='".$fEstado."'/>";

      $s_pre = "select * from usuarios where Usuario='".$_SESSION['USUARIO_ACTUAL']."'";
      $q_pre = mysql_query($s_pre) or die ($s_pre.mysql_error());
      $r_pre = mysql_num_rows($q_pre);
      if($r_pre!=0)$f_pre = mysql_fetch_array($q_pre) ;
      echo "<input type='hidden' name='prepor' id='prepor' value='".$f_pre['CodPersona']."' />";

?>
<table width="908" align="center">
<tr>
  <td>
	<div id="header">
	<ul>
	<!-- CSS Tabs PESTA�AS OPCIONES -->
	<li><a onClick="document.getElementById('tab1').style.display='block';" href="#">Transacci&oacute;n</a></li>
  
	</ul>
	</div>
  </td>
</tr>
</table>
<? echo" <input type='hidden' id='regresar' name='regresar' value='".$_GET['regresar']."' />
         <input type='hidden' id='activo' name='activo' value='' />";?>
<!-- ****************************************************** COMIENZO TAB1 ************************************************ -->
<div id="tab1" style="display: block;">
<div style="width:950px; height=15px;" class="divFormCaption">Informaci&oacute;n General</div>
<table class="tblForm" width="950">
<tr>
  <td width="109" height="5"></td>
</tr>
<tr>
   <td class="tagForm">Activo #:</td>
   <td width="360" class="gallery clearfix">
       <input type="text" id="nro_activo" name="nro_activo" size="9" style="text-align:center;" readonly/>
       <input type="text" name="descripcion" id="descripcion" size="45" readonly/>
       <input type="hidden" id="btActivo" name="btActivo" value="..." onclick="cargarVentanaLista(this.form, 'af_selectoractivos.php?limit=0&campo=2&ventana=insertarActivo','height=550, width=900, left=200, top=100, resizable=yes');"/> <a id="selector_activo" href="af_selectoractivos.php?filtrar=default&limit=0&campo=2&ventana=insertarActivo&iframe=true&width=77%&height=100%" rel="prettyPhoto[iframe1]">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;"/>
            </a></td>
   <td width="102" class="tagForm">Organismo:</td>
   <td width="309"><input type="hidden" name="codorganismo" id="codorganismo"/><input type="text" id="organismo" name="organismo" size="60" readonly/></td>
</tr>
<tr>
<input type="hidden" id="seldetalle3" />
<input type="hidden" id="candetalle3" />
<? echo"<input type='hidden' id='inform' name='inform' value=''/>
        <input type='hidden' id='monto' name='monto' value='$monto'/>";?>
  <td class="tagForm">Tipo Baja:</td>
  <td><select id="tipobaja" name="tipobaja" style="width:312px;" onchange="CargarInfoBajaActivos(this.form, this.id, 'insertarDatos_2')|ActivarTable(this.form, this.id);">
       <option value=""></option>
       <?
         $st = "select * from af_tipotransaccion where FlagAltaBaja='B'"; //echo $st;
		 $qt = mysql_query($st) or die ($st.mysql_error());
		 $rt = mysql_num_rows($qt);
		 if($rt!=0) 
		 for($i=0; $i<$rt; $i++){
			 $ft= mysql_fetch_array($qt);
			 if($_GET['tipobaja']==$ft['TipoTransaccion'])echo" <option value='".$ft['TipoTransaccion']."' selected>".$ft['Descripcion']."</option>";
			 else echo" <option value='".$ft['TipoTransaccion']."'>".$ft['Descripcion']."</option>";		 
		 }
	   ?>
      </select></td>
  <td class="tagForm">Dependencia:</td>
  <td><input type="text" id="dpendencia" name="dpendencia" size="60" readonly/>
      <input type="hidden" id="coddependencia" name="coddependencia"/></td>
</tr>
<tr>
  <td class="tagForm">Fecha:</td>
  <td><input type="text" id="f_actual" name="f_actual" size="10" value="<?=date("d-m-Y");?>" maxlength="10" disabled style="text-align:center"/> Nro. Factura:<input type="text" id="nrofactura" name="nrofactura" disabled/></td>
  <td class="tagForm">Centro Costo:</td>
  <td><input type="hidden" id="codcentrocosto" name="codcentrocosto"/><input type="text" id="centrocosto" name="centrocosto" size="60" readonly/></td>
</tr>

<tr>
  <td class="tagForm">Fecha Baja:</td>
  <td><input type="text" id="f_baja" name="f_baja" size="10" value="<?=date("d-m-Y");?>" maxlength="10" style="text-align:center"/></td>
  <td class="tagForm">Responsable:</td>
  <td><input type="hidden" id="codresponsable" name="codresponsable"/><input type="text" id="responsable" name="responsable" size="60"/></td>
</tr>

<tr>
  <td class="tagForm"></td>
  <td><input type="checkbox" id="flagContabilizado" name="flagContabilizado"/>Contabilizado</td>
  <td class="tagForm"></td>
  <td></td>
</tr>
<tr>
  <td class="tagForm">Nro. Documento:</td>
  <td><input type="text" id="nro_documento" name="nro_documento" style="text-align:right;" size="28"/></td>
</tr>
<tr>
  <td class="tagForm">Voucher Baja:</td>
  <td><input type="text" name="periodoVoucher" id="periodoVoucher" size="10" disabled value="<?=date("Y-m");?>"/> <input type="text" id="codVoucher" name="codvoucher" size="10" disabled/></td>
  <td class="tagForm">Valor Activo:</td>
  <td><input type="text" id="monto_local" name="monto_local" size="15" style="text-align:right" readonly/> <b>BsF.</b></td>
</tr>
<!--<tr>
 <td class="tagForm">Valor Baja:</td>
 <td><input type="text" id="valorBaja" name="valorBaja" /></td>
 <td class="tagForm"></td>
 <td></td>
</tr>-->
</table>

<table class="tblForm" width="950">
<tr>
  <td width="154" height="5"></td>
</tr>
<tr>
	 <td class="tagForm">Tipo Movimiento:</td>
	 <td><input type="hidden" name="radioEstado" id="radioEstado" value="I"/><input type='radio' id='radio1' name='radio1' onclick="estadosPosee02(this.form)|selectMotMovimiento(this.form);" disabled/>Interno
	     <input type='radio' id='radio2' name='radio2' onclick="estadosPosee02(this.form)|selectMotMovimiento(this.form);" checked />Externo</td>
   <td class="tagForm">Motivo Traslado:</td>
	 <td align='left' colspan='2'><select id="motivoTrasladoInterno" name="motivoTrasladoInterno" class="selectMed" style="display:none;" disabled>
                                  <?
                                    $s_movint = "select * from mastmiscelaneosdet where CodMaestro='MMOVINTER'";
									$q_movint = mysql_query($s_movint) or die ($s_movint.mysql_error());
									$r_movint = mysql_num_rows($q_movint);
									
									for($i=0; $i<$r_movint; $i++){
										$f_movint = mysql_fetch_array($q_movint);
										echo"<option value='".$f_movint['CodDetalle']."'>".$f_movint['Descripcion']."</option>";
									}
								  ?>
                                  </select>
                                  <select id="motivoTrasladoExterno" name="motivoTrasladoExterno" class="selectMed" style="display:block;">
                                  <?
                                    $s_movext = "select * from mastmiscelaneosdet where CodMaestro='MMOVEXTER'";
									$q_movext = mysql_query($s_movext) or die ($s_movext.mysql_error());
									$r_movext = mysql_num_rows($q_movext);
									
									for($i=0;$i<$r_movext; $i++){
										$f_movext = mysql_fetch_array($q_movext);
										echo"<option value='".$f_movext['CodDetalle']."'>".$f_movext['Descripcion']."</option>";
									}
								  ?>
                                  </select></td>
</tr>
<tr>
  <td class="tagForm">Concep. de Movimiento:</td>
   <td colspan="2">
       <select id="conceptoMovimiento" name="conceptoMovimiento" class="selectBig">
        <? $s_cm = "select * from af_tipomovimientos where TipoMovimiento='DE' and FlagDesincorporacion='S'";
           $q_cm = mysql_query($s_cm) or die ($s_cm.mysql_error());
           $r_cm = mysql_num_rows($q_cm);
          
           if($r_cm!='0'){
		    for($i=0;$i<$r_cm;$i++){
               $f_cm = mysql_fetch_array($q_cm);
			     echo"<option value='".$f_cm['CodTipoMovimiento']."'>".$f_cm['DescpMovimiento']."</option>";
            }
		   }
        ?>         
       </select></td>  
</tr>
<tr>
   <td class="tagForm">C&oacute;digo Interno:</td>
   <td width="321"><input type="text" id="codigo_interno" name="codigo_interno" size="15" style="text-align:right"  maxlength="10" readonly/></td>
   <td class="tagForm">Categor&iacute;a:</td>
   <td><input type="text" id="categoria" name="categoria" size="60" readonly/><input type="hidden" name="codcategoria" id="codcategoria"/></td>
</tr>
<tr>
   <td class="tagForm">Estado:</td>
   <td><input type="text" value="Preparaci&oacute;n" id="pr" name="Preparacion" readonly size="15"/></td>
   <td class="tagForm">Ubicaci&oacute;n:</td>
   <td class="gallery clearfix">
       <?php 
          $s= "select * from af_ubicaciones where CodUbicacion='0501' ";
          $q= mysql_query($s) or die ($s.mysql_error());
          $f= mysql_fetch_array($q);
       ?>
       <input type="hidden" name="codubicacion" id="codubicacion" value="<?=$f['CodUbicacion'];?>" disabled/>
       <input type="text" name="ubicacion" id="ubicacion" size="60" value="<?=$f['Descripcion'];?>" readonly/>
       <a id="c_costo" href="af_selectorubicacion_activo.php?filtrar=default&limit=0&campo=1&cod=codubicacion&nom=ubicacion&ventana=selListadoLista03&iframe=true&width=70%&height=100%" rel="prettyPhoto[iframe2]" style="visibility:<?=$valor3;?>">
              <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;"/>
            </a>
    </td>
</tr>
<tr>
 <td></td>
 <td></td>
 <td class="tagForm"></td>
 <td></td>
</tr>

<tr>
   <td class="tagForm">Aprobado Por:</td>
   <td><input type="text" id="aprobadopor" name="aprobadopor" size="60" disabled/></td>
</tr>
<tr>
 <td class="tagForm">Comentario:</td>
 <td colspan="3"><textarea id="comentario" name="comentario" style="width:319px;"></textarea></td>
</tr>
<tr>
 <td height="6"></td>
</tr>
<? 
if((!$_GET)or(!$_POST)) $visible = 'style="visibility:hidden"';
else $visible=strtr($visible, "\"", "");

$s_usuario = "select 
				   mp.NomCompleto,
				   mp.CodPersona 
			  from 
				   usuarios u 
				   inner join mastpersonas mp on (mp.CodPersona = u.CodPersona)
			 where 
				   u.Usuario = '".$_SESSION['USUARIO_ACTUAL']."'";
$q_usuario = mysql_query($s_usuario) or die ($s_usuario.mysql_error());
$f_usuario = mysql_fetch_array($q_usuario);
echo"<input='hidden' id='cod_prepor' nanme='cod_prepor' value='".$f_usuario['CodPersona']."'/>";					 
?>
<tr><td align="center" colspan="5">Ultima Modif.:<input type="text" name="ultimo_usuario" value="" size="25" readonly/> <input type="text" name="ultima_fecha" value="" size="20" readonly/></td></tr>
<tr><td class="tagForm">Distribuci&oacute;n Contable</td></tr>
</table>
<table class="tblForm" width="950">
<tr>
<td>
   <table width="800" id="mostrar" name="mostrar" style="visibility:hidden" class="tblLista">
   <thead>
   <tr class="trListaHead">
        <th scope="col" width="15">#</th>
        <th scope="col" width="75">Cuenta</th>
        <th width="200" scope="col">Descripci&oacute;n</th>
        <th scope="col" width="50">Local</th>
   </tr>
   </thead>
   
   <tr><td colspan="4"><div id="scrool" style="display:none;overflow:scroll; width:850px; height:150px;">
   <div id="resultados" name="resultados" style="width:840px">
   </div></div>
   </td></tr>
   </table>
   
</td>
</tr>
</table>
</div>
<!-- ****************************************************** COMIENZO TAB2 ************************************************ -->
<div id="tab2" style="display: none;">
</div>
<!-- ****************************************************** FIN TAB ****************************************************** -->
<center><input type="submit" name="btGuardar" id="btGuardar" value="Guardar Registro"/><input type="button" name="btCancelar" id="btCancelar" value="Cancelar" onclick="cargarPagina(this.form,'<?=$regresar?>.php?limit=0');"/></center>
</form>
