<?php
// ------------------------------------- ####
include("../lib/fphp.php");
include ("fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('02', $concepto);
//	------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="../css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" src="../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.formatCurrency-1.4.0.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.formatCurrency.all.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.timeentry.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.numeric.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.mask.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" src="js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" src="js/form.js" charset="utf-8"></script>

<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript_02.js"></script>
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
		<td class="titulo">Revisar Movimientos</td>
		<td align="right"><a class="cerrar" href="framemain.php"  onclick=";">[cerrar]</a></td>
	</tr>
</table>
<hr width="100%" color="#333333" />

<? 
/// FILTRO QUE PERMITE REALIZAR BUSQUEDAS ESPECIFICAS
if(!$_POST) $fOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"]; else $cOrganismo = "checked"; 
if(!$_POST){$fEstado="PR"; $cEstado="checked";}
$filtro = "";

if ($fOrganismo!= ""){$filtro.=" AND (Organismo = '".$fOrganismo."')"; $cOrganismo = "checked"; }else $dOrganismo = "disabled";
if ($fmovimiento!= "")$filtro.=" AND (MovimientoNumero = '".$fmovimiento."')";
if ($fEstado !=""){ $filtro .="AND (Estado='".$fEstado."')"; $cEstado="checked";} else $dEstado="disabled";

echo "<input type='hidden' id='regresar' name='regresar' value='af_movimientoactivos'/>";

echo"<form name='frmentrada' id='frmentrada' action='af_movimientorevisar.php?limit=0' method='POST'>
<table class='tblForm' width='1150' height='50'>
<tr>
   <td>
   <table>
   <tr>
       <td width='5'></td>
       <td align='right'>Organismo:</td>
       <td align='left'>
	       <input type='checkbox' id='checkOrganismo' name='checkOrganismo' value='1' $cOrganismo onclick='this.checked=true;'/>
           <select name='fOrganismo' id='fOrganismo' class='selectBig' $dOrganismo>";
           getOrganismos($_SESSION['ORGANISMO_ACTUAL'],3);
           echo"
           </select>
       </td>
       <td align='right'>Movimiento >=</td>
	   <td><input type='text' id='fmovimiento' name='fmovimiento' value='$fmovimiento'/></td>
	   
	   <td align='right'>Estado:</td>
       <td><input type='checkbox' name='checkEstado' id='checkEstado' value='1' $cEstado onclick='enabledEstado(this.form);'/>
	       <select id='fEstado' name='fEstado' class='selectSma' $dEstado>";
		   getEstado($fEstado, 6);
	   echo"</select></td>	   
   </tr>
   </table>
   </td>
</tr>
</table>
<center><input type='submit' name='btBuscar' value='Buscar'/></center>
</form>";

  /// CONSULTA PARA OBTENER DATOS DE LA TABLA A MOSTRAR
 $sa= "select * from 
                      af_movimientos 
                where 
                      Organismo='".$_SESSION['ORGANISMO_ACTUAL']."' $filtro
             order by 
                      Organismo,MovimientoNumero"; 
  $qa= mysql_query($sa) or die ($sa.mysql_error());
  $ra= mysql_num_rows($qa);
  
?>

<form id="tabs" name="tabs">

<table width="1142" class="tblBotones">
 <tr>
  <td><div id="rows"></div></td>
  <td align="left">Registros <?=$ra;?></td>
  <td align="right">
    
 <input type="button" name="btRevisar" id="btRevisar" class="btLista" value="Revisar" onclick="cargarOpcion(this.form,'af_movimientoactivover.php?revisar=revisar','BLANK', 'height=550, width=1100, left=150, top=50, resizable=no');"/>
  </tr>
</table>

<input type="hidden" id="sel_detalle"/>
<input type="hidden" id="can_detalle"/>
<input type="hidden" id="controlador"/>
<input type="hidden" id="ultimoTR"/>
<input type="hidden" id="cant_veces"/>
<center>
	<div style="overflow:scroll; width:1140px; height:150px;">
		<input type="hidden" id="registro" name="registro"/>
		<table width="120%" class="tblLista" border="0">
		<thead>
		  <tr class="trListaHead">
				<th width="40" scope="col">Movimiento#</th>
				<th width="50" scope="col">Tipo</th>
				<th width="200" scope="col">Motivo Traslado</th>
		        <th width="200" scope="col">Comentario</th>
		        <th width="40" scope="col">Estado</th>
		        <th width="100" scope="col">Preparado Por</th>
		        <th width="30" scope="col">F. Preparaci&oacute;n</th>
		        <th width="100" scope="col">Aprobado Por</th>
		        <th width="30" scope="col">F. Aprobaci&oacute;n</th>
		  </tr>
		 </thead>
		  <?
		  
		  if($ra!=0){
		      
		   for($i=0;$i<$ra;$i++){
		     $fa= mysql_fetch_array($qa);
			 
			 $datosMov= $fa['Organismo']."|".$fa['MovimientoNumero']."|".$fa['Estado']."|".$fa['Anio'];
			  
			 $id= $fa['Organismo']."|".$fa['MovimientoNumero']."|".$fa['Estado']."|".$fa['NroActa']."|".$fa['Anio']."|".$fa['TipoActa'];
			 //// ----------------------------------------------------------------
			 if($fa['InternoExternoFlag']=='I'){
				    $flag = 'Interno'; 
					$motivo = 'MMOVINTER'; 
				 }else{ 
				    $flag = 'Externo'; 
					$motivo = 'MMOVEXTER'; 
				 }
				 
				 $s_mt = "select * 
				            from mastmiscelaneosdet 
						   where CodMaestro='$motivo' and 
						         CodDetalle = '".$fa['MotivoTraslado']."'"; //echo $s_mt; 
				 $q_mt = mysql_query($s_mt) or die ($s_mt.mysql_error());
				 $f_mt = mysql_fetch_array($q_mt);
			 //// ----------------------------------------------------------------
			 list($fpano, $fpmes, $fpdia)= split('[-]',$fa['FechaPreparacion']); $fechaPreparacion = $fpdia.'-'.$fpmes.'-'.$fpano;
			 //// ----------------------------------------------------------------
			 if($fa['Estado']=='PR') $estado = 'Preparación';
			 //// ----------------------------------------------------------------
			 $s_ppor = "select NomCompleto from mastpersonas where CodPersona='".$fa['PreparadoPor']."'";
			 $q_ppor = mysql_query($s_ppor) or die ($s_ppor.mysql_error());
			 $f_ppor = mysql_fetch_array($q_ppor);
			 //// ----------------------------------------------------------------
		     $cont++;
		    echo"<tr class='trListaBody' onclick='mClk(this, \"registro\")|MostrarDetalleMovimiento(this,\"$datosMov\");' id='$id'>		
				<td align='center'>".$fa['MovimientoNumero']."</td>
				<td align='center'>$flag</td>
				<td align='left'>".$f_mt['Descripcion']."</td>
		        <td align='left'>".$fa['Comentario']."</td>
		        <td align='center'>$estado</td>
		        <td align='left'>".$f_ppor['NomCompleto']."</td>
		        <td align='center'>$fechaPreparacion</td>
		        <td align='right'></td>
				<td align='right'>".$fa['CodCentroCosto']."</td>
			</tr>";
		    }
		 }
		  ?>
</table>
</div>
</center>

<center>
	<div style="overflow:scroll; width:1140px; height:150px;">
		<table width="170%" class="tblLista" border="0">
		<thead>
		  <tr class="trListaHead">
		  		<th width="40" scope="col">Movimiento#</th>
		        <th width="40" scope="col">A&ntilde;o</th>
				<th width="40" scope="col">Activo</th>
				<th width="250" scope="col">Descripci&oacute;n Activo</th>
				<th width="100" scope="col">C. Costo Actual</th>
		        <th width="250" scope="col">Descripcion</th>
		        <th width="100" scope="col">C.Costo Anterior</th>
		        <th width="250" scope="col">Descripci&oacute;n</th>
		        <th width="200" scope="col">Ubicaci&oacute;n Actual</th>
		  </tr>
		  </thead>
          <? /// ------------------------------------ ####
		     /// Mostrando los detalles del movimiento al seleccionar
		  ?>
          <tbody id="listaDetalles">
          </tbody>
		  <?
		  /* 
		   /// ------------------------------------------------------------------
		   /// consulta para obtener los activos relaionados en los movimientos
		   $s_con= "select * from 
		                      af_movimientos 
		                where 
		                      Organismo='".$_SESSION['ORGANISMO_ACTUAL']."' $filtro
		             order by 
		                      Organismo,MovimientoNumero";
		  $q_con= mysql_query($s_con) or die ($s_con.mysql_error());
		  $r_con= mysql_num_rows($q_con);
		  
		  if($r_con!=0){
		    for($i=0;$i<$r_con;$i++){
			   $f_con = mysql_fetch_array($q_con);
			   
			   $s_movdetalle = "select 
			                          ac.Activo,
									  ac.Descripcion as DescpActivo,
									  afmd.CentroCosto,
									  afmd.CentroCostoAnterior,
									  afmd.MovimientoNumero as NroMovimiento,
									  afmd.Anio as AnioMov,
									  ccosto1.Descripcion as DescpCentroCostoActual,
									  ccosto2.Descripcion as DescpCentroCostoAnterior,
									  afu.Descripcion as DescpUbicacionActual
							      from 
								      af_movimientosdetalle afmd
									  inner join af_activo ac on (ac.Activo = afmd.Activo)
									  inner join ac_mastcentrocosto ccosto1 on (afmd.CentroCosto = ccosto1.CodCentroCosto)
									  inner join ac_mastcentrocosto ccosto2 on (afmd.CentroCostoAnterior = ccosto2.CodCentroCosto)
									  inner join af_ubicaciones afu on (afu.CodUbicacion = afmd.Ubicacion)
								 where 
								      MovimientoNumero = '".$f_con['MovimientoNumero']."'";
			   $q_movdetalle = mysql_query($s_movdetalle) or die ($s_movdetalle.mysql_error());
			   $r_movdetalle = mysql_num_rows($q_movdetalle);
			   if($r_movdetalle!=0) for($x=0; $x<$r_movdetalle; $x++){
			   
			     $f_movdetalle = mysql_fetch_array($q_movdetalle);
			 
				 echo"<tr class='trListaBodyAF' onclick='mClkAF(this,\"registro\");' id='".$f_movdetalle['Activo']."'>
					<td align='center'>".$f_movdetalle['NroMovimiento']."</td>
					<td align='center'>".$f_movdetalle['AnioMov']."</td>
					<td align='center'>".$f_movdetalle['Activo']."</td>
					<td align='left'>".htmlentities($f_movdetalle['DescpActivo'])."</td>
					<td align='center'>".$f_movdetalle['CentroCosto']."</td>
					<td align='left'>".htmlentities($f_movdetalle['DescpCentroCostoActual'])."</td>
					<td align='center'>".$f_movdetalle['CentroCostoAnterior']."</td>
					<td align='left'>".htmlentities($f_movdetalle['DescpCentroCostoAnterior'])."</td>
					<td align='left'>".htmlentities($f_movdetalle['DescpUbicacionActual'])."</td>
				</tr>";
			  }
			}
		  }*/
		  ?>
</table>
</div>
</center>

</form>
</body>
</html>
