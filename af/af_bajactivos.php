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
<?
$s = "select * from usuarios where Usuario='".$_SESSION['USUARIO_ACTUAL']."'";
$q = mysql_query($s) or die ($s.mysql_error());
$f = mysql_fetch_array($q);
echo "<input type='hidden' id='usuario_actual' name='usuario_actual' value='".$f['CodPersona']."'/>";



if($accion=='ver'){
  $activado = "disabled"; 
  $botonAccion = "hidden";
  $onclick = "window.close();";
  $valor02 = "Guardar Registro";
  $valor03 = "disabled";
  $valor04 = "readonly";
  $regresar = "";
  $titulo = "Ver Transacción";
  $div = "block";
  $table_style = "visible";
  $selector = "hidden";
 
}elseif($accion=="modificar"){

  $valor01 = "";
  $valor02 = "Modificar";
  $valor03 = "";
  $valor04 = "";
  $regresar_1= "cargarPagina(this.form,'af_bajactivoslistar.php?limit=0');";
  $titulo = "Modificar Transacción";
  $accion = "Modificar";

}elseif($accion=="revisar"){
  $activado = "disabled";	
  $valor01 = "";
  $valor02 = "Revisar";
  $valor03 = "disabled";
  $valor04 = "readonly";
  $regresar_1 = "window.close();" ;
  $titulo = "Revisar Transacción";
  $selector = "hidden";
  $accion = "Revisar";
  
}elseif($accion=="aprobar"){
  $activado = "disabled";
  $valor01 = "";
  $valor02 = "Aprobar";
  $valor03 = "disabled";
  $valor04 = "readonly";
  $regresar_1 = "window.close();";
  $titulo = "Aprobar Transacción";
  $selector = "hidden";
  $accion = "Aprobar";
  
}



list($activo, $organismo, $CodTransaccionBaja)= split('[|]', $registro);
//echo $activo, $organismo, $CodTransaccionBaja;
//// CONSULTA PRINCIPAL
$sql_a = "select 
				a.*,
				b.Descripcion ,
				b.FechaIngreso,
				b.NumeroOrden,
				c.Organismo as DescpOrganismo,
				d.Dependencia as DescpDependencia,
				e.Descripcion as DescpCentroCosto,
				f.NomCompleto
			from 
				af_transaccionbaja a 
				inner join af_activo b on (a.Activo = b.Activo and b.CodOrganismo=a.Organismo)
				inner join mastorganismos c on (c.CodOrganismo = a.Organismo)
				inner join mastdependencias d on (d.CodDependencia = a.Dependencia) 
				inner join ac_mastcentrocosto e on (e.CodCentroCosto = a.CentroCosto)
				inner join mastpersonas f on (f.CodPersona = a.Responsable)
			where 
				a.Organismo = '$organismo' and 
				a.Activo='$activo' and 
				a.CodTransaccionBaja= '$CodTransaccionBaja'";
$qry_a = mysql_query($sql_a) or die ($sql_a.mysql_error()); 
$row_a = mysql_num_rows($qry_a); 

if($row_a!='0')$field_a=mysql_fetch_array($qry_a);
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Baja de Activos | <?=$titulo;?></td>
    <? if($accion=='Revisar'){ ?>
		   <td align="right"><a class="cerrar" href="#" onclick="window.close();">[cerrar]</a></td>
    <? }else{ ?>
       <td align="right"><a class="cerrar" href="<?=$regresar?>.php" onclick="<?=$onclick;?>">[cerrar]</a></td>
    <? }?>
	</tr>
</table>
<hr width="100%" color="#333333" />
<form id="frmentrada" name="frmentrada" action="af_activosmenoresagregar.php?limit=0"  onsubmit="return guardarTransaccionBaja(this,'<?=$accion;?>');">
<? echo"<input type='hidden' id='registro' name='registro' value='".$registro."'/>
		<input type='hidden' id='FechaIngreso' name='FechaIngreso' value='".$field_a['FechaIngreso']."'/>
		<input type='hidden' id='NumeroOrden' name='NumeroOrden' value='".$field_a['NumeroOrden']."'/> 
		<input type='hidden' id='CodTransaccionBaja' name='CodTransaccionBaja' value='".$field_a['CodTransaccionBaja']."'/> 
		<input type='hidden' id='OrdenSecuencia' name='OrdenSecuencia' value='".$field_a['Secuencia']."'/>

