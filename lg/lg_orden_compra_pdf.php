<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("../lib/lg_fphp.php");
connect();
//---------------------------------------------------
list($Anio, $CodOrganismo, $NroOrden) = split('[.]', $registro);
$hoja_anexa = false;
//---------------------------------------------------
//	consulto los datos de la orden
$sql = "SELECT 
			oc.*,
			mp.DocFiscal AS DocFiscalProveedor,
			mp.Direccion,
			mp.Telefono1 AS TelProveedor,
			fp.Descripcion AS NomFormaPago,
			i.FactorPorcentaje,
			o.Organismo,
			o.DocFiscal AS DocFiscalOrganismo,
			o.Telefono1 AS TelOrganismo,
			o.Fax1,
			o.Direccion AS DireccionOrganismo,
			pr.NroInscripcionSNC
		FROM
			lg_ordencompra oc
			INNER JOIN mastpersonas mp ON (oc.CodProveedor = mp.CodPersona)
			INNER JOIN mastproveedores pr ON (mp.CodPersona = pr.CodProveedor)
			INNER JOIN mastorganismos o ON (oc.CodOrganismo = o.CodOrganismo)
			LEFT JOIN mastformapago fp ON (pr.CodFormaPago = fp.CodFormaPago)
			LEFT JOIN masttiposervicioimpuesto tsi ON (oc.CodTipoServicio = tsi.CodTipoServicio)
			LEFT JOIN mastimpuestos i ON (tsi.CodImpuesto = tsi.CodImpuesto)
		WHERE
			oc.Anio = '".$Anio."' AND
			oc.CodOrganismo = '".$CodOrganismo."' AND
			oc.NroOrden = '".$NroOrden."'";
$query_mast = mysql_query($sql) or die (mysql_error());
if (mysql_num_rows($query_mast) != 0) $field_mast = mysql_fetch_array($query_mast);
##	
$sql = "SELECT
			o.*,
			c.Ciudad,
			c.CodPostal,
			e.Estado AS NomEstado
		FROM mastorganismos o
		LEFT JOIN mastciudades c ON c.CodCiudad = o.CodCiudad
		LEFT JOIN mastmunicipios m ON m.CodMunicipio = c.CodMunicipio
		LEFT JOIN mastestados e ON e.CodEstado = m.CodEstado
		WHERE o.CodOrganismo = '$field_mast[CodOrganismo]'";
$field_organismo = getRecord($sql);
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_mast;
		global $hoja_anexa;
		global $field_organismo;
		
		//	imprimo la cabecera
		$this->Image($_PARAMETRO['PATHLOGO'].$field_organismo['Logo'], 10, 11, 15, 11);
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(25, 10); $this->Cell(195, 5, utf8_decode($field_mast['Organismo']), 0, 0, 'L');
		$this->SetXY(25, 13); 
		$this->Cell(30, 5, ('R.I.F. '.$field_mast['DocFiscalOrganismo']), 0, 0, 'L');
		$this->Cell(35, 5, ('Telefono: '.$field_mast['TelOrganismo']), 0, 0, 'L'); 
		$this->Cell(35, 5, ('Fax: '.$field_mast['Fax1']), 0, 0, 'L');		
		$this->SetXY(25, 16); $this->Cell(195, 5, utf8_decode($field_mast['DireccionOrganismo']), 0, 0, 'L');		
		$this->SetXY(25, 19); $this->Cell(195, 5, ('DIRECCION DE ADMINISTRACION'), 0, 0, 'L');
		//------
		$this->SetFont('Arial', '', 9);
		$this->SetXY(165, 11); $this->Cell(20, 5, ('Nro. Orden: '), 0, 0, 'R');
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(20, 5, $field_mast['NroInterno']);
		$this->SetFont('Arial', '', 9);
		$this->SetXY(165, 15); $this->Cell(20, 5, ('Fecha: '), 0, 0, 'R'); 
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(20, 5, formatFechaDMA($field_mast['FechaOrden']));
		$this->SetFont('Arial', '', 9);
		$this->SetXY(165, 19); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'R'); 
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(20, 5, $this->PageNo().' de {nb}');
		//------		
		$this->SetFont('Arial', 'B', 12);
		$this->SetXY(10, 30); 
		if (!$hoja_anexa) $this->Cell(195, 10, 'ORDEN DE COMPRA', 0, 1, 'C');
		else $this->Cell(195, 10, 'LISTA DE PARTIDAS', 0, 1, 'C');
		//------
		//	imprimo los datos del proveedor (solamente en la primera hoja)
		if ($this->PageNo() == 1) {
			$this->Ln(5);
			$this->SetFillColor(245, 245, 245);
			$this->SetFont('Arial', '', 8); 
			$this->Cell(30, 5, 'Proveedor: ', 0, 0, 'L', 1);
			$this->SetFont('Arial', 'B', 8); 
			$this->Cell(95, 5, utf8_decode($field_mast['NomProveedor']), 0, 0, 'L');
			$this->SetFont('Arial', '', 8); 
			$this->Cell(30, 5, 'R.I.F: ', 0, 0, 'L', 1);
			$this->Cell(95, 5, $field_mast['DocFiscalProveedor'], 0, 0, 'L');
			$this->Ln(6);
			$this->Cell(30, 5, utf8_decode('Dirección: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, utf8_decode(substr($field_mast['Direccion'],0,50)), 0, 0, 'L');
			$this->Cell(30, 5, ('S.N.C.: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, $field_mast['NroInscripcionSNC'], 0, 0, 'L');
			$this->Ln(6);
			$this->Cell(30, 5, ('Lugar de Entrega: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, utf8_decode($field_mast['Entregaren']), 0, 0, 'L');
			$this->Cell(30, 5, utf8_decode('Teléfono: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, $field_mast['TelProveedor'], 0, 0, 'L');
			$this->Ln(6);
			$this->Cell(30, 5, ('Email: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, utf8_decode($field_mast['Email']), 0, 0, 'L');
			$this->Cell(30, 5, ('Modalidad de Pago: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, utf8_decode($field_mast['NomFormaPago']), 0, 0, 'L');
			$this->Ln(6);
			$this->Cell(195, 5, ('Especificaciones de la orden de compra: '), 0, 1, 'L', 1);
			$this->MultiCell(195, 4, utf8_decode($field_mast['Observaciones']), 0, 'J');
			$this->SetY(95);
		} else $this->Ln(5);
		//------
		if (!$hoja_anexa) {
			$this->SetDrawColor(0, 0, 0);
			$this->SetFillColor(255, 255, 255);
			$this->SetFont('Arial', '', 8);
			$this->SetWidths(array(10, 100, 15, 20, 25, 25));
			$this->SetAligns(array('C', 'L', 'C', 'R', 'R', 'R'));
			$this->Row(array('Item', 
							 utf8_decode('Descripción'), 
							 'Uni.', 
							 'Cant.', 
							 'P. Unitario', 
							 'Total'));
		}
		$this->Ln(1);
	}
	
	//	Pie
	function Footer() {
		global $field_mast;
		global $_PARAMETRO;
		//---------------------------------------------------
		//	obtengo las firmas
		list($_PREPARADO['Nombre'], $_PREPARADO['Cargo'], $_PREPARADO['Nivel']) = getFirma($field_mast['PreparadaPor']);
		list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirma($_PARAMETRO["FIRMAOP3"]);
		list($_APROBADO['Nombre'], $_APROBADO['Cargo'], $_APROBADO['Nivel']) = getFirma(getPersonaUnidadEjecutora($_PARAMETRO["CATADM"]));
		//---------------------------------------------------
		$this->SetXY(10, -40);		
		$y = $this->GetY();
		$this->SetXY(10, $y);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', 'B', 8);
		
		$this->Rect(10, $y, 195, 30, "DF");
		$this->Rect(10, $y+5, 195, 0.1, "DF");
		$this->Rect(60, $y, 0.1, 30, "DF");
		$this->Rect(110, $y, 0.1, 30, "DF");
		$this->Rect(160, $y, 0.1, 30, "DF");
		
		$this->SetXY(10, 240); $this->Cell(50, 5, 'PREPARADO POR', 0, 0, 'L');
		$this->SetXY(60, 240); $this->Cell(50, 5, 'REVISADO POR', 0, 0, 'L');
		$this->SetXY(110, 240); $this->Cell(50, 5, 'APROBADO POR', 0, 0, 'L');
		$this->SetXY(160, 240); $this->Cell(50, 5, 'PROVEEDOR', 0, 0, 'L');

		$this->SetFont('Arial', 'B', 6);
		$this->SetXY(10, 246); $this->MultiCell(50, 3, substr($_PREPARADO['Nivel'].' '.$_PREPARADO['Nombre'], 0, 35), 0, 'L');
		$this->SetXY(60, 246); $this->MultiCell(50, 3, substr($_REVISADO['Nivel'].' '.$_REVISADO['Nombre'], 0, 35), 0, 'L');
		$this->SetXY(110, 246); $this->MultiCell(50, 3, substr($_APROBADO['Nivel'].' '.$_APROBADO['Nombre'], 0, 35), 0, 'L');
		$this->SetXY(160, 246); $this->Cell(50, 5, '', 0, 0, 'L');

		$this->SetXY(10, 252); $this->MultiCell(50, 3, $_PREPARADO['Cargo'], 0, 'C');
		$this->SetXY(60, 252); $this->MultiCell(50, 3, $_REVISADO['Cargo'], 0, 'C'); 
		$this->SetXY(110, 252); $this->MultiCell(50, 3, $_APROBADO['Cargo'], 0, 'C'); 
		$this->SetXY(160, 252); $this->Cell(50, 5, '', 0, 0, 'L');

		$this->SetFont('Arial', 'B', 6);
		$this->SetXY(10, 265); $this->Cell(50, 5, 'Fecha: '.formatFechaDMA($field_mast['FechaPreparacion']), 0, 0, 'L'); 
		$this->SetXY(60, 265); $this->Cell(50, 5, 'Fecha:            /                 /', 0, 0, 'L'); 
		$this->SetXY(110, 265); $this->Cell(50, 5, 'Fecha:            /                 /', 0, 0, 'L'); 
		$this->SetXY(160, 265); $this->Cell(50, 5, 'Fecha:            /                 /', 0, 0, 'L'); 
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 38);
//----

//----
//	imprimo los detalles de la orden
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$sql = "SELECT *
		FROM lg_ordencompradetalle
		WHERE
			Anio = '".$Anio."' AND
			CodOrganismo = '".$CodOrganismo."' AND
			NroOrden = '".$NroOrden."'";
$query_detalle = mysql_query($sql) or die ($sql.mysql_error());	$i=0;
while ($field_detalle = mysql_fetch_array($query_detalle)) {	$i++;
	$pdf->Row(array($i, 
					utf8_decode($field_detalle['Descripcion']), 
					$field_detalle['CodUnidad'], 
					number_format($field_detalle['CantidadPedida'], 2, ',', '.'), 
					number_format($field_detalle['PrecioUnit'], 2, ',', '.'), 
					number_format($field_detalle['PrecioCantidad'], 2, ',', '.')));
}

//	imprimo totales
if ($pdf->GetY() < 200) $y = 200;
else {
	$pdf->AddPage();
	$y = $pdf->GetY();
}
$pdf->SetXY(10, $y);
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0);
$pdf->Rect(10, $y, 195, 0.1, "DF");
//----
$tipo_impuesto = "(".$field_mast['CodTipoServicio']." ".number_format($field_mast['FactorImpuesto'], 2, ',', '.')." %): ";
$monto_total_en_letras = convertir_a_letras($field_mast['MontoTotal'], "moneda");

$pdf->SetFillColor(245, 245, 245);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY(10, $y+2); $pdf->Cell(120, 5, ('Monto total en letras: '), 0, 1, 'L', 1);
$pdf->SetXY(10, $y+7); $pdf->MultiCell(120, 4, utf8_decode((strtoupper($monto_total_en_letras))), 0, 'J');

$pdf->SetXY(135, $y+2); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(43, 5, 'Monto Afecto: ', 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($field_mast['MontoAfecto'], 2, ',', '.'), 0, 0, 'R');

$pdf->SetXY(135, $y+8);
$pdf->Cell(43, 5, 'Monto No Afecto: ', 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($field_mast['MontoNoAfecto'], 2, ',', '.'), 0, 0, 'R');

$pdf->SetXY(135, $y+14);
$pdf->Cell(43, 5, $tipo_impuesto, 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($field_mast['MontoIGV'], 2, ',', '.'), 0, 0, 'R');

$pdf->SetXY(135, $y+20);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(43, 5, 'Monto Total: ', 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($field_mast['MontoTotal'], 2, ',', '.'), 0, 0, 'R');
//----

//----
//	consulto las partidas de la orden
list($cod_partida_igv, $CodCuenta_igv, $CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
$sql = "SELECT 
			do.cod_partida, 
			pc.denominacion,
			do.Monto,
			do.CodPresupuesto,
			do.Ejercicio,
			do.CodFuente,
			pc.CodCuenta,
			pc.CodCuentaPub20,
			ff.Denominacion AS Fuente,
			ue.Denominacion AS UnidadEjecutora,
			cp.CategoriaProg,
			CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
		FROM
			lg_distribucioncompromisos do
			INNER JOIN pv_partida pc ON (do.cod_partida = pc.cod_partida)
			LEFT JOIN pv_fuentefinanciamiento ff ON (ff.CodFuente = do.CodFuente)
			LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = do.CodOrganismo AND pv.CodPresupuesto = do.CodPresupuesto)
			LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
			LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
			LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
		WHERE
			do.Anio = '".$Anio."' AND
			do.CodOrganismo = '".$CodOrganismo."' AND
			do.CodProveedor = '".$field_mast['CodProveedor']."' AND
			do.CodTipoDocumento = 'OC' AND
			do.NroDocumento = '".$NroOrden."'
		ORDER BY Ejercicio, CodPresupuesto, CodFuente, cod_partida";
$query_partida = mysql_query($sql) or die ($sql.mysql_error());	$i=0; $total_partida = 0;
$rows_partida = mysql_num_rows($query_partida);
//	imprimo hoja anexa
$hoja_anexa = true; 
$pdf->AddPage(); 
$y = $pdf->GetY();

$pdf->SetXY(10, $y);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 8);
$pdf->SetWidths(array(20, 10, 20, 120, 25));
$pdf->SetAligns(array('C', 'C', 'C', 'L', 'R'));
$pdf->Row(array('Cat. Prog.', 
				'F.F.', 
				'Partida', 
				utf8_decode('Descripción'), 
				'Monto'));
$pdf->SetDrawColor(255, 255, 255);
$pdf->Ln(1);
//----
//	imprimo las partidas de la orden
while ($field_partida = mysql_fetch_array($query_partida)) {	$i++;
	$total_partida += $field_partida['Monto'];
	
	$pdf->Row(array($field_partida['CatProg'],
					$field_partida['CodFuente'],
					$field_partida['cod_partida'],
					utf8_decode($field_partida['denominacion']),
					number_format($field_partida['Monto'], 2, ',', '.')));
}
//----
$y = $pdf->GetY(); //	$y+=38;
$pdf->SetXY(10, $y);
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0);
$pdf->Rect(10, $y, 195, 0.1, "DF");
//----
$pdf->SetXY(135, $y+1);
$pdf->SetFillColor(245, 245, 245);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(43, 5, 'Total: ', 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($total_partida, 2, ',', '.'), 0, 0, 'R');
//----

//----
//	consulto las cuentas contables de la orden
if ($_PARAMETRO['CONTORDENDIS'] == "T") {
	$sql = "(SELECT 
				CodCuenta, 
				Descripcion,
				'".$field_mast['MontoIGV']."' AS Monto
			FROM ac_mastplancuenta
			WHERE CodCuenta = '".$CodCuenta_igv."'
			GROUP BY CodCuenta)
			UNION
			(SELECT 
				ocd.CodCuenta, 
				c.Descripcion,
				SUM(ocd.PrecioCantidad) AS Monto
			FROM 
				lg_ordencompradetalle ocd
				INNER JOIN ac_mastplancuenta c ON (ocd.CodCuenta = c.CodCuenta)
			WHERE
				ocd.Anio = '".$Anio."' AND
				ocd.CodOrganismo = '".$CodOrganismo."' AND
				ocd.NroOrden = '".$NroOrden."'
			GROUP BY CodCuenta)
			ORDER BY CodCuenta";
}
elseif ($_PARAMETRO['CONTORDENDIS'] == "F") {
	$sql = "(SELECT 
				CodCuenta, 
				Descripcion,
				'".$field_mast['MontoIGV']."' AS Monto
			FROM ac_mastplancuenta20
			WHERE CodCuenta = '".$CodCuentaPub20_igv."'
			GROUP BY CodCuenta)
			UNION
			(SELECT 
				c.CodCuenta, 
				c.Descripcion,
				SUM(ocd.PrecioCantidad) AS Monto
			FROM 
				lg_ordencompradetalle ocd
				INNER JOIN ac_mastplancuenta20 c ON (ocd.CodCuentaPub20 = c.CodCuenta)
			WHERE
				ocd.Anio = '".$Anio."' AND
				ocd.CodOrganismo = '".$CodOrganismo."' AND
				ocd.NroOrden = '".$NroOrden."'
			GROUP BY CodCuenta)
			ORDER BY CodCuenta";
}
$query_cuenta = mysql_query($sql) or die ($sql.mysql_error());
$rows_cuenta = mysql_num_rows($query_cuenta);
//	imprimo hoja anexa
$y = $pdf->GetY() + 20;

$pdf->SetXY(10, $y);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(195, 20, 'LISTA DE CUENTAS CONTABLES', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->SetWidths(array(25, 145, 25));
$pdf->SetAligns(array('C', 'L', 'R'));
$pdf->Row(array('Cuenta', 
				utf8_decode('Descripción'), 
				'Monto'));
$pdf->SetDrawColor(255, 255, 255);
$pdf->Ln(1);
//----
//	imprimo las partidas de la orden
while ($field_cuenta = mysql_fetch_array($query_cuenta)) {	$i++;
	$total_cuenta += $field_cuenta['Monto'];
	
	$pdf->Row(array($field_cuenta['CodCuenta'],
					utf8_decode($field_cuenta['Descripcion']),
					number_format($field_cuenta['Monto'], 2, ',', '.')));
}
//----
$y = $pdf->GetY();
$pdf->SetXY(10, $y);
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0);
$pdf->Rect(10, $y, 195, 0.1, "DF");
//----
$pdf->SetXY(135, $y+1);
$pdf->SetFillColor(245, 245, 245);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(43, 5, 'Total: ', 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($total_cuenta, 2, ',', '.'), 0, 0, 'R');
//----
//---------------------------------------------------

$pdf->Output();
?> 
