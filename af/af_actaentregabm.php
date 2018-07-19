<?php
// ------------------------------------- ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect();

class PDF extends FPDF{

	function Header(){


		global $Anio, $CodOrganismo, $nroActaEntrega;

		/// Obteniendo nro de acta de Entrega

		$sql = "select
					  a.FechaActa,
					  b.FechaRevisadoPor,
					  b.InventarioFisicoFecha
				  from
				     af_actaentregaactivo a
					 inner join af_activo b on (b.Activo=a.Activo and b.CodOrganismo=a.CodOrganismo)
			    where
					 a.CodOrganismo='".$CodOrganismo."' and
					 a.NroActa='".$nroActaEntrega."' and
					 a.Anio = '".$Anio."' and
					 a.TipoActa = 'AA'";
	    $qry = mysql_query($sql) or die ($sql.mysql_error());
	    $row = mysql_num_rows($qry);
		if($row !=0) $field = mysql_fetch_array($qry);
		//global $Periodo;
		global $fp_hasta,$fp_desde;
		//echo $Periodo.'/'.$fp_hasta.'****';

		//list($sano, $smes, $sdia) = split('[-]', $field['FechaRevisadoPor']);
		list($sano, $smes, $sdia) = split('[-]', $field['FechaActa']);
		list($a, $m, $d) = split('[-]', $field['Fecha']);

		$this->Image('../imagenes/logos/logo.jpg', 20, 10, 15, 15);
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(35, 10); $this->Cell(100, 8,utf8_decode( 'República Bolivariana de Venezuela'), 0, 1, 'L');
		$this->SetXY(35, 14); $this->Cell(100, 8,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L');
		$this->SetXY(35, 18); $this->Cell(100, 8,utf8_decode('Dirección de Servicios Generales'), 0, 1, 'L');

		$this->SetXY(35, 10); $this->Cell(140, 8, 'Fecha:', 0, 0, 'R');
		//$this->Cell(10, 8,"19-12-2013",0,1,'');
		//$this->Cell(10, 8,date("d-m-Y"),0,1,'');
		$this->Cell(10, 8, $sdia."-".$smes."-".$sano,0,1,'');
		$this->SetXY(20, 14); $this->Cell(155, 8, utf8_decode('Pág.:'), 0, 1, 'R'); /// NRO DE PÁGINA

		$this->SetXY(20, 18); $this->Cell(155, 8, utf8_decode('Nro.:'), 0, 0, 'R');/// NRO DE DOCUMENTO
							  $this->Cell(10, 8, $nroActaEntrega.'-'.$sano, 0, 1, 'L');$this->Ln(5);

		$this->SetFont('Arial', 'B', 10);
		   $this->Cell(50, 5, '', 0, 0, 'C');
		   $this->Cell(100, 5, utf8_decode('ACTA DE ASIGNACIÓN DE BIENES MUEBLES'), 0, 1, 'C');
		   $this->Ln(1);

	     $this->Ln();
  }
	//Page footer
	function Footer(){
	    //Position at 1.5 cm from bottom
	    $this->SetXY(152,14);
	    //Arial italic 8
	    $this->SetFont('Arial','I',8);
	    //Page number
	    $this->Cell(0,8,' '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

//Instanciation of inherited class
$pdf=new PDF('P','mm','letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->SetAutoPageBreak(1,4);


// BUSCO EL O LOS ACTIVOS SEGUN EL NRO DE ACTA DE ENTREGA
$sa = "select a.*, b.*
	       from af_actaentregaactivo a
			        inner join af_activo b on (b.Activo=a.Activo and b.CodOrganismo=a.CodOrganismo and a.NroActa=b.NroActaEntrega)
		    where
		         a.CodOrganismo='".$CodOrganismo."' and
			       a.NroActa='".$nroActaEntrega."' and
			       a.Anio = '".$Anio."' and
			       a.TipoActa ='AA'";
$qa = mysql_query($sa) or die ($sa.mysql_error());
$ra = mysql_num_rows($qa);
if($ra!=0) $fa = mysql_fetch_array($qa);

// CONSULTA DE DATOS
$scon = "select
				a.AprobadoPor,
				mp.NomCompleto as NomCompletoAprobadoPor,
				mp.NDocumento as NDocumentoAprobadoPor,
				a.ClasificacionPublic20,
				c.Descripcion as DescripClasificacion20,
				a.Activo,
				a.Descripcion as DescripActivo,
				a.Ubicacion,
				b.Descripcion as DescripUbicacion,
				a.MontoLocal,
				a.FechaRevisadoPor,
				a.UltimaFechaModif,
				mp2.NomCompleto as NomCompletoUsuario,
    			mp2.NDocumento as NDocumentoUsuario,
				mp5.NomCompleto as NomCompletoResponsable,
				mp5.NDocumento as NDocumentoResponsable,
				a.RevisadoPor,
				a.CargoRevisadoPor,
				a.ConformadoPor,
				a.CargoConformadoPor,
				a.AprobadoPor,
				a.CargoAprobadoPor,
				a.EmpleadoUsuario,
				a.Marca,
				a.Modelo,
				mp3.NomCompleto as NomCompletoConformadoPor,
    			mp3.NDocumento as NDocumentoConformadoPor,
				mp4.NDocumento as NDocumentoRevisadoPor,
				a.CodigoInterno,
				a.EmpleadoResponsable,
				a.CodDependencia
			from
				af_activo a
				inner join mastpersonas mp on (mp.CodPersona=a.AprobadoPor)
				inner join mastpersonas mp2 on (mp2.CodPersona=a.EmpleadoUsuario)
				inner join mastpersonas mp3 on (mp3.CodPersona=a.ConformadoPor)
				inner join mastpersonas mp4 on (mp4.CodPersona=a.RevisadoPor)
				inner join mastpersonas mp5 on (mp5.CodPersona=a.EmpleadoResponsable)
				inner join af_ubicaciones b on (b.CodUbicacion=a.Ubicacion)
				inner join af_clasificacionactivo20 c on (c.CodClasificacion=a.ClasificacionPublic20)
		    where
			    a.Activo='".$fa['Activo']."' and
				a.Estado='AP' and
				a.CodOrganismo = '".$_GET['CodOrganismo']."'";
$qcon = mysql_query($scon) or die ($scon.mysql_error());
$rcon = mysql_num_rows($qcon);
if($rcon!=0)$fcon=mysql_fetch_array($qcon);


list($anos, $meses, $dias, $hora) = split('[-, ]', $fa['UltimaFechaModif']);
 //echo $anos, $meses, $dias, $hora;
 list($h, $m, $s) = split('[:]', $hora);
 if($h>12)$t = "pm"; else $t = "am";
 if($h==13)$h="01";
 elseif($h==14)$h="02";
 elseif($h==15)$h="03";
 elseif($h==16)$h="04";
 elseif($h==17)$h="05";
 elseif($h==18)$h="06";
 elseif($h==19)$h="07";
 elseif($h==20)$h="08";
 elseif($h==21)$h="09";
 elseif($h==22)$h="10";
 elseif($h==23)$h="11";
 elseif($h==24)$h="12";

 $hora = $h.':'.$m.':'.$s;

 //list($ano, $mes, $dia) = split('[-]', $fa['FechaRevisadoPor']);
 list($ano, $mes, $dia) = split('[-]', $fa['FechaActa']);
 $fecha = $dia.'-'.$mes.'-'.$ano;

 switch($mes){
		case "01": $fmes= Enero;break;
		case "02": $fmes= Febrero;break;
		case "03": $fmes= Marzo;break;
		case "04": $fmes= Abril;break;
		case "05": $fmes= Mayo;break;
		case "06": $fmes= Junio;break;
		case "07": $fmes= Julio; break;
		case "08": $fmes= Agosto; break;
		case "09": $fmes= Septiembre; break;
		case "10": $fmes= Octubre; break;
		case "11": $fmes= Noviembre; break;
		case "12": $fmes= Diciembre; break;
    }

$montoLocal = number_format($fcon['MontoLocal'],2,',','.');

/// Consulta realizada para obtener el cargo actual del empleado Usuario
$scon03 = "select
							  a.CodPersona,
							  b.DescripCargo
						 from
							  rh_empleadonivelacion a
							  inner join rh_puestos b on (a.CodCargo=b.CodCargo)
			 			where
							  a.Secuencia=(select max(Secuencia) from rh_empleadonivelacion where CodPersona='".$fcon['EmpleadoResponsable']."') and
							  a.CodPersona='".$fcon['EmpleadoResponsable']."' "; //echo $scon03;
$qcon03 = mysql_query($scon03) or die ($scon03.mysql_error());
$fcon03 = mysql_fetch_array($qcon03);

/// Se obtienen la descripcion de cargos de los firmanmtes
$cargo[0]= $fcon['CargoRevisadoPor'];
$cargo[1]= $fcon['CargoConformadoPor'];
$cargo[2]= $fcon['CargoAprobadoPor'];
$v_cargo = 3;

for($x=0; $x<$v_cargo; $x++){
 $scargos = "select DescripCargo from rh_puestos where CodCargo ='$cargo[$x]'"; //echo $scargos, $x;
 $qcargos = mysql_query($scargos) or die ($scargos.mysql_error());
 $fcargos = mysql_fetch_array($qcargos);

 if($x==0)$d_cargoRevisadoPor=$fcargos['DescripCargo'];
 if($x==1)$d_cargoConformadoPor=$fcargos['DescripCargo'];
 if($x==2)$d_cargoAprobadoPor=$fcargos['DescripCargo'];
}

/// Se obtienen los nombres de los firmantes + número de cédula
$cedula[0]=$fcon['RevisadoPor'];
$cedula[1]=$fcon['ConformadoPor'];
$cedula[2]=$fcon['AprobadoPor'];
$v_cedula = 3;

for($y=0; $y<$v_cedula; $y++){
  $scn = "select NomCompleto,Ndocumento  from mastpersonas where CodPersona='$cedula[$y]'";
  $qcn = mysql_query($scn) or die ($scn.mysql_error());
  $fcn = mysql_fetch_array($qcn);

  if($y==0){$n_RevisadoPor=$fcn['NomCompleto']; $c_RevisadoPor=$fcn['Ndocumento'];}
  if($y==1){$n_ConformadoPor=$fcn['NomCompleto'];$c_ConformadoPor=$fcn['Ndocumento'];}
  if($y==2){$n_AprobadoPor=$fcn['NomCompleto']; $c_AprobadoPor=$fcn['Ndocumento'];}
}


	 list($nombreCompleto, $cargo, $nivel01) = getFirma($fcon['RevisadoPor']);
	 list($nombreCompleto02, $cargo02, $nivel02) = getFirma($fcon['ConformadoPor']);
   list($nombreCompleto03, $cargo03, $nivel03) = getFirma($fcon['AprobadoPor']);
	 list($nombreCompleto04, $cargo04, $nivel04) = getFirma($fcon['EmpleadoResponsable']);

function nameDate($fecha){
  //$fecha= empty($fecha)?date('d/m/Y'):$fecha;
	$dias = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
	$dd   = explode('-',$fecha);
	$ts   = mktime(0,0,0,$dd[1],$dd[0],$dd[2]);
	//return $dias[date('w',$ts)].'/'.date('m',$ts).'/'.date('Y',$ts);
	return $dias[date('w',$ts)];
}

$mesNombre = nameDate($fecha);


///-------------------------------------------------------///
$DCA = $fa['DescripCargoAprob']; // Descripcion Cargo Aprobador
///-------------------------------------------------------///
///-------------------------------------------------------///

//echo strtr(strtolower("RODRÍGUEZ"), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú");

///  Convertidos de datos
$n_ConformadoPor = ucwords(strtr(strtolower(utf8_encode($n_ConformadoPor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //Directora General
$cargo02 = ucwords(strtr(strtolower(utf8_encode($cargo02)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Cargo Directora General

$n_AprobadoPor = ucwords(strtr(strtolower(utf8_encode($n_AprobadoPor)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Quien Aprueba
//$cargo03 = ucwords(strtr(strtolower(utf8_encode($cargo03)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú"));
$cargo03 = ucwords(strtr(strtolower(utf8_encode($DCA)),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú"));

$empleado_responsable = ucwords(strtr(strtolower(utf8_encode($fcon['NomCompletoResponsable'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Empleado Responsable
//$empleado_responsable_cargo =  ucwords(strtr(strtolower(utf8_encode($fcon03['DescripCargo'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Cargo Empleado Responsable
$empleado_responsable_cargo =  ucwords(strtr(strtolower(utf8_encode($fa['DescripCargoRespon'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); // Cargo Empleado Responsable

 //// Obteniendo datos de la ubicacion del o los activos
 //$subic = "select CodUbicacion, Descripcion from af_ubicaciones where CodUbicacion='".$fcon['Ubicacion']."'";
 $subic = "select Dependencia, CodDependencia from mastdependencias where CodDependencia='".$fcon['CodDependencia']."'";
 $qubic = mysql_query($subic) or die ($subic.mysql_error());
 $rubic = mysql_num_rows($qubic);
 if($rubic != 0) $fubic = mysql_fetch_array($qubic);
 $ubicacion = ucwords(strtr(strtolower(utf8_encode($fubic['Dependencia'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú"));


$parrafo1 = utf8_decode("En el día de hoy, ").utf8_decode($mesNombre).' '.$dia.(" del mes de ").$fmes.utf8_decode(" del año ").$ano.utf8_decode(", reunidos en las instalaciones donde funciona la Contraloría del Estado Delta Amacuro, ubicada en la Calle Centurión, Quinta Paola N° 36, Municipio Tucupita, Estado Delta Amacuro, los ciudadanos: ").$n_ConformadoPor.utf8_decode(", titular de la cédula de identidad N°").$fcon['NDocumentoConformadoPor'].' '.$cargo02.(", ").$n_AprobadoPor.utf8_decode(", titular de la cédula de identidad N°").$c_AprobadoPor.' '.$cargo03.(" y ").$empleado_responsable.utf8_decode(", titular de la cédula de identidad N°").$fcon['NDocumentoResponsable'].utf8_decode(" quien desempeña el cargo de ").$empleado_responsable_cargo.utf8_decode(", con el objeto de asignarle al último de los mencionados como Responsable Patrimonial Primario; en calidad de uso, guarda y custodia de los bienes muebles que a continuación se especifican:");

/*$parrafo1 = utf8_decode("En el día de hoy, ").utf8_decode($mesNombre).' '.$dia.(" del mes de ").$fmes.utf8_decode(" del año ").$ano.utf8_decode(", reunidos en las instalaciones donde funciona la Contraloría del Estado Delta Amacuro, ubicada en la Calle Centurión, Quinta Paola N° 36, Municipio Tucupita, Estado Delta Amacuro, los ciudadanos: ").$n_ConformadoPor.utf8_decode(", titular de la cédula de identidad N°").$fcon['NDocumentoConformadoPor'].' '.$cargo02.(", ").$n_AprobadoPor.utf8_decode(", titular de la cédula de identidad N° ").$c_AprobadoPor.' '.$cargo03.(" y ").utf8_decode('Omairys Josefina Montero Patiño').utf8_decode(", titular de la cédula de identidad N° ").$fcon['NDocumentoUsuario'].utf8_decode(" quien desempeña el cargo de ").$empleado_responsable_cargo.utf8_decode(", con el único objeto de hacerle entrega al último de los mencionados como Responsable Patrimonial Primario; en calidad de uso, guarda y custodia del bien mueble que a continuación se especifica:");
*/
$parrafo2 = utf8_decode("De igual manera se certifica que los bienes antes mencionados se encuentran ubicados fisicamente en ").utf8_decode($ubicacion).utf8_decode(" y los mismos son propiedad de este ente de Control Fiscal, tal como se desprende del registro de Bienes e Inventarios llevado ante esta Contraloría, el mismo quedará bajo su responsabilidad absoluta, siendo éste responsable de vigilar, conservar y salvaguardar, los bienes muebles entregados mediante la presente Acta; queda entendido que cualquier daño material que pueda ocurrirle a los referidos bienes muebles; con ocasión de negligencia u omisión en su uso; queda sujeta a las sanciones administrativas previstas en articulo 91 numeral 2 de la Ley Orgánica de la Contraloría General de la República y del Sistema Nacional de Control Fiscal,Disciplinarias prevista en el artículo 33 numeral 7 de la Ley del Estatuto de Función Pública y Penal prevista en el artículo 53 de la Ley Contra la Corrupcion, salvo aquellos daños naturales u hechos fortuitos que se presenten, lo cual deberá ser notificado por escrito ante la Dirección de Servicios Generales de ésta Contraloría. Es todo, terminó se leyó y conformes firman.");

$pdf->SetFont('Arial', '', 11);
		//$pdf->SetXY(20,43);
		$pdf->Cell(12,'','','');
		$pdf->MultiCell(175, 6, $parrafo1, 0, 'J');
		$pdf->Ln(2);

$pdf->SetFont('Arial', '', 7);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(200, 200, 200); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(12,'','','');
	$pdf->Cell(24, 3, 'CLASIFICACION', 1, 0, 'C', 1);
	$pdf->Cell(14, 3, 'CANTIDAD', 1, 0, 'C', 1);
	$pdf->Cell(20, 3, utf8_decode('COD. INTERNO'), 1, 0, 'C', 1);
	$pdf->Cell(50, 3, 'DESCRIPCION', 1, 0, 'C', 1);
	$pdf->Cell(35, 3, 'MARCA', 1, 0, 'C', 1);
	$pdf->Cell(30, 3, 'MODELO', 1, 1, 'C', 1); //$pdf->Ln();

// BUSCO EL O LOS ACTIVOS SEGUN EL NRO DE ACTA DE ENTREGA
$sa = "select
              a.*,
			  b.*
	     from
		      af_actaentregaactivo a
			  inner join af_activo b on (b.Activo=a.Activo and b.CodOrganismo=a.CodOrganismo and a.NroActa=b.NroActaEntrega)
		where
		      a.CodOrganismo='".$CodOrganismo."' and
			  a.NroActa='".$nroActaEntrega."' and
			  a.TipoActa = 'AA' and
			  a.Anio = '".$Anio."'";
$qa = mysql_query($sa) or die ($sa.mysql_error());
$ra = mysql_num_rows($qa);
if($ra!=0)
   for($i=0; $i<$ra; $i++){
	   $fa = mysql_fetch_array($qa);

	   $s_marca = "select * from lg_marcas where CodMarca='".$fa['Marca']."'";
	   $q_marca = mysql_query($s_marca) or die ($s_marca.mysql_error());
	   $r_marca = mysql_num_rows($q_marca);
	   if($r_marca != 0)$f_marca = mysql_fetch_array($q_marca);

$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
$pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 7);
	 $pdf->SetWidths(array(24,14,20,50,35,30));
	 $pdf->SetAligns(array('C','C','C','L','L','l'));
	 $pdf->Row(array($fa['ClasificacionPublic20'],'1',$fa['CodigoInterno'],utf8_decode($fa['Descripcion']),$f_marca['Descripcion'],$fa['Modelo']));
   }


switch($ra){
		case "1": $valor= 5;break;
		case "2": $valor= 4;break;
		case "3": $valor= 3;break;
		case "4": $valor= 2;break;
		case "5": $valor= 1;break;
		case "6": $valor= 0;break;
}

  for($i=0; $i<$valor; $i++){
	 $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 9);
	 $pdf->SetWidths(array(24,14,20,50,35,30));
	 $pdf->SetAligns(array('C','C','C','L','L','L'));
	 $pdf->Row(array('','','','','',''));
  }
 $pdf->Ln(2);
	    $pdf->SetFont('Arial', '', 11);
		//$pdf->SetXY(20,150);
		$pdf->Cell(12,'','','');
		$pdf->MultiCell(175, 6, $parrafo2, 0, 'J');

	 /*$pdf->Rect(35,233,50,'');
	 $pdf->Rect(125,233,50,'');
	// $pdf->Rect(35,250,50,'');
	 $pdf->Rect(80,248,50,'');*/


	 //// ------ QUIEN APRUEBA
	 /*$pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetXY(40, 233); $pdf->Cell(40, 5,$nivel03.$nombreCompleto03, 0, 1, 'C');
	 $pdf->SetXY(40, 236); $pdf->Cell(40, 5,$cargo03, 0, 1, 'C');

	 /// ------ QUIEN REVISA
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetXY(130, 233); $pdf->Cell(40, 5,$nivel02.$nombreCompleto02, 0, 1, 'C');
	 $pdf->SetXY(130, 236); $pdf->Cell(40, 5,$cargo02, 0, 1, 'C');

	 //// ------ QUIEN RECIBE
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetXY(85, 248); $pdf->Cell(40, 5,$nivel04.$nombreCompleto04, 0, 1, 'C');
	 $pdf->SetXY(85, 251); $pdf->Cell(40, 5,ucwords(strtolower($cargo04)), 0, 1, 'C');*/


	 $pdf->ln(7);
      $pdf->SetFont('Arial','B','8');
	  $pdf->Cell(95, 3,"________________________________", 0, 0, 'C'); $pdf->Cell(95, 3,"________________________________", 0, 1, 'C'); $pdf->ln(1);
	  $pdf->Cell(95, 5,$nivel03.' '.$nombreCompleto03,0,0,'C');    $pdf->Cell(95, 5,$nivel02.' '.$nombreCompleto02,0,1,'C');
	  //$pdf->Cell(90, 5,$cargoEspleResponAnterior,0,0,'C');						   $pdf->Cell(105, 5,$cargoEmpleadoResponsable,0,1,'C'); $pdf->Ln(9);
	  $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetWidths(array(90,105));
	 $pdf->SetAligns(array('C','C'));
	 $pdf->Row(array($cargo03, ucwords(strtolower($fa['DescripCargoConform']))));
	 //$pdf->Row(array($cargo03, $cargo02));
	 $pdf->Ln(5);

  $pdf->SetFont('Arial','B','8');
	  $pdf->Cell(190, 3,"________________________________", 0, 1, 'C');
	  $pdf->Cell(190, 5,$nivel04.' '.$nombreCompleto04, 0, 1, 'C');
	  //$pdf->Cell(190, 5,ucwords(strtolower($cargo04)), 0, 1, 'C'); $pdf->ln(1);
		$pdf->Cell(190, 5,ucwords(strtolower($fa['DescripCargoRespon'])), 0, 1, 'C'); $pdf->ln(1);


	 $pdf->SetXY(20,270); $pdf->Cell(40, 5, "REF.: FOR-DSG-002");
	 //$pdf->SetXY(20,271); $pdf->Cell(40, 5, "REF.: FOR-DSG-002");



//---------------------------------------------------*/
/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(100,10,'',0,1,'L');
	$pdf->Cell(100,10,'ELABORADO POR:',0,0,'L');$pdf->Cell(120,10,'REVISADO POR:',0,0,'L');$pdf->Cell(100,10,'CONFORMADO POR:',0,1,'L');
	$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(100,5,'T.S.U. MARIANA SALAZAR',0,0,'L');$pdf->Cell(120,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,2,'ASISTENTE DE PRESUPUESTI I',0,0,'L');$pdf->Cell(120,2,'JEFE(A) DIV. ADMINISTRACION Y PRESUPUESTO',0,0,'L');$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');*/
$pdf->Output();
?>
