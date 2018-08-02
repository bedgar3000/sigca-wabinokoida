<?php
//	FUNCION PARA CARGAR SELECTS 
function loadSelectValores($tabla, $codigo=NULL, $opt=0) {
	switch ($tabla) {
		case "ESTADO-BANCARIO":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Actualizado";
			$c[] = "CO"; $v[] = "Contabilizado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "ESTADO-BANCARIO2":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Actualizado";
			break;
			
		case "ESTADO-OBLIGACIONES":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "PA"; $v[] = "Pagada";
			break;
			
		case "ESTADO-OBLIGACIONES-PAGADAS":
			$c[] = "PA"; $v[] = "Pagada";
			break;
		
		case "ESTADO-ORDEN-PAGO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "GE"; $v[] = "Generada";
			$c[] = "PA"; $v[] = "Pagada";
			$c[] = "AN"; $v[] = "Anulada";
			break;
		
		case "ESTADO-ORDEN-PAGO-PREPAGO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "PP"; $v[] = "Pago Parcial";
			break;
			
		case "ORDENAR-ORDEN-PAGO":
			$c[] = "NroOrden"; $v[] = "Nro. Orden";
			$c[] = "NomProveedorPagar, CodTipoDocumento, NroDocumento"; $v[] = "Proveedor/Documento";
			$c[] = "MontoTotal"; $v[] = "Monto a Pagar";
			$c[] = "FechaProgramada"; $v[] = "Fecha Prog. Pago";
			break;
			
		case "ESTADO-PAGO":
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "IM"; $v[] = "Impreso";
			break;
			
		case "ESTADO-PAGO2":
			$c[] = "IM"; $v[] = "Impreso";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "ORDENAR-LIBRO-CHEQUE":
			$c[] = "pa.NroPago"; $v[] = "Nro. Cheque";
			$c[] = "pa.FechaPago"; $v[] = "Fecha de Pago";
			$c[] = "pa.VoucherPago"; $v[] = "Voucher de Pago";
			break;
			
		case "ESTADO-ENTREGA-CHEQUE":
			$c[] = "C"; $v[] = "Custodia";
			$c[] = "E"; $v[] = "Entregado";
			break;
			
		case "ESTADO-TRANSACCION-BANCARIA":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Actualizada";
			$c[] = "CO"; $v[] = "Contabilizada";
			break;
			
		case "SISTEMA-FUENTE-REGISTRO-COMPRA":
			$c[] = "CP"; $v[] = "Cuentas x Pagar";
			$c[] = "CC"; $v[] = "Caja Chica";
			break;
			
		case "ORDENAR-REGISTRO-COMPRA":
			$c[] = "Fecha"; $v[] = "Fecha";
			$c[] = "Proveedor"; $v[] = "Nombre o Razón o Social";
			break;
			
		case "CLASIFICACION-CXP":
			$c[] = "O"; $v[] = "Obligaciones";
			$c[] = "C"; $v[] = "Otros de Ctas. Por Pagar";
			$c[] = "P"; $v[] = "Préstamos";
			$c[] = "E"; $v[] = "Otros Externos";
			break;
			
		case "ESTADO-CAJACHICA":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "IMPUESTO-PROVISION":
			$c[] = "N"; $v[] = "Provisión del Documento";
			$c[] = "P"; $v[] = "Pago del Documento";
			break;
			
		case "IMPUESTO-IMPONIBLE":
			$c[] = "N"; $v[] = "Monto Afecto";
			$c[] = "B"; $v[] = "Monto Bruto";
			$c[] = "I"; $v[] = "IGV/IVA";
			$c[] = "T"; $v[] = "Monto Total";
			break;
			
		case "IMPUESTO-COMPROBANTE":
			$c[] = "IVA"; $v[] = "IVA";
			$c[] = "ISLR"; $v[] = "ISLR";
			$c[] = "1X1000"; $v[] = "1X1000";
			$c[] = "OTRO"; $v[] = "OTRO";
			break;
			
		case "TIPO-TRANSACCION-BANCARIA":
			$c[] = "I"; $v[] = "Ingreso";
			$c[] = "E"; $v[] = "Egreso";
			$c[] = "T"; $v[] = "Transacción";
			break;
		
		case "ESTADO-CAUSADO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Causado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
		
		case "ESTADO-PAGADO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "PA"; $v[] = "Pagado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
		
		case "ESTADO-CHEQUE":
			$c[] = "C"; $v[] = "Custodia";
			$c[] = "E"; $v[] = "Entregado";
			break;
		
		case "ESTADO-CHEQUE-COBRO":
			$c[] = "S"; $v[] = "Cobrado";
			$c[] = "N"; $v[] = "Pendiente";
			break;
		
		case "ESTADO-VIATICOS":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "ESTADO-REQUERIMIENTO-CAJACHICA":
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AP/CO"; $v[] = "Aprobado / Completado";
			break;
			
		case "estado-retencion":
			$c[] = "PA"; $v[] = "Pagado";
			$c[] = "EN"; $v[] = "Enterado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "estado-retencion-filtro":
			$c[] = "PA"; $v[] = "Pagado";
			$c[] = "EN"; $v[] = "Enterado";
			$c[] = "PA/EN"; $v[] = "Pagado/Enterado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "ordenes-pago":
			$c[] = "P"; $v[] = "Presupuestarias";
			$c[] = "F"; $v[] = "Financieras";
			break;
			
		case "certificaciones-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "certificaciones-tipo":
			$c[] = "AP"; $v[] = "Aporte";
			$c[] = "SV"; $v[] = "Servicios";
			$c[] = "PS"; $v[] = "Personal";
			break;
			
		case "certificaciones-tipo":
			$c[] = "AP"; $v[] = "Aporte";
			$c[] = "SV"; $v[] = "Servicios";
			$c[] = "PS"; $v[] = "Personal";
			break;
			
		case "adelanto-tipo":
			$c[] = "P"; $v[] = "Proveedor";
			$c[] = "C"; $v[] = "Contratista";
			break;
			
		case "compromiso-tipo":
			$c[] = "OC"; $v[] = "Orden de Compra";
			$c[] = "OS"; $v[] = "Orden de Servicio";
			break;
			
		case "adelanto-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "PA"; $v[] = "Pagado";
			$c[] = "AC"; $v[] = "Aplicado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "gastos-aplicacion":
			$c[] = "CC"; $v[] = "Caja Chica";
			$c[] = "RG"; $v[] = "Reporte Gastos";
			$c[] = "AP"; $v[] = "Adelanto a Proveedores";
			break;
	}
	
	$i = 0;
	switch ($opt) {
		case 0:
			foreach ($c as $cod) {
				if ($cod == $codigo) echo "<option value='".$cod."' selected>".$v[$i]."</option>";
				else echo "<option value='".$cod."'>".$v[$i]."</option>";
				$i++;
			}
			break;
			
		case 1:
			foreach ($c as $cod) {
				if ($cod == $codigo) echo "<option value='".$cod."' selected>".$v[$i]."</option>";
				$i++;
			}
			break;
	}
}

