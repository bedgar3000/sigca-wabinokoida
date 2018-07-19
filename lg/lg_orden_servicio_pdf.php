<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("../lib/lg_fphp.php");
connect();
//---------------------------------------------------
//	variables globales
list($Anio, $CodOrganismo, $NroOrden)=SPLIT('[.]', $registro);
$hoja_anexa = false;

//	consulto los datos de la orden
$sql = "SELECT 
			oc.*,
			mp.DocFiscal As DocFiscalPersona,
			mp.Direccion,
			mp.Telefono1,
			fp.Descripcion AS NomFormaPago,
			i.FactorPorcentaje,
			o.Organismo,
			o.DocFiscal AS DocFiscalOrganismo,
			o.Telefono1,
			o.Fax1,
			o.Direccion AS DireccionOrganismo,
			pr.NroInscripcionSNC,
			r.CodInterno AS NroRequisicion,
			r.FechaAprobacion AS FechaRequisicion,
			ue.CodCentroCosto AS CodCentroCostoUnidad
		FROM
			lg_ordenservicio oc
			INNER JOIN mastpersonas mp ON (oc.CodProveedor = mp.CodPersona)
			INNER JOIN mastproveedores pr ON (mp.CodPersona = pr.CodProveedor)
			INNER JOIN mastorganismos o ON (oc.CodOrganismo = o.CodOrganismo)
			LEFT JOIN mastformapago fp ON (pr.CodFormaPago = fp.CodFormaPago)
			LEFT JOIN masttiposervicioimpuesto tsi ON (oc.CodTipoServicio = tsi.CodTipoServicio)
			LEFT JOIN mastimpuestos i ON (i.CodImpuesto = tsi.CodImpuesto)
			LEFT JOIN lg_requerimientosdet rd ON (oc.Anio = rd.Anio AND oc.NroOrden = rd.NroOrden)
			LEFT JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento AND r.Clasificacion = 'SER')
			LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = oc.CodOrganismo AND ppto.CodPresupuesto = oc.CodPresupuesto)
			LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = ppto.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
		WHERE
			oc.Anio = '".$Anio."' AND
			oc.CodOrganismo = '".$CodOrganismo."' AND
			oc.NroOrden = '".$NroOrden."'
		ORDER BY FechaRequisicion DESC";
