<?php
//// --------------------------------------------------------------- ####
////    				FUNCIONES
function cambioFormato($num){
	$num = str_replace(".","",$num);
	$num = str_replace(",",".",$num);
	return ($num);
}
//// --------------------------------------------------------------- ####
////  					MODULO ACTIVO FIJO
//// ---------------------------------------------------------------
//// FUNCION QUE PERMITE OBTENER LA DEPENDENCIA SEGUN EL ORGANISMO
//// SELECCIONADO
function getDependenciaTransferir($DEPENDENCIA, $ORGANISMO){
  connect();
  $s_dep = "select * from mastdependencias where CodOrganismo = '".$ORGANISMO."'";
  $q_dep = mysql_query($s_dep) or die ($s_dep.mysql_error()); //echo $s_dep;
  $r_dep = mysql_num_rows($q_dep);
  
  if($r_dep!='0'){
	for($i=0;$i<$r_dep;$i++){
	   $f_dep = mysql_fetch_array($q_dep);
	   echo"<option value='".$f_dep['CodDependencia']."'>".$f_dep['Dependencia']."</option>"; 
	}
  }
}
//// ---------------------------------------------------------------------
//// CARGAR ESTADO
function getEstado($fEstado, $opt){
    connect();
	switch ($opt) {
	      case 0:
		     $tstatus[0]="Preparaci&oacute;n"; $vstatus[0]="PR";
			 $tstatus[1]="Aprobado"; $vstatus[1]="AP";
			 $tstatus[2]="Anulado"; $vstatus[2]="AN";
			 $tcant=3;
		     for ($i=0; $i<$tcant; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
				else echo "<option value='".$vstatus[$i]."'>".$tstatus[$i]."</option>";
			}
			break;
		  case 1:
		     $tstatus[0]="Preparaci&oacute;n"; $vstatus[0]="PR";
			 $tstatus[1]="Aprobado"; $vstatus[1]="AP";
			 $tcant = 2;
		     for ($i=0; $i<$tcant; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
				else echo "<option value='".$vstatus[$i]."'>".$tstatus[$i]."</option>";
			}
			break;
		  case 2:
		     /*$tstatus[0]="Activo"; $vstatus[0]="A";
			 $tstatus[1]="Inactivo"; $vstatus[1]="I";*/
			 $tstatus[0]="Pendiente"; $vstatus[0]="PE";
			 $tstatus[1]="Aprobado"; $vstatus[1]="AP";
			 $tcant = 2;
		     for ($i=0; $i<$tcant; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
				else echo "<option value='".$vstatus[$i]."'>".$tstatus[$i]."</option>";
			}
			break;
			case 3:
		     $tstatus[0]="Preparaci&oacute;n"; $vstatus[0]="PR";
			 $tstatus[1]="Aprobado"; $vstatus[1]="AP";
			 $tstatus[2]="Anulado"; $vstatus[2]="AN";
			 $tcant = 3;
		     for ($i=0; $i<$tcant; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
				else echo "<option value='".$vstatus[$i]."'>".$tstatus[$i]."</option>";
			}
			break;
		    case 4:
		     $tstatus[0]="Activado"; $vstatus[0]="A";
			 $tstatus[1]="Inactivo"; $vstatus[1]="I";
			 $tcant = 2;
		     for ($i=0; $i<$tcant; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
				else echo "<option value='".$vstatus[$i]."'>".$tstatus[$i]."</option>";
			}
			break; 
			case 5:
		     $tstatus[0]="Preparaci&oacute;n"; $vstatus[0]="PR";
			 $tstatus[1]="Revisado"; $vstatus[1]="RV";
			 $tstatus[2]="Aprobado"; $vstatus[2]="AP";
			 $tstatus[3]="Anulado";$vstatus[3]="AN";
			 $tcant=4;
		     for ($i=0; $i<$tcant; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
				else echo "<option value='".$vstatus[$i]."'>".$tstatus[$i]."</option>";
			}
			break;
			case 6:
		     $tstatus[0]="Preparaci&oacute;n"; $vstatus[0]="PR";
			 $tstatus[1]="Revisado"; $vstatus[1]="RV";
			 $tstatus[2]="Aprobado"; $vstatus[2]="AP";
			 $tstatus[3]="Anulado";$vstatus[3]="AN";
			 $tcant=4;
		     for ($i=0; $i<$tcant; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
			}
			break;
	}	
}
/// -------------------------------------------------------------------------
/// CARGAR SITUACION ACTIVO
function getSituacionActivo($fSituacionActivo, $opt){
connect();
	switch ($opt) {
		case 0:
			$sql = "SELECT CodSituActivo, Descripcion FROM af_situacionactivo";
			$query = mysql_query($sql) or die ($sql.mysql_error());
			$rows = mysql_num_rows($query);
			for($i=0; $i<$rows; $i++) {
				$field = mysql_fetch_array($query);
				if($field['0']==$fSituacionActivo)echo"<option value='".$field['0']."' selected>".$field['1']."</option>";
				else echo"<option value='".$field['0']."'>".$field['1']."</option>";
			}
			break;
		case 1:
			$sql = "SELECT CodSituActivo, Descripcion FROM af_situacionactivo WHERE CodSituActivo<>'DE'";
			$query = mysql_query($sql) or die ($sql.mysql_error());
			$rows = mysql_num_rows($query);
			for($i=0; $i<$rows; $i++) {
				$field = mysql_fetch_array($query);
				if($field['0']==$fSituacionActivo)echo"<option value='".$field['0']."' selected>".$field['1']."</option>";
				else echo"<option value='".$field['0']."'>".$field['1']."</option>";
			}
			break;
	}
}
/// ---------------------------------------------------------------------------
///  CARGAR TIPO SEGURO
function getT_Seguro($ftseguro, $opt){
connect();
	switch ($opt) {
		case 0:
			$sql = "SELECT CodTipoSeguro, Descripcion FROM af_tiposeguro";
			$query = mysql_query($sql) or die ($sql.mysql_error());
			$rows = mysql_num_rows($query);
			for($i=0; $i<$rows; $i++) {
				$field = mysql_fetch_array($query);
				if($field['0']==$ftseguro)echo"<option value='".$field['0']."' selected>".$field['1']."</option>";
 				else echo"<option value='".$field['0']."'>".$field['1']."</option>";
			}
			break;
	}
}
/// ---------------------------------------------------------------------------
/// CARGAR COLOR
function getColor($fcolor, $opt){
connect();
   switch($opt){
	   case 0:
	      $sql = "SELECT CodDetalle, Descripcion FROM mastmiscelaneosdet WHERE CodMaestro = 'COLOR'";
		  $qry = mysql_query($sql) or die ($sql.mysql_error());
		  $rows = mysql_num_rows($qry);
		  for($i=0; $i<$rows; $i++){
		    $field= mysql_fetch_array($qry);
			if($field['0']==$fcolor)echo"<option value='".$field['0']."' selected>".$field['1']."</option>";
			else echo"<option value='".$field['0']."'>".$field['1']."</option>";
		  }
		  break;
   }
}
/// ---------------------------------------------------------------------------
/// CARGAR CATEGORIA
function getCategoria($fCategoria, $opt){
connect();
  switch ($opt){
     case 0: 
	     $sql = "select CodCategoria, DescripcionLocal from af_categoriadeprec";
		 $qry = mysql_query($sql) or die ($sql.mysql_error());
		 $rows = mysql_num_rows($qry);
		 for($i=0; $i<$rows; $i++){
		  	 $field = mysql_fetch_array($qry);
			 if($field['0']==$fCategoria)echo"<option value='".$field['0']."' selected>".$field['0']." - ".$field['1']."</option>";
			 else echo"<option value='".$field['0']."'>".$field['0']." - ".$field['1']."</option>";
		 }
		 break;
	case 1: 
	     $sql = "select CodCategoria, DescripcionLocal from af_categoriadeprec";
		 $qry = mysql_query($sql) or die ($sql.mysql_error());
		 $rows = mysql_num_rows($qry);
		 for($i=0; $i<$rows; $i++){
		  	 $field = mysql_fetch_array($qry);
			 if($field['0']==$fCategoria)echo"<option value='".$field['0']."' selected>".$field['1']."</option>";
			 else echo"<option value='".$field['0']."'>".$field['1']."</option>";
		 }
		 break;
  }
}
/// ----------------------------------------------------------------------------
/// CARGAR CLASIFICACION ACTIVO
function getClasifActivo($fCatClasf, $opt){
connect();
  switch ($opt){
     case 0: 
	     $sql = "select CodClasificacion, Descripcion from af_clasificacionactivo";
		 $qry = mysql_query($sql) or die ($sql.mysql_error());
		 $rows = mysql_num_rows($qry);
		 for($i=0; $i<$rows; $i++){
		  	 $field = mysql_fetch_array($qry);
			 echo"<option value='".$field['0']."'>".$field['1']."</option>";
		 }
		 break;
  }
}
/// ----------------------------------------------------------------------------
/// CARGAR TIPOACTIVO
function getTipoActivo($fTipoActivo, $opt){
connect();
  switch ($opt){
     case 0: 
	     $sql = "select CodDetalle,Descripcion from mastmiscelaneosdet where CodMaestro = 'TIPOACTIVO'";
		 $qry = mysql_query($sql) or die ($sql.mysql_error());
		 $rows = mysql_num_rows($qry);
	     for($i=0; $i<$rows; $i++){
		  	 $field = mysql_fetch_array($qry);
			 if($field['0']==$fTipoActivo)echo"<option value='".$field['0']."' selected>".$field['1']."</option>";
			 else echo"<option value='".$field['0']."'>".$field['1']."</option>";
		 }
		 break;
  }
}
/// ----------------------------------------------------------------------------
function getEstadoListActivo($fEstado, $opt){
	$tstatus[0]="Pendiente de Activar"; $vstatus[0]="PE";
	$tstatus[1]="Activado"; $vstatus[1]="AC";
	$cantidad = 2;
	switch ($opt) {
	      case 0:
		     for ($i=0; $i<$cantidad; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
				else echo "<option value='".$vstatus[$i]."'>".$tstatus[$i]."</option>";
			}
			break;
	}	
}
/// *****************************************************************************
///                     Referencia: af_listactivos.php
/// *****************************************************************************
function getEstadoListActivo2($fEstado, $opt){
	$tstatus[0]="Pendiente de Activar"; $vstatus[0]="PE";
	$tstatus[1]="Activado"; $vstatus[1]="AP";
	$cantidad = 2;
	switch ($opt) {
	      case 0:
		     for ($i=0; $i<$cantidad; $i++) {
				if ($fEstado==$vstatus[$i]) echo "<option value='".$vstatus[$i]."' selected>".$tstatus[$i]."</option>";
				else echo "<option value='".$vstatus[$i]."'>".$tstatus[$i]."</option>";
			}
			break;
	}	
}
/// *****************************************************************************
///                     Referencia: af_listactivos.php
/// *****************************************************************************
function getEstadoConservacion($fEstadoConservacion, $opt){
	
 $s_estconv= "select * from mastmiscelaneosdet where CodMaestro='ESTCONSERV'";
 $q_estconv= mysql_query($s_estconv) or die ($s_estconv.mysql_error());
 $r_estconv= mysql_num_rows($q_estconv);
    
switch($opt){
  case 0:
	for($i=0; $i<$r_estconv; $i++) {
	  $field = mysql_fetch_array($q_estconv); 
	  if($fEstadoConservacion==$field['CodDetalle']) 
		 echo"<option value='".$field['CodDetalle']."' selected>".$field['Descripcion']."</option>";
	  else 
		 echo "<option value='".$field['CodDetalle']."'>".$field['Descripcion']."</option>";
	}
	break;
}	
} 

