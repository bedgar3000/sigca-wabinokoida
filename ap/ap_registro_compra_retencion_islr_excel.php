<?php
extract($_POST);
extract($_GET);
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=$nombre_archivo.xls");
header("Pragma: no-cache");
header("Expires: 0");
//---------------------------------------------------
include("../lib/fphp.php");
include("lib/fphp.php");
include("../lib/css_excel.php");
//---------------------------------------------------
if ($fCodOrganismo != "") $filtro .=" AND (r.CodOrganismo = '".$fCodOrganismo."')";
if ($fPeriodod != "" || $fPeriodoh != "") {
    if ($fPeriodod != "") $filtro .=" AND (r.PeriodoFiscal >= '".$fPeriodod."')";
    if ($fPeriodoh != "") $filtro .=" AND (r.PeriodoFiscal <= '".$fPeriodoh."')";
}
if ($fCodProveedor != "") $filtro .=" AND (r.CodProveedor = '".$fCodProveedor."')";
if ($fEstado != "") {
    if ($fEstado == "PA/EN") $filtro .=" AND (r.Estado = 'PA' OR r.Estado = 'EN')";
    else $filtro .=" AND (r.Estado = '".$fEstado."')";
}
if ($fCodBanco != "") $filtro .=" AND (cb.CodBanco = '".$fCodBanco."')";
if ($fNroCuenta != "") $filtro .=" AND (ob.NroCuenta = '".$fNroCuenta."')";
//---------------------------------------------------
?>
<!--	IMPRIMO TITULOS		-->
<table>
	<tr>
    	<th class="thead" width="115">Comprobante</th>
    	<th class="thead" width="350">Nombre o Raz&oacute;n Social</th>
    	<th class="thead" width="60">Periodo Fiscal</th>
    	<th class="thead" width="75">Fecha Comprobante</th>
    	<th class="thead" width="115">Nro. Control</th>
        <th class="thead" width="115">Nro. Factura</th>
        <th class="thead" width="115">Fecha Factura</th>
        <th class="thead" width="75">Monto Imponible</th>
        <th class="thead" width="75">Monto Exento</th>
    	<th class="thead" width="75">Monto Impuesto</th>
    	<th class="thead" width="75">Monto Factura</th>
    	<th class="thead" width="50">%</th>
    	<th class="thead" width="75">Monto Retenido</th>
    </tr>

<!--	IMPRIMO CUERPO		-->
<?php
$sql = "SELECT
            CONCAT(SUBSTRING(PeriodoFiscal, 1, 4), SUBSTRING(PeriodoFiscal, 6, 2), NroComprobante) AS NroComprobante,
            p.NomCompleto AS NomProveedor,
            r.PeriodoFiscal,
            r.FechaComprobante,
            r.NroDocumento,
            r.NroControl,
            r.NroFactura,
            r.FechaFactura,
            r.MontoAfecto,
            r.MontoNoAfecto,
            r.MontoImpuesto,
            r.MontoFactura,
            i.FactorPorcentaje,
            ABS(r.MontoRetenido) AS MontoRetenido
        FROM
            ap_retenciones r
            INNER JOIN mastorganismos o ON (o.CodOrganismo = r.CodOrganismo)
            INNER JOIN mastpersonas p ON (p.CodPersona = r.CodProveedor)
            INNER JOIN mastimpuestos i ON (i.CodImpuesto = r.CodImpuesto)
            LEFT JOIN ap_obligaciones ob ON (ob.CodProveedor = r.CodProveedor AND
                                             ob.CodTipoDocumento = r.CodTipoDocumento AND
                                             ob.NroDocumento = r.NroDocumento)
            LEFT JOIN ap_ctabancaria cb ON (cb.NroCuenta = ob.NroCuenta)
        WHERE
            r.TipoComprobante = 'ISLR'
            $filtro
        ORDER BY FechaComprobante, NroComprobante";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while ($field = mysql_fetch_array($query)) {
	$MontoRetenido += $field['MontoRetenido'];
	?>
    <tr>
        <td class="tbody" align="center">=CONCATENAR(<?=$field['NroComprobante']?>)</th>
        <td class="tbody" align="left"><?=$field['NomProveedor']?></th>
        <td class="tbody" align="center"><?=$field['PeriodoFiscal']?></th>
        <td class="tbody" align="center"><?=$field['FechaComprobante']?></th>
        <td class="tbody" align="left"><?=$field['NroControl']?></th>
        <td class="tbody" align="left"><?=$field['NroFactura']?></th>
        <td class="tbody" align="center"><?=$field['FechaFactura']?></th>
        <td class="tbody" align="right">=DECIMAL(<?=number_format($field['MontoAfecto'], 2, ',', '')?>; 2)</th>
        <td class="tbody" align="right">=DECIMAL(<?=number_format($field['MontoNoAfecto'], 2, ',', '')?>; 2)</th>
        <td class="tbody" align="right">=DECIMAL(<?=number_format($field['MontoImpuesto'], 2, ',', '')?>; 2)</th>
        <td class="tbody" align="right">=DECIMAL(<?=number_format($field['MontoFactura'], 2, ',', '')?>; 2)</th>
        <td class="tbody" align="right">=DECIMAL(<?=number_format($field['FactorPorcentaje'], 2, ',', '')?>; 2)</th>
        <td class="tbody" align="right">=DECIMAL(<?=number_format($field['MontoRetenido'], 2, ',', '')?>; 2)</th>
    </tr>
    <?php
}
?>
<tr>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right"></th>
    <td class="tbody" align="right">TOTAL:</th>
    <td class="tbody" align="right">=DECIMAL(<?=number_format($MontoRetenido, 2, ',', '')?>; 2)</th>
</tr>
</table>