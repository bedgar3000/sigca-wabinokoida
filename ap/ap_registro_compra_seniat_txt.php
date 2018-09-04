<?php
session_start();
header('Content-Type: text/html; charset=iso-8859-1');
set_time_limit(-1);
//---------------------------------------------------
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
##	filtro
if ($fCodOrganismo != "") $filtro .=" AND (r.CodOrganismo = '".$fCodOrganismo."')";
if ($fPeriodod != "" || $fPeriodoh != "") {
	if ($fPeriodod != "") $filtro .=" AND (r.PeriodoFiscal >= '".$fPeriodod."')";
	if ($fPeriodoh != "") $filtro .=" AND (r.PeriodoFiscal <= '".$fPeriodoh."')";
}
if ($fCodProveedor != "") $filtro .=" AND (r.CodProveedor = '".$fCodProveedor."')";
if ($fFechaComprobanted != "" || $fFechaComprobanteh != "") {
	if ($fFechaComprobanted != "") $filtro .=" AND (r.FechaComprobante >= '".formatFechaAMD($fFechaComprobanted)."')";
	if ($fFechaComprobanteh != "") $filtro .=" AND (r.FechaComprobante <= '".formatFechaAMD($fFechaComprobanteh)."')";
}
if ($fEstado != "") {
	if ($fEstado == "PA/EN") $filtro .=" AND (r.Estado = 'PA' OR r.Estado = 'EN')";
	else $filtro .=" AND (r.Estado = '".$fEstado."')";
}
if ($fCodBanco != "") $filtro .=" AND (cb.CodBanco = '".$fCodBanco."')";
if ($fNroCuenta != "") $filtro .=" AND (ob.NroCuenta = '".$fNroCuenta."')";
//---------------------------------------------------
##	genero y abro el archivo
$texto = "";
$archivo = fopen("../lib/".$nombre_archivo.".txt", "w+");
//---------------------------------------------------
##	saltos de linea
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
##	consulto registros
$sql = "SELECT
			REPLACE(o.DocFiscal, '-', '') AS RifOrganismo,
			REPLACE(r.PeriodoFiscal, '-', '') AS PeriodoFiscal,
			r.FechaComprobante AS FechaComprobante,
			'C' AS Campo1,
			'01' AS Campo2,
			REPLACE(p.DocFiscal, '-', '') AS RifProveedor,
			r.NroControl,
			r.NroFactura,
			r.MontoFactura AS MontoFactura,
			r.MontoAfecto AS MontoImponible,
			ABS(r.MontoRetenido) AS MontoRetenido,
			'0' AS Campo3,
			CONCAT(SUBSTRING(PeriodoFiscal, 1, 4), SUBSTRING(PeriodoFiscal, 6, 2), NroComprobante) AS Comprobante,
			r.MontoNoAfecto AS MontoExento,
			i.FactorPorcentaje AS PorcentajeIVA,
			'0' AS Campo6
		FROM
			ap_retenciones r
			INNER JOIN mastorganismos o ON (r.CodOrganismo = o.CodOrganismo)
			INNER JOIN mastpersonas p ON (r.CodProveedor = p.CodPersona)
			LEFT JOIN ap_obligaciones ob ON (ob.CodProveedor = r.CodProveedor AND
											 ob.CodTipoDocumento = r.CodTipoDocumento AND
											 ob.NroDocumento = r.NroDocumento)
			LEFT JOIN ap_ctabancaria cb ON (cb.NroCuenta = ob.NroCuenta)
			LEFT JOIN masttiposervicio ts ON (ts.CodTipoServicio = ob.CodTipoServicio)
			LEFT JOIN masttiposervicioimpuesto tsi ON (tsi.CodTipoServicio = ts.CodTipoServicio)
			INNER JOIN mastimpuestos i ON (i.CodImpuesto = tsi.CodImpuesto AND i.CodRegimenFiscal = 'I')
		WHERE
			r.TipoComprobante = 'IVA'
			$filtro
		ORDER BY FechaComprobante, Comprobante";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while ($field = mysql_fetch_array($query)) {
	$texto .= $field[0]."\t".$field[1]."\t".$field[2]."\t".$field[3]."\t".$field[4]."\t".$field[5]."\t".$field[6]."\t".$field[7]."\t".$field[8]."\t".$field[9]."\t".$field[10]."\t".$field[11]."\t".$field[12]."\t".$field[13]."\t".$field[14]."\t".$field[15].$nl;
}
//---------------------------------------------------
##	imprimo y cierro el archivo
fwrite($archivo, $texto);
fclose($archivo);
?>