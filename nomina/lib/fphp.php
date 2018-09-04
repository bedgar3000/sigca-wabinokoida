<?php
//	FUNCION PARA CARGAR SELECTS 
function loadSelectValores($tabla, $codigo, $opt) {
	switch ($tabla) {			
		case "ORDENAR-CARRERAS":
			$c[] = "e.CodEmpleado"; $v[] = "Empleado";
			$c[] = "e.CodDependencia"; $v[] = "Dependencia";
			$c[] = "e.Fingreso"; $v[] = "Fecha de Ingreso";
			$c[] = "pu1.DescripCargo, pu2.DescripCargo"; $v[] = "Cargo";
			break;
		
		case "ESTADO-CARRERAS":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			break;

		case "ORDENAR-EVALUACION":
			$c[] = "e1.CodEmpleado"; $v[] = "Empleado";
			$c[] = "p1.NomCompleto"; $v[] = "Nombre";
			$c[] = "pu1.DescripCargo"; $v[] = "Cargo";
			$c[] = "ee.Periodo"; $v[] = "Periodo";
			$c[] = "ee.Estado"; $v[] = "Estado";
			break;

		case "ESTADO-EVALUACION":
			$c[] = "EE"; $v[] = "En Evaluación";
			$c[] = "EV"; $v[] = "Evaluado";
			break;

		case "TIPO-VACACIONES":
			$c[] = "G"; $v[] = "Goce";
			$c[] = "I"; $v[] = "Interrupcion";
			break;

		case "ESTADO-VACACIONES":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "CONCEPTO-TIPO":
			$c[] = "I"; $v[] = "Ingresos";
			$c[] = "D"; $v[] = "Descuentos";
			$c[] = "A"; $v[] = "Aportes";
			$c[] = "P"; $v[] = "Provisiones";
			$c[] = "T"; $v[] = "Totales";
			break;

		case "ESTADO-AJUSTE":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "ESTADO-PRESTACIONES":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "PA"; $v[] = "Pagado";
			break;

		case "proyeccion-tipo":
			$c[] = "NF"; $v[] = "Nómina Actual";
			$c[] = "NI"; $v[] = "Nuevo Ingreso";
			$c[] = "VA"; $v[] = "Vacante";
			break;

		case "proypresupuestaria-estado":
			$c[] = "AP"; $v[] = "En Preparación";
			$c[] = "GE"; $v[] = "Generado";
			break;

		case "TIPOJORHOR":
			$c[] = "HD"; $v[] = "HD";
			$c[] = "HN"; $v[] = "HN";
			break;

		case "TIPOJORDIA":
			$c[] = "DD"; $v[] = "DD";
			$c[] = "DF"; $v[] = "DF";
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
		case "ESTADO-EVALUACION":
			$c[] = "EE"; $v[] = "En Evaluación";
			$c[] = "EV"; $v[] = "Evaluado";
			break;

		case "TIPO-VACACIONES":
			$c[] = "G"; $v[] = "Goce";
			$c[] = "I"; $v[] = "Interrupcion";
			break;

		case "ESTADO-VACACIONES":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "CONCEPTO-TIPO":
			$c[] = "I"; $v[] = "Ingresos";
			$c[] = "D"; $v[] = "Descuentos";
			$c[] = "A"; $v[] = "Aportes";
			$c[] = "P"; $v[] = "Provisiones";
			$c[] = "T"; $v[] = "Totales";
			break;

		case "ESTADO-AJUSTE":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "proyeccion-tipo":
			$c[] = "NF"; $v[] = "Nómina Actual";
			$c[] = "NI"; $v[] = "Nuevo Ingreso";
			$c[] = "VA"; $v[] = "Vacante";
			break;

		case "proypresupuestaria-estado":
			$c[] = "AP"; $v[] = "En Preparación";
			$c[] = "GE"; $v[] = "Generado";
			break;
	}
	
	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}