";?>
<table width="950px" align="center">
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
<? echo" <input type='hidden' id='regresar' name='regresar' value='".$regresar."' />
         <input type='hidden' id='activo' name='activo' value='' />";?>
<div id="tab1" style="display: block;">
<div style="width:950px; height=15px;" class="divFormCaption">Informaci&oacute;n General</div>
<table class="tblForm" width="950">
<tr>
  <td width="109" height="10"></td>
</tr>
<tr>
   <td class="tagForm">Activo #:</td><input type="hidden" id="codtransaccionbaja" name="codtransaccionbaja" value="<?=$field_a['CodTransaccionBaja'];?>" />
   <td width="360" class="gallery clearfix">
      <input type="text" id="nro_activo" name="nro_activo" size="9" value="<?=$field_a['Activo'];?>" disabled/>
      <input type="text" name="descripcion" id="descripcion" size="45" value="<?=$field_a['Descripcion'];?>" readonly/> 
      <input type="hidden" id="btActivo" name="btActivo" value="..." onclick="cargarVentanaLista(this.form, 'af_selectoractivos.php?limit=0&campo=2&ventana=insertarActivo','height=550, width=900, left=200, top=100, resizable=yes');"/> 
      <a id="selector_activo" href="af_selectoractivos.php?filtrar=default&limit=0&campo=2&ventana=insertarActivo&iframe=true&width=77%&height=100%" rel="prettyPhoto[iframe1]" style="visibility:<?=$selector;?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;"/>
            </a>
   </td>
   <td width="102" class="tagForm">Organismo:</td>
   <td width="309">
   		<input type="hidden" name="codorganismo" id="codorganismo" value="<?=$field_a['Organismo']?>"/>
        <input type="text" id="organismo" name="organismo" value="<?=$field_a['DescpOrganismo'];?>" size="60" readonly/>
   </td>
</tr>
<tr>
<input type="hidden" id="seldetalle3" />
<input type="hidden" id="candetalle3" />
  <td class="tagForm">Tipo Baja:</td>
  <td><select id="tipobaja" name="tipobaja" style="width:312px;" onchange="CargarInfoBajaActivos(this.form, this.id, 'insertarDatos_2')|ActivarTable(this.form, this.id);" <?=$activado;?>>
       <?
         $st = "select * from af_tipotransaccion where FlagAltaBaja='B'";
		 $qt = mysql_query($st) or die ($st.mysql_error());
		 $rt = mysql_num_rows($qt);
		 if($rt!=0){ 
		    for($i=0; $i<$rt; $i++){
				$ft= mysql_fetch_array($qt);
              if($ft['TipoTransaccion']==$field_a['TipoTransaccion'])echo" <option value='".$ft['TipoTransaccion']."' selected>".$ft['Descripcion']."</option>";
			  else echo" <option value='".$ft['TipoTransaccion']."'>".$ft['Descripcion']."</option>";		 
		 } 
		 }
	   ?>
      </select>
  </td>
  <td class="tagForm">Dependencia:</td>
  <td><input type="text" id="dpendencia" name="dpendencia" size="60" value="<?=$field_a['DescpDependencia'];?>" readonly/>
      <input type="hidden" id="coddependencia" name="coddependencia" value="<?=$field_a['Dependencia'];?>"/></td>
</tr>
<tr>
  <td class="tagForm">Fecha:</td>
  <? 
   list($ano, $mes, $dia) = split('[-]',$field_a['Fecha']); $fecha = $dia.'-'.$mes.'-'.$ano;
   list($a, $b, $c) = split('[-]', $field_a['FechaBaja']); $FechaBaja = $c.'-'.$b.'-'.$a;
  ?>
  <td><input type="text" id="f_actual" name="f_actual" size="10" value="<?=$fecha;?>" style="text-align:center;" disabled/> Nro. Factura:<input type="text" id="nrofactura" name="nrofactura" value="<?=$field_a['FacturaNumero'];?>" <?=$activado;?>/></td> 
  <td class="tagForm">Centro Costo:</td>
  <td><input type="hidden" id="codcentrocosto" name="codcentrocosto" value="<?=$field_a['CentroCosto'];?>"/><input type="text" id="centrocosto" name="centrocosto" value="<?=$field_a['DescpCentroCosto'];?>" size="60" readonly/></td>
</tr>

<tr>
  <td class="tagForm">Fecha Baja:</td>
  <td><input type="text" id="f_baja" name="f_baja" size="10" value="<?=$FechaBaja;?>" style="text-align:center" <?=$activado;?>/></td>
  <td class="tagForm">Responsable:</td>
  <td><input type="hidden" id="codresponsable" name="codresponsable" value="<?=$field_a['Responsable'];?>"/><input type="text" id="responsable" name="responsable" size="60" value="<?=$field_a['NomCompleto'];?>" readonly/></td>