//	FUNCION PARA IMPRIMIR EN UNA TABLA VALORES
function printValores($tabla, $codigo) {
	switch ($tabla) {
		case "ESTADO-BANCARIO":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Actualizado";
			$c[] = "CO"; $v[] = "Contabilizado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "ESTADO-OBLIGACIONES":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "PA"; $v[] = "Pagada";
			break;
		
		case "ESTADO-ORDEN-PAGO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "GE"; $v[] = "Generada";
			$c[] = "PA"; $v[] = "Pagada";
			$c[] = "AN"; $v[] = "Anulada";
			break;
		
		case "ESTADO-ORDEN-PAGO-PREPAGO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "PP"; $v[] = "Pago Parcial";
			break;
			
		case "ESTADO-PAGO":
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "IM"; $v[] = "Impreso";
			$c[] = "AN"; $v[] = "Anulado";
			break;
		
		case "ESTADO-CHEQUE":
			$c[] = "C"; $v[] = "Custodia";
			$c[] = "E"; $v[] = "Entregado";
			break;
		
		case "ESTADO-CHEQUE-COBRO":
			$c[] = "S"; $v[] = "Cobrado";
			$c[] = "N"; $v[] = "Pendiente";
			break;
		
		case "ORIGEN-PAGO":
			$c[] = "A"; $v[] = "Automatico";
			break;
			
		case "SISTEMA-FUENTE-REGISTRO-COMPRA":
			$c[] = "CP"; $v[] = "Cuentas x Pagar";
			$c[] = "CC"; $v[] = "Caja Chica";
			break;
			
		case "CLASIFICACION-CXP":
			$c[] = "O"; $v[] = "Obligaciones";
			$c[] = "C"; $v[] = "Otros de Ctas. Por Pagar";
			$c[] = "P"; $v[] = "Préstamos";
			$c[] = "E"; $v[] = "Otros Externos";
			break;
			
		case "ESTADO-CAJACHICA":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "IMPUESTO-PROVISION":
			$c[] = "N"; $v[] = "Provisión del Documento";
			$c[] = "P"; $v[] = "Pago del Documento";
			break;
			
		case "IMPUESTO-IMPONIBLE":
			$c[] = "N"; $v[] = "Monto Afecto";
			$c[] = "B"; $v[] = "Monto Bruto";
			$c[] = "I"; $v[] = "IGV/IVA";
			$c[] = "T"; $v[] = "Monto Total";
			break;
			
		case "IMPUESTO-COMPROBANTE":
			$c[] = "IVA"; $v[] = "IVA";
			$c[] = "ISLR"; $v[] = "ISLR";
			$c[] = "1X1000"; $v[] = "1X1000";
			$c[] = "OTRO"; $v[] = "OTRO";
			
		case "TIPO-TRANSACCION-BANCARIA":
			$c[] = "I"; $v[] = "Ingreso";
			$c[] = "E"; $v[] = "Egreso";
			$c[] = "T"; $v[] = "Transacción";
			break;
			
		case "ESTADO-TRANSACCION-BANCARIA":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Actualizada";
			$c[] = "CO"; $v[] = "Contabilizada";
			break;
		
		case "ESTADO-CAUSADO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Causado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
		
		case "ESTADO-PAGADO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "PA"; $v[] = "Pagado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
		
		case "ESTADO-VIATICOS":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "estado-retencion":
			$c[] = "PA"; $v[] = "Pagado";
			$c[] = "EN"; $v[] = "Enterado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "certificaciones-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "certificaciones-tipo":
			$c[] = "AP"; $v[] = "Aporte";
			$c[] = "SV"; $v[] = "Servicios";
			$c[] = "PS"; $v[] = "Personal";
			break;
			
		case "adelanto-tipo":
			$c[] = "P"; $v[] = "Proveedor";
			$c[] = "C"; $v[] = "Contratista";
			break;
			
		case "compromiso-tipo":
			$c[] = "OC"; $v[] = "Orden de Compra";
			$c[] = "OS"; $v[] = "Orden de Servicio";
			break;
			
		case "adelanto-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "PA"; $v[] = "Pagado";
			$c[] = "AC"; $v[] = "Aplicado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "gastos-aplicacion":
			$c[] = "CC"; $v[] = "Caja Chica";
			$c[] = "RG"; $v[] = "Reporte Gastos";
			$c[] = "AP"; $v[] = "Adelanto a Proveedores";
			break;
	}
	
	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectTipoDocumentoObligacion($codigo, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT CodTipoDocumento, Descripcion FROM ap_tipodocumento WHERE Clasificacion = 'O' AND Estado = 'A' ORDER BY CodTipoDocumento";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT CodTipoDocumento, Descripcion FROM ap_tipodocumento WHERE Clasificacion = 'O' AND Estado = 'A' AND CodTipoDocumento = '$codigo'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
			
		case 10:
			$sql = "SELECT CodTipoDocumento, Descripcion FROM ap_tipodocumento WHERE Clasificacion = 'O' AND Estado = 'A' ORDER BY CodTipoDocumento";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php }
			}
			break;
			
		case 11:
			$sql = "SELECT CodTipoDocumento, Descripcion FROM ap_tipodocumento WHERE Clasificacion = 'O' AND Estado = 'A' AND CodTipoDocumento = '$codigo'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php
			}
			break;
	}
}

//	OBTENGO EL ULTIMO NUMERO DE PAGO PARA LA CUENTA BANCARIA
function getNroOrdenPago($CodTipoPago, $NroCuenta) {
	$sql = "SELECT UltimoNumero
			FROM ap_ctabancariatipopago
			WHERE
				CodTipoPago = '".$CodTipoPago."' AND
				NroCuenta = '".$NroCuenta."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	$codigo = (int) ($field['UltimoNumero'] + 1);
	$codigo = (string) str_repeat("0", 10-strlen($codigo)).$codigo;
	return $codigo;
}

//	devuelve el nro de cuenta bancaria por default del tipo de pago
function ctabancariadefault($CodOrganismo, $CodTipoPago) {
	$sql = "SELECT NroCuenta
			FROM ap_ctabancariadefault
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND 
				CodTipoPago = '".$CodTipoPago."'";
	return getVar3($sql);
}
?>