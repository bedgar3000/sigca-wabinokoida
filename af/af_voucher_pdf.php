<?php
extract($_POST);
extract($_GET);
//---------------------------------------------------
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("../lib/ac_fphp.php");
connect();
//---------------------------------------------------
//echo $activo.'//';
//list($organismo, $periodo, $voucher, $codContabilidad) = split("[ ]", $registro);
//---------------------------------------------------
//	consulto la informacion general
/*$sql = "SELECT
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
			vm.CodContabilidad = '".$codContabilidad."'";
$query_mast = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_mast) != 0) $field_mast = mysql_fetch_array($query_mast);*/

//---------------------------------------------------
list($anio, $mes) = split("[-]", $periodo);
list($cod, $nro) = split("[-]", $voucher);
$comprobante = "$anio$mes-$cod$nro";
//---------------------------------------------------
$sql_a = "select 
                a.*,
				b.NomCompleto as NombreUsuario ,
				c.Descripcion  as DescpCentroCosto
		    from 
			    af_activo a 
				inner join mastpersonas b on (b.CodPersona=a.EmpleadoUsuario)
				inner join ac_mastcentrocosto c on (c.CodCentroCosto=a.CentroCosto)
		   where 
		        a.Activo='$activo'"; //echo $sql_a;
$qry_a = mysql_query($sql_a) or die ($sql_a.mysql_error());
$row_a = mysql_num_rows($qry_a);
if($row_a!=0) $field_a = mysql_fetch_array($qry_a);








//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_a;
		global $organismo;
		global $periodo;
		global $voucher;
		global $comprobante;
		
		$this->Image($_PARAMETRO["PATHLOGO"].'logo.jpg', 10, 5, 11, 12);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(20, 5); $this->Cell(100, 5, $_SESSION['NOMBRE_ORGANISMO_ACTUAL'], 0, 1, 'L');
		$this->SetXY(20, 10); $this->Cell(100, 5, utf8_decode('DIRECCIÓN DE ADMINISTRACIÓN'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(225, 5); $this->Cell(20, 5, utf8_decode('Voucher N°: '), 0, 0, 'R'); 
		$this->Cell(30, 5, $field_a['VoucherIngPub20'], 0, 1, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(225, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'R'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(260, 5, utf8_decode('Voucher de Activo Fijo'), 0, 0, 'C');
		$this->Ln(5);
		
		//	imprimo datos generales
		$fecha = formatFechaDMA($field_a['FechaIngreso']);
		$this->SetFont('Arial', 'B', 8); $this->Cell(25, 5, utf8_decode('Fecha Ingreso: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 8); $this->Cell(25, 5, $fecha, 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8); $this->Cell(25, 5, utf8_decode('Usuario: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 8); $this->Cell(25, 5, $field_a['NombreUsuario'], 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8); $this->Cell(25, 5, utf8_decode('C.Costo: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 8); $this->Cell(25, 5, strtoupper(utf8_decode($field_a['DescpCentroCosto'])).' '.$field_mast['Abreviatura'], 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8); $this->Cell(25, 5, utf8_decode('Descripción: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 8); $this->MultiCell(250, 5, utf8_decode($field_a['Descripcion']), 0, 'L');
		$this->Ln(2);
		
		//	imprimo cuerpo
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(25, 70, 30, 70, 15, 25, 25));
		$this->SetAligns(array('C', 'L', 'C', 'L', 'C', 'R', 'R'));
		$this->Row(array('Cuenta',
						 utf8_decode('Descripción'),
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
/*$sql = "SELECT
			vd.*,
			pc.Descripcion AS NomCuenta,
			p.NomCompleto AS NomPersona
		FROM
			ac_voucherdet vd
			INNER JOIN mastpersonas p ON (vd.CodPersona = p.CodPersona)
			INNER JOIN ac_mastplancuenta pc ON (vd.CodCuenta = pc.CodCuenta)
		WHERE
			vd.CodOrganismo = '".$organismo."' AND
			vd.Periodo = '".$periodo."' AND
			vd.Voucher = '".$voucher."'";
$query_detalle = mysql_query($sql) or die($sql.mysql_error());*/
$sql_b = "select 
                a.Monto,
				a.Activo,
				a.CuentaContable,
				b.SignoFlag,
				c.Descripcion 
		    from 
			    af_activodistribcontable a 
				inner join af_tipotranscuenta b on (b.TipoTransaccion=a.TipoTransaccion) and 
				 								   (b.CuentaContable=a.CuentaContable) and 
												   (b.Secuencia=a.Secuencia) 
				inner join ac_mastplancuenta20 c on (c.CodCuenta=b.CuentaContable)
		   where 
		        a.Activo = '".$field_a['Activo']."'";
$qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error);


while ($field_b = mysql_fetch_array($qry_b)) {
	if($field_b['SignoFlag']==""){ 
	   $haber = $field_b['Monto']; $debe = 0; $total_haber += $haber; 
	}
	else{ 
	  $debe = $field_b['Monto']; $haber = 0; $total_debe += $debe; 
	}
	$pdf->Ln(2);
	$pdf->Row(array($field_b['CuentaContable'],
					$field_b['Descripcion'],
					$field_b['ReferenciaTipoDocumento'].'-'.$field_b['ReferenciaNroDocumento'],
					$field_b['NomPersona'],
					$field_b['CodCentroCosto'],
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
/*$pdf->SetXY(70, $y+1); $pdf->Cell(55, 5, 'PREPARADO POR', 0, 0, 'C');
$pdf->SetXY(135, $y+1); $pdf->Cell(55, 5, 'AUTORIZADO POR', 0, 0, 'C');
$pdf->SetXY(200, $y+1); $pdf->Cell(55, 5, 'CONTABILIDAD', 0, 0, 'C');*/
//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>