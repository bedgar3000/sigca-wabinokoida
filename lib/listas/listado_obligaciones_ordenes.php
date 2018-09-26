<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
include("../fphp.php");
//	------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
<link type="text/css" rel="stylesheet" href="../../css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../../css/estilo.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" src="../../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/fscript.js" charset="utf-8"></script>
</head>

<body>
<!-- ui-dialog -->
<div id="cajaModal"></div>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Listado de Obligaciones</td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<center>
<div style="overflow:scroll; width:700px; height:260px;">
<table width="1225" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="15">&nbsp;</th>
        <th scope="col" colspan="2">Documento</th>
        <th scope="col" width="300" align="left">Proveedor</th>
        <th scope="col" width="75">Fecha Documento</th>
        <th scope="col" width="75">Nro. Registro</th>
        <th scope="col" width="450" align="left">Glosa</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
	$sql = "SELECT
				o.Comentarios,
				o.FechaRegistro,
				o.NroRegistro,
				o.CodProveedor,
				o.CodTipoDocumento,
				o.NroDocumento,
				p.NomCompleto As NomProveedor
			FROM
				ap_documentos d
				INNER JOIN ap_obligaciones o ON (o.CodProveedor = d.CodProveedor AND
												 o.CodTipoDocumento = d.ObligacionTipoDocumento AND
												 o.NroDocumento = d.ObligacionNroDocumento)
				INNER JOIN mastpersonas p ON (p.CodPersona = d.CodProveedor)
			WHERE
				d.CodOrganismo = '".$CodOrganismo."' AND
				d.Anio = '".$Anio."' AND
				d.ReferenciaTipoDocumento = '".$ReferenciaTipoDocumento."' AND
				d.ReferenciaNroDocumento = '".$ReferenciaNroDocumento."' AND
				d.Estado = 'RV'";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	while ($field = mysql_fetch_array($query)) {	$nro++;
		?>
		<tr class="trListaBody" onclick="facturacion_activos_obligacion('<?=$field['CodTipoDocumento']?>', '<?=$field['NroDocumento']?>', '<?=formatFechaDMA($field['FechaRegistro'])?>');">
			<th>
				<?=$nro?>
			</th>
			<td align="center" width="25">
            	<?=$field['CodTipoDocumento']?>
			</td>
			<td width="125">
            	<?=$field['NroDocumento']?>
			</td>
			<td>
            	<?=htmlentities($field['NomProveedor'])?>
			</td>
			<td align="center">
            	<?=formatFechaDMA($field['FechaRegistro'])?>
			</td>
			<td align="center">
            	<?=$field['NroRegistro']?>
			</td>
			<td>
            	<?=htmlentities($field['Comentarios'])?>
			</td>
		</tr>
		<?php
	}
    ?>
    </tbody>
</table>
</div>
</center>
</body>
</html>