<?php
// ------------------------------------- ####
include("../lib/fphp.php");
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
//	------------------------------------
include ("fphp.php");
connect();
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('01', $concepto);
//	------------------------------------
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
<script type="text/javascript" language="javascript" src="af_fscript.js"></script>
<script type="text/javascript" language="javascript" src="af_fscript_02.js"></script>
<script type="text/javascript" src="../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/fscript.js" charset="utf-8"></script>
<!--<style type="text/css">

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
		<td class="titulo">Lista Actas | Incorporación Bienes</td>
		<td align="right"><a class="cerrar" href="framemain.php">[cerrar]</a></td>
	</tr>
</table>
<hr width="100%" color="#333333" />

<? 
/// FILTRO QUE PERMITE REALIZAR BUSQUEDAS ESPECIFICAS
if(!$_POST) $fOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"]; else $cOrganismo = "checked"; 
if(!$_POST){ $fanio = date("Y"); $cEstado = "checked";} 

//if(!$_POST) $fDependencia = $_SESSION["FILTRO_DEPENDENCIA_ACTUAL"]; else{ $fDependencia = $_POST['fDependencia']; $cDependencia = "checked"; }
$filtro = "";

if ($fOrganismo!=""){$filtro .=" AND (CodOrganismo ='".$fOrganismo."')"; $cOrganismo = "checked"; }else $dOrganismo = "disabled";
if ($fDependencia!=""){$filtro .=" AND (CodDependencia ='".$fDependencia."')"; $cDependencia = "checked"; } else $dDependencia = "disabled";
if ($fanio != ""){$filtro.="AND (Anio = '".$fanio."')"; $cAnio="checked";}else $dAnio = "disabled";
if ($fdesde != "" and $fhasta != "") { // FECHA DE REGISTRO DEL DOCUMENTO

  list($d, $m, $a)=SPLIT('[/.-]', $_POST['fdesde']); $fechadesde=$a.'-'.$m.'-'.$d;
  list($d, $m, $a)=SPLIT('[/.-]', $_POST['fhasta']); $fechahasta=$a.'-'.$m.'-'.$d;
  
	if ($fdesde != "") $filtro .= " AND (FechaActa >= '$fechadesde')";
	if ($fhasta != "") $filtro .= " AND (FechaActa <= '$fechahasta')"; 
	$cFecha = "checked"; 
	
	list($a, $m, $d)=SPLIT('[/.-]', $fechadesde); $fechadesde=$d.'-'.$m.'-'.$a;
    list($a, $m, $d)=SPLIT('[/.-]', $fechahasta); $fechahasta=$d.'-'.$m.'-'.$a;
	
} else $dFecha = "disabled";


echo"<form name='frmentrada' id='frmentrada' action='af_rpincorporacionbienes.php?limit=0' method='POST'>
<table class='tblForm' width='1000' height='50'>
<tr>
   <td>
   <table>
   <tr>
       <td align='right' width='100'>Organismo:</td>
       <td align='left' width='400'>
	       <input type='checkbox' id='checkOrganismo' name='checkOrganismo' value='1' $cOrganismo onclick='this.checked=true;'/>
           <select name='fOrganismo' id='fOrganismo' class='selectBig' $dOrganismo>";
           getOrganismos($_SESSION['ORGANISMO_ACTUAL'],3);
           echo"
           </select>
       </td>
       <td align='right' width='120'>Fecha Acta:</td>
	   <td> 
           <input type='checkbox' id='chkFecha' name='chkFecha' value='1' $cFecha onclick='enabledFechaDesdeHasta(this.form);'/>
	       desde:<input type='text' name='fdesde' id='fdesde' size='8' maxlength='10' $dFecha value='$fechadesde' class='datepicker'/>
	       hasta:<input type='text' name='fhasta' id='fhasta' size='8' maxlength='10' $dFecha value='$fechahasta' class='datepicker'/>
   </td>
   </tr>
   
   <tr>
       <td align='right'>Dependencia:</td>
       <td align='left'>
	       <input type='checkbox' id='checkDependencia' name='checkDependencia' value='1' $cDependencia onclick='enabledDependencia(this.form);'/>
           <select name='fDependencia' id='fDependencia' class='selectBig' $dDependencia>
		    <option></option>";
              //getDependencias($fDependencia, $fOrganismo,  2);
			  getDependenciaSeguridad($fDependencia, $fOrganismo, 3);
           echo"
           </select>
       </td>
       <td align='right' width='120'>Fecha Acta:</td>
	   <td><input type='checkbox' id='chkAnio' name='chkAnio' value='1' $cAnio onclick='enabledAnio(this.form)'/>
	       <input type='text' id='fanio' name='fanio' size='4' maxlength='4' $dAnio value='$fanio'/>
	   </td>
   </tr>
   
   
   </table>
   </td>
</tr>
</table>
<center><input type='submit' name='btBuscar' value='Buscar'/></center>
</form>";

  /// CONSULTA PARA OBTENER DATOS DE LA TABLA A MOSTRAR
  $sa= "select  
               * 
		  from 
                      af_actaincorpactivo 
                where 
                      CodOrganismo='".$_SESSION['ORGANISMO_ACTUAL']."' $filtro
             
			 group by NroActa"; //echo $sa;
  $qa= mysql_query($sa) or die ($sa.mysql_error());
  $ra= mysql_num_rows($qa);
  