</tr>

<tr><?
     if($field_a['ContabilizadoFlag']=='S')$status = "checked onclick='this.checked=true;'"; 
	?>
  <td class="tagForm"></td>
  <td><input type="checkbox" id="flagContabilizado" name="flagContabilizado"  <?=$status;?> <?=$activado;?>/>Contabilizado</td>
  <td class="tagForm"></td>
  <td></td>
</tr>


<tr>
  <td class="tagForm">Nro. Documento:</td>
  <td><input type="text" id="nro_documento" name="nro_documento" style="text-align:right;" size="28" value="<?=$field_a['Resolucion'];?>" <?=$valor03;?>/></td>
</tr>
<tr>
  <td class="tagForm">Voucher Baja:</td>
  <td><input type="text" name="periodoVoucher" id="periodoVoucher" style="text-align:center;" size="10" disabled value="<?=$field_a['Periodo'];?>"/> <input type="text" id="codVoucher" name="codvoucher" size="10" disabled/></td>
  <td class="tagForm">Valor Activo:</td>
  <?
   $localIngreso = number_format($field_a['LocalIngreso'],2,',','.');
  ?>
  <td><input type="text" id="monto_local" name="monto_local" size="15" style="text-align:right" value="<?=$localIngreso;?>" readonly/> <b>BsF.</b></td>
</tr>
</table>

<table class="tblForm" width="950">
<tr>
  <td width="154" height="5"></td>
</tr>
<!--<tr>
   <td class="tagForm">Situaci&oacute;n Activo:</td>
   <td>
       <select id="situacion_activo" name="situacion_activo" class="selectMed">
        
        <? $s_activo = "select * from af_situacionactivo";
           $q_activo = mysql_query($s_activo) or die ($s_activo.mysql_error());
           $r_activo = mysql_num_rows($q_activo);
          
           if($r_activo!='0'){
		    for($i=0;$i<$r_activo;$i++){
               $f_activo = mysql_fetch_array($q_activo);
			   if($f_activo['CodSituActivo']=='OP'){
                echo"<option value='".$f_activo['CodSituActivo']."' selected>".$f_activo['Descripcion']."</option>";
			   }else{
			     echo"<option value='".$f_activo['CodSituActivo']."'>".$f_activo['Descripcion']."</option>";
			   }	
            }
		   }
        ?>         
       </select></td> 
   <td class="tagForm" width="100">Organismo:</td>
   <td><select id="organismo" name="organismo" class="selectBig">
       <?
        $s_org = "select * from mastorganismos where CodOrganismo='".$_SESSION['ORGANISMO_ACTUAL']."'";
        $q_org = mysql_query($s_org) or die ($s_org.mysql_error());
        $r_org = mysql_num_rows($q_org);
        if($r_org!='0'){
            for($i=0;$i<$r_org;$i++){
              $f_org = mysql_fetch_array($q_org);
              echo"<option value='".$f_org['CodOrganismo']."'>".$f_org['Organismo']."</option>";
            }
			
        }
        
       ?>
       </select></td>
</tr>-->
<tr>
	 <td class="tagForm">Tipo Movimiento:</td>
	 <td><input type="hidden" name="radioEstado" id="radioEstado" value="I"/><input type='radio' id='radio1' name='radio1' onclick="estadosPosee02(this.form)|selectMotMovimiento(this.form);" disabled/>Interno
	     <input type='radio' id='radio2' name='radio2' checked onclick="estadosPosee02(this.form)|selectMotMovimiento(this.form)|this.checked=true;"  />Externo</td>
         <td align='right'>Motivo Traslado:</td>
	 <td align='left' colspan='2'><select id="motivoTrasladoInterno" name="motivoTrasladoInterno" class="selectMed" style="display:none;">
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
                                  <select id="motivoTrasladoExterno" name="motivoTrasladoExterno" class="selectMed" style="display:block;" <?=$valor03;?>>
                                  <?
                                    $s_movext = "select * from mastmiscelaneosdet where CodMaestro='MMOVEXTER'";
									$q_movext = mysql_query($s_movext) or die ($s_movext.mysql_error());
									$r_movext = mysql_num_rows($q_movext);
									
									for($i=0;$i<$r_movext; $i++){
										$f_movext = mysql_fetch_array($q_movext);
										if($f_movext['CodDetalle']==$field_a['MotivoTraslado'])
										   echo"<option value='".$f_movext['CodDetalle']."' selected>".$f_movext['Descripcion']."</option>";
										else echo"<option value='".$f_movext['CodDetalle']."'>".$f_movext['Descripcion']."</option>";
									}
								  ?>
                                  </select></td>
