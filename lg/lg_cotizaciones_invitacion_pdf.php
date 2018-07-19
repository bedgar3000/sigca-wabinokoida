<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_det;
		//	datos generales
		$sql = "SELECT
					c.CodProveedor,
					c.NomProveedor,
					c.CotizacionNumero,
					c.FechaInvitacion,
					c.FechaDocumento,
					c.NroCotizacionProv,
					c.Numero,
					c.NumeroInterno,
					c.CodOrganismo,
					mp.DocFiscal AS ProRif,
					mp.Direccion AS ProDireccion,
					mp.Telefono1 AS ProTel,
					mp.Fax AS ProFax,
					p.RepresentanteLegal,
					o.Organismo,
					o.DocFiscal AS OrgRif,
					o.Direccion AS OrgDireccion,
					o.Telefono1 AS OrgTel,
					o.Fax1 AS OrgFax,
					o.PaginaWeb
				FROM
					lg_cotizacion c
					INNER JOIN mastpersonas mp ON (c.CodProveedor = mp.CodPersona)
					INNER JOIN mastproveedores p ON (p.CodProveedor = mp.CodPersona)
					INNER JOIN mastorganismos o ON (c.CodOrganismo = o.CodOrganismo)
				WHERE c.CotizacionSecuencia = '".$field_det['CotizacionSecuencia']."'
				GROUP BY Numero, CodProveedor
				ORDER BY NumeroInterno";
		$query_mast = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_mast) != 0) $field_mast = mysql_fetch_array($query_mast);
		##	
		//	membrete
		##	obtengo los valores
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $field_mast['CodOrganismo']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"]);
		##	colores
		$this->SetDrawColor(0, 0, 0);
		$this->SetTextColor(0, 0, 0);
		##	imprimo membrete
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 10, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(20, 5); $this->Cell(100, 5, utf8_decode($field_mast['Organismo']), 0, 1, 'L');
		$this->SetXY(20, 10); $this->MultiCell(100, 5, utf8_decode($field_mast['OrgDireccion']), 0, 'L');
		$this->SetXY(20, 15); $this->Cell(40, 5, 'Telf. '.$field_mast['OrgTel'], 0, 0, 'L');
		$this->SetXY(60, 15); $this->Cell(40, 5, 'Fax: '.$field_mast['OrgFax'], 0, 1, 'L');
		$this->SetXY(20, 20); $this->Cell(60, 5, 'R.I.F. '.$field_mast['OrgRif'], 0, 1, 'L');
		$this->SetXY(60, 20); $this->Cell(40, 5, 'Email: '.$field_mast['PaginaWeb'], 0, 1, 'L');
		$this->SetXY(175, 15); $this->Cell(15, 5, utf8_decode('# Solicitud: '), 0, 0, 'R'); $this->Cell(60, 5, $field_mast['NumeroInterno'], 0, 1, 'L');
		$this->SetXY(175, 20); $this->Cell(15, 5, 'Fecha: ', 0, 0, 'R'); $this->Cell(60, 5, formatFechaDMA($field_mast['FechaInvitacion']), 0, 1, 'L');
		$this->Ln(10);
		$this->SetXY(10, 30);
		$this->SetFillColor(250, 250, 250);
		$this->SetFont('Arial', '', 8);
		$this->Cell(35, 5, 'Proveedor: ', 0, 0, 'L', 1);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(150, 6, utf8_decode($field_mast['NomProveedor']), 0, 1, 'L');
		$this->SetFont('Arial', '', 8);
		$this->Cell(35, 5, 'R.I.F: ', 0, 0, 'L', 1);
		$this->Cell(50, 6, $field_mast['ProRif'], 0, 1, 'L');
		$this->Cell(35, 5, 'Domicilio: ', 0, 0, 'L', 1);
		$this->MultiCell(150, 6, utf8_decode($field_mast['ProDireccion']), 0, 'L');
		$this->Cell(35, 5, 'Telefono Contacto: ', 0, 0, 'L', 1);
		$this->Cell(35, 6, $field_mast['ProTel'], 0, 0, 'L');
		$this->Cell(15, 5, 'Fax: ', 0, 0, 'L', 1);
		$this->Cell(50, 6, $field_mast['ProFax'], 0, 1, 'L');
		$this->Ln(5);
		//	imprimo cuerpo
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(10, 155, 15, 15));
		$this->SetAligns(array('C', 'L', 'C', 'R'));
		$this->Row(array('Item',
						 'Descripcion',
						 'Uni.',
						 'Cant.'));
		$this->Ln(1);
	}
	//	Pie de página.
	function Footer() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_det;
		global $Condiciones;
		global $Observaciones;
		global $Especificaciones;
		global $InvitadoPor;
		//	obtengo las firmas
		$Firma = getUnidadEjecutora($_PARAMETRO["CATCOMPRAS"]);
		##	
		$this->SetTextColor(0, 0, 0);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		
		$this->SetXY(10, 170); 
		$this->SetFillColor(245, 245, 245);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(196, 5, utf8_decode('Especificaciones Técnicas: '), 0, 0, 'L', 1);
		$this->Ln(6);
		$this->SetFont('Arial', '', 8);
		$this->MultiCell(196, 5, utf8_decode($Especificaciones), 0, 'L');
		
		
		$this->SetXY(10, 210);
		$this->Rect(20, 210, 75, 0.1, "D");
		$this->SetFont('Arial', 'B', 9);
		$this->SetXY(10, 214); $this->MultiCell(98, 3, utf8_decode($Firma), 0, 'C');
		##
		$this->SetY(230);
		$this->SetDrawColor(0, 0, 0); $this->SetFillColor(0, 0, 0); $this->SetTextColor(0, 0, 0);
		$y=$this->GetY();
		$this->Rect(10, $y, 200, 0.1, "DF");
		$this->Ln(2);
		$this->SetFillColor(245, 245, 245);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(98, 5, utf8_decode('Condiciones de Entrega: '), 0, 0, 'L', 1);
		$this->Cell(4, 5);
		$this->Cell(98, 5, utf8_decode('Observaciones: '), 0, 1, 'L', 1);
		$this->Ln(2);
		$this->SetDrawColor(255, 255, 255); $this->SetFillColor(255, 255, 255); $this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', '', 8);	
		$this->SetXY(10, 240); $this->MultiCell(98, 5, utf8_decode($Condiciones), 0, 'L');
		$this->SetXY(112, 240 ); $this->MultiCell(98, 5, utf8_decode($Observaciones), 0, 'L');
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(5, 110);
//---------------------------------------------------
//	detalles
$filtro = "AND ";
if ($origen == "cotizaciones_items_invitar_cotizar") {
	$filtro .= "c.CodRequerimiento = '".$CodRequerimiento."' AND
				c.Secuencia = '".$Secuencia."' AND
				c.CodProveedor = '".$CodProveedor."'";
}
elseif ($origen == "cotizaciones_items_invitar_lista") {
	$filtro .= "c.Numero = '".$Numero."'";
}
elseif ($origen == "cotizaciones_proveedores_invitar_lista") {
	$NroCotizacionProv = $sel_registros;
	$filtro .= "c.NroCotizacionProv = '".$NroCotizacionProv."'";
}
$sql = "SELECT
			c.CotizacionSecuencia,
			c.CodProveedor,
			c.Condiciones,
			c.Observaciones,
			c.Especificaciones,
			c.InvitadoPor,
			C.NumeroInterno,
			rd.CodItem,
			rd.CommoditySub,
			rd.Descripcion,
			rd.CodUnidad,
			rd.CantidadPedida
		FROM
			lg_cotizacion c
			INNER JOIN lg_requerimientosdet rd ON (c.CodRequerimiento = rd.CodRequerimiento AND
												   c.CodOrganismo = rd.CodOrganismo AND
												   c.Secuencia = rd.Secuencia)
		WHERE 1 $filtro
		ORDER BY NumeroInterno, CodProveedor, CodItem, CommoditySub";
$query_det = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while ($field_det = mysql_fetch_array($query_det)) {
	$Condiciones = $field_det['Condiciones'];
	$Observaciones = $field_det['Observaciones'];
	$Especificaciones = $field_det['Especificaciones'];
	$InvitadoPor = $field_det['InvitadoPor'];
	//	una pagina por proveedor
	if ($Grupo != $field_det['CodProveedor']) {
		$Grupo = $field_det['CodProveedor'];
		$CotizacionSecuencia = $field_det['CotizacionSecuencia'];
		$i = 0;
		$pdf->AddPage();
	}
	$i++;
	$pdf->Ln(2);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($i,
					utf8_decode($field_det['Descripcion']),
					$field_det['CodUnidad'],
					number_format($field_det['CantidadPedida'], 2, ',', '.')));
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>