<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$FlagLotes = (!empty($FlagLotes)?'S':'N');
		$FlagItem = (!empty($FlagItem)?'S':'N');
		$FlagKit = (!empty($FlagKit)?'S':'N');
		$FlagImpuestoVentas = (!empty($FlagImpuestoVentas)?'S':'N');
		$FlagAuto = (!empty($FlagAuto)?'S':'N');
		$FlagDisponible = (!empty($FlagDisponible)?'S':'N');
		$Peso = setNumero($Peso);
		$Volumen = setNumero($Volumen);
		$StockMin = setNumero($StockMin);
		$StockMax = setNumero($StockMax);
		$PrecioUnitario = setNumero($PrecioUnitario);
		$iCodMarca = (!empty($CodMarca)?"CodMarca = '$CodMarca',":"CodMarca = NULL,");
		$iCtaInventario = (!empty($CtaInventario)?"CtaInventario = '$CtaInventario',":"CtaInventario = NULL,");
		$iCtaGasto = (!empty($CtaGasto)?"CtaGasto = '$CtaGasto',":"CtaGasto = NULL,");
		$iCtaInventarioPub20 = (!empty($CtaInventarioPub20)?"CtaInventarioPub20 = '$CtaInventarioPub20',":"CtaInventarioPub20 = NULL,");
		$iCtaGastoPub20 = (!empty($CtaGastoPub20)?"CtaGastoPub20 = '$CtaGastoPub20',":"CtaGastoPub20 = NULL,");
		$iCtaVenta = (!empty($CtaVenta)?"CtaVenta = '$CtaVenta',":"CtaVenta = NULL,");
		$iCtaVentaPub20 = (!empty($CtaVentaPub20)?"CtaVentaPub20 = '$CtaVentaPub20',":"CtaVentaPub20 = NULL,");
		$iCtaTransito = (!empty($CtaTransito)?"CtaTransito = '$CtaTransito',":"CtaTransito = NULL,");
		$iCtaTransitoPub20 = (!empty($CtaTransitoPub20)?"CtaTransitoPub20 = '$CtaTransitoPub20',":"CtaTransitoPub20 = NULL,");
		$iPartidaPresupuestal = (!empty($PartidaPresupuestal)?"PartidaPresupuestal = '$PartidaPresupuestal',":"PartidaPresupuestal = NULL,");
		$iCodImpuesto = (!empty($CodImpuesto)?"CodImpuesto = '$CodImpuesto',":"CodImpuesto = NULL,");
		##	valido
		if (!trim($CodTipoItem) || !trim($Descripcion) || !trim($CodUnidad) || !trim($CodUnidadComp) || !trim($CodUnidadEmb) || !trim($CodLinea) || !trim($CodFamilia) || !trim($CodSubFamilia) || !trim($CodInterno) || !trim($CodProcedencia)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($FlagImpuestoVentas == 'S' && !trim($CodImpuesto)) die("Debe seleccionar el impuesto de ventas");
		else {
			$sql = "SELECT * FROM lg_itemmast WHERE CodInterno = '$CodInterno'";
			$codigo = getRecords($sql);
			if (count($codigo)) die("Código ya ingresado");
		}
		##	codigo
		$CodItem = codigo('lg_itemmast','CodItem',10);
		##	inserto
		$sql = "INSERT INTO lg_itemmast
				SET
					CodItem = '$CodItem',
					CodInterno = '$CodInterno',
					Descripcion = '$Descripcion',
					CodTipoItem = '$CodTipoItem',
					CodUnidad = '$CodUnidad',
					CodUnidadComp = '$CodUnidadComp',
					CodUnidadEmb = '$CodUnidadEmb',
					CantidadComp = '$CantidadComp',
					CantidadEmb = '$CantidadEmb',
					CodLinea = '$CodLinea',
					CodFamilia = '$CodFamilia',
					CodSubFamilia = '$CodSubFamilia',
					FlagLotes = '$FlagLotes',
					FlagItem = '$FlagItem',
					FlagKit = '$FlagKit',
					FlagImpuestoVentas = '$FlagImpuestoVentas',
					FlagAuto = '$FlagAuto',
					FlagDisponible = '$FlagDisponible',
					Imagen = '$Imagen',
					$iCodMarca
					Color = '$Color',
					CodProcedencia = '$CodProcedencia',
					CodBarra = '$CodBarra',
					StockMin = '$StockMin',
					StockMax = '$StockMax',
					$iCtaInventario
					$iCtaGasto
					$iCtaInventarioPub20
					$iCtaGastoPub20
					$iCtaVenta
					$iCtaVentaPub20
					$iCtaTransito
					$iCtaTransitoPub20
					$iPartidaPresupuestal
					$iCodImpuesto
					DiasVencimiento = '$DiasVencimiento',
					PrecioUnitario = '$PrecioUnitario',
					Volumen = '$Volumen',
					Peso = '$Peso',
					Moneda = '$Moneda',
					CodigoSNC = '$CodigoSNC',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	unidades
		$Secuencia = 0;
		for ($i=0; $i < count($unidades_CodUnidad); $i++)
		{
			++$Secuencia;
			$unidades_Valor[$i] = setNumero($unidades_Valor[$i]);
			##	valido
			if (!trim($unidades_Valor[$i])) die("Debe agregar un valor válido para la conversión de unidades");
			##	inserto
			$sql = "INSERT INTO lg_itemunidades
					SET
						CodItem = '$CodItem',
						CodUnidad = '$unidades_CodUnidad[$i]',
						CodUnidadConv = '$unidades_CodUnidadConv[$i]',
						Valor = '$unidades_Valor[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$FlagLotes = (!empty($FlagLotes)?'S':'N');
		$FlagItem = (!empty($FlagItem)?'S':'N');
		$FlagKit = (!empty($FlagKit)?'S':'N');
		$FlagImpuestoVentas = (!empty($FlagImpuestoVentas)?'S':'N');
		$FlagAuto = (!empty($FlagAuto)?'S':'N');
		$FlagDisponible = (!empty($FlagDisponible)?'S':'N');
		$Peso = setNumero($Peso);
		$Volumen = setNumero($Volumen);
		$StockMin = setNumero($StockMin);
		$StockMax = setNumero($StockMax);
		$PrecioUnitario = setNumero($PrecioUnitario);
		$iCodMarca = (!empty($CodMarca)?"CodMarca = '$CodMarca',":"CodMarca = NULL,");
		$iCtaInventario = (!empty($CtaInventario)?"CtaInventario = '$CtaInventario',":"CtaInventario = NULL,");
		$iCtaGasto = (!empty($CtaGasto)?"CtaGasto = '$CtaGasto',":"CtaGasto = NULL,");
		$iCtaInventarioPub20 = (!empty($CtaInventarioPub20)?"CtaInventarioPub20 = '$CtaInventarioPub20',":"CtaInventarioPub20 = NULL,");
		$iCtaGastoPub20 = (!empty($CtaGastoPub20)?"CtaGastoPub20 = '$CtaGastoPub20',":"CtaGastoPub20 = NULL,");
		$iCtaVenta = (!empty($CtaVenta)?"CtaVenta = '$CtaVenta',":"CtaVenta = NULL,");
		$iCtaVentaPub20 = (!empty($CtaVentaPub20)?"CtaVentaPub20 = '$CtaVentaPub20',":"CtaVentaPub20 = NULL,");
		$iCtaTransito = (!empty($CtaTransito)?"CtaTransito = '$CtaTransito',":"CtaTransito = NULL,");
		$iCtaTransitoPub20 = (!empty($CtaTransitoPub20)?"CtaTransitoPub20 = '$CtaTransitoPub20',":"CtaTransitoPub20 = NULL,");
		$iPartidaPresupuestal = (!empty($PartidaPresupuestal)?"PartidaPresupuestal = '$PartidaPresupuestal',":"PartidaPresupuestal = NULL,");
		$iCodImpuesto = (!empty($CodImpuesto)?"CodImpuesto = '$CodImpuesto',":"CodImpuesto = NULL,");
		##	valido
		if (!trim($CodTipoItem) || !trim($Descripcion) || !trim($CodUnidad) || !trim($CodUnidadComp) || !trim($CodUnidadEmb) || !trim($CodLinea) || !trim($CodFamilia) || !trim($CodSubFamilia) || !trim($CodInterno) || !trim($CodProcedencia)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($FlagImpuestoVentas == 'S' && !trim($CodImpuesto)) die("Debe seleccionar el impuesto de ventas");
		else {
			$sql = "SELECT * FROM lg_itemmast WHERE CodInterno = '$CodInterno' AND CodItem <> '$CodItem'";
			$codigo = getRecords($sql);
			if (count($codigo)) die("Código ya ingresado");
		}
		##	actualizo
		$sql = "UPDATE lg_itemmast
				SET
					CodInterno = '$CodInterno',
					Descripcion = '$Descripcion',
					CodTipoItem = '$CodTipoItem',
					CodUnidad = '$CodUnidad',
					CodUnidadComp = '$CodUnidadComp',
					CodUnidadEmb = '$CodUnidadEmb',
					CantidadComp = '$CantidadComp',
					CantidadEmb = '$CantidadEmb',
					CodLinea = '$CodLinea',
					CodFamilia = '$CodFamilia',
					CodSubFamilia = '$CodSubFamilia',
					FlagLotes = '$FlagLotes',
					FlagItem = '$FlagItem',
					FlagKit = '$FlagKit',
					FlagImpuestoVentas = '$FlagImpuestoVentas',
					FlagAuto = '$FlagAuto',
					FlagDisponible = '$FlagDisponible',
					Imagen = '$Imagen',
					$iCodMarca
					Color = '$Color',
					CodProcedencia = '$CodProcedencia',
					CodBarra = '$CodBarra',
					StockMin = '$StockMin',
					StockMax = '$StockMax',
					$iCtaInventario
					$iCtaGasto
					$iCtaInventarioPub20
					$iCtaGastoPub20
					$iCtaVenta
					$iCtaVentaPub20
					$iCtaTransito
					$iCtaTransitoPub20
					$iPartidaPresupuestal
					$iCodImpuesto
					DiasVencimiento = '$DiasVencimiento',
					PrecioUnitario = '$PrecioUnitario',
					Volumen = '$Volumen',
					Peso = '$Peso',
					Moneda = '$Moneda',
					CodigoSNC = '$CodigoSNC',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodItem = '$CodItem'";
		execute($sql);
		##	unidades
		execute("DELETE FROM lg_itemunidades WHERE CodItem = '$CodItem'");
		$Secuencia = 0;
		for ($i=0; $i < count($unidades_CodUnidad); $i++)
		{
			++$Secuencia;
			$unidades_Valor[$i] = setNumero($unidades_Valor[$i]);
			##	valido
			if (!trim($unidades_Valor[$i])) die("Debe agregar un valor válido para la conversión de unidades");
			##	inserto
			$sql = "INSERT INTO lg_itemunidades
					SET
						CodItem = '$CodItem',
						CodUnidad = '$unidades_CodUnidad[$i]',
						CodUnidadConv = '$unidades_CodUnidadConv[$i]',
						Valor = '$unidades_Valor[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM lg_itemmast WHERE CodItem = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "unidades_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'unidades', 'unidades_<?=$id?>');" id="unidades_<?=$id?>">
			<th><?=$id?></th>
			<td>
				<select name="unidades_CodUnidad[]" class="cell">
					<option value="">&nbsp;</option>
					<?=loadSelect("mastunidades", "CodUnidad", "Descripcion")?>
				</select>
			</td>
			<td>
				<input type="text" name="unidades_Valor[]" value="0,00000" style="text-align: right;" class="cell currency5">
			</td>
            <td>
				<select name="unidades_CodUnidadConv[]" class="cell">
					<option value="">&nbsp;</option>
					<?=loadSelect("mastunidades", "CodUnidad", "Descripcion")?>
				</select>
            </td>
		</tr>
		<?php
	}
}
?>