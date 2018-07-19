<?php
extract($_POST);
extract($_GET);
//---------------------------------------------------
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("../lib/ac_fphp.php");
connect();
//---------------------------------------------------
list($organismo, $periodo, $voucher, $codContabilidad) = split("[ ]", $registro);
//global $codContabilidad;
//if($codContabilidad!='')$variable = "and vd.CodContabilidad = '$codContabilidad'";else $variable = '';
//---------------------------------------------------
//	consulto la informacion general
$sql = "SELECT
			vm.*,
			p1.NomCompleto AS NomPreparadoPor,
			p2.NomCompleto AS NomAprobadoPor
		FROM
			ac_vouchermast vm
			LEFT JOIN mastpersonas p1 ON (vm.PreparadoPor = p1.CodPersona)
			LEFT JOIN mastpersonas p2 ON (vm.AprobadoPor = p2.CodPersona)
		WHERE
			vm.CodOrganismo = '".$organismo."' AND
			vm.Periodo = '".$periodo."' AND
			vm.Voucher = '".$voucher."' and 
			vm.CodContabilidad = '".$codContabilidad."'"; //echo $sql;
$query_mast = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_mast) != 0) $field_mast = mysql_fetch_array($query_mast);
//---------------------------------------------------
list($anio, $mes) = split("[-]", $periodo);
list($cod, $nro) = split("[-]", $voucher);
$comprobante = "$anio$mes-$cod$nro";
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_mast;
		global $organismo;
		global $periodo;
		global $voucher;
		global $comprobante;
		
		$this->Image($_PARAMETRO["PATHLOGO"].'logo.jpg', 10, 10, 11, 12);
		$this->SetFont('Arial', '', 8);
		//$this->SetXY(21, 10); $this->Cell(100, 5, $_SESSION['NOMBRE_ORGANISMO_ACTUAL'], 0, 1, 'L');
		$this->SetXY(21, 10); $this->Cell(100, 5, utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L');
		$this->SetXY(21, 15); $this->Cell(100, 5, utf8_decode('Dirección de Administración'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(225, 5); $this->Cell(20, 5, utf8_decode('Comprobante: '), 0, 0, 'R'); 
		$this->Cell(30, 5, $comprobante, 0, 1, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(225, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'R'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(260, 5, utf8_decode('Voucher de Contabilidad'), 0, 0, 'C');
		$this->Ln(5);
		
		list($a, $m, $d) = split('[-]', $field_mast['FechaVoucher']);
		$fecha = $d.'-'.$m.'-'.$a;
		//	imprimo datos generales
		$this->SetFont('Arial', 'B', 8); $this->Cell(25, 5, utf8_decode('Fecha Registro: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 8); $this->Cell(25, 5, $fecha, 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8); $this->Cell(25, 5, utf8_decode('Usuario: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 8); $this->Cell(25, 5, $field_mast['NomAprobadoPor'], 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8); $this->Cell(25, 5, utf8_decode('C.Costo: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 8); $this->Cell(25, 5, $field_mast['CodCentroCosto'].' '.$field_mast['Abreviatura'], 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8); $this->Cell(25, 5, utf8_decode('Descripción: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 8); $this->MultiCell(250, 5, utf8_decode($field_mast['TituloVoucher']), 0, 'L');
		$this->Ln(2);
		
		//	imprimo cuerpo
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(25, 70, 30, 70, 15, 25, 25));
		$this->SetAligns(array('C', 'L', 'C', 'L', 'C', 'R', 'R'));
		$this->Row(array('Cuenta',
						 'Nombre',
						 'Documento',
						 'Persona',
						 'C.Costo',
						 'Debe',
						 'Haber'));
	}
	//	Pie de página.
	function Footer() {
		
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(5, 1);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 8);
//---------------------------------------------------
$total_debe = 0;
$total_haber = 0;
//	imprimo cuerpo
$sql = "SELECT
			vd.*,
			pc.Descripcion AS NomCuenta,
			pc1.Descripcion AS NomCuenta1,
			p.NomCompleto AS NomPersona
		FROM
			ac_voucherdet vd
			INNER JOIN mastpersonas p ON (vd.CodPersona = p.CodPersona)
			left JOIN ac_mastplancuenta pc ON (vd.CodCuenta = pc.CodCuenta)
			left JOIN ac_mastplancuenta20 pc1 ON (vd.CodCuenta = pc1.CodCuenta)
		WHERE
			vd.CodOrganismo = '".$organismo."' AND
			vd.Periodo = '".$periodo."' AND
			vd.Voucher = '".$voucher."' $variable" ; 
$query_detalle = mysql_query($sql) or die($sql.mysql_error());
while ($field_detalle = mysql_fetch_array($query_detalle)) {
	if($codContabilidad=='T') $tabla= "ac_mastplancuenta";elseif($codContabilidad=='F')$tabla='ac_mastplancuenta20';
	$sql_a = "select * from $tabla where CodCuenta='".$field_detalle['CodCuenta']."'";
	$qry_a = mysql_query($sql_a) or die ($sql_a.mysql_error());
	$field_a = mysql_fetch_array($qry_a);
	
	
	if ($field_detalle['MontoVoucher'] < 0) { $haber = $field_detalle['MontoVoucher']; $debe = 0; $total_haber += $haber; }
	else { $debe = $field_detalle['MontoVoucher']; $haber = 0; $total_debe += $debe; }
	$pdf->Ln(2);
	
	$pdf->Row(array($field_detalle['CodCuenta'],
					utf8_decode($field_a['Descripcion']),
					$field_detalle['ReferenciaTipoDocumento'].'-'.$field_detalle['ReferenciaNroDocumento'],
					$field_detalle['NomPersona'],
					$field_detalle['CodCentroCosto'],
					number_format($debe, 2, ',', '.'),
					number_format($haber, 2, ',', '.')));
}
//---------------------------------------------------
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(0, 0, 0);
$y = $pdf->GetY() + 1;
$pdf->Rect(220, $y, 50, 0.1, "FD");
$pdf->SetY($y+2);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Row(array('',
				'',
				'',
				'',
				'',
				number_format($total_debe, 2, ',', '.'),
				number_format($total_haber, 2, ',', '.')));
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(0, 0, 0);
$y = $pdf->GetY() + 1;
$pdf->Rect(220, $y, 50, 0.1, "FD");
//---------------------------------------------------
$pdf->Ln(30);
$y = $pdf->GetY();
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0);
$pdf->Rect(70, $y, 55, 0.1, "FD");	$pdf->Rect(135, $y, 55, 0.1, "FD");	$pdf->Rect(200, $y, 55, 0.1, "FD");
$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetXY(70, $y+1); $pdf->Cell(55, 5, 'PREPARADO POR', 0, 0, 'C');
$pdf->SetXY(135, $y+1); $pdf->Cell(55, 5, 'CONFORMADO POR', 0, 0, 'C');
$pdf->SetXY(200, $y+1); $pdf->Cell(55, 5, 'APROBADO POR', 0, 0, 'C');
//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>