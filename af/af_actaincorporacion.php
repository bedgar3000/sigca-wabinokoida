<?php
// ------------------------------------- ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect();
extract ($_POST);
extract ($_GET);

class PDF extends FPDF{

function Header(){
	global $CodOrganismo, $nroActaIncorp, $Anio;

	/// Consultamos el ultimo nro de Acta Incorporación Almacenado según Activo y el Organismo(codOrganismo)
	/*$sql = "select
				  a.*,
				  b.*
			  from
				  af_actaincorpactivo a
				  inner join af_activo b on (b.NroIncorporacion = a.NroActa and a.Activo=b.Activo and b.CodOrganismo=a.CodOrganismo)
			 where
				  a.Activo='".$Activo."' and a.CodOrganismo='".$CodOrganismo."'"; //echo $s_actincorp; */

	 $sql= "select b.FechaRevisadoPor,
				  			 b.FechaIngreso
			  	  from af_actaincorpactivo a
				         inner join af_activo b on (b.NroIncorporacion = a.NroActa and
								                            b.CodOrganismo = a.CodOrganismo and
																						a.Activo = b.Activo)
					 where a.NroActa= '".$nroActaIncorp."' and
				         a.CodOrganismo= '".$CodOrganismo."' and
				         a.Anio= '".$Anio."'";
	 $qry= mysql_query($sql) or die ($sql.mysql_error());
	 $row= mysql_num_rows($qry); if($row != 0)$field = mysql_fetch_array($qry);

	 list($a, $m, $d) = split('[-]', $field['FechaIngreso']);
	 $FechaIngreso = $d.'-'.$m.'-'.$a;

	list($sano, $smes, $sdia) = split('[-]', $field['FechaRevisadoPor']);

	$this->Image('../imagenes/logos/logo.jpg', 20, 10, 15, 15);
	$this->SetFont('Arial', 'B', 8);
	$this->SetXY(35, 10); $this->Cell(100, 8,utf8_decode( 'República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(35, 14); $this->Cell(100, 8,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L');
	$this->SetXY(35, 18); $this->Cell(100, 8,utf8_decode('Dirección de Servicios Generales'), 0, 1, 'L');

	$this->SetXY(35, 10); $this->Cell(140, 8, 'Fecha:', 0, 0, 'R');$this->Cell(10, 8,$FechaIngreso,0,1,'');
	$this->SetXY(20, 14); $this->Cell(155, 8, utf8_decode('Pág.:'), 0, 1, 'R'); /// NRO DE PÁGINA

	$this->SetXY(20, 18); $this->Cell(155, 8, utf8_decode('Nro.:'), 0, 0, 'R');/// NRO DE DOCUMENTO
						  $this->Cell(10, 8, $nroActaIncorp.'-'.$a, 0, 1, 'L');$this->Ln(10);


	$this->SetFont('Arial', 'B', 10);
	   $this->Cell(50, 5, '', 0, 0, 'C');
	   $this->Cell(100, 5, utf8_decode('ACTA DE INCORPORACION DE BIENES MUEBLES'), 0, 1, 'C');
	   $this->Ln(10);

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


$sql = "select
			  a.Activo,
			  a.CodOrganismo,
			  a.EmpleadoAprob,
			  a.DescripCargoAprob,
			  a.DescripCargoConform,
			  a.EmpleadoConform,
			  a.NroActa,
			  a.AprobadoPor,
			  a.ConformadoPor,
			  c.NDocumento
		  from
			  af_actaincorpactivo a
			  inner join af_activo b on (b.NroIncorporacion = a.NroActa and a.Activo=b.Activo and b.CodOrganismo=a.CodOrganismo)
			  inner join mastpersonas c on (c.CodPersona = a.AprobadoPor)
		 where
			  a.NroActa= '".$nroActaIncorp."' and
			  a.CodOrganismo='".$CodOrganismo."' and
			  a.Anio = '".$Anio."'";
$qry = mysql_query($sql) or die ($sql.mysql_error());
$row = mysql_num_rows($qry); if($row != 0)$field = mysql_fetch_array($qry); //echo $row;


list($nombreCompleto, $cargo, $nivel) = getFirmaxDependencia($_PARAMETRO['FIRMAINVENTARIODEP']);

list($nombreAprobadoPor, $cargoAprobadoPor, $nivelaprobadoPor) = getFirma($field['AprobadoPor']);

$EmpleadoAprob = ucwords(strtr(strtolower(utf8_encode($field['EmpleadoAprob'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //
$DescripCargoAprob = ucwords(strtr(strtolower(utf8_encode($field['DescripCargoAprob'])),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")); //

$parrafo1 = utf8_decode("El (la) suscrito(a) ").$nivelaprobadoPor." ".$EmpleadoAprob.utf8_decode(", C.I: N° ").($field['NDocumento'].' '.$DescripCargoAprob).utf8_decode(" de la Contraloría del Estado, en cumplimiento al Artículo 8, Título 11 de la Ley de Contraloría del Estado Delta Amacuro, hace constar por medio de la presente, que los bienes que a continuación se especifican han sido incorporados al Inventario General de esta Institución.");


$pdf->SetFont('Arial', '', 12);
		$pdf->SetXY(20,50);
		$pdf->MultiCell(175, 6, $parrafo1, 0, 'J');
		$pdf->Ln(6);

$pdf->SetFont('Arial', '', 7);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(200, 200, 200); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 7.5);
	$pdf->Cell(12,'','','');
	$pdf->Cell(24, 3, 'CLASIFICACION', 1, 0, 'C', 1);
	$pdf->Cell(14, 3, 'CANTIDAD', 1, 0, 'C', 1);
	$pdf->Cell(20, 3, utf8_decode('COD. INTERNO'), 1, 0, 'C', 1);
	$pdf->Cell(50, 3, 'DESCRIPCION', 1, 0, 'C', 1);
	$pdf->Cell(50, 3, 'UBICACION', 1, 0, 'C', 1);
	$pdf->Cell(15, 3, 'PRECIO', 1, 1, 'C', 1);

$s_a = "select
			  a.DescripCargoConform,
			  b.*,
			  c.Descripcion as DescripUbicacion,
			  d.Descripcion as DescripClasificacion20
		  from
			  af_actaincorpactivo a
			  inner join af_activo b on (b.NroIncorporacion = a.NroActa and a.Activo=b.Activo and b.CodOrganismo=a.CodOrganismo)
			  inner join af_ubicaciones c on (c.CodUbicacion = b.Ubicacion)
			  inner join af_clasificacionactivo20 d on (d.CodClasificacion = b.ClasificacionPublic20)
		 where
			  a.NroActa= '".$nroActaIncorp."' and
			  a.CodOrganismo='".$CodOrganismo."' and
			  a.Anio = '".$Anio."'";
$q_a = mysql_query($s_a) or die ($s_a.mysql_error());
$r_a = mysql_num_rows($q_a); //echo $r_a;

if($r_a != 0){
  for($i=0; $i<$r_a; $i++){
	 $f_a = mysql_fetch_array($q_a);
	 $totalMontoLocal = $f_a['MontoLocal'] + $totalMontoLocal;

	 $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 8);
	 $pdf->SetWidths(array(24,14,20,50,50,15));
	 $pdf->SetAligns(array('C','C','C','L','L','R'));
	 $pdf->Row(array($f_a['DescripClasificacion20'],'1',$f_a['CodigoInterno'],utf8_decode($f_a['Descripcion']),utf8_decode($f_a['DescripUbicacion']),number_format($f_a['MontoLocal'],2,',','.')));
  }
}
  switch($ra){
		case "1": $valor= 11;break;
		case "2": $valor= 10;break;
		case "3": $valor= 9;break;
		case "4": $valor= 8;break;
		case "5": $valor= 7;break;
		case "6": $valor= 6;break;
		case "7": $valor= 5; break;
		case "8": $valor= 4; break;
		case "9": $valor= 3; break;
		case "10": $valor= 2; break;
		case "11": $valor= 1; break;
		case "12": $valor= 0; break;
  }

  for($i=0; $i<$valor; $i++){
	 $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', '', 6.5);
	 $pdf->SetWidths(array(24,14,20,50,50,13));
	 $pdf->SetAligns(array('C','L','R','R','R','R'));
	 $pdf->Row(array('','','','','',''));
  }
     $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->Cell(12,'','','');
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetWidths(array('','',158,15));
	 $pdf->SetAligns(array('C','L','R','',''));
	 $pdf->Row(array('','','Total en Bs.==> ',number_format($totalMontoLocal,2,',','.'))); $pdf->Ln(2);


//list($nombreCompleto, $cargo, $nivel) = getfirma($field['AprobadoPor']);
//list($nombreCompleto02, $cargo02, $nivel02) = getfirma($field['ConformadoPor']);

$sc = "select * from mastdependencias where CodDependencia='0003'";


//list($nombreCompleto02, $cargo02, $nivel02) = getFirmaxDependencia($_PARAMETRO['APROBACTIVOPOR']);
list($nombreCompleto02, $cargo02, $nivel02) = getFirma($field['ConformadoPor']);

list($nomb_Aprob_por, $cargo_Aprob_por, $nivel_Aprob_por) = getFirma($field['AprobadoPor']);



	 $pdf->ln(3);
      $pdf->SetFont('Arial','B','8');
	  $pdf->Cell(95, 3,"________________________________", 0, 0, 'C'); $pdf->Cell(95, 3,"________________________________", 0, 1, 'C'); $pdf->ln(1);
	  $pdf->Cell(95, 5,$nivel_Aprob_por.' '.$nomb_Aprob_por,0,0,'C');    $pdf->Cell(95, 5,$nivel02.' '.$nombreCompleto02,0,1,'C');
	  //$pdf->Cell(90, 5,$cargoEspleResponAnterior,0,0,'C');						   $pdf->Cell(105, 5,$cargoEmpleadoResponsable,0,1,'C'); $pdf->Ln(9);
	  $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetWidths(array(90,105));
	 $pdf->SetAligns(array('C','C'));
	 //$pdf->Row(array($field['DescripCargoAprob'], $cargo02));
	 $pdf->Row(array($field['DescripCargoAprob'], $field['DescripCargoConform']));
	 //$pdf->Ln(5);

	 $pdf->SetXY(20,270); $pdf->Cell(40, 5, "REF.: FOR-DSG-001");






	 /// ------------ QUIEN APRUEBA
	 /*$pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetXY(72, 160); $pdf->Cell(60, 5,$nivel03.' '.$nombreCompleto03, 0, 1, 'C');
	 $pdf->SetXY(72, 163); $pdf->Cell(62, 5,$cargo03, 0, 1, 'C');
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