</tr>
<tr>
  <td class="tagForm">Concep. de Movimiento:</td>
   <td colspan="2">
       <select id="conceptoMovimiento" name="conceptoMovimiento" class="selectBig" <?=$valor03;?> >
        
        <? 
		$s_cm = "select * from af_tipomovimientos where TipoMovimiento='DE' and FlagDesincorporacion='S'";
        $q_cm = mysql_query($s_cm) or die ($s_cm.mysql_error());
        $r_cm = mysql_num_rows($q_cm);
          
       if($r_cm!='0'){
		    for($i=0;$i<$r_cm;$i++){
               $f_cm = mysql_fetch_array($q_cm);
			   if($f_cm['CodTipoMovimiento']==$field_a['ConceptoMovimiento']){
                echo"<option value='".$f_cm['CodTipoMovimiento']."' selected>".$f_cm['DescpMovimiento']."</option>";
			   }else{
			     echo"<option value='".$f_cm['CodTipoMovimiento']."'>".$f_cm['DescpMovimiento']."</option>";
			   }	
            }
		   }
        ?>         
       </select></td>  
</tr>
<tr>
   <td class="tagForm">C&oacute;digo Interno:</td>
   <td width="321"><input type="text" id="codigo_interno" name="codigo_interno" size="15" style="text-align:right" value="<?=$field_a['CodigoInterno'];?>"  maxlength="10" disabled/></td>
   <?
   $sql_b = "select * from af_categoriadeprec where CodCategoria='".$field_a['Categoria']."'";
   $qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error());
   $row_b = mysql_num_rows($qry_b);
   if($row_b!=0)$field_b = mysql_fetch_array($qry_b);
   
   ?>
   <td class="tagForm">Categor&iacute;a:</td>
   <td><input type="text" id="categoria" name="categoria" size="60" value="<?=$field_b['DescripcionLocal'];?>" readonly/><input type="hidden" name="codcategoria" id="codcategoria" value="<?=$field_a['Categoria'];?>"/></td>
</tr>
<tr>
   <td class="tagForm">Estado:</td>
   <?
   //// ESTADOS QUE MANEJA
    if($field_a['Estado']=='PR') $estado = "Preparación";
	elseif($field_a['Estado']=='RV') $estado = "Revisado";
	elseif($field_a['Estado']=='AP') $estado = "Aprobado";
	elseif($field_a['Estado']=='AN') $estado = "Anulado";
	
	//// UBICACION DEL ACTIVO
	$sql_c = "select * from af_ubicaciones where CodUbicacion='".$field_a['Ubicacion']."'";
	$qry_c = mysql_query($sql_c) or die ($sql_c.mysql_error());
	$row_c = mysql_num_rows($qry_c);
	
	if($row_c != 0) $field_c = mysql_fetch_array($qry_c);
	
	
   ?>
   <td><input type="text" value="<?=$estado;?>" id="pr" name="Preparacion" disabled size="15"/></td>
   <td class="tagForm">Ubicaci&oacute;n:</td>
   <td><input type="hidden" name="codubicacion" id="codubicacion" value="<?=$field_a['Ubicacion'];?>" disabled/>
       <input type="text" name="ubicacion" id="ubicacion" size="60" value="<?=$field_c['Descripcion'];?>" readonly/></td>
</tr>
<tr>
 <td></td>
 <td></td>
 <td class="tagForm"></td>
 <td></td>
</tr>

<tr>
   <td class="tagForm">Aprobado Por:</td>
   <?
   $sql_e = "select * from mastpersonas where CodPersona= '".$field_a['AprobadoPor']."'";
   $qry_e = mysql_query($sql_e) or die ($sql_e.mysql_error());
   $field_e = mysql_fetch_array($qry_e);
   
   ?>
   <td><input type="text" id="aprobadopor" name="aprobadopor" size="60" value="<?=$field_e['NomCompleto'];?>" disabled /></td>
</tr>
<tr>
 <td class="tagForm">Comentario:</td>
 <td colspan="3"><textarea id="comentario" name="comentario" style="width:319px;" <?=$valor04;?> ><?=$field_a['Comentario'];?></textarea></td> 