// obtiene la cuenta bancaria por defecto de un tipo de pago para un organismo
function getCuentaBancariaDefault($CodOrganismo, $CodTipoPago) {
	$sql = "SELECT NroCuenta
			FROM ap_ctabancariadefault 
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodTipoPago = '".$CodTipoPago."'";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field['NroCuenta'];
}

//	imprimir estado del proceso
function printEstadoProceso($Estado, $FlagProcesado, $FlagPagado) {
	if ($Estado == "A") {
		if ($FlagPagado == "S") $src = "imagenes/check_red.png";
		else {
			if ($FlagProcesado == "N") $src = "imagenes/reloj.png";
			else $src = "imagenes/check.png";
		}
	} else $src = "imagenes/inactivo.png";
	$img = "<img src='$src' width='16' height='16' />";
	return $img;
}

//	imprimir estado del proceso
function printProcesoAprobado($FlagAprobado) {
	if ($FlagAprobado == "S") $src = "imagenes/bandera.png";
	else $src = "imagenes/menos.png";
	$img = "<img src='$src' width='16' height='16' />";
	return $img;
}

//	obtiene 
function getFirmaNomina($nomina, $periodo, $proceso, $campo) {
	$sql = "SELECT
				mp.Busqueda,
				p.DescripCargo,
				mp.Sexo
			FROM
				pr_procesoperiodo pp
				INNER JOIN mastpersonas mp ON (pp.$campo = mp.CodPersona)
				INNER JOIN mastempleado me ON (mp.CodPersona = me.CodPersona)
				INNER JOIN rh_puestos p ON (me.CodCargo = p.CodCargo)
			WHERE
				pp.CodTipoNom = '".$nomina."' AND 
				pp.Periodo = '".$periodo."' AND 
				pp.CodTipoproceso = '".$proceso."'";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	
	if ($field['Sexo'] == "M") {
		$denominacion_cargo = $field['DescripCargo'];
		$cargo = str_replace("JEFE (A)", "JEFE", $denominacion_cargo);
		$cargo = str_replace("DIRECTOR (A)", "DIRECTOR", $denominacion_cargo);
	} else {
		$denominacion_cargo = $field['DescripCargo'];
		$cargo = str_replace("JEFE (A)", "JEFA", $denominacion_cargo);
		$cargo = str_replace("DIRECTOR (A)", "DIRECTORA", $denominacion_cargo);
	}
	
	return array($field['Busqueda'], $cargo);
}

