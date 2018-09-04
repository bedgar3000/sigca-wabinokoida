function setMontosVentas(pu, id) {
	var MontoAfecto = 0;
	var MontoNoAfecto = 0;
	var MontoImpuesto = 0;
	var MontoDcto = 0;
	var MontoTotal = 0;
	var igv = new Number($('#igv').val());
	var igvp = igv / 100 + 1;

	var thPrecioUnit = 0;
	var thMontoTotal = 0;
	var thPrecioUnitOriginal = 0;
	var thPrecioUnitFinal = 0;
	var thMontoTotalFinal = 0;

	//	
	$('input[name="detalle_Secuencia[]"]').each(function(idx) {
		var detalle_FlagExonIva = $('input[name="detalle_FlagExonIva[]"]:eq('+idx+')').prop('checked');
		var detalle_CantidadPedida = setNumero($('input[name="detalle_CantidadPedida[]"]:eq('+idx+')').val());
		var detalle_PrecioUnit = setNumero($('input[name="detalle_PrecioUnit[]"]:eq('+idx+')').val());
		var detalle_PrecioUnitOriginal = setNumero($('input[name="detalle_PrecioUnitOriginal[]"]:eq('+idx+')').val());

		if (detalle_PrecioUnitOriginal > 0) {
			var detalle_PorcentajeDcto = setNumero($('input[name="detalle_PorcentajeDcto[]"]:eq('+idx+')').val());
			var detalle_MontoDcto = 0;

			if (detalle_PrecioUnit < detalle_PrecioUnitOriginal) {
				detalle_MontoDcto = (detalle_PrecioUnitOriginal - detalle_PrecioUnit) * detalle_CantidadPedida;
				detalle_PorcentajeDcto = detalle_MontoDcto * 100 / (detalle_PrecioUnitOriginal * detalle_CantidadPedida);
			}
			else detalle_PorcentajeDcto = 0;
		} else {
			var detalle_PorcentajeDcto = 0;
			var detalle_MontoDcto = 0;
		}

		var detalle_MontoTotal = detalle_CantidadPedida * detalle_PrecioUnit;

		if (!detalle_FlagExonIva) var detalle_PrecioUnitFinal = detalle_PrecioUnit / igvp;
		else var detalle_PrecioUnitFinal = detalle_PrecioUnit;
		
		var detalle_MontoTotalFinal = detalle_CantidadPedida * detalle_PrecioUnitFinal;
		var detalle_MontoTotalOriginal = detalle_CantidadPedida * detalle_PrecioUnitOriginal;

		$('input[name="detalle_PorcentajeDcto[]"]:eq('+idx+')').val(detalle_PorcentajeDcto).formatCurrency();
		$('input[name="detalle_MontoDcto[]"]:eq('+idx+')').val(detalle_MontoDcto).formatCurrency();
		$('input[name="detalle_MontoTotal[]"]:eq('+idx+')').val(detalle_MontoTotal).formatCurrency();
		$('input[name="detalle_PrecioUnitFinal[]"]:eq('+idx+')').val(detalle_PrecioUnitFinal).formatCurrency({ roundToDecimalPlace: 5 });
		$('input[name="detalle_MontoTotalFinal[]"]:eq('+idx+')').val(detalle_MontoTotalFinal).formatCurrency();
		
		if (detalle_FlagExonIva) MontoNoAfecto += (detalle_MontoTotal + detalle_MontoDcto);
		else MontoAfecto += (detalle_MontoTotal + detalle_MontoDcto);
		MontoDcto += detalle_MontoDcto;

		thPrecioUnit = thPrecioUnit + detalle_PrecioUnit;
		thMontoTotal = thMontoTotal + detalle_MontoTotal;
		thPrecioUnitOriginal = thPrecioUnitOriginal + detalle_PrecioUnitOriginal;
		thPrecioUnitFinal = thPrecioUnitFinal + detalle_PrecioUnitFinal;
		thMontoTotalFinal = thMontoTotalFinal + detalle_MontoTotalFinal;
	});
	MontoAfecto = MontoAfecto / igvp;
	MontoImpuesto = MontoAfecto * igv / 100;
	MontoTotal = MontoAfecto + MontoNoAfecto + MontoImpuesto - MontoDcto;
	var MontoTotalsDesc = MontoAfecto + MontoNoAfecto + MontoImpuesto;
	var PorcentajeDcto = MontoDcto * 100 / MontoTotalsDesc;

	$('#MontoAfecto').val(MontoAfecto).formatCurrency();
	$('#MontoNoAfecto').val(MontoNoAfecto).formatCurrency();
	$('#MontoDcto').val(MontoDcto).formatCurrency();
	$('#MontoImpuesto').val(MontoImpuesto).formatCurrency();
	$('#MontoTotal').val(MontoTotal).formatCurrency();
	$('#PorcentajeDcto').val(PorcentajeDcto).formatCurrency();

	$('#thPrecioUnit').html(thPrecioUnit).formatCurrency();
	$('#thMontoTotal').html(thMontoTotal).formatCurrency();
	$('#thPrecioUnitOriginal').html(thPrecioUnitOriginal).formatCurrency();
	$('#thPrecioUnitFinal').html(thPrecioUnitFinal).formatCurrency();
	$('#thMontoTotalFinal').html(thMontoTotalFinal).formatCurrency();
}