</tr>
<tr>
 <td height="6"></td>
</tr>
<? 

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
	echo"<input='hidden' id='cod_prepor' nanme='cod_prepor' value='".$f_usuario['CodPersona']."' />";		

	$ss = "select * from usuarios where CodPersona='".$field_a['UltimoUsuario']."' ";
	$qq = mysql_query($ss) or die ($ss.mysql_error());
	$ff = mysql_fetch_array($qq);
			 
?>
<tr><td align="center" colspan="5">Ultima Modif.:<input type="text" name="ultimo_usuario" value="<?=$field_a['UltimoUsuario'];?>" size="25" readonly/> 
          <input type="text" name="ultima_fecha" value="<?=$field_a['UltimaFechaModif'];?>" style="text-align:center;" size="20" readonly/></td></tr>
<tr><td class="tagForm">Distribuci&oacute;n Contable</td></tr>
</table>

<table class="tblForm" width="950">
<tr>
<td>
   <table width="800" id="mostrar" name="mostrar" style="visibility:<?=$table_style;?>" class="tblLista">
   <thead>
   <tr class="trListaHead">
        <th scope="col" width="15">#</th>
        <th scope="col" width="75">Cuenta</th>
        <th width="200" scope="col">Descripci&oacute;n</th>
        <th scope="col" width="50">Local</th>
   </tr>
   </thead>
   
   <tr><td colspan="4"><div id="scrool" style="display:<?=$div;?>;overflow:scroll; width:850px; height:150px;">
   <div id="resultados" name="resultados" style="width:840px">
   <?
   echo "<table>";
   $sql_d = "select 
   					a.Secuencia,
					a.CuentaContable,
					a.Descripcion,
					a.MontoLocal,
					b.Descripcion as DescpContabilidad,
					b.CodContabilidad 
			   from 
			  		af_transaccionbajacuenta a 
					inner join ac_contabilidades b on (b.CodContabilidad=a.Contabilidad)
			  where 
			        a.Activo='".$field_a['Activo']."' and a.CodTransaccionBaja='".$field_a['CodTransaccionBaja']."' 
			 order by a.Contabilidad, a.Secuencia"; //echo $sql_d;
   $qry_d = mysql_query($sql_d) or die ($sql_d.mysql_error());
   $row_d = mysql_num_rows($qry_d); 
   if($row_d!=0){
     for($i=0; $i<$row_d; $i++){
		$field_d = mysql_fetch_array($qry_d);
  		$monto = number_format($field_d['MontoLocal'],2,',','.');
  		if($contabilidad!=$field_d['CodContabilidad']){
  		echo"<tr >
         	<td align='center' width='46' style='font-family:Lucida Grande, Verdana, Arial, Helvetica, sans-serif;font-size:10px;background-color:#C0C0C0;border-style:outset;'><b>".$field_d['CodContabilidad']."</b></td>
		 	<td align='center' width='180' style='font-family:Lucida Grande, Verdana, Arial, Helvetica, sans-serif;font-size:10px;background-color:#C0C0C0;border-style:outset;'><b>".$field_d['DescpContabilidad']."</b></td>
		 	</tr>";
  			$contabilidad =$field_d['CodContabilidad'];
  		}
  		echo"<tr>
    	<td align='right' style='font-family:Lucida Grande, Verdana, Arial, Helvetica, sans-serif;font-size:10px;'>".$field_d['Secuencia']."</td>
		<td style='font-family:Lucida Grande, Verdana, Arial, Helvetica, sans-serif;font-size:10px;'>".$field_d['CuentaContable']."</td>
		<td width='445' style='font-family:Lucida Grande, Verdana, Arial, Helvetica, sans-serif;font-size:10px;'>".$field_d['Descripcion']."</td>
		<td width='100' style='font-family:Lucida Grande, Verdana, Arial, Helvetica, sans-serif;font-size:10px;text-align:right;'>$monto</td>
  		</tr>"; 
	 }
} 
 echo "</table>";
   ?>
   </div></div>
   </td></tr>
   </table>
   
</td>
</tr>
</table>
</div>
<!-- ****************************************************** FIN TABS ***************************************************** -->
<center><input type="submit" name="btGuardar" id="btGuardar" value="<?=$valor02;?>" <?=$botonAccion;?>/>
<input type="button" name="btCancelar" id="btCancelar" value="Cancelar" onclick="<?=$regresar_1;?>" <?=$botonAccion;?>/></center>
</form>