?>

<form id="tabs" name="tabs">


<table width="1000" class="tblLista">
 <tr> <input type="hidden" id="registro" name="registro"/>
  <td><div id="rows"></div></td>
  <td align="right"></td>
  <td align="right">
   <input type="button" name="btImprimir" id="btImprimir" class="btLista" value="Imprimir" onclick="imprimirActaResponsabilidadUso(this.form, 'incorpporacion_bienes');"/>
  
    <!--<input type="button" name="btAgregar" id="btAgregar" class="btLista" value="Agregar" onclick="cargarPaginaAgregar(this.form, 'af_activosmenoresagregar.php?regresar=af_activosmenores&fEstado=<?=$fEstado;?>&fOrganismo=<?=$fOrganismo;?>&fBuscarPor=<?=$fBuscarPor;?>&fDependencia=<?=$fDependencia;?>&fSituacionActivo=<?=$fSituacionActivo;?>&fClasf20=<?=$fClasf20;?>&DescpClasf20=<?=$DescpClasf20;?>&fClasificacion=<?=$fClasificacion;?>&fubicacion=<?=$fubicacion;?>&BuscarValor=<?=$BuscarValor;?>&fubicacion2=<?=$fubicacion2;?>&DescpClasificacion=<?=$DescpClasificacion;?>');"/>
    
    <input type="button" name="btVer" id="btVer" class="btLista" value="Ver" onclick="cargarOpcion(this.form,'af_activosmenoresver.php?','BLANK', 'height=600, width=920, left=250, top=50, resizable=no');"/>
    
    <input type="button" name="btModificar" id="btModificar" class="btLista" value="Modificar" onclick="cargarOpcionEditarActMenor(this.form, 'af_activosmenoreseditar.php?regresar=af_activosmenores&fEstado=<?=$fEstado;?>&fOrganismo=<?=$fOrganismo;?>&fBuscarPor=<?=$fBuscarPor;?>&fDependencia=<?=$fDependencia;?>&fSituacionActivo=<?=$fSituacionActivo;?>&fClasf20=<?=$fClasf20;?>&DescpClasf20=<?=$DescpClasf20;?>&fClasificacion=<?=$fClasificacion;?>&fubicacion=<?=$fubicacion;?>&BuscarValor=<?=$BuscarValor;?>&fubicacion2=<?=$fubicacion2;?>&DescpClasificacion=<?=$DescpClasificacion;?>','SELF')"/>
    
    <!--<input type="button" name="btModificar" id="btModificar" class="btLista" value="Modificar" onclick="cargarOpcionListActEditar(this.form, 'af_activosmenoreseditar.php?regresar=af_activosmenores&fEstado=<?=$fEstado;?>&fOrganismo=<?=$fOrganismo;?>&fBuscarPor=<?=$fBuscarPor;?>&fDependencia=<?=$fDependencia;?>&fSituacionActivo=<?=$fSituacionActivo;?>&fClasf20=<?=$fClasf20;?>&DescpClasf20=<?=$DescpClasf20;?>&fClasificacion=<?=$fClasificacion;?>&fubicacion=<?=$fubicacion;?>&BuscarValor=<?=$BuscarValor;?>&fubicacion2=<?=$fubicacion2;?>&DescpClasificacion=<?=$DescpClasificacion;?>','SELF')"/>
    
    <input type="button" name="btMovimiento" id="btMovimiento" class="btLista" value="Movimientos" />-->
  </tr>
</table>

<center>
  <div style="overflow:scroll; width:1000px; height:300px;">
      <table width="1500" class="tblLista">
        <thead>
          <tr class="trListaHead">
                <th width="40" align="center">Organismo</th>
                <th width="40" align="center">Dependencia</th>
        		<th width="40" align="center">NroActa</th>
               
        		<th width="250" align="center">Aprobado Por</th>
                <th width="250" align="center">Conformado Por</th>
                <th width="80" align="center">Fecha Acta</th>
          </tr>
          </thead>
      <?
      
      if($ra!=0){
          
       for($i=0;$i<$ra;$i++){
         $fa= mysql_fetch_array($qa);
    	
    	 list($a, $b, $c) = split('[-]', $fa['FechaActa'] );
    	 $fechaActa = $c.'-'.$b.'-'.$a;
    	 
    	 /// -------------------------------------------
    	 $id = $fa['NroActa'].'-'.$fa['CodOrganismo'].'-'.$fa['Anio'];
    	 
        echo"<tr class='trListaBody' onclick='mClk(this, \"registro\");' id='$id'>
    		
    		<td align='center'>".$fa['CodOrganismo']."</td>
    		<td align='center'>".$fa['CodDependencia']."</td>
    		<td align='center'>".$fa['NroActa']."</td>
    		<td align='left'>".$fa['EmpleadoAprob']."</td>
    		<td align='left'>".$fa['EmpleadoConform']."</td>
    		<td align='center'>$fechaActa</td>
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
