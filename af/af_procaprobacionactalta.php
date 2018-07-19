<?php
// ------------------------------------- ####
include("../lib/fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
//	------------------------------------
include ("fphp.php");
connect();
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('03', $concepto);
//	------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link href="css1.css" rel="stylesheet" type="text/css" />-->
<link href="../css/estilo.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" language="javascript" src="fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
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
		<td class="titulo">Aprobaci&oacute;n de Alta de Activos</td>
		<td align="right"><a class="cerrar" href="framemain.php">[cerrar]</a></td>
	</tr>
</table>
<hr width="100%" color="#333333" />

<? 
/// FILTRO QUE PERMITE REALIZAR BUSQUEDAS ESPECIFICAS
if(!$_POST) $fOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"]; else $cOrganismo = "checked"; 


if(($_POST)and($centro_costos!="")){ $valor3="visible";}else $valor3="hidden";

$filtro = "";

//echo $fOrganismo;echo $fDependencia;echo $fTipoActivo;

if ($fOrganismo != "") { $filtro .= " AND (CodOrganismo = '".$fOrganismo."')"; $cOrganismo = "checked"; }else $dOrganismo = "disabled";
if ($fBuscarPor != "") { $filtro .= " AND ($fBuscarPor = '".$BuscarValor."')"; $cBuscarPor = "checked"; } else $dBuscarPor = "disabled";
if ($fCategoria != "") { $filtro .= " AND (Categoria = '".$fCategoria."')"; $cCategoria = "checked"; } else $dCategoria = "disabled"; 
if ($centro_costos != "") { 
    $filtro .= " AND (CentroCosto = '".$centro_costos."')"; 
	$cCosto = "checked"; $visib_centrocosto='style="visibility:visible"';
}else{ 
    $dCosto = "disabled";
	$visib_centrocosto='style="visibility:hidden"';
}

if ($fDependencia != "") { $filtro .= " AND (CodDependencia = '".$fDependencia."')"; $cDependencia = "checked"; } else $dDependencia = "disabled";
if ($fTipoActivo != "") { $filtro .= " AND (TipoActivo = '".$fTipoActivo."')"; $cTipoActivo = "checked"; } else $dTipoActivo = "disabled";
if ($fSituacionActivo != "") { $filtro .= " AND (SituacionActivo = '".$fSituacionActivo."')"; $cSituacionActivo = "checked"; } else $dSituacionActivo = "disabled";
if ($fT_Seguro!= "") { $filtro .= " AND (TipoSeguro = '".$fT_Seguro."')"; $cT_Seguro = "checked"; } else $dT_Seguro = "disabled";

if ($fColor!= "") { $filtro .= " AND (Color = '".$fColor."')"; $cColor = "checked"; } else $dColor = "disabled";
if ($fproveedor!= ""){ 
   $filtro .= " AND (CodProveedor = '".$fproveedor."')"; 
   $cProveedor = "checked";  
   $visib_proveedor='style="visibility:visible"';
}else{ 
   $dProveedor = "disabled"; 
   $visib_proveedor='style="visibility:hidden"';
}
if ($fCatClasf != "") { 
   $filtro .="AND (Categoria = '".$fCatClasf."')"; 
   $cCatClasf = "checked"; $visib_CatClasf='style="visibility:visible"'; 
}else{ 
   $dCatClasf = "disabled";
   $visib_CatClasf='style="visibility:hidden"';
}
if ($fClasificacion != ""){$filtro.="AND (Clasificacion = '".$fClasificacion."')"; $cCatClasf = "checked"; } else $dCatClasf = "disabled";


echo"<form name='frmentrada' id='frmentrada' action='af_procaprobacionactalta.php?limit=0' method='POST'>
<table class='tblForm' width='90%' height='50'>
<tr>
  <td align='right' width='125'>Organismo:</td>
  <td align='left'>
    <input type='checkbox' id='checkOrganismo' name='checkOrganismo' value='1' $cOrganismo onclick='this.checked=true;'/>
    <select name='fOrganismo' id='fOrganismo' class='selectMed' $dOrganismo>";
      getOrganismos($_SESSION['ORGANISMO_ACTUAL'],3);
       echo"
    </select>
  </td>
	<td width='15'></td>
  <td align='right'>Buscar Por:</td>       
	<td align='left'>
   <select name='fBuscarPor' id='fBuscarPor' class='selectMed' $cBuscarPor>
	   <option value=''></option>
	   <option value='Activo'>Nro de Activo</option>
	   <option value='CodigoBarras'>Código Barras</option>
	   <option value='Categoria'>Categoría</option>
	   <option value='TipoActivo'>Tipo Activo</option>
	 </select></td>
  <td align='right'>C.Costo:</td>
	<td class='gallery clearfix'><input type='checkbox' name='chekcCosto' id='checkCosto' value='1' $cCosto onclick='enabledCosto(this.form);'/>
	  <input type='hidden' id='centro_costos' name='centro_costos' value='$centro_costos'/>
	  <input type='text' id='centro_costos2' name='centro_costos2' size='40' value='$centro_costos2' $dCosto readonly/>";?>
	  <input type='hidden' id='btcosto' name='btcosto' value='...' onclick="cargarVentanaLista(this.form,'af_listacentroscostos.php?limit=0&campo=9','height=500,width=800,left=200,top=100,resizable=yes');" <?=$dCosto;?>/> <!--<a id="c_costo" href="af_listacentroscostos.php?filtrar=default&limit=0&campo=9&iframe=true&width=70%&height=100%" rel="prettyPhoto[iframe1]" <?=$visib_centrocosto;?> >
       	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;"/>
        </a>-->
       
      <a id="c_costo" href="../lib/listas/listado_centro_costos.php?filtrar=default&limit=0&campo=1&cod=centro_costos&nom=centro_costos2&ventana=selListadoLista03&iframe=true&width=70%&height=100%" rel="prettyPhoto[iframe1]" style="visibility:<?=$valor3;?>">
       	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;"/>
      </a>
	 <?  echo" </select></td>   
	<td align='right'></td>
</tr>
   
<tr>
  <td align='right'>Dependencia:</td>
  <td align='left'>
    <input type='checkbox' id='checkDependencia' name='checkDependencia' value='1' $cDependencia onclick='enabledDependencia(this.form);'/>
    <select name='fDependencia' id='fDependencia' class='selectMed' $dDependencia>
      <option value=''></option>";
        //getDependencias($fDependencia, $fOrganismo,  2);
		getDependencias($fDependencia, $fOrganismo, 3);
         echo"
    </select>
  </td>
	<td width='15'></td>
  <td align='right'>Buscar Valor >=</td>
  <td align='left'><input type='text' id='BuscarValor' name='BuscarValor' size='15' /></td>
	<td align='right'>Proveedor:</td>
	<td class='gallery clearfix'><input type='checkbox' name='chkproveedor' id='chkproveedor' value='1' $cProveedor onclick='enabledProveedorAprobacionAltaActivos(this.form);'/>
	   <input type='hidden' name='fproveedor' id='fproveedor' value='$fproveedor'/>
     <input type='text' name='proveedor' id='proveedor' value='$proveedor' size='40' $dProveedor readonly/>";?> 
     <input type="hidden" name="btProveedor" id="btProveedor" value="..." onclick="cargarVentanaLista(this.form, 'af_listaproveedor.php?limit=0&campo=4','height=500, width=850, left=200, top=100, resizable=yes');" <?=$dProveedor;?>/> <a id="proveedor_01" href="af_listaproveedor.php?filtrar=default&limit=0&campo=4&cierre=1&iframe=true&width=75%&height=100%" rel="prettyPhoto[iframe2]" <?=$visib_proveedor;?>>
       	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;"/>
     </a>
	  <? echo" </select></td>
</tr>
   
<tr>
  <td align='right'>Tipo de Activo:</td>
  <td align='left'>
    <input type='checkbox' id='checkTipoActivo' name='checkTipoActivo' value='1' $cTipoActivo onclick='enabledTipoActivo(this.form);'/>
    <select name='fTipoActivo' id='fTipoActivo' class='selectMed' $dTipoActivo>
       <option value=''></option>";
         getTipoActivo($fTipoActivo, 0);
        echo"
    </select>
  </td>
  <td width='15'></td>
  <td align='right'>Categor&iacute;a:</td>
  <td><input type='checkbox' name='checkCategoria' id='checkCategoria' value='1' $cCategoria onclick='enabledCategoria(this.form);'/>
      <select id='fCategoria' name='fCategoria' style='width:187px' $dCategoria/>
  		   <option value=''></option>";
	         getCategoria($fCategoria, 0);
	   echo"</select>
  </td>
</tr>
   
<tr>
  <td align='right'>Situaci&oacute;n:</td>
  <td align='left'>
    <input type='checkbox' id='checkSituacionActivo' name='checkSituacionActivo' value='1' $cSituacionActivo onclick='enabledSituacionActivo(this.form);'/>
    <select name='fSituacionActivo' id='fSituacionActivo' class='selectMed' $dSituacionActivo>
      <option value=''></option>";
		    getSituacionActivo($fSituacionActivo, 0);
	      echo"
	  </select>
  </td>
	<td width='15'></td>
	<td align='right'>Cat/Clasf.:</td>
  <td class='gallery clearfix'><input type='checkbox' id='chkCatClasf' name='chkCatClasf' value='1' $cCatClasf onclick='enabledCatClasf(this.form);'/>
	  <input type='text' id='fCatClasf' name='fCatClasf' value='$fCatClasf' $dCatClasf style='width:85px;' readonly/> <input type='hidden' name='fCodCatClasf' id='fCodCatClasf' value='$fCodCatClasf'/>";
		     ///getCategoria($fCatClasf, 1);
	   echo"
	   </select><input type='hidden' id='fClasificacion' name='fClasificacion' value='$fClasificacion'/><input type='text' id='DescpClasificacion' name='DescpClasificacion' value='$DescpClasificacion' $dCatClasf size='16' readonly/>";?><input type='hidden' id='btClasif' name='btClasif' value='...' onclick="cargarVentanaLista(this.form, 'af_listaclasificacionactivo.php?limit=0&campo=17&ventana=insertarClasificacionActivo','height=500, width=800, left=200, top=100, resizable=yes');" <?=$dCatClasf?>/> <a id="clasificacion" href="af_listaclasificacionactivo.php?filtrar=default&limit=0&campo=17&ventana=insertarClasificacionActivo&iframe=true&width=70%&height=100%" rel="prettyPhoto[iframe3]" <?=$visib_CatClasf;?>>
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;"/>
            </a>
	<? echo"</td>		
</tr>
  
<tr>
  <td align='right'># de Lineas:</td>
  <td align='left'><input type='text' name='numLineas' id='numLineas' value=''/></td>
</tr>
   
</table>

<center><input type='submit' name='btBuscar' value='Buscar'/></center>
</form>";

  /// CONSULTA PARA OBTENER DATOS DE LA TABLA A MOSTRAR
  $sa= "select * from af_activo 
                where 
                      CodOrganismo='".$_SESSION['ORGANISMO_ACTUAL']."' and 
                      Estado='PE' $filtro
             order by 
                      Activo"; //echo $sa;
  $qa= mysql_query($sa) or die ($sa.mysql_error());
  $ra= mysql_num_rows($qa);
  
?>

<form id="tabs" name="tabs">
  <table width="90%" class="tblLista">
  <tr> <input type="hidden" id="registro" name="registro"/>
       <input type="hidden" id="direccion" name="direccion"/>
       <input type="hidden" id="accion" name="accion" value="aprobar"/>
    <td width="135"><div id="rows"># de Lineas:<?=$ra;?></div></td>
    <td width="843" align="right">
      <input type="button" class="btLista" id="btVer" name="btVer" value="Ver" onclick="cargarPagVerAltActivo(this.form,document.getElementById('direccion').value+'.php?accion=ver','BLANK','height=590, width=1000, left=200, top=40, resizable=yes')"/>
      <input type="button" class="btLista" name="btModificar" id="btModificar"  value="Modificar" onclick="cargarPagModifAltActivo(this.form,document.getElementById('direccion').value+'.php?accion=modificar&regresar=af_procaprobacionactalta');"/>
  
     <!--<input type="button" class="btLista" name="btModificar" id="btModificar"  value="Modificar" onclick="cargarPagModifAltActivo(this.form,'af_listactivosmodificar.php?regresar=af_procaprobacionactalta');"/>-->
  
     <!--<input type="button" name="btModificar" id="btModificar" class="btLista" value="Modificar" onclick="cargarPaginaModificar(this.form, 'af_listactivosmodificar.php?regresar=af_procaprobacionactalta&fOrganismo=<?=$fOrganismo;?>&fBuscarPor=<?=$fBuscarPor;?>&fCategoria=<?=$fCategoria;?>&centro_costos=<?=$centro_costos;?>&fDependencia=<?=$fDependencia;?>&fTipoActivo=<?=$fTipoActivo;?>&fSituacionActivo=<?=$fSituacionActivo;?>&fT_Seguro=<?=$fT_Seguro;?>&fColor=<?=$fColor;?>&fubicacion=<?=$fubicacion;?>&fClasificacion=<?=$fClasificacion;?>&fCatClasf=<?=$fCatClasf;?>&DescpClasificacion=<?=$DescpClasificacion;?>&fEstado=<?=$fEstado;?>&fCodclasficacionPub20=<?=$fCodclasficacionPub20;?>&BuscarValor=<?=$BuscarValor;?>&fClasificacionPub20=<?=$fClasificacionPub20;?>&centro_costos2=<?=$centro_costos2;?>&fubicacion2=<?=$fubicacion2;?>&accion=altaActivo','SELF')"/>-->
     <input type="button"  id="btMasivo" name="btMasivo" class="btLista01" value="Aprobaci&oacute;n Masiva" onclick="cargarMasivoAltaActivo(this.form);"/>
     <input type="button" name="btAprobar" class="btLista" id="btAprobar" value="Aprobar" onclick="cargarAltaActivo(this.form,document.getElementById('direccion').value+'.php?accion=aprobar','BLANK','height=590, width=1000, left=200, top=40, resizable=yes')"/>
  </tr>
  </table>

  <center>
    <div style="overflow:scroll; width:90%; height:300px;">
      <table width="300%" class="tblLista" border="0">
      <thead>
      <tr class="trListaHead">
        <th width="150" >Organismo</th>
        <th width="200" >Dependencia</th>
        <th width="70" >Activo</th>
        <th width="200" >Decripci&oacute;n Local</th>
        <th width="150" >Categor&iacute;a</th>
        <th width="100" >Monto Local</th>
        <th width="200" >Clasificaci&oacute;n</th>
        <th width="100" >Situaci&oacute;n</th>
        <th width="140" >Centro Costos</th>
        <th width="100" >Fecha Adquisi&oacute;n</th>
        <th width="100" >P. Ingreso</th>
        <th width="100" >P. Depreciaci&oacute;n</th>
        <th width="70" >N&uacute;mero Serie</th>
        <th width="70" >C&oacute;digo Barras</th>
        <th width="200" >Proveedor</th>
        <th width="70" >Ult. Usuario</th>
      </tr>
      </thead>
      <?
      
      if($ra!=0){
          
       for($i=0;$i<$ra;$i++){
         $fa= mysql_fetch_array($qa);
    	 if($fa['TipoActivo']=='I') $tipoActivo= 'Individual'; else $tipoActivo = 'Principal';
    	 if($fa['Estado']=='A') $estado = 'Activo';else $estado = 'Inactivo';
    	 /// -------------------------------------------
    	 $scon = "select * from mastpersonas where CodPersona = '".$fa['CodProveedor']."'";
    	 $qcon = mysql_query($scon) or die ($scon.mysql_error());
    	 $fcon = mysql_fetch_array($qcon);
    	 /// -------------------------------------------
    	 $s_dep = "select CodDependencia,Dependencia from mastdependencias where CodDependencia='".$fa['CodDependencia']."'";
    	 $q_dep = mysql_query($s_dep) or die ($s_dep.mysql_error());
    	 $f_dep = mysql_fetch_array($q_dep);
    	 /// -------------------------------------------
    	 $s_sitActivo = "select * from af_situacionactivo where  CodSituActivo= '".$fa['SituacionActivo']."'";
    	 $q_sitActivo = mysql_query($s_sitActivo) or die ($s_sitActivo.mysql_error()) ;
    	 $f_sitActivo = mysql_fetch_array($q_sitActivo);
    	 /// -------------------------------------------
    	 $s_catDeprec = "select * from af_categoriadeprec where CodCategoria = '".$fa['Categoria']."'";
    	 $q_catDeprec = mysql_query($s_catDeprec) or die ($s_catDeprec.mysql_error());
    	 $f_catDeprec = mysql_fetch_array($q_catDeprec);
    	 /// -------------------------------------------
    	 $s_mostrar = "select 
    	                     cc.Descripcion as DescripCentroCosto,
    						 ca.Descripcion as DescripClasificacionActivo
    					 from
    					     ac_mastcentrocosto cc,
    						 af_clasificacionactivo ca
    					where
    					     cc.CodCentroCosto = '".$fa['CentroCosto']."' and 
    						 ca.CodClasificacion = '".$fa['Clasificacion']."'";
    	 $q_mostrar = mysql_query($s_mostrar) or die ($s_mostrar.mysql_error());
    	 $f_mostrar = mysql_fetch_array($q_mostrar);
    	 /// -------------------------------------------
         $montoLocal = number_format($fa['MontoLocal'],2,',','.');
    	 list($a,$m,$d) = split('[-]',$fa['FechaIngreso']); $fechaAdquision = $d."-".$m."-".$a;
    	 /// -------------------------------------------
         //$id= $fa['NroOrden']."|".$fa['Secuencia'];
    	 $id = $fa['CodOrganismo']."|".$fa['Activo']."|".$fa['CodDependencia'];
    	 $valor = $fa['Naturaleza'];
         
        echo"<tr class='trListaBody' onclick='mClkMulti(this)| cargarDireccion(tabs,\"$valor\");' id='row_$id'>
    	    
    	    <td align='center'>".$fa['CodOrganismo']."<input type='checkbox' name='documento' class='altaActivo' id='$id' value='$id' style='display:none'/></td>
    		<td align='center'>".$f_dep['Dependencia']."</td>
    		<td align='center'>".$fa['Activo']."</td>
            <td align='left'>".$fa['Descripcion']."</td>
    		<td align='left'>".utf8_decode($f_catDeprec['DescripcionLocal'])."</td>
    		<td align='right'>$montoLocal</td>
    		<td align='left'>".$f_mostrar['DescripClasificacionActivo']."</td>
            <td align='left'>".$f_sitActivo['Descripcion']."</td>
    		<td align='left'>".$f_mostrar['DescripCentroCosto']."</td>
    		<td align='center'>$fechaAdquision</td>
    		<td align='center'>".$fa['PeriodoIngreso']."</td>
    		<td align='center'>".$fa['PeriodoInicioDepreciacion']."</td>
            <td align='center'>".$fa['NumeroSerie']."</td>
    		<td align='center'>".$fa['CodigoBarras']."</td>
            <td align='left'>".$fcon['NomCompleto']."</td>
            <td align='center'>".$fa['UltimoUsuario']."</td>
    	</tr>";
        }
     }
      ?>
</table>
</div>
</center>
</form>
</body>
</html>