/// ----------------------------------------------------------------------------
/// SEGURIDAD ALTERNA - CARGA DEPENDENCIA 
function getDependenciaSeguridad($dependencia, $organismo, $opt) {
	connect();
	if ($opt==3 && $_SESSION["USUARIO_ACTUAL"]==$_SESSION["SUPER_USUARIO"]) $opt=0;
	switch ($opt) {
		case 0:
			$sql="SELECT CodDependencia, Dependencia FROM mastdependencias WHERE CodOrganismo='".$organismo."' AND CodDependencia<>'' ORDER BY CodDependencia";
			$query=mysql_query($sql) or die ($sql.mysql_error());
			$rows=mysql_num_rows($query);
			for ($i=0; $i<$rows; $i++) {
				$field=mysql_fetch_array($query);
				if ($field[0]==$dependencia) echo "<option value='".$field[0]."' selected>".htmlentities($field[1])."</option>"; 
				else echo "<option value='".$field[0]."'>".htmlentities($field[1])."</option>";
			}
			break;
		case 1:
			$sql="SELECT CodDependencia, Dependencia FROM mastdependencias WHERE CodDependencia='".$dependencia."'";
			$query=mysql_query($sql) or die ($sql.mysql_error());
			$rows=mysql_num_rows($query);
			if ($rows!=0) {
				$field=mysql_fetch_array($query);
				echo "<option value='".$field[0]."'>".htmlentities($field[1])."</option>";
			}
			break;
		case 3:
			$sql="SELECT s.CodDependencia, o.Dependencia FROM seguridad_alterna s INNER JOIN mastdependencias o ON (s.CodDependencia=o.CodDependencia) WHERE s.Usuario='".$_SESSION["USUARIO_ACTUAL"]."' AND s.CodAplicacion='".$_SESSION["APLICACION_ACTUAL"]."' AND s.FlagMostrar='S' AND s.CodOrganismo='$organismo' GROUP BY s.CodDependencia ORDER BY s.CodDependencia";
			$query=mysql_query($sql) or die ($sql.mysql_error());
			$rows=mysql_num_rows($query);
			for ($i=0; $i<$rows; $i++) {
				$field=mysql_fetch_array($query);
				if ($field[0]==$dependencia) echo "<option value='".$field[0]."' selected>".htmlentities($field[1])."</option>"; 
				else echo "<option value='".$field[0]."'>".htmlentities($field[1])."</option>";
			}
			break;
	}
}
//// ---------------------------------------------------------------------
//// PARA CARGAR SELECT DE NATURALEZA DE ACTIVO
function getNaturaleza($fNaturaleza, $opt){
  connect();
  switch ($opt){
      case 0:
	       $tvalor[0]="Activo Menor"; $vvalor[0]="AM";
	       $tvalor[1]="Activo Normal"; $vvalor[1]="AN";
		   $valor= 2;
	       for($i=0;$i<$valor;$i++){
			  if($fNaturaleza==$vvalor[$i]) echo"<option value='$vvalor[$i]' selected>$tvalor[$i]</option>";
			  else echo"<option value='$vvalor[$i]'>$tvalor[$i]</option>";
		   }
  }
} 
//// ---------------------------------------------------------------------
//// PARA CARGAR SELECT DE ORDENAR POR
function getOrdenarPor($fOrdenarPor,$opt){
  connect();
  switch($opt){
      case 0:
	       $tvalor[0]="Activo"; $vvalor[0]="Activo";
	       $tvalor[1]="Codigo Interno"; $vvalor[1]="CodigoInterno";
		   $tvalor[2]="Descripcion"; $vvalor[2]="Descripcion";
		   $valor= 3;
	       for($i=0;$i<$valor;$i++){
			  if($fOrdenarPor==$vvalor[$i]) echo"<option value='$vvalor[$i]' selected>$tvalor[$i]</option>";
			  else echo"<option value='$vvalor[$i]'>$tvalor[$i]</option>";
		   }   
  
  }
}
//// ---------------------------------------------------------------------
//// PARA CARGAR SELECT DE ORDENAR POR
function getContabilidad($fContabilidad, $opt){
 connect();
 switch($opt){
  case 0:
       $sql = "select * from ac_contabilidades";
	   $qry = mysql_query($sql) or die ($sql.mysql_error());
	   $row = mysql_num_rows($qry);
	   for($i=0;$i<$row;$i++){
	      $field = mysql_fetch_array($qry);
	      if($field['CodContabilidad']==$fContabilidad) 
	        echo"<option value='".$field['CodContabilidad']."' selected>".$field['Descripcion']."</option> ";
	      else 
	        echo"<option value='".$field['CodContabilidad']."'>".$field['Descripcion']."</option> ";
	   }
 }
}
//// ---------------------------------------------------------------------
//// PARA CARGAR SELECT MOSTRAR AF_AGRUPARCONSOLIDARACT.PHP
function getMostrar($fMostrar,$opt){
  connect();
  switch($opt){
      case 0:
	       $tvalor[0]="Activos sin relacionar"; $vvalor[0]="SR";
	       $tvalor[1]="Activos relacionados"; $vvalor[1]="AR";
		   $tvalor[2]="Todos los Activos"; $vvalor[2]="TA";
		   $valor= 3;
	       for($i=0;$i<$valor;$i++){
			  if($fMostrar==$vvalor[$i]) echo"<option value='$vvalor[$i]' selected>$tvalor[$i]</option>";
			  else echo"<option value='$vvalor[$i]'>$tvalor[$i]</option>";
		   }   
           break;  
  }
}
//// ---------------------------------------------------------------------
//// 
function getBienes($fBienes,$opt){
    connect();
	switch($opt){
	  case 0:
	       $tvalor[0]="Inmuebles"; $vvalor[0]="01";
	       $tvalor[1]="Muebles";   $vvalor[1]="02";	
		   $valor= 2;
		   for($i=0; $i<$valor; $i++){
		     if($fBienes==$vvalor[$i]) echo"<option value='$vvalor[$i]' selected>$tvalor[$i]</option>";
			 else echo"<option value='$vvalor[$i]'>$tvalor[$i]</option>";
		   }
		   break;
	}
}
//// ---------------------------------------------------------------------
//// 
function myTruncate($string, $limit, $break, $pad) {
if(strlen($string) <= $limit)
return $string;
if(false!== ($breakpoint = strpos($string,$break,$limit))) {
 if($breakpoint < strlen($string)){
    $string = substr($string, 0, $breakpoint) . $pad;
  }
}
return $string;
}
//// ---------------------------------------------------------------------
//// 
function getTipoCuenta($fBienes,$opt){
    connect();
	switch($opt){
	  case 0:
	       $tvalor[0]="Cuentas del Tesoro"; $vvalor[0]="CT";
	       $tvalor[1]="Cuentas de la Hacienda";   $vvalor[1]="CH";	
		   $tvalor[2]="Cuentas del Presupuesto";   $vvalor[2]="CP";
		   $tvalor[3]="Cuentas de Resultado del Presupuesto";  $vvalor[3]="CP";	
		   $tvalor[4]="Cuentas de Patrimonio";  $vvalor[4]="CD";	
		   $valor= 5;
		   for($i=0; $i<$valor; $i++){
		     if($fBienes==$vvalor[$i]) echo"<option value='$vvalor[$i]' selected>$tvalor[$i]</option>";
			 else echo"<option value='$vvalor[$i]'>$tvalor[$i]</option>";
		   }
		   break;
	}
}
/// -------------------------------------------------------------------
function SituacionGeneracion($fSituacion, $opt){
  connect();
  switch($opt){
   case 0: 
          $tvalor[0]="Activos Pendientes"; $vvalor[0]="=";
		  $tvalor[1]="Activos Generados"; $vvalor[1]="<>";	
		  $valor=2;
		  for($i=0; $i<$valor; $i++){
		    if($fSituacion==$vvalor[$i]) echo"<option value='$vvalor[$i]' selected>$tvalor[$i]</option>";
			else echo"<option value='$vvalor[$i]'>$tvalor[$i]</option>";
		  }   
  }

}
/// -------------------------------------------------------------------
function getTipoActa($fTipoActa, $opt){
  connect();
  switch($opt){
   case 0: 
          $tvalor[0]="Asignacion"; $vvalor[0]="AA";
		  $tvalor[1]="Movimiento"; $vvalor[1]="AE";	
		  $valor=2;
		  for($i=0; $i<$valor; $i++){
		    if($fTipoActa==$vvalor[$i]) echo"<option value='$vvalor[$i]' selected>$tvalor[$i]</option>";
			else echo"<option value='$vvalor[$i]'>$tvalor[$i]</option>";
		  }   
  }

}
/// -------------------------------------------------------------------
function getTipoMovimientos($fTipoMov, $opt){
  connect();
  switch($opt){
   case 0: 
          $sql = "select * from af_tipomovimientos";
		  $qry = mysql_query($sql) or die ($sql.mysql_error());
		  $row = mysql_num_rows($qry);
		  if($row!=0)
		  for($i=0; $i<$row; $i++){
			$field = mysql_fetch_array($qry);
		    if($fTipoMov==$field['CodTipoMovimiento']) 
			   echo"<option value='".$field['CodTipoMovimiento']."' selected>'".$field['CodTipoMovimiento']."'-'".$field['DescpMovimiento']."'</option>";
			else 
			   echo"<option value='".$field['CodTipoMovimiento']."'>'".$field['CodTipoMovimiento']."'-'".$field['DescpMovimiento']."'</option>";
		  }   
  }

}
?>