//	
function avisoPrestacionesIntereses() {
	global $_PARAMETRO;
	global $Ahora;
	if ($_PARAMETRO['ACTPRESTINT'] == "S") {
		list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
		$FechaActual = "$AnioActual-$MesActual-$DiaActual";
		$PeriodoActual = "$AnioActual-$MesActual";
		$CodTipoProceso = "PRS";
		$CodConcepto = "0110";
		$Personas = array();
		##	consulto los empleados
		$sql = "SELECT
					le.*,
					e.CodEmpleado,
					e.Fegreso,
					p.NomCompleto AS NomPersona,
					p.Ndocumento
				FROM
					pr_liquidacionempleado le
					INNER JOIN mastpersonas p ON (p.CodPersona = le.CodPersona)
					INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				WHERE le.EstadoPago = 'PE'";
		$field = getRecords($sql);
		foreach($field as $f) {
			$CodPersona = $f['CodPersona'];
			##	
			list($AnioEgreso, $MesEgreso, $DiaEgreso) = explode("-", $f['Fegreso']);
			$AnioInicial = intval($AnioEgreso);
			$MesInicial = intval($MesEgreso) + 1;
			if ($MesInicial > 12) {
				++$AnioInicial;
				$MesInicial = "01";
			}
			elseif ($MesInicial < 10) $MesInicial = "0".$MesInicial;
			$PeriodoInicial = $AnioInicial."-".$MesInicial;
			##	
			$sql = "SELECT
						ti.Periodo,
						ti.Porcentaje,
						tnec.Periodo AS PeriodoNomina,
						tnec.Cantidad,
						tnec.Monto
					FROM
						masttasainteres ti
						LEFT JOIN pr_tiponominaempleadoconcepto tnec ON (tnec.Periodo = ti.Periodo AND
																		 tnec.CodTipoNom = '".$f['CodTipoNom']."' AND
																		 tnec.CodPersona = '".$f['CodPersona']."' AND
																		 tnec.CodOrganismo = '".$f['CodOrganismo']."' AND
																		 tnec.CodTipoProceso = '".$CodTipoProceso."' AND
																		 tnec.CodConcepto = '".$CodConcepto."')
					WHERE ti.Periodo >= '".$PeriodoInicial."'
					ORDER BY Periodo DESC";
			$field_periodos = getRecords($sql);
			foreach($field_periodos as $fp) {
				list($AnioPeriodo, $MesPeriodo) = explode("-", $fp['Periodo']);
				$DiasAnio = getFechaDias("01-01-$AnioPeriodo", "31-12-$AnioPeriodo");
				$Monto = $f['TotalNeto'] * $fp['Porcentaje'] / 100 * 30 / $DiasAnio;
				if ($fp['PeriodoNomina'] == "" || $fp['Porcentaje'] != $fp['Cantidad']) {
					$Personas[] = $f['CodPersona']." ".$F['NomPersona'];
				}
			}
		}
		##	si se encontro empleados pendientes
		if (count($Personas) > 0) {
			?>
            <script type="text/javascript" language="javascript">
			$(document).ready(function() {
				$("#cajaModal").dialog({
					buttons: {
						"Si": function() {
							$(this).dialog("close");
							$.ajax({
								type: "POST",
								url: "lib/form_ajax.php",
								data: "modulo=prestaciones_control&accion=intereses-actualizacion-masiva",
								async: false,
								success: function(resp) {
									if (resp.trim() != "") cajaModal(resp, "error", 400);
									else cajaModal("Se actualizaron los intereses correctamente", "exito", 400);
								}
							});
						},
						"No": function() {
							$(this).dialog("close");
						}
					}
				});
				cajaModalConfirm("Se encontraron prestaciones sociales con intereses pendientes.<br/ >¿Actualizar ahora?", 400);
			});
			</script>
            <?php
		}
	}
}

//	
function getPeriodosTasas($Anio) {
	$sql = "SELECT SUBSTRING(Periodo, 1, 4) AS Anio FROM masttasainteres WHERE Estado = 'A' GROUP BY Anio ORDER BY Anio DESC";
	$field = getRecords($sql);
	foreach ($field as $f) {
		if ($f[0] == $Anio) { ?><option value="<?=$f[0]?>" selected="selected"><?=$f[0]?></option><?php }
		else { ?><option value="<?=$f[0]?>"><?=$f[0]?></option><?php }
	}
}

function loadSelectConceptos($CodConcepto=NULL, $Tipo=NULL, $opt=0) {
	$filtro = "";
	$Grupo = "";
	$i = 0;
	if ($Tipo) $filtro .= " AND Tipo = '".$Tipo."'";
	switch ($opt) {
		case 0:
			$sql = "SELECT CodConcepto, Descripcion, Tipo FROM pr_concepto WHERE Estado = 'A' $filtro ORDER BY Tipo, Descripcion";
			$field = getRecords($sql);
			foreach($field as $f) {
				if ($Grupo != $f['Tipo']) {
					$Grupo = $f['Tipo'];
					if ($i) ?></optgroup><?php
					?><optgroup label="<?=printValores('CONCEPTO-TIPO', $f['Tipo'])?>"><?php
				}
				?><option value="<?=$f['CodConcepto']?>"><?=htmlentities($f['Descripcion'])?></option><?php
				++$i;
			}
			if (count($field)) ?></optgroup><?php
			break;
	}
}
?>