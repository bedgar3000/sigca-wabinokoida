<?php
//// -------------------------------------------------------------------- ####
////                               CARGAR ESTADO
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

//// -------------------------------------------------------------------- ####
////                          CARGAR SITUACION ACTIVO
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

//// -------------------------------------------------------------------- ####
////                     Referencia: af_listactivos.php
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

//// -------------------------------------------------------------------- ####
////                     FUNCION FILTRO BIENES
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

//// -------------------------------------------------------------------- ####
////            PARA CARGAR SELECT DE NATURALEZA DE ACTIVO
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
//// -------------------------------------------------------------------- ####
////            SEGURIDAD ALTERNA - CARGA DEPENDENCIA 
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
//// -------------------------------------------------------------------- ####
////
function getSeleccionTipoActa($ftipoacta, $opt){
	connect();
	  switch ($opt){
	      case 0:
		       $tvalor[0]="Acta Incorporacion"; $vvalor[0]="AI";
		       $tvalor[1]="Acta Asignación"; $vvalor[1]="AA";
		       $tvalor[2]="Acta Entrega"; $vvalor[2]="AE";
		       $tvalor[3]="Acta Responsabilidad Uso"; $vvalor[3]="AR";

			   $valor= 4;
		       for($i=0;$i<$valor;$i++){
				  if($ftipoacta==$vvalor[$i]) echo"<option value='$vvalor[$i]' selected>$tvalor[$i]</option>";
				  else echo"<option value='$vvalor[$i]'>$tvalor[$i]</option>";
			   }
	  }
}
//// -------------------------------------------------------------------- ####
////            PARA CARGAR SELECT DE NATURALEZA DE ACTIVO
/*function getFirmaxDependencia($CodDependencia, $swNomCompleto=NULL, $swEstado=NULL) {
	global $_PARAMETRO;
	//	obtengo responsable de la dependencia
	$sql = "SELECT
				d.CodPersona,
				d.CodCargo AS CodCargoDependencia,
				e.CodCargo AS CodCargoEmpleado,
				e.CodCargoTemp AS CodCargoEmpleadoTemp
			FROM
				mastdependencias d
				INNER JOIN mastempleado e ON (e.CodPersona = d.CodPersona)
			WHERE d.CodDependencia = '".$CodDependencia."'";
	 $qry = mysql_query($sql) or die ($sql.mysql_error());
	 $field= mysql_fetch_array($qry);
	//	valido si es encargado
	if ($field['CodCargoDependencia'] == $field['CodCargoEmpleado'] || $field['CodCargoDependencia'] == $field['CodCargoEmpleadoTemp'])
		list($Nombre, $Cargo, $Nivel) = getFirma($field['CodPersona'], $swNomCompleto, $swEstado);
	else
		list($Nombre, $Cargo, $Nivel) = getFirma($field['CodPersona'], $swNomCompleto, $swEstado, $field['CodCargoDependencia']);
	##
	return array($Nombre, $Cargo, $Nivel);
}*/

?>