function modalDescuentoVentas() {
	if ($('#sel_detalle').val() == '') {
		cajaModal('Debe seleccionar una linea');
	}
	else {
		var sel_detalle = $('#sel_detalle').val();
		var res = sel_detalle.split("_");
		var nro = res[1];
		var TipoDetalle = '';
		var CodItem = '';
		var CodInterno = '';
		var Descripcion = '';
		var CodUnidad = '';
		var CodUnidadVenta = '';
		var CantidadPedida = '';
		var PrecioUnit = '';
		var PrecioUnitOriginal = '';
		var MontoTotal = '';
		var PorcentajeDcto = '';
		var MontoDcto = '';
		var FlagExonIva = 'N';
		var i = 0;
		var PrecioVenta = 0;
		//	
		$('input[name="detalle_Secuencia[]"]').each(function(idx) {
			var id = 'detalle_Secuencia' + nro;
			if ($('input[name="detalle_Secuencia[]"]:eq('+idx+')').attr('id') == id) {
				i = idx;
				TipoDetalle = $('input[name="detalle_TipoDetalle[]"]:eq('+idx+')').val();
				CodItem = $('input[name="detalle_CodItem[]"]:eq('+idx+')').val();
				CodInterno = $('input[name="detalle_CodInterno[]"]:eq('+idx+')').val();
				Descripcion = $('input[name="detalle_Descripcion[]"]:eq('+idx+')').val();
				CodUnidad = $('#detalle_CodUnidad'+nro).val();
				CodUnidadVenta = $('#detalle_CodUnidadVenta'+nro).val();
				CantidadPedida = $('input[name="detalle_CantidadPedida[]"]:eq('+idx+')').val();
				PrecioUnit = $('input[name="detalle_PrecioUnit[]"]:eq('+idx+')').val();
				PrecioUnitOriginal = $('input[name="detalle_PrecioUnitOriginal[]"]:eq('+idx+')').val();
				MontoTotal = $('input[name="detalle_MontoTotal[]"]:eq('+idx+')').val();
				PorcentajeDcto = $('input[name="detalle_PorcentajeDcto[]"]:eq('+idx+')').val();
				MontoDcto = $('input[name="detalle_MontoDcto[]"]:eq('+idx+')').val();
				if ($('input[name="detalle_MontoDcto[]"]:eq('+idx+')').prop('checked')) FlagExonIva = 'S';
				PrecioVenta = setNumero(PrecioUnitOriginal);
			}
		});

		if (PrecioVenta > 0) {
			//	ajax
			$.post('co_documento_descuento_form.php', 'TipoDetalle='+TipoDetalle+'&CodItem='+CodItem+'&CodInterno='+CodInterno+'&Descripcion='+Descripcion+'&CodUnidad='+CodUnidad+'&CodUnidadVenta='+CodUnidadVenta+'&CantidadPedida='+CantidadPedida+'&PrecioUnit='+PrecioUnit+'&PrecioUnitOriginal='+PrecioUnitOriginal+'&MontoTotal='+MontoTotal+'&PorcentajeDcto='+PorcentajeDcto+'&MontoDcto='+MontoDcto+'&FlagExonIva='+FlagExonIva, function(data) {
				$("#cajaModal").dialog({
					buttons: {
						"Aceptar": function() {
							$(this).dialog("close");
							$('input[name="detalle_CodUnidad[]"]:eq('+i+')').val($('#descuento_CodUnidad').val());
							$('input[name="detalle_CodUnidadVenta[]"]:eq('+i+')').val($('#descuento_CodUnidadVenta').val());
							$('input[name="detalle_CantidadPedida[]"]:eq('+i+')').val($('#descuento_CantidadPedida').val());
							$('input[name="detalle_PrecioUnit[]"]:eq('+i+')').val($('#descuento_PrecioUnit').val());
							$('input[name="detalle_MontoTotal[]"]:eq('+i+')').val($('#descuento_MontoTotal').val());
							$('input[name="detalle_PorcentajeDcto[]"]:eq('+i+')').val($('#descuento_PorcentajeDcto').val());
							$('input[name="detalle_MontoDcto[]"]:eq('+i+')').val($('#descuento_MontoDcto').val());
							$('input[name="detalle_PrecioUnitFinal[]"]:eq('+i+')').val($('#descuento_PrecioUnitFinal').val());
							$('input[name="detalle_MontoTotalFinal[]"]:eq('+i+')').val($('#descuento_MontoTotalFinal').val());
							setMontosVentas();
						},
						"Cancelar": function() {
							$(this).dialog("close");
						}
					}
				});
				$("#cajaModal").dialog({ title: "<img src='../imagenes/info.png' width='24' align='absmiddle' />Aplicar Descuento", width: 500 });
				$("#cajaModal").html(data);
				$('#cajaModal').dialog('open');
				inicializar();
		    });
		} else {
			cajaModal('No puede aplicar descuento a un producto sin Precio de Venta');
		}
	}
}