$query_mast = mysql_query($sql) or die (mysql_error());
if (mysql_num_rows($query_mast) != 0) $field_mast = mysql_fetch_array($query_mast);
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	var $widths;
	var $aligns;
	
	//	ancho de las celdas
	function SetWidths($w) {
		$this->widths = $w;
	}
	
	//	alineacion de las celdas
	function SetAligns($a) {
		$this->aligns = $a;
	}
	
	//	dibujar celdas
	function Row($data) {
		// calculo el alto de la celda
		$nb = 0;
		for($i=0;$i<count($data);$i++)
			$nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
		$h = 5 * $nb;		
		//	genero un salto de pagina si es necesario
		$this->CheckPageBreak($h);		
		//	dibujo las celdas de las filas
		for($i=0;$i<count($data);$i++) {
			$w = $this->widths[$i];
			$a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';			
			//	posicion actual
			$x = $this->GetX();
			$y = $this->GetY();			
			//	dibujo el borde
			$this->Rect($x, $y, $w, $h, "DF");			
			//	imprimo el texto
			$this->MultiCell($w, 5, $data[$i], 0, $a);			
			//	coloco la posicion a la derecha de la celda
			$this->SetXY($x+$w, $y);
		}		
		//	siguiente linea
		$this->Ln($h);
	}
	
	//	valido salto de pagina
	function CheckPageBreak($h) {
		//	si la altura se desborda añado pagina
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}
	
	//	calcula el numero de lineas de un MultiCell de acuerdo al ancho y el texto	
	function NbLines($w, $txt) {
		$cw = &$this->CurrentFont['cw'];
		if($w == 0) $w = $this->w-$this->rMargin-$this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
		$s = str_replace("\r", '', $txt);
		$nb = strlen($s);
		if($nb > 0 and $s[$nb-1] == "\n")
		$nb--;
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;
		while($i<$nb) {
			$c = $s[$i];
			if($c == "\n") {
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				continue;
			}
			if($c == ' ')
				$sep = $i;
			$l += $cw[$c];
			if($l > $wmax) {
				if($sep == -1) {
					if($i == $j)
						$i++;
				} else $i=$sep+1;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
			} else $i++;
		}
		return $nl;
	}
	
	//	Cabecera
	function Header() {
		global $field_mast;
		global $hoja_anexa;
		//------
		$this->Image('../imagenes/logos/logo-alcaldia.jpg', 10, 10, 15, 13);	
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(25, 10); $this->Cell(195, 5, utf8_decode($field_mast['Organismo']), 0, 0, 'L');
		$this->SetXY(25, 13); 
		$this->Cell(30, 5, utf8_decode('R.I.F. '.$field_mast['DocFiscalOrganismo']), 0, 0, 'L');
		$this->Cell(35, 5, utf8_decode('Telefono: '.$field_mast['Telefono1']), 0, 0, 'L'); 
		$this->Cell(35, 5, utf8_decode('Fax: '.$field_mast['Fax1']), 0, 0, 'L');		
		$this->SetXY(25, 16); $this->Cell(195, 5, utf8_decode($field_mast['DireccionOrganismo']), 0, 0, 'L');
		$this->SetXY(25, 19); $this->Cell(195, 5, utf8_decode(getUnidadEjecutora($_PARAMETRO["CATADM"])), 0, 0, 'L');
		//------
		$this->SetFont('Arial', '', 9);
		$this->SetXY(165, 11); $this->Cell(20, 5, utf8_decode('Nro. Orden: '), 0, 0, 'R');
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(20, 5, $field_mast['NroInterno']);
		$this->SetFont('Arial', '', 9);
		$this->SetXY(165, 15); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'R'); 
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(20, 5, formatFechaDMA($field_mast['FechaDocumento']));
		$this->SetFont('Arial', '', 9);
		$this->SetXY(165, 19); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'R'); 
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(20, 5, $this->PageNo().' de {nb}');
		//------		
		$this->SetFont('Arial', 'B', 12);
		$this->SetXY(10, 30); 
		if (!$hoja_anexa) $this->Cell(195, 10, 'ORDEN DE SERVICIO', 0, 1, 'C');
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
			$this->Ln(6);
			$this->Cell(30, 5, utf8_decode('Dirección: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, utf8_decode($field_mast['Direccion']), 0, 0, 'L');
			$this->Ln(6);
			##	
			$CategoriaProgSolicitante = getCategoriaXCentroCosto($field_mast['CodCentroCostoUnidad']);
			$UnidadSolicitante = getUnidadEjecutora($CategoriaProgSolicitante);
			$CodPersonaSolicitante = getPersonaUnidadEjecutora($CategoriaProgSolicitante);
			$sql = "SELECT NomCompleto FROM mastpersonas WHERE CodPersona = '$CodPersonaSolicitante'";
			$PersonaSolicitante = getVar3($sql);
			##	
			$this->Cell(30, 5, utf8_decode('Jefe de Unidad: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, utf8_decode($PersonaSolicitante), 0, 0, 'L');
			$this->Cell(30, 5, utf8_decode('Nro. Requisición: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, $field_mast['NroRequisicion'], 0, 0, 'L');
			$this->Ln(6);
			$this->Cell(30, 5, utf8_decode('Unidad Solicitante: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, utf8_decode($UnidadSolicitante), 0, 0, 'L');
			$this->Cell(30, 5, utf8_decode('Fecha Requisición: '), 0, 0, 'L', 1);
			$this->Cell(95, 5, formatFechaDMA($field_mast['FechaRequisicion']), 0, 0, 'L');
			$this->Ln(6);
			$this->Cell(30, 5, utf8_decode('Descripción: '), 0, 0, 'L', 1);
			$this->MultiCell(165, 5, utf8_decode($field_mast['Descripcion']), 0, 'L');
			$this->Ln(3);
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
		list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirma(getPersonaUnidadEjecutora($_PARAMETRO["CATCOMPRAS"]));
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
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 50);
//----

//----
//	imprimo los detalles de la orden
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$sql = "SELECT *, (CantidadPedida * PrecioUnit) AS PrecioCantidad
		FROM lg_ordenserviciodetalle
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
if ($pdf->GetY() < 150) $y = 150; else { $pdf->AddPage(); $y = 150; }
$pdf->SetXY(10, $y);
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0);
$pdf->Rect(10, $y, 195, 0.1, "DF");
//----
$tipo_impuesto = "(".$field_mast['CodTipoServicio']." ".number_format($field_mast['FactorPorcentaje'], 2, ',', '.')." %): ";
$monto_total_en_letras = convertir_a_letras($field_mast['TotalMontoIva'], "moneda");
$pdf->SetFillColor(245, 245, 245);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY(10, $y+2); $pdf->Cell(120, 5, utf8_decode('Monto total en letras: '), 0, 1, 'L', 1);
$pdf->SetXY(10, $y+7); $pdf->MultiCell(120, 4, utf8_decode(strtoupper($monto_total_en_letras)), 0, 'J');
$pdf->SetXY(135, $y+2); 
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(43, 5, 'Monto Afecto: ', 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($field_mast['MontoOriginal'], 2, ',', '.'), 0, 0, 'R');
$pdf->SetXY(135, $y+8);
$pdf->Cell(43, 5, $tipo_impuesto, 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($field_mast['MontoIva'], 2, ',', '.'), 0, 0, 'R');
$pdf->SetXY(135, $y+14);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(43, 5, 'Monto Total: ', 0, 0, 'R', 1);
$pdf->Cell(27, 5, number_format($field_mast['TotalMontoIva'], 2, ',', '.'), 0, 0, 'R');
//----

//----
//	consulto los activos
$pdf->SetY($y+20);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(195, 5, 'DESCRIPCION DEL ACTIVO', 1, 1, 'C', 1);
$pdf->SetWidths(array(20,80,45,25,25));
$pdf->SetAligns(array('C','L','C','C','C'));
$pdf->Row(array(utf8_decode('Código'),
				utf8_decode('Descripción'),
				'Marca',
				'Modelo',
				'Serial'));
$pdf->Ln(1);
$sql = "SELECT
			a.CodigoInterno,
			a.Descripcion,
			a.Marca,
			a.Modelo,
			a.NumeroSerie
		FROM
			lg_ordenserviciodetalle osd
			INNER JOIN af_activo a ON (a.Activo = osd.NroActivo)
		WHERE
			osd.Anio = '".$Anio."' AND
			osd.CodOrganismo = '".$CodOrganismo."' AND
			osd.NroOrden = '".$NroOrden."'";
$field_activo = getRecords($sql);
foreach($field_activo as $f) {
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($f['CodigoInterno'],
					utf8_decode($f['Descripcion']),
					$f['Marca'],
					$f['Modelo'],
					$f['NumeroSerie']));
}
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
			do.CodTipoDocumento = 'OS' AND
			do.NroDocumento = '".$NroOrden."'
		ORDER BY Ejercicio, CodPresupuesto, CodFuente, cod_partida";
$query_partida = mysql_query($sql) or die ($sql.mysql_error());	$i=0; $total_partida = 0;
$rows_partida = mysql_num_rows($query_partida);
//	imprimo hoja anexa
$hoja_anexa = true; 
$pdf->AddPage();
$y = $pdf->GetY();

$pdf->SetXY(10, $y);
$pdf->SetDrawColor(0, 0, 0);
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
				'".$field_mast['MontoIva']."' AS Monto
			FROM ac_mastplancuenta
			WHERE CodCuenta = '".$CodCuenta_igv."' AND ".floatval($field_mast['MontoIva'])." > 0
			GROUP BY CodCuenta)
			UNION
			(SELECT 
				ocd.CodCuenta, 
				c.Descripcion,
				SUM(ocd.CantidadPedida * ocd.PrecioUnit) AS Monto
			FROM 
				lg_ordenserviciodetalle ocd
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
				'".$field_mast['MontoIva']."' AS Monto
			FROM ac_mastplancuenta20
			WHERE CodCuenta = '".$CodCuentaPub20_igv."' AND ".floatval($field_mast['MontoIva'])." > 0
			GROUP BY CodCuenta)
			UNION
			(SELECT 
				c.CodCuenta, 
				c.Descripcion,
				SUM(ocd.CantidadPedida * ocd.PrecioUnit) AS Monto
			FROM 
				lg_ordenserviciodetalle ocd
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
$pdf->SetAligns(array('R', 'L', 'R'));
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