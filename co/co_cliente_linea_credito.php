<?php
$sql = "SELECT
			p.NomCompleto,
			p.CodPersona,
			cl.MontoLineaCredito,
			cl.LineaCreditoMoneda,
			cl.Clasificacion,
			'0.00' AS MontoUtilizado,
			'0.00' AS MontoPorAprobar,
			cl.MontoLineaCredito AS MontoDisponible
		FROM mastpersonas p
		INNER JOIN mastcliente cl ON cl.CodPersona = p.CodPersona
		WHERE p.CodPersona = '$CodPersona'";
$field = getRecord($sql);
//	------------------------------------
$_width = 800;
?>
<form method="POST" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="CodPersona" id="CodPersona" value="<?=$CodPersona?>" />

	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Línea de Crédito del Cliente</td>
	    </tr>
		<tr>
			<td class="tagForm">Cliente:</td>
			<td>
				<input type="text" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:66px;" readonly />
				<input type="text" name="NomCompleto" id="NomCompleto" value="<?=htmlentities($field['NomCompleto'])?>" style="width:225px;" readonly />
			</td>
			<td class="tagForm">Linea Crédito:</td>
			<td>
				<input type="text" name="MontoLineaCredito" id="MontoLineaCredito" value="<?=number_format($field['MontoLineaCredito'],2,',','.')?>" style="width:150px; text-align: right;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Linea Crédito:</td>
			<td>
				<input type="text" name="MontoLineaCredito" id="MontoLineaCredito" value="<?=number_format($field['MontoLineaCredito'],2,',','.')?>" style="width:200px; text-align: right;" readonly />
				<input type="text" name="LineaCreditoMoneda" id="LineaCreditoMoneda" value="<?=printValoresGeneral('monedas',$field['LineaCreditoMoneda'])?>" style="width:91px;" readonly />
			</td>
			<td class="tagForm">Monto Utilizado:</td>
			<td>
				<input type="text" name="MontoUtilizado" id="MontoUtilizado" value="<?=number_format($field['MontoUtilizado'],2,',','.')?>" style="width:150px; text-align: right;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Fecha Vencimiento:</td>
			<td>
				<input type="text" name="FechaVencLineaCredito" id="FechaVencLineaCredito" value="<?=formatFechaDMA($field['FechaVencLineaCredito'])?>" style="width:66px;" readonly />
			</td>
			<td class="tagForm">Monto Por Aprobar:</td>
			<td>
				<input type="text" name="MontoPorAprobar" id="MontoPorAprobar" value="<?=number_format($field['MontoPorAprobar'],2,',','.')?>" style="width:150px; text-align: right;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Clasif. Crédito:</td>
			<td>
				<input type="text" name="Clasificacion" id="Clasificacion" value="<?=printValoresGeneral('cliente-clasificacion',$field['Clasificacion'])?>" style="width:66px;" readonly />
			</td>
			<td class="tagForm" style="font-weight: bold; font-size: 12px;">Monto Disponible:</td>
			<td>
				<input type="text" name="MontoDisponible" id="MontoDisponible" value="<?=number_format($field['MontoDisponible'],2,',','.')?>" style="width:150px; text-align: right; font-weight: bold; font-size: 12px;" readonly />
			</td>
		</tr>
	</table>
	<br>

	<div style="overflow:scroll; height:200px; width:100%; min-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%;">
			<thead>
				<tr>
					<th style="min-width: 200px; text-align: left;">Organismo</th>
					<th style="min-width: 200px; text-align: left;">Establecimiento</th>
					<th style="min-width: 100px;">Documento</th>
					<th style="min-width: 75px;">Fecha Doc.</th>
					<th style="min-width: 75px;">Fecha Venc.</th>
					<th style="min-width: 50px;">Dias Atraso</th>
					<th style="min-width: 50px;">Moneda</th>
					<th style="min-width: 125px;">Monto Total</th>
					<th style="min-width: 125px;">Monto Pagado</th>
				</tr>
			</thead>
			
			<tbody>
			</tbody>
		</table>
	</div>
</form>