function setMontosDescuentoVentas(porMonto) {
	var igv = setNumero($('#descuento_igv').val());
	var igvp = igv / 100 + 1;
	var FlagExonIva = setNumero($('#descuento_FlagExonIva').val());
	var PrecioUnitLista = setNumero($('#descuento_PrecioUnitLista').val());
	var PrecioUnit = setNumero($('#descuento_PrecioUnit').val());
	var PrecioUnitOriginal = setNumero($('#descuento_PrecioUnitOriginal').val());
	var CantidadPedida = setNumero($('#descuento_CantidadPedida').val());
	var MontoDcto = setNumero($('#descuento_MontoDcto').val());
	var PorcentajeDcto = setNumero($('#descuento_PorcentajeDcto').val());
	var PrecioUnitFinal = setNumero($('#descuento_PrecioUnitFinal').val());
	var MontoTotalFinal = setNumero($('#descuento_MontoTotalFinal').val());
	var MontoTotal = 0;

	if (porMonto) {
		PorcentajeDcto = MontoDcto * 100 / PrecioUnitLista;
	} else {
		MontoDcto = PrecioUnitLista * PorcentajeDcto / 100;
	}
	PrecioUnit = PrecioUnitLista - MontoDcto;
	MontoTotal = PrecioUnit * CantidadPedida;
	if (FlagExonIva != 'S') PrecioUnitFinal = PrecioUnit / igvp;
	MontoTotalFinal = PrecioUnitFinal * CantidadPedida;

	$('#descuento_PrecioUnit').val(PrecioUnit).formatCurrency();
	$('#descuento_MontoDcto').val(MontoDcto).formatCurrency();
	$('#descuento_PorcentajeDcto').val(PorcentajeDcto).formatCurrency();
	$('#descuento_MontoTotal').val(MontoTotal).formatCurrency();
	$('#descuento_PrecioUnitFinal').val(PrecioUnitFinal).formatCurrency();
	$('#descuento_MontoTotalFinal').val(MontoTotalFinal).formatCurrency();
}

function verInventarioAlmacen() {
	if ($('#sel_detalle').val() == '') {
		cajaModal('Debe seleccionar una linea');
	}
	else {
		var sel_detalle = $('#sel_detalle').val();
		var res = sel_detalle.split("_");
		var nro = res[1];
		var id = 'detalle_Secuencia' + nro;
		var CodItem = '';
		var TipoItem = '';
		//	
		$('input[name="detalle_Secuencia[]"]').each(function(idx) {
			if ($('input[name="detalle_Secuencia[]"]:eq('+idx+')').attr('id') == id) {
				i = idx;
				TipoDetalle = $('input[name="detalle_TipoDetalle[]"]:eq('+idx+')').val();
				CodItem = $('input[name="detalle_CodItem[]"]:eq('+idx+')').val();
			}
		});
		//	
		if (TipoDetalle != 'I') {
			cajaModal('Debe seleccionar un Item');
		}
		else {
			var url = "gehen.php?anz=co_inventario_almacen&CodItem="+CodItem+"&iframe=true&width=100%&height=350";
			$('#a_inventario_almacen').attr('href', url);
			$('#a_inventario_almacen').click();
		}
	}
}

function cambiarUnidad(i) {
	var CodUnidad = $('#detalle_CodUnidad'+i).val();
	var CodUnidadVenta = $('#detalle_CodUnidadVenta'+i).val();
	var MontoVenta = new Number($('#detalle_MontoVenta'+i).val());
	var MontoVentaUnitario = new Number($('#detalle_MontoVentaUnitario'+i).val());

	if (CodUnidad == CodUnidadVenta) {
		$('#detalle_PrecioUnit'+i).val(MontoVentaUnitario).formatCurrency();
		$('#detalle_PrecioUnitOriginal'+i).val(MontoVentaUnitario).formatCurrency();
	} else {
		$('#detalle_PrecioUnit'+i).val(MontoVenta).formatCurrency();
		$('#detalle_PrecioUnitOriginal'+i).val(MontoVenta).formatCurrency();
	}
	$('#detalle_MontoDcto'+i).val('0').formatCurrency();
	$('#detalle_PorcentajeDcto'+i).val('0').formatCurrency();
	setMontosVentas();
}