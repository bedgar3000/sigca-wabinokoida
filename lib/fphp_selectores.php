<?php
function loadSelect($tabla, $campo1, $campo2, $codigo=NULL, $opt=0) {
	switch ($opt) {
		case 0:		
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE Estado = 'A' ORDER BY $campo2";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE Estado = 'A' AND $campo1 = '$codigo'";			
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php
			}
			break;
			
		case 10:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE Estado = 'A' ORDER BY $campo1";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 11:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE Estado = 'A' AND $campo1 = '$codigo'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=htmlentities($field[1])?></option><?php
			}
			break;
	}
}

function loadSelect2($tabla, $campo1, $campo2, $codigo=NULL, $opt=0, $campos=NULL, $valores=NULL, $campo3=NULL, $pos = 0) {
	$filtro = "";
	if ($campos) {
		for($i=0;$i<count($campos);$i++) {
			$filtro .= " AND $campos[$i] = '$valores[$i]'";
		}
	}
	if ($campo3) { $c3 = ", $campo3"; $order = "$campo3"; } else { $c3 = ""; $order = "$campo2"; }
	switch ($opt) {
		case 0:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE 1 $filtro ORDER BY $order";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=(($campo3 && $pos)?"$field[2] - ":"")?><?=htmlentities($field[1])?><?=(($campo3 && !$pos)?" - $field[2]":"")?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=(($campo3 && $pos)?"$field[2] - ":"")?><?=htmlentities($field[1])?><?=(($campo3 && !$pos)?" - $field[2]":"")?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE $campo1 = '$codigo' $filtro";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=(($campo3 && $pos)?"$field[2] - ":"")?><?=htmlentities($field[1])?><?=(($campo3 && !$pos)?" - $field[2]":"")?></option><?php
			}
			break;
			
		case 10:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE 1 $filtro ORDER BY $campo1";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 11:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE $campo1 = '$codigo' $filtro";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php
			}
			break;
			
		case 20:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE 1 $filtro ORDER BY $campo1";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
			}
			break;

		case 30:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE 1 $filtro GROUP BY $campo1 ORDER BY $order";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=(($campo3 && $pos)?"$field[2] - ":"")?><?=htmlentities($field[1])?><?=(($campo3 && !$pos)?" - $field[2]":"")?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=(($campo3 && $pos)?"$field[2] - ":"")?><?=htmlentities($field[1])?><?=(($campo3 && !$pos)?" - $field[2]":"")?></option><?php }
			}
			break;
			
	}
}

//	FUNCION PARA CARGAR EL MISCELANEO EN UN SELECT
function getMiscelaneos($detalle, $maestro, $opt=0) {
	switch ($opt) {
		case 0:
			$sql = "SELECT CodDetalle, Descripcion
					FROM mastmiscelaneosdet
					WHERE
						Estado = 'A' AND
						CodMaestro = '".$maestro."'
					ORDER BY CodDetalle";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $detalle) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT CodDetalle, Descripcion
					FROM mastmiscelaneosdet
					WHERE
						CodDetalle = '".$detalle."' AND
						CodMaestro = '".$maestro."'
					ORDER BY CodDetalle";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
			
		case 10:
			$sql = "SELECT CodDetalle, Descripcion
					FROM mastmiscelaneosdet
					WHERE
						Estado = 'A' AND
						CodMaestro = '".$maestro."'
					ORDER BY CodDetalle";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $detalle) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php }
			}
			break;
			
		case 11:
			$sql = "SELECT CodDetalle, Descripcion
					FROM mastmiscelaneosdet
					WHERE
						CodDetalle = '".$detalle."' AND
						CodMaestro = '".$maestro."'
					ORDER BY CodDetalle";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php
			}
			break;
			
		case 20:
			$sql = "SELECT CodDetalle, Descripcion
					FROM mastmiscelaneosdet
					WHERE
						Estado = 'A' AND
						CodMaestro = '".$maestro."'
					ORDER BY CodDetalle";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $detalle) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS 
function loadSelectGeneral($tabla, $codigo, $opt=0) {
	switch ($tabla) {
		case "ESTADO":
			$c[] = "A"; $v[] = "Activo";
			$c[] = "I"; $v[] = "Inactivo";
			break;
			
		case "ORDENAR-PERSONA":
			$c[] = "p.CodPersona"; $v[] = "C&oacute;digo";
			$c[] = "p.NomCompleto"; $v[] = "Nombre Completo";
			$c[] = "p.Ndocumento"; $v[] = "Nro. Documento";
			$c[] = "p.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-CCOSTO":
			$c[] = "cc.CodCentroCosto"; $v[] = "C&oacute;digo";
			$c[] = "cc.Descripcion"; $v[] = "Descripci&oacute;n";
			$c[] = "gcc.Descripcion"; $v[] = "Grupo";
			$c[] = "sgcc.Descripcion"; $v[] = "Sub-Grupo";
			$c[] = "cc.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-PARAMETRO":
			$c[] = "p.ParametroClave"; $v[] = "C&oacute;digo";
			$c[] = "p.DescripcionParam"; $v[] = "Descripci&oacute;n";
			$c[] = "p.TipoValor"; $v[] = "Tipo";
			$c[] = "p.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-APLICACION":
			$c[] = "a.CodAplicacion"; $v[] = "C&oacute;digo";
			$c[] = "a.Descripcion"; $v[] = "Descripci&oacute;n";
			$c[] = "a.PeriodoContable"; $v[] = "Periodo Contable";
			$c[] = "a.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-ORGANISMO":
			$c[] = "o.CodOrganismo"; $v[] = "C&oacute;digo";
			$c[] = "o.Descripcion"; $v[] = "Descripci&oacute;n";
			$c[] = "o.CodPersona"; $v[] = "Tipo";
			$c[] = "o.RepresentLegal"; $v[] = "Representante Legal";
			$c[] = "o.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-DEPENDENCIA":
			$c[] = "d.Estructura"; $v[] = "C&oacute;digo";
			$c[] = "d.Dependencia"; $v[] = "Descripci&oacute;n";
			$c[] = "d.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-PLANCUENTAS":
			$c[] = "pc.CodCuenta"; $v[] = "C&oacute;digo";
			$c[] = "pc.Descripcion"; $v[] = "Descripci&oacute;n";
			$c[] = "pc.TipoCuenta"; $v[] = "Tipo de Cuenta";
			$c[] = "pc.TipoSaldo"; $v[] = "Naturaleza";
			$c[] = "pc.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-GRUPOCC":
			$c[] = "gcc.CodGrupoCentroCosto"; $v[] = "C&oacute;digo";
			$c[] = "gcc.Descripcion"; $v[] = "Descripci&oacute;n";
			$c[] = "gcc.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-CENTROCOSTO":
			$c[] = "cc.CodCentroCosto"; $v[] = "C&oacute;digo";
			$c[] = "cc.Descripcion"; $v[] = "Descripci&oacute;n";
			$c[] = "cc.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-TIPOCUENTA":
			$c[] = "tc.cod_tipocuenta"; $v[] = "C&oacute;digo";
			$c[] = "tc.descp_tipocuenta"; $v[] = "Descripci&oacute;n";
			break;
			
		case "ORDENAR-CLASIFICADOR":
			$c[] = "p.cod_partida"; $v[] = "C&oacute;digo";
			$c[] = "p.denominacion"; $v[] = "Descripci&oacute;n";
			break;
			
		case "ORDENAR-ORGANISMO-EXTERNO":
			$c[] = "CodOrganismo"; $v[] = "C&oacute;digo";
			$c[] = "Organismo"; $v[] = "Descripci&oacute;n";
			$c[] = "Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-DEPENDENCIA-EXTERNA":
			$c[] = "CodDependencia"; $v[] = "C&oacute;digo";
			$c[] = "Dependencia"; $v[] = "Descripci&oacute;n";
			$c[] = "Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-ENTE":
			$c[] = "CodDependencia"; $v[] = "C&oacute;digo";
			$c[] = "Dependencia"; $v[] = "Descripci&oacute;n";
			$c[] = "Estado"; $v[] = "Estado";
			break;
			
		case "BUSCAR-ITEMS":
			$c[] = "i.CodItem"; $v[] = "Código";
			$c[] = "i.Descripcion"; $v[] = "Descripción";
			$c[] = "i.CodLinea"; $v[] = "Linea";
			$c[] = "i.CodFamilia"; $v[] = "Familia";
			$c[] = "i.CodSubFamilia"; $v[] = "Sub-Familia";
			$c[] = "i.CodInterno"; $v[] = "Cod. Interno";
			break;
			
		case "BUSCAR-EMPLEADOS":
			$c[] = "mp.Apellido1"; $v[] = "Apellido Paterno";
			$c[] = "mp.Apellido2"; $v[] = "Apellido Materno";
			$c[] = "mp.Nombres"; $v[] = "Nombre";
			$c[] = "mp.Busqueda"; $v[] = "Nombre Búsqueda";
			$c[] = "mp.NomCompleto"; $v[] = "Nombre Completo";
			break;
			
		case "ORDENAR-EMPLEADOS":
			$c[] = "e.CodEmpleado"; $v[] = "Código";
			$c[] = "p.NomCompleto"; $v[] = "Nombre Completo";
			$c[] = "p.Ndocumento"; $v[] = "Nro. Documento";
			$c[] = "e.Fingreso"; $v[] = "Fecha de Ingreso";
			$c[] = "d.Dependencia"; $v[] = "Dependencia";
			break;
			
		case "COMPARATIVOS":
			$c[] = "="; $v[] = "=";
			$c[] = "&lt;"; $v[] = "&lt;";
			$c[] = "&gt;"; $v[] = "&gt;";
			$c[] = "&lt;="; $v[] = "&lt;=";
			$c[] = "&gt;="; $v[] = "&gt;=";
			$c[] = "&lt;&gt;"; $v[] = "&lt;&gt;";
			break;
			
		case "SEXO":
			$c[] = "M"; $v[] = "MASCULINO";
			$c[] = "F"; $v[] = "FEMENINO";
			break;
			
		case "MAXLIMIT":
			$c[] = "10"; $v[] = "10";
			$c[] = "25"; $v[] = "25";
			$c[] = "50"; $v[] = "50";
			$c[] = "100"; $v[] = "100";
			$c[] = "1000"; $v[] = "1000";
			break;
			
		case "TIPO-SALDO":
			$c[] = "D"; $v[] = "Deudora";
			$c[] = "A"; $v[] = "Acreedora";
			break;
			
		case "NIVEL-CUENTA":
			$c[] = "1"; $v[] = "Grupo";
			$c[] = "2"; $v[] = "Sub-Grupo";
			$c[] = "3"; $v[] = "Rubro";
			$c[] = "4"; $v[] = "Cuenta";
			$c[] = "5"; $v[] = "Sub-Cuenta de Primer Orden";
			$c[] = "6"; $v[] = "Sub-Cuenta de Segundo Orden";
			$c[] = "7"; $v[] = "Sub-Cuenta Anexa";
			break;
			
		case "ORDENAR-PAISES":
			$c[] = "p.CodPais"; $v[] = "C&oacute;digo";
			$c[] = "p.Pais"; $v[] = "Descripci&oacute;n";
			$c[] = "p.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-ESTADOS":
			$c[] = "e.CodEstado"; $v[] = "C&oacute;digo";
			$c[] = "e.Estado"; $v[] = "Descripci&oacute;n";
			$c[] = "e.Status"; $v[] = "Estado";
			break;
			
		case "ORDENAR-MUNICIPIOS":
			$c[] = "m.CodMunicipio"; $v[] = "C&oacute;digo";
			$c[] = "m.Municipio"; $v[] = "Descripci&oacute;n";
			$c[] = "m.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-CIUDADES":
			$c[] = "c.CodCiudad"; $v[] = "C&oacute;digo";
			$c[] = "c.Ciudad"; $v[] = "Descripci&oacute;n";
			$c[] = "c.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-TIPOPAGO":
			$c[] = "tp.CodTipoPago"; $v[] = "C&oacute;digo";
			$c[] = "tp.TipoPago"; $v[] = "Descripci&oacute;n";
			$c[] = "tp.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-BANCO":
			$c[] = "b.CodBanco"; $v[] = "C&oacute;digo";
			$c[] = "b.Banco"; $v[] = "Descripci&oacute;n";
			$c[] = "b.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-USUARIO":
			$c[] = "u.Usuario"; $v[] = "Usuario";
			$c[] = "p.NomCompleto"; $v[] = "Persona";
			$c[] = "u.FechaExpirar"; $v[] = "Fecha Expira";
			$c[] = "u.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-MISCELANEO":
			$c[] = "mm.CodMaestro"; $v[] = "Maestro";
			$c[] = "mm.Descripcion"; $v[] = "Descripci&oacute;n";
			$c[] = "u.Estado"; $v[] = "Estado";
			break;
			
		case "ORDENAR-IMPUESTO":
			$c[] = "i.CodImpuesto"; $v[] = "Código";
			$c[] = "i.Descripcion"; $v[] = "Descripción";
			$c[] = "i.Estado"; $v[] = "Estado";
			break;
			
		case "ESTADO-DOCUMENTOS":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "RV"; $v[] = "Facturado";
			break;
			
		case "PRIORIDAD":
			$c[] = "N"; $v[] = "Normal";
			$c[] = "U"; $v[] = "Urgente";
			$c[] = "M"; $v[] = "Muy Urgente";
			break;
			
		case "ORDENAR-CLASIFICACION-ACTIVO":
			$c[] = "CodClasificacion"; $v[] = "Código";
			$c[] = "Descripcion"; $v[] = "Descripción";
			break;
			
		case "ORDENAR-LINEAS":
			$c[] = "CodLinea"; $v[] = "Código";
			$c[] = "Descripcion"; $v[] = "Descripción";
			break;
		
		case "ORDENAR-ITEMS":
			$c[] = "CodItem"; $v[] = "Item";
			$c[] = "CodInterno"; $v[] = "Código";
			$c[] = "Descripcion"; $v[] = "Descripción";
			$c[] = "CodUnidad"; $v[] = "Unidad";
			$c[] = "CodLinea, CodFamilia, CodSubFamilia"; $v[] = "CodLinea/CodFamilia/CodSubFamilia";
			$c[] = "Estado"; $v[] = "Estado";
			break;
		
		case "DIRIGIDO":
			$c[] = "C"; $v[] = "Compras";
			$c[] = "A"; $v[] = "Almacen";
			break;
			
		case "ESTADO-COMPRA":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "CO"; $v[] = "Completada";
			break;
			
		case "ESTADO-COMPRA-DETALLE":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "CO"; $v[] = "Completada";
			break;
		
		case "ESTADO-SERVICIO":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "CE"; $v[] = "Cerrado";
			$c[] = "CO"; $v[] = "Completado";
			break;
		
		case "ESTADO-SERVICIO-DETALLE":
			$c[] = "N"; $v[] = "Pendiente";
			$c[] = "S"; $v[] = "Completado";
			break;
		
		case "TIPO-MOVIMIENTO-TRANSACCION":
			$c[] = "I"; $v[] = "Ingreso";
			$c[] = "E"; $v[] = "Egreso";
			$c[] = "T"; $v[] = "Transferencia";
			break;
		
		case "FLAG":
			$c[] = "S"; $v[] = "Si";
			$c[] = "N"; $v[] = "No";
			break;

		case "ESTADO-POSTULANTE":
			$c[] = "P"; $v[] = "Postulante";
			$c[] = "A"; $v[] = "Aceptado";
			$c[] = "C"; $v[] = "Contratado";
			$c[] = "D"; $v[] = "Descalificado";
			break;

		case "DIA-SEMANA":
			$c[] = "01"; $v[] = "Lunes";
			$c[] = "02"; $v[] = "Martes";
			$c[] = "03"; $v[] = "Miercoles";
			$c[] = "04"; $v[] = "Jueves";
			$c[] = "05"; $v[] = "Viernes";
			$c[] = "06"; $v[] = "Sabado";
			$c[] = "07"; $v[] = "Domingo";
			break;

		case "PODER-PUBLICO":
			$c[] = "N"; $v[] = "Nacional";
			$c[] = "E"; $v[] = "Estadal";
			$c[] = "M"; $v[] = "Municipal";
			break;
			
		case "trimestre":
			$c[] = "I"; $v[] = "I";
			$c[] = "II"; $v[] = "II";
			$c[] = "III"; $v[] = "III";
			$c[] = "IV"; $v[] = "IV";
			break;

		case "contribuyente-estado":
			$c[] = "AC"; $v[] = "Activo";
			$c[] = "SP"; $v[] = "Suspendido";
			$c[] = "CA"; $v[] = "Cancelado";
			$c[] = "CE"; $v[] = "Cesado";
			break;

		case "HORA-12":
			$j=0;
			for($i=0;$i<12;$i++) {
				++$j;
				if ($j<10) $valor = "0$j"; else $valor = "$j";
				$c[$i] = "$valor"; $v[$i] = "$valor";
			}
			break;

		case "MINUTO":
			for($i=0;$i<60;$i++) {
				if ($i<10) $valor = "0$i"; else $valor = "$i";
				$c[$i] = "$valor"; $v[$i] = "$valor";
			}
			break;

		case "MES":
			$j=0;
			for($i=0;$i<12;$i++) {
				++$j;
				if ($j<10) $valor = "0$j"; else $valor = "$j";
				$c[$i] = "$valor"; $v[$i] = "$valor";
			}
			break;

		case "MES-NOMBRE":
			$j=0;
			for($i=0;$i<12;$i++) {
				++$j;
				if ($j<10) $valor = "0$j"; else $valor = "$j";
				$c[$i] = "$valor"; $v[$i] = getNombreMes("0000-$valor");
			}
			break;
			
		case "ESTADO-PERMISOS":
			$c[] = "P"; $v[] = "Pendiente";
			$c[] = "A"; $v[] = "Aprobado";
			$c[] = "N"; $v[] = "Anulado";
			break;

		case "CONCEPTO-TIPO":
			$c[] = "I"; $v[] = "Ingresos";
			$c[] = "D"; $v[] = "Descuentos";
			$c[] = "A"; $v[] = "Aportes";
			$c[] = "P"; $v[] = "Provisiones";
			$c[] = "T"; $v[] = "Totales";
			break;

		case "ESTADO-ACTIVO":
			$c[] = "PE"; $v[] = "Pendiente de Activar";
			$c[] = "AP"; $v[] = "Activado";
			break;
			
		case "ESTADO-ACTUACION":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "TE"; $v[] = "Terminada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			break;
			
		case "ESTADO-ACTUACION-PRORROGAS":
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "TE"; $v[] = "Terminada";
			$c[] = "AP/TE"; $v[] = "Aprobada/Terminada";
			break;
			
		case "ESTADO-VALORACION":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AC"; $v[] = "Auto de Proceder";
			$c[] = "AA"; $v[] = "Auto de Archivo";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			$c[] = "VJ"; $v[] = "Enviado VJPA";
			break;
			
		case "ESTADO-POTESTAD":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "TE"; $v[] = "Terminada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			break;
			
		case "ESTADO-DETERMINACION-VALORACION":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AC"; $v[] = "Auto de Proceder";
			$c[] = "AA"; $v[] = "Auto de Archivo";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			$c[] = "DV"; $v[] = "Devuelto";
			break;
			
		case "ESTADO-DETERMINACION":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "TE"; $v[] = "Terminada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			break;	
					
		case "ACTUALIZAR-PERSONA":
			$c[] = "Persona"; $v[] = "Persona";
			$c[] = "Empleado"; $v[] = "Empleado";
			$c[] = "Proveedor"; $v[] = "Proveedor";
			$c[] = "Cliente"; $v[] = "Cliente";
			$c[] = "Otro"; $v[] = "Otro";
			break;
			
		case "TIPO-PERSONA":
			$c[] = "N"; $v[] = "Natural";
			$c[] = "J"; $v[] = "Jurídica";
			break;
			
		case "CLASE-PERSONA":
			$c[] = "EsEmpleado"; $v[] = "Empleado";
			$c[] = "EsProveedor"; $v[] = "Proveedor";
			$c[] = "EsCliente"; $v[] = "Cliente";
			$c[] = "EsOtro"; $v[] = "Otro";
			break;
			
		case "ESTADO-OBLIGACIONES":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "PA"; $v[] = "Pagada";
			break;
			
		case "activo-naturaleza":
			$c[] = "AN"; $v[] = "Activo Normal";
			$c[] = "AM"; $v[] = "Activo Menor";
			break;

		case "presupuesto-estado":
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "forma-evaluacion-poa":
			$c[] = "C"; $v[] = "Cantidad";
			$c[] = "P"; $v[] = "Porcentual";
			break;

		case "poa-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "presupuesto-hacienda-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "plan-obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "plan-obras-tipo":
			$c[] = "PU"; $v[] = "Dominio Público";
			$c[] = "PR"; $v[] = "Dominio Privado";
			break;

		case "plan-obras-situacion":
			$c[] = "AI"; $v[] = "A Iniciar";
			$c[] = "EJ"; $v[] = "En Ejecución";
			$c[] = "TE"; $v[] = "Terminado";
			$c[] = "PA"; $v[] = "Paralizado";
			break;

		case "obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "obras-situacion":
			$c[] = "AI"; $v[] = "A Iniciar";
			$c[] = "EJ"; $v[] = "En Ejecución";
			$c[] = "TE"; $v[] = "Terminado";
			$c[] = "PA"; $v[] = "Paralizado";
			break;
			
		case "IMPUESTO-IMPONIBLE":
			$c[] = "N"; $v[] = "Monto Afecto";
			$c[] = "B"; $v[] = "Monto Bruto";
			$c[] = "I"; $v[] = "IGV/IVA";
			$c[] = "T"; $v[] = "Monto Total";
			break;
			
		case "IMPUESTO-PROVISION":
			$c[] = "N"; $v[] = "Provisión del Documento";
			$c[] = "P"; $v[] = "Pago del Documento";
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

		case "cliente-clasificacion":
			$c[] = "E"; $v[] = "Excelente";
			$c[] = "B"; $v[] = "Bueno";
			$c[] = "R"; $v[] = "Regular";
			$c[] = "M"; $v[] = "Malo";
			break;

		case "monedas":
			$c[] = "L"; $v[] = "Local";
			break;

		case "co-cotizacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			break;
			
		case "ESTADO-TRANSACCION":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Ejecutado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "servicios-digitos":
			$c[] = "2"; $v[] = "2";
			$c[] = "4"; $v[] = "4";
			$c[] = "6"; $v[] = "6";
			$c[] = "8"; $v[] = "8";
			$c[] = "10"; $v[] = "10";
			break;

		case "co-documento-pagos":
			$c[] = "PP"; $v[] = "Adelantos Pendientes de Pago";
			$c[] = "PA"; $v[] = "Pagados";
			break;

		case "co-documento1-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "co-documento1-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "co-documento2-estado":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "co-documento2-estado-detalle":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "co-documento3-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
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

function getOrganismos($organismo, $opt=0, $Usuario=NULL, $CodAplicacion=NULL) {
	if (isset($_SESSION["USUARIO_ACTUAL"]) && !$Usuario) $Usuario = $_SESSION["USUARIO_ACTUAL"];
	if (isset($_SESSION["APLICACION_ACTUAL"]) && !$CodAplicacion) $CodAplicacion = $_SESSION["APLICACION_ACTUAL"];
	if ($opt == 3 && $Usuario == $_SESSION["SUPER_USUARIO"]) $opt = 0;
	switch ($opt) {
		case 0:
			$sql="SELECT CodOrganismo, Organismo FROM mastorganismos WHERE CodOrganismo<>'' ORDER BY CodOrganismo";
			$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows=mysql_num_rows($query);
			for ($i=0; $i<$rows; $i++) {
				$field=mysql_fetch_array($query);
				if ($field[0]==$organismo) echo "<option value='".$field[0]."' selected>".($field[1])."</option>"; 
				else echo "<option value='".$field[0]."'>".($field[1])."</option>";
			}
			break;
		case 1:
			$sql="SELECT CodOrganismo, Organismo FROM mastorganismos WHERE CodOrganismo='$organismo'";
			$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows=mysql_num_rows($query);
			if ($rows!=0) {
				$field=mysql_fetch_array($query);
				echo "<option value='".$field[0]."'>".($field[1])."</option>";
			}
			break;
		case 3:
			$sql = "SELECT s.CodOrganismo, o.Organismo 
					FROM 
						seguridad_alterna s 
						INNER JOIN mastorganismos o ON (s.CodOrganismo = o.CodOrganismo) 
						WHERE 
							s.Usuario = '".$Usuario."' AND 
							s.CodAplicacion = '".$CodAplicacion."' AND 
							s.FlagMostrar = 'S' 
						GROUP BY s.CodOrganismo 
						ORDER BY s.CodOrganismo";
			$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows=mysql_num_rows($query);
			for ($i=0; $i<$rows; $i++) {
				$field=mysql_fetch_array($query);
				if ($field[0]==$organismo) echo "<option value='".$field[0]."' selected>".($field[1])."</option>"; 
				else echo "<option value='".$field[0]."'>".($field[1])."</option>";
			}
			break;
	}
}

function getDependencias($dependencia, $organismo, $opt=0) {
	if ($opt==3 && $_SESSION["USUARIO_ACTUAL"]==$_SESSION["SUPER_USUARIO"]) $opt=0;
	switch ($opt) {
		case 0:
			$sql="SELECT CodDependencia, Dependencia FROM mastdependencias WHERE CodOrganismo='".$organismo."' AND CodDependencia<>'' ORDER BY CodDependencia";
			$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows=mysql_num_rows($query);
			for ($i=0; $i<$rows; $i++) {
				$field=mysql_fetch_array($query);
				if ($field[0]==$dependencia) echo "<option value='".$field[0]."' selected>".($field[1])."</option>"; 
				else echo "<option value='".$field[0]."'>".($field[1])."</option>";
			}
			break;
		case 1:
			$sql="SELECT CodDependencia, Dependencia FROM mastdependencias WHERE CodDependencia='".$dependencia."'";
			$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows=mysql_num_rows($query);
			if ($rows!=0) {
				$field=mysql_fetch_array($query);
				echo "<option value='".$field[0]."'>".($field[1])."</option>";
			}
			break;
		case 3:
			$sql="SELECT s.CodDependencia, o.Dependencia FROM seguridad_alterna s INNER JOIN mastdependencias o ON (s.CodDependencia=o.CodDependencia) WHERE s.Usuario='".$_SESSION["USUARIO_ACTUAL"]."' AND s.CodAplicacion='".$_SESSION["APLICACION_ACTUAL"]."' AND s.FlagMostrar='S' AND s.CodOrganismo='$organismo' GROUP BY s.CodDependencia ORDER BY s.CodDependencia";
			$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows=mysql_num_rows($query);
			for ($i=0; $i<$rows; $i++) {
				$field=mysql_fetch_array($query);
				if ($field[0]==$dependencia) echo "<option value='".$field[0]."' selected>".($field[1])."</option>"; 
				else echo "<option value='".$field[0]."'>".($field[1])."</option>";
			}
			break;
	}
}

function loadSelectPeriodosBono($Periodo, $CodOrganismo, $CodTipoNom, $opt=0) {
	$sql = "SELECT Anio, CodOrganismo, CodBonoAlim, Periodo
			FROM rh_bonoalimentacion
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodTipoNom = '".$CodTipoNom."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[Anio]_$field[CodOrganismo]_$field[CodBonoAlim]";
		if ($field[3] == $Periodo) { ?><option value="<?=$id?>" selected="selected"><?=$field[3]?></option><?php }
		else { ?><option value="<?=$id?>"><?=$field[3]?></option><?php }
	}
}

function loadSelectPeriodosBonoAnio($Anio, $CodOrganismo, $CodTipoNom, $opt=0) {
	$sql = "SELECT Anio
			FROM rh_bonoalimentacion
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodTipoNom = '".$CodTipoNom."'
			GROUP BY Anio";
	$field = getRecords($sql);
	foreach($field as $f) {
		if ($f[0] == $Anio) { ?><option value="<?=$f[0]?>" selected="selected"><?=$f[0]?></option><?php }
		else { ?><option value="<?=$f[0]?>"><?=$f[0]?></option><?php }
	}
}

function loadSelectPeriodosBonoMes($Anio, $Mes, $CodOrganismo, $CodTipoNom, $opt=0) {
	$sql = "SELECT Anio, CodOrganismo, CodBonoAlim, Periodo, SUBSTRING(Periodo, 6, 2) AS Mes, Descripcion
			FROM rh_bonoalimentacion
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodTipoNom = '".$CodTipoNom."' AND
				Anio = '".$Anio."'
			ORDER BY Periodo";
	$field = getRecords($sql);
	foreach($field as $f) {
		$id = $f['Anio'].'_'.$f['CodOrganismo'].'_'.$f['CodBonoAlim'];
		if ($f['Mes'] == $Mes) { ?><option value="<?=$id?>" selected="selected"><?=$f['Mes']?></option><?php }
		else { ?><option value="<?=$id?>"><?=$f['Mes']?></option><?php }
	}
}

function loadSelectSemanasBono($Semana, $Periodo, $opt=0) {
	list($Anio, $CodOrganismo, $CodBonoAlim) = split("[_]", $Periodo);
	//	consulto los dias
	$sql = "SELECT
				ba.FechaInicio,
				ba.FechaFin,
				ba.TotalDiasPeriodo AS DiasPeriodo
			FROM rh_bonoalimentacion ba
			WHERE
				ba.Anio = '".$Anio."' AND
				ba.CodOrganismo = '".$CodOrganismo."' AND
				ba.CodBonoAlim = '".$CodBonoAlim."'";
	$field_dias = getRecord($sql);
	//	obtengo el nro de semanas
	$swSemana = true;
	$ns = 0;
	$nro_semanas = 0;
	$fi = formatFechaDMA($field_dias['FechaInicio']);
	while($swSemana) {
		++$ns;
		++$nro_semanas;
		$dsemana = getWeekDay($fi);
		$dias_semana = 7 - $dsemana + 1;
		$ff = obtenerFechaFin($fi, $dias_semana);
		if (formatFechaAMD($ff) >= $field_dias['FechaFin']) { $ff = formatFechaDMA($field_dias['FechaFin']); $swSemana = false; }
		$ttl[$ns] = "$fi|$ff";
		$fechai[$ns] = $fi;
		$fechaf[$ns] = $ff;
		$fi = obtenerFechaFin($ff, 2);
	}
	//	semanas
	for($ns=1;$ns<=$nro_semanas;$ns++) {
		?><option value="<?=$ttl[$ns]?>"><?=$ttl[$ns]?></option><?php
	}
}

function loadSelectPeriodosNomina($Periodo, $CodOrganismo, $CodTipoNom, $opt=0) {
	switch ($opt) {
		case 0:
			$sql = "SELECT Periodo
					FROM pr_procesoperiodo
					WHERE
						CodTipoNom = '".$CodTipoNom."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						Estado = 'A'
					GROUP BY Periodo
					ORDER BY Periodo DESC";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				list($anio, $mes) = split("[-]", $field['Periodo']);
				if ($grupo != $anio) {
					$grupo = $anio;
					?><optgroup label="<?=$anio?>"><?=$anio?></optgroup><?php
				}
				if ($field[0] == $Periodo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT Periodo
					FROM pr_procesoperiodo
					WHERE
						CodTipoNom = '".$CodTipoNom."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						Estado = 'A' AND
						FlagAprobado = 'S' AND
						FlagPagado = 'N'
					GROUP BY Periodo
					ORDER BY Periodo DESC";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				list($anio, $mes) = split("[-]", $field['Periodo']);
				if ($grupo != $anio) {
					$grupo = $anio;
					?><optgroup label="<?=$anio?>"><?=$anio?></optgroup><?php
				}
				if ($field[0] == $Periodo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
			}
			break;
	}
}

function loadSelectProcesosVacaciones($CodTipoProceso=NULL, $opt=0) {
	switch ($opt) {
		case 0:
			$sql = "SELECT CodTipoProceso, Descripcion 
					FROM pr_tipoproceso 
					WHERE CodTipoProceso = 'BVC' OR CodTipoProceso = 'RTV'
					ORDER BY CodTipoProceso";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $CodTipoProceso) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT CodTipoProceso, Descripcion 
					FROM pr_tipoproceso 
					WHERE CodTipoProceso = '".$CodTipoProceso."' 
					ORDER BY CodTipoProceso";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectDependiente($tabla, $campo1, $campo2, $campo3, $codigo1, $codigo2, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE Estado = 'A' AND $campo3 = '$codigo2' ORDER BY $campo1";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo1) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE Estado = 'A' AND $campo1 = '$codigo1'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectDependiente2($tabla, $campo1, $campo2, $campo3, $campo4, $codigo1, $codigo2, $codigo3, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE Estado = 'A' AND $campo3 = '$codigo2' AND $campo4 = '$codigo3' ORDER BY $campo1";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo1) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE Estado = 'A' AND $campo1 = '$codigo1'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectDependienteEstado($codigo1, $codigo2, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT CodEstado, Estado FROM mastestados WHERE Status = 'A' AND CodPais = '$codigo2' ORDER BY CodEstado";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo1) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT CodEstado, Estado FROM mastestados WHERE $campo1 = '$codigo1'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectDependienteSE($tabla, $campo1, $campo2, $campo3, $codigo1, $codigo2, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE $campo3 = '$codigo2' ORDER BY $campo1";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo1) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE $campo1 = '$codigo1'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectAplicacion($codigo, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT CodAplicacion, Descripcion
					FROM mastaplicaciones
					WHERE
						Estado = 'A' AND
						(CodAplicacion = '".$_SESSION["APLICACION_ACTUAL"]."' OR
						 CodAplicacion = 'GE')
					ORDER BY CodAplicacion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT CodAplicacion, Descripcion
					FROM mastaplicaciones
					WHERE CodAplicacion = '".$codigo."'
					ORDER BY CodAplicacion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
			
		case 10:
			$sql = "SELECT CodAplicacion, Descripcion
					FROM mastaplicaciones
					WHERE
						Estado = 'A' AND
						(CodAplicacion = '".$_SESSION["APLICACION_ACTUAL"]."' OR
						 CodAplicacion = 'GE')
					ORDER BY CodAplicacion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php }
			}
			break;
			
		case 11:
			$sql = "SELECT CodAplicacion, Descripcion
					FROM mastaplicaciones
					WHERE CodAplicacion = '".$codigo."'
					ORDER BY CodAplicacion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=($field[1])?></option><?php
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECT PERIODOS DE NOMINA
function loadSelectNominaPeriodos($organismo, $nomina, $periodo) {
	global $_PARAMETRO;
	$sql = "SELECT Periodo 
			FROM pr_procesoperiodo 
			WHERE 
				CodOrganismo = '".$organismo."' AND 
				CodTipoNom = '".$nomina."' AND
				(CodTipoProceso = 'FIN' OR CodTipoProceso = 'PPA')
			GROUP BY Periodo
			ORDER BY Periodo DESC";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		list($anio, $mes) = split("[-]", $field['Periodo']);
		if ($grupo != $anio) {
			$grupo = $anio;
			?><optgroup label="<?=$anio?>"><?=$anio?></optgroup><?php
		}
		
		if ($field[0] == $periodo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlNominas($CodOrganismo, $CodTipoNom) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	$sql = "SELECT
				tn.CodTipoNom,
				tn.Nomina
			FROM
				pr_procesoperiodo pp
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
			WHERE
				pp.FlagAprobado = 'S'
				$filtro
			GROUP BY CodTipoNom
			ORDER BY CodTipoNom";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoNom) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlPeriodos($CodOrganismo, $CodTipoNom, $Periodo) {
	if ($CodOrganismo != "") $filtro .= " AND CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND CodTipoNom = '".$CodTipoNom."'";
	$sql = "SELECT Periodo
			FROM pr_procesoperiodo
			WHERE
				FlagAprobado = 'S'
				$filtro
			GROUP BY Periodo
			ORDER BY Periodo DESC";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		list($Anio, $Mes) = split("[-]", $field['Periodo']);
		if ($Grupo != $Anio) {
			$Grupo = $Anio;
			?><optgroup label="<?=$Anio?>"><?=$Anio?></optgroup><?php
		}
		if ($field[0] == $Periodo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlPeriodosAnio($CodOrganismo, $CodTipoNom, $Anio) {
	if ($CodOrganismo != "") $filtro .= " AND CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND CodTipoNom = '".$CodTipoNom."'";
	$sql = "SELECT SUBSTRING(Periodo, 1, 4) AS Anio
			FROM pr_procesoperiodo
			WHERE
				FlagAprobado = 'S'
				$filtro
			GROUP BY Anio
			ORDER BY Anio DESC";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $Anio) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlPeriodosMes($CodOrganismo, $CodTipoNom, $Anio, $Mes) {
	if ($CodOrganismo != "") $filtro .= " AND CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND CodTipoNom = '".$CodTipoNom."'";
	$filtro .= " AND SUBSTRING(Periodo, 1, 4) = '".$Anio."'";
	$sql = "SELECT 
				SUBSTRING(Periodo, 1, 4) AS Anio, 
				SUBSTRING(Periodo, 6, 2) AS Mes
			FROM pr_procesoperiodo
			WHERE
				FlagAprobado = 'S'
				$filtro
			GROUP BY Periodo
			ORDER BY Periodo DESC";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[1] == $Mes) { ?><option value="<?=$field[1]?>" selected="selected"><?=$field[1]?></option><?php }
		else { ?><option value="<?=$field[1]?>"><?=$field[1]?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlProcesos($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND pp.CodTipoNom = '".$CodTipoNom."'";
	if ($Periodo != "") $filtro .= " AND pp.Periodo = '".$Periodo."'";
	$sql = "SELECT
				tp.CodTipoProceso,
				tp.Descripcion
			FROM
				pr_procesoperiodo pp
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
			WHERE
				pp.FlagAprobado = 'S'
				$filtro
			GROUP BY CodTipoProceso
			ORDER BY Descripcion";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoProceso) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlProcesosAnioMes($CodOrganismo, $CodTipoNom, $Anio, $Mes, $CodTipoProceso) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND pp.CodTipoNom = '".$CodTipoNom."'";
	if ($Periodo != "") $filtro .= " AND pp.Periodo = '".$Periodo."'";
	$sql = "SELECT
				tp.CodTipoProceso,
				tp.Descripcion
			FROM
				pr_procesoperiodo pp
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
			WHERE
				pp.FlagAprobado = 'S'
				$filtro
			GROUP BY CodTipoProceso
			ORDER BY Descripcion";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoProceso) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlNominas2($CodOrganismo, $CodTipoNom) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	$sql = "SELECT
				tn.CodTipoNom,
				tn.Nomina
			FROM
				pr_procesoperiodo pp
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
			WHERE
				pp.FlagPagado = 'N' AND
				pp.FlagAprobado = 'S'
				$filtro
			GROUP BY CodTipoNom
			ORDER BY CodTipoNom";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoNom) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlPeriodos2($CodOrganismo, $CodTipoNom, $Periodo) {
	if ($CodOrganismo != "") $filtro .= " AND CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND CodTipoNom = '".$CodTipoNom."'";
	$sql = "SELECT Periodo
			FROM pr_procesoperiodo
			WHERE
				FlagPagado = 'N' AND
				FlagAprobado = 'S'
				$filtro
			GROUP BY Periodo
			ORDER BY Periodo DESC";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		list($Anio, $Mes) = split("[-]", $field['Periodo']);
		if ($Grupo != $Anio) {
			$Grupo = $Anio;
			?><optgroup label="<?=$Anio?>"><?=$Anio?></optgroup><?php
		}
		if ($field[0] == $Periodo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlProcesos2($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND pp.CodTipoNom = '".$CodTipoNom."'";
	if ($Periodo != "") $filtro .= " AND pp.Periodo = '".$Periodo."'";
	$sql = "SELECT
				tp.CodTipoProceso,
				tp.Descripcion
			FROM
				pr_procesoperiodo pp
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
			WHERE
				pp.FlagPagado = 'N' AND
				pp.FlagAprobado = 'S'
				$filtro
			GROUP BY CodTipoProceso
			ORDER BY Descripcion";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoProceso) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlNominas3($CodOrganismo, $CodTipoNom) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	$sql = "SELECT
				tn.CodTipoNom,
				tn.Nomina
			FROM
				pr_procesoperiodo pp
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
			WHERE
				FlagAprobado = 'S'
				$filtro
			GROUP BY CodTipoNom
			ORDER BY CodTipoNom";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoNom) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlPeriodos3($CodOrganismo, $CodTipoNom, $Periodo) {
	if ($CodOrganismo != "") $filtro .= " AND CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND CodTipoNom = '".$CodTipoNom."'";
	$sql = "SELECT Periodo
			FROM pr_procesoperiodo
			WHERE
				FlagAprobado = 'S'
				$filtro
			GROUP BY Periodo
			ORDER BY Periodo DESC";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		list($Anio, $Mes) = split("[-]", $field['Periodo']);
		if ($Grupo != $Anio) {
			$Grupo = $Anio;
			?><optgroup label="<?=$Anio?>"><?=$Anio?></optgroup><?php
		}
		if ($field[0] == $Periodo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlProcesos3($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND pp.CodTipoNom = '".$CodTipoNom."'";
	if ($Periodo != "") $filtro .= " AND pp.Periodo = '".$Periodo."'";
	$sql = "SELECT
				tp.CodTipoProceso,
				tp.Descripcion
			FROM
				pr_procesoperiodo pp
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
			WHERE
				FlagAprobado = 'S'
				$filtro
			GROUP BY CodTipoProceso
			ORDER BY Descripcion";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoProceso) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectPeriodosNominaProcesos($CodTipoProceso, $Periodo, $CodOrganismo, $CodTipoNom, $opt) {
	switch ($opt) {
		case 1:
			$sql = "SELECT
						pp.CodTipoProceso,
						tp.Descripcion
					FROM
						pr_procesoperiodo pp
						INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
					WHERE
						pp.Periodo = '".$Periodo."' AND
						pp.CodTipoNom = '".$CodTipoNom."' AND
						pp.CodOrganismo = '".$CodOrganismo."' AND
						pp.Estado = 'A' AND
						pp.FlagPagado = 'N' AND
						pp.FlagAprobado = 'S'
					ORDER BY Descripcion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $CodTipoProceso) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
			}
			break;
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadNominaPeriodos($CodTipoNom, $Periodo) {
	$filtro = "";
	if ($CodTipoNom != "") $filtro .= " AND CodTipoNom = '".$CodTipoNom."'";
	$sql = "SELECT CONCAT(Periodo, '-', Mes) AS Periodo
			FROM pr_tiponominaperiodo
			WHERE 1 $filtro
			ORDER BY Periodo DESC";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		list($Anio, $Mes) = split("[-]", $field['Periodo']);
		if ($Grupo != $Anio) {
			$Grupo = $Anio;
			?><optgroup label="<?=$Anio?>"><?=$Anio?></optgroup><?php
		}
		if ($field[0] == $Periodo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlNominasPrenomina($CodOrganismo, $CodTipoNom) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	$sql = "SELECT
				tn.CodTipoNom,
				tn.Nomina
			FROM
				pr_procesoperiodoprenomina pp
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
			WHERE 1 $filtro
			GROUP BY CodTipoNom
			ORDER BY CodTipoNom";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoNom) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlPeriodosPrenomina($CodOrganismo, $CodTipoNom, $Periodo) {
	if ($CodOrganismo != "") $filtro .= " AND CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND CodTipoNom = '".$CodTipoNom."'";
	$sql = "SELECT Periodo
			FROM pr_procesoperiodoprenomina
			WHERE 1 $filtro
			GROUP BY Periodo
			ORDER BY Periodo DESC";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		list($Anio, $Mes) = split("[-]", $field['Periodo']);
		if ($Grupo != $Anio) {
			$Grupo = $Anio;
			?><optgroup label="<?=$Anio?>"><?=$Anio?></optgroup><?php
		}
		if ($field[0] == $Periodo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
	}
}

//	funcion para cargar los periodos disponibles para un tipo de nomina
function loadControlProcesosPrenomina($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) {
	if ($CodOrganismo != "") $filtro .= " AND pp.CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND pp.CodTipoNom = '".$CodTipoNom."'";
	if ($Periodo != "") $filtro .= " AND pp.Periodo = '".$Periodo."'";
	$sql = "SELECT
				tp.CodTipoProceso,
				tp.Descripcion
			FROM
				pr_procesoperiodoprenomina pp
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
			WHERE 1 $filtro
			GROUP BY CodTipoProceso
			ORDER BY Descripcion";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field[0] == $CodTipoProceso) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
		else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
	}
}

//	FUNCION PARA CARGAR LAS DEPENDENCIAS EN UN SELECT
function loadSelectTipoServicioDocumento($CodTipoDocumento, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT ts.CodTipoServicio, ts.Descripcion
					FROM
						masttiposervicio ts
						INNER JOIN ap_tipodocumento td ON (td.CodRegimenFiscal = ts.CodRegimenFiscal)
					WHERE td.CodTipoDocumento = '".$CodTipoDocumento."'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows = mysql_num_rows($query);
			for ($i=0; $i<$rows; $i++) {
				$field = mysql_fetch_array($query);
				echo "<option value='".$field[0]."'>".($field[1])."</option>";
			}
			break;
	}
}

//	FUNCION PARA CARGAR LAS DEPENDENCIAS EN UN SELECT
function loadSelectAlmacen($CodAlmacen, $FlagCommodity, $opt) {
	if ($FlagCommodity != "") $filtro = "AND FlagCommodity = '".$FlagCommodity."'";
	switch ($opt) {
		case 0:
			$sql = "SELECT CodAlmacen, Descripcion
					FROM lg_almacenmast
					WHERE Estado = 'A' $filtro
					ORDER BY CodAlmacen";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $CodAlmacen) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT CodAlmacen, Descripcion
					FROM lg_almacenmast
					WHERE CodAlmacen = '".$CodAlmacen."' AND Estado = 'A' $filtro
					ORDER BY CodAlmacen";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
	}
}

//	FUNCION PARA CARGAR LAS DEPENDENCIAS EN UN SELECT
function loadDependenciaFiscal($dependencia, $organismo, $opt=0) {
	switch ($opt) {
		case 0:
			$sql = "SELECT
						s.CodDependencia,
						o.Dependencia
					FROM
						seguridad_alterna s
						INNER JOIN mastdependencias o ON (s.CodDependencia = o.CodDependencia)
					WHERE
						s.Usuario = '".$_SESSION["USUARIO_ACTUAL"]."' AND
						s.CodAplicacion = '".$_SESSION["APLICACION_ACTUAL"]."' AND
						s.FlagMostrar = 'S' AND
						s.CodOrganismo = '$organismo' AND
						o.FlagControlFiscal = 'S'
					GROUP BY s.CodDependencia
					ORDER BY s.CodDependencia";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows = mysql_num_rows($query);
			for ($i=0; $i<$rows; $i++) {
				$field = mysql_fetch_array($query);
				if ($field[0] == $dependencia) echo "<option value='".$field[0]."' selected>".($field[1])."</option>"; 
				else echo "<option value='".$field[0]."'>".($field[1])."</option>";
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS 
function getFechaEventos($FechaInicio, $FechaFin, $Fecha, $opt, $CodPersona=NULL) {
	$nro_dias = getFechaDias($FechaInicio, $FechaFin) + 1;
	$fi = $FechaInicio;
	$j = 0;
	for($i=0;$i<$nro_dias;$i++) {
		list($d, $m, $a) = explode('-', $fi);
		$DiaFeriado = $m.'-'.$d;
		$sql = "SELECT *
				FROM rh_feriados
				WHERE
					(AnioFeriado <> '' AND CONCAT(AnioFeriado, '-', DiaFeriado) = '".formatFechaAMD($fi)."') OR
					(AnioFeriado = '' AND DiaFeriado = '".$DiaFeriado."')";	//echo "$sql; \n\n";
		$field_feriado = getRecord($sql);
		if (!count($field_feriado)) {
			$DiaSemana = getWeekDay($fi);
			/*$sql = "SELECT *
					FROM
						mastempleado e
						INNER JOIN rh_horariolaboraldet hld ON (hld.CodHorario = e.CodHorario AND 
																hld.Dia = '".$DiaSemana."' AND 
																hld.FlagLaborable = 'S')
					WHERE e.CodPersona = '".$CodPersona."'";
			$field_horario = getRecord($sql);
			if ((count($field_horario) && $CodPersona) || (!$CodPersona)) {*/
				$c[$j] = $fi; 
				$v[$j] = $fi;
				$fi = obtenerFechaFin($fi, 2);
				++$j;
			//}
		} else {
			$fi = obtenerFechaFin($fi, 2);
		}
	}
	
	$i = 0;
	switch ($opt) {
		case 0:
			foreach ($c as $cod) {
				if ($cod == $Fecha) echo "<option value='".$cod."' selected>".$v[$i]."</option>";
				else echo "<option value='".$cod."'>".$v[$i]."</option>";
				$i++;
			}
			break;
			
		case 1:
			foreach ($c as $cod) {
				if ($cod == $Fecha) echo "<option value='".$cod."' selected>".$v[$i]."</option>";
				$i++;
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectTipoDocumentoCxP($codigo, $FlagAutoNomina, $opt) {
	if ($FlagAutoNomina != "") $filtro = " AND td.FlagAutoNomina = '".$FlagAutoNomina."'";
	switch ($opt) {
		case 0:
			$sql = "SELECT
						td.CodTipoDocumento,
						td.Descripcion,
						td.CodRegimenFiscal,
						rf.Descripcion AS NomRegimenFiscal
					FROM
						ap_tipodocumento td
						INNER JOIN ap_regimenfiscal rf ON (rf.CodRegimenFiscal = td.CodRegimenFiscal)
					WHERE
						td.Estado = 'A'
						$filtro
					ORDER BY NomRegimenFiscal, CodRegimenFiscal, Descripcion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));	$i=0;
			while ($field = mysql_fetch_array($query)) {
				if ($Grupo != $field['CodRegimenFiscal']) {
					$Grupo = $field['CodRegimenFiscal'];
					if ($i==0) { ?></optgroup><?php }
					?><optgroup label="<?=htmlentities($field['NomRegimenFiscal'])?>"><?php
				}
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
				++$i;
			}
			?></optgroup><?php
			break;
			
		case 1:
			$sql = "SELECT
						td.CodTipoDocumento,
						td.Descripcion,
						td.CodRegimenFiscal,
						rf.Descripcion AS NomRegimenFiscal
					FROM
						ap_tipodocumento td
						INNER JOIN ap_regimenfiscal rf ON (rf.CodRegimenFiscal = td.CodRegimenFiscal)
					WHERE
						td.Estado = 'A' AND
						td.CodTipoDocumento = '$codigo'
						$filtro
					ORDER BY NomRegimenFiscal, CodRegimenFiscal, Descripcion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?>
                <optgroup label="<?=htmlentities($field['NomRegimenFiscal'])?>">
                <option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option>
                </optgroup>
				<?php
			}
			break;
			
		case 10:
			$sql = "SELECT
						td.CodTipoDocumento,
						td.Descripcion,
						td.CodRegimenFiscal,
						rf.Descripcion AS NomRegimenFiscal
					FROM
						ap_tipodocumento td
						INNER JOIN ap_regimenfiscal rf ON (rf.CodRegimenFiscal = td.CodRegimenFiscal)
					WHERE
						td.Estado = 'A'
						$filtro
					ORDER BY NomRegimenFiscal, CodRegimenFiscal, Descripcion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));	$i=0;
			while ($field = mysql_fetch_array($query)) {
				if ($Grupo != $field['CodRegimenFiscal']) {
					$Grupo = $field['CodRegimenFiscal'];
					if ($i==0) { ?></optgroup><?php }
					?><optgroup label="<?=htmlentities($field['NomRegimenFiscal'])?>"><?php
				}
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=htmlentities($field[1])?></option><?php }
				++$i;
				
			}
			break;
			
		case 11:
			$sql = "SELECT
						td.CodTipoDocumento,
						td.Descripcion,
						td.CodRegimenFiscal,
						rf.Descripcion AS NomRegimenFiscal
					FROM
						ap_tipodocumento td
						INNER JOIN ap_regimenfiscal rf ON (rf.CodRegimenFiscal = td.CodRegimenFiscal)
					WHERE
						td.Estado = 'A' AND
						td.CodTipoDocumento = '$codigo'
						$filtro
					ORDER BY NomRegimenFiscal, CodRegimenFiscal, Descripcion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?>
                <optgroup label="<?=htmlentities($field['NomRegimenFiscal'])?>">
                <option value="<?=$field[0]?>" selected="selected"><?=$field[0]?>&nbsp;&nbsp;&nbsp;<?=htmlentities($field[1])?></option>
                </optgroup>
				<?php
			}
			break;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectProfesiones($CodProfesion, $CodGradoInstruccion, $Area, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT CodProfesion, Descripcion 
					FROM rh_profesiones 
					WHERE Estado = 'A' AND CodGradoInstruccion = '".$CodGradoInstruccion."' AND Area = '".$Area."' 
					ORDER BY Descripcion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $CodProfesion) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT CodProfesion, Descripcion 
					FROM rh_profesiones 
					WHERE Estado = 'A' AND CodGradoInstruccion = '".$CodGradoInstruccion."' AND Area = '".$Area."' AND CodProfesion = '".$CodProfesion."'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php
			}
			break;
	}
}
//	------------------------------

function loadSelectUnidadEjecutora($codigo=NULL, $opt=0, $CodDependencia) {
	$filtro = "";
	if ($CodDependencia) $filtro .= " AND ued.CodDependencia = '$CodDependencia'";
	switch ($opt) {
		case 0:
			$sql = "SELECT ue.CodUnidadEjec, ue.Denominacion
					FROM pv_unidadejecutora ue
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
					WHERE 1 $filtro 
					GROUP BY CodUnidadEjec
					ORDER BY Denominacion";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT CodUnidadEjec, Denominacion FROM pv_unidadejecutora WHERE CodUnidadEjec = '$codigo' $filtro";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=htmlentities($field[1])?></option><?php
			}
			break;
			
		case 10:
			$sql = "SELECT ue.CodUnidadEjec, ue.Denominacion
					FROM pv_unidadejecutora ue
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
					WHERE 1 $filtro 
					GROUP BY CodUnidadEjec
					ORDER BY CodUnidadEjec";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php }
			}
			break;
			
	}
}

function loadSelectFromParametros($ParametroClave, $tabla, $id, $descripcion, $ValorDef = NULL, $sep = ',') {
	$sql = "SELECT ValorParam FROM mastparametros WHERE ParametroClave = '$ParametroClave'";
	$ValorParam = getVar3($sql);
	
	$valores = explode($sep, $ValorParam);
	foreach ($valores as $valor) {
		$sql = "SELECT $descripcion FROM $tabla WHERE $id = '$valor'";
		$nombre = getVar3($sql);

		if ($ValorDef == $valor) $selected = 'selected'; else $selected = '';

		?><option value="<?=$valor?>" <?=$selected?>><?=$nombre?></option><?php
	}
}

function loadSelectFromParametros2($tabla, $campo1, $campo2, $ParametroClave, $codigo=NULL, $opt=0, $campos=NULL, $valores=NULL, $campo3=NULL, $sep = ',') {
	$sql = "SELECT ValorParam FROM mastparametros WHERE ParametroClave = '$ParametroClave'";
	$ValorParam = getVar3($sql);
	$ArrayParam = explode($sep, $ValorParam);

	$filtro = "";
	if ($campos) {
		for($i=0;$i<count($campos);$i++) {
			$filtro .= " AND $campos[$i] = '$valores[$i]'";
		}
	}
	$i = 0;
	foreach ($ArrayParam as $valor) {
		if ($i == 0) $filtro .= " AND (";
		else $filtro .= " OR ";
		$filtro .= " $campo1 = '$valor'";
		++$i;
	}
	if ($i > 0) $filtro .= ")";

	if ($campo3) { $c3 = ", $campo3"; $order = "$campo3"; } else { $c3 = ""; $order = "$campo2"; }
	switch ($opt) {
		case 0:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE 1 $filtro ORDER BY $order";
			$f = getRecords($sql);
			foreach ($f as $field) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=($campo3?"$field[2] - ":"")?><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($campo3?"$field[2] - ":"")?><?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE $campo1 = '$codigo' $filtro";
			$f = getRecords($sql);
			foreach ($f as $field) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($campo3?"$field[2] - ":"")?><?=htmlentities($field[1])?></option><?php
			}
			break;
			
		case 10:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE 1 $filtro ORDER BY $campo1";
			$f = getRecords($sql);
			foreach ($f as $field) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php }
			}
			break;
			
	}
}

function loadBonoNomina($CodOrganismo, $CodTipoNom, $opt=0) {
	$sql = "SELECT tn.CodTipoNom, tn.Nomina
			FROM
				rh_bonoalimentacion ba
				INNER JOIN tiponomina tn ON (ba.CodTipoNom = tn.CodTipoNom)
			WHERE ba.CodOrganismo = '$CodOrganismo'
			GROUP BY CodTipoNom
			ORDER BY Nomina";
	$field = getRecords($sql);

	$i = 0;
	switch ($opt) {
		case 0:
			foreach ($field as $f) {
				if ($f['CodTipoNom'] == $CodTipoNom) $selected = "selected"; else $selected = "";
				echo "<option value='$f[CodTipoNom]' $selected>$f[Nomina]</option>";
				$i++;
			}
			break;
			
		case 1:
			foreach ($c as $cod) {
				if ($f['CodTipoNom'] == $CodTipoNom) echo "<option value='$f[CodTipoNom]' $selected>$f[Nomina]</option>";
				$i++;
			}
			break;
	}

}

function loadBonoPeriodo($CodOrganismo, $CodTipoNom, $Periodo, $opt=0) {
	$sql = "SELECT ba.Periodo
			FROM rh_bonoalimentacion ba
			WHERE
				ba.CodOrganismo = '$CodOrganismo'
				AND ba.CodTipoNom = '$CodTipoNom'
			GROUP BY Periodo
			ORDER BY Periodo";
	$field = getRecords($sql);

	$i = 0;
	switch ($opt) {
		case 0:
			foreach ($field as $f) {
				if ($f['Periodo'] == $Periodo) $selected = "selected"; else $selected = "";
				echo "<option value='$f[Periodo]' $selected>$f[Periodo]</option>";
				$i++;
			}
			break;
			
		case 1:
			foreach ($c as $cod) {
				if ($f['Periodo'] == $Periodo) echo "<option value='$f[Periodo]' $selected>$f[Periodo]</option>";
				$i++;
			}
			break;
	}

}

function loadBonoAnio($CodOrganismo, $CodTipoNom, $Anio, $opt=0) {
	$sql = "SELECT SUBSTRING(ba.Periodo, 1, 4) AS Anio
			FROM rh_bonoalimentacion ba
			WHERE
				ba.CodOrganismo = '$CodOrganismo'
				AND ba.CodTipoNom = '$CodTipoNom'
			GROUP BY Anio
			ORDER BY Anio";
	$field = getRecords($sql);

	$i = 0;
	switch ($opt) {
		case 0:
			foreach ($field as $f) {
				if ($f['Anio'] == $Anio) $selected = "selected"; else $selected = "";
				echo "<option value='$f[Anio]' $selected>$f[Anio]</option>";
				$i++;
			}
			break;
			
		case 1:
			foreach ($c as $cod) {
				if ($f['Anio'] == $Anio) echo "<option value='$f[Anio]' $selected>$f[Anio]</option>";
				$i++;
			}
			break;
	}

}

function loadBonoMes($CodOrganismo, $CodTipoNom, $Anio, $Mes, $opt=0) {
	$sql = "SELECT SUBSTRING(ba.Periodo, 6, 2) AS Mes
			FROM rh_bonoalimentacion ba
			WHERE
				ba.CodOrganismo = '$CodOrganismo'
				AND ba.CodTipoNom = '$CodTipoNom'
				AND SUBSTRING(ba.Periodo, 1, 4) = '$Anio'
			GROUP BY Mes
			ORDER BY Mes";
	$field = getRecords($sql);

	$i = 0;
	switch ($opt) {
		case 0:
			foreach ($field as $f) {
				if ($f['Mes'] == $Mes) $selected = "selected"; else $selected = "";
				echo "<option value='$f[Mes]' $selected>$f[Mes]</option>";
				$i++;
			}
			break;
			
		case 1:
			foreach ($c as $cod) {
				if ($f['Mes'] == $Mes) echo "<option value='$f[Mes]' $selected>$f[Mes]</option>";
				$i++;
			}
			break;
	}

}

function loadBonoProceso($CodOrganismo, $CodTipoNom, $Anio, $Mes, $CodBonoAlim, $opt=0) {
	$sql = "SELECT ba.*
			FROM rh_bonoalimentacion ba
			WHERE
				ba.CodOrganismo = '$CodOrganismo'
				AND ba.CodTipoNom = '$CodTipoNom'
				AND SUBSTRING(ba.Periodo, 1, 4) = '$Anio'
				AND SUBSTRING(ba.Periodo, 6, 2) = '$Mes'
			ORDER BY CodBonoAlim";
	$field = getRecords($sql);

	$i = 0;
	switch ($opt) {
		case 0:
			foreach ($field as $f) {
				if ($f['CodBonoAlim'] == $CodBonoAlim) $selected = "selected"; else $selected = "";
				echo "<option value='$f[CodBonoAlim]' $selected>".formatFechaDMA($f['FechaInicio'])." AL ".formatFechaDMA($f['FechaFin'])."</option>";
				$i++;
			}
			break;
			
		case 1:
			foreach ($c as $cod) {
				if ($f['CodBonoAlim'] == $CodBonoAlim) echo "<option value='$f[CodBonoAlim]' $selected>".formatFechaDMA($f['FechaInicio'])." AL ".formatFechaDMA($f['FechaFin'])."</option>";
				$i++;
			}
			break;
	}

}

function loadSelectTiposCertificacion($codigo=NULL, $opt=0, $campos=NULL, $valores=NULL, $campo3=NULL) {
	$tabla = "ap_tiposcertificacion";
	$campo1 = "CodTipoCertif";
	$campo2 = "Descripcion";
	$filtro = " AND CodTipoCertif <> '09'";
	if ($campos) {
		for($i=0;$i<count($campos);$i++) {
			$filtro .= " AND $campos[$i] = '$valores[$i]'";
		}
	}
	if ($campo3) { $c3 = ", $campo3"; $order = "$campo3"; } else { $c3 = ""; $order = "$campo2"; }
	switch ($opt) {
		case 0:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE 1 $filtro ORDER BY $order";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=($campo3?"$field[2] - ":"")?><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($campo3?"$field[2] - ":"")?><?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE $campo1 = '$codigo' $filtro";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($campo3?"$field[2] - ":"")?><?=htmlentities($field[1])?></option><?php
			}
			break;
			
		case 10:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE 1 $filtro ORDER BY $campo1";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php }
			}
			break;
			
		case 11:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE $campo1 = '$codigo' $filtro";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?> - <?=htmlentities($field[1])?></option><?php
			}
			break;
			
		case 20:
			$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE 1 $filtro ORDER BY $campo1";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=$field[0]?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=$field[0]?></option><?php }
			}
			break;

		case 30:
			$sql = "SELECT $campo1, $campo2 $c3 FROM $tabla WHERE 1 $filtro GROUP BY $campo1 ORDER BY $order";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=($campo3?"$field[2] - ":"")?><?=htmlentities($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($campo3?"$field[2] - ":"")?><?=htmlentities($field[1])?></option><?php }
			}
			break;
			
	}
}

function unidades_item($CodItem, $CodUnidad, $opt = 0) {
	$sql = "SELECT * FROM lg_itemmast WHERE CodItem = '$CodItem'";
	$field = getRecord($sql);
	$CodUnidades[$field['CodUnidad']] = $field['CodUnidad'];
	$CodUnidades[$field['CodUnidadComp']] = $field['CodUnidadComp'];
	$CodUnidades[$field['CodUnidadEmb']] = $field['CodUnidadEmb'];

	$options = '';
	switch ($opt) {
		case 0:
			foreach ($CodUnidades as $key => $value)
			{
				if ($key == $CodUnidad) $options .= '<option value="'.$key.'" selected>'.$value.'</option>';
				else $options .= '<option value="'.$key.'">'.$value.'</option>';
			}
			break;
		
		case 1:
			$options .= '<option value="'.$CodUnidad.'">'.$CodUnidad.'</option>';
			break;
	}

	return $options;
}

function vendedores($CodPersona = NULL, $opt = 0) {
	$filtro = '';
	if ($opt)
	{
		$filtro .= " AND v.CodPersona = '$CodPersona'";
	}
	if ($CodPersona)
	{
		$filtro .= " AND (v.Estado = 'A' OR v.CodPersona = '$CodPersona')";
	}
	else
	{
		$filtro .= " AND (v.Estado = 'A')";
	}
	$sql = "SELECT
				v.*,
				p.NomCompleto
			FROM co_vendedor v
			INNER JOIN mastpersonas p On p.CodPersona = v.CodPersona
			WHERE 1 $filtro";
	$field = getRecords($sql);

	$options = '';
	foreach ($field as $row)
	{
		if ($row['CodPersona'] == $CodPersona) $options .= '<option value="'.$row['CodPersona'].'" selected>'.$row['NomCompleto'].'</option>';
		else $options .= '<option value="'.$row['CodPersona'].'">'.$row['NomCompleto'].'</option>';
	}

	return $options;
}

function cajeros($CodPersona = NULL, $opt = 0) {
	$filtro = '';
	if ($opt)
	{
		$filtro .= " AND c.CodPersona = '$CodPersona'";
	}
	$sql = "SELECT
				c.*,
				p.NomCompleto
			FROM co_cajeros c
			INNER JOIN mastpersonas p On p.CodPersona = c.CodPersona
			WHERE 1 $filtro";
	$field = getRecords($sql);

	$options = '';
	foreach ($field as $row)
	{
		if ($row['CodPersona'] == $CodPersona) $options .= '<option value="'.$row['CodPersona'].'" selected>'.$row['NomCompleto'].'</option>';
		else $options .= '<option value="'.$row['CodPersona'].'">'.$row['NomCompleto'].'</option>';
	}

	return $options;
}

function choferes($CodPersona = NULL, $opt = 0) {
	$filtro = '';
	if ($opt)
	{
		$filtro .= " AND ch.CodPersona = '$CodPersona'";
	}
	$sql = "SELECT
				ch.*,
				p.NomCompleto
			FROM lg_choferes ch
			INNER JOIN mastpersonas p On p.CodPersona = ch.CodPersona
			WHERE 1 $filtro";
	$field = getRecords($sql);

	$options = '';
	foreach ($field as $row)
	{
		if ($row['CodPersona'] == $CodPersona) $options .= '<option value="'.$row['CodPersona'].'" selected>'.$row['NomCompleto'].'</option>';
		else $options .= '<option value="'.$row['CodPersona'].'">'.$row['NomCompleto'].'</option>';
	}

	return $options;
}

function tipos_pago_tarjeta($CodTipoPago = NULL, $opt = 0) {
	$filtro = '';
	if ($opt)
	{
		$filtro .= " AND CodTipoPago = '$CodTipoPago'";
	}
	$sql = "SELECT *
			FROM co_tipopago
			WHERE
				(FlagEsTarjetaCredito = 'S' OR FlagEsTarjetaDebito = 'S')
				$filtro";
	$field = getRecords($sql);

	$options = '';
	foreach ($field as $row)
	{
		if ($row['CodTipoPago'] == $CodTipoPago) $options .= '<option value="'.$row['CodTipoPago'].'" selected>'.$row['Descripcion'].'</option>';
		else $options .= '<option value="'.$row['CodTipoPago'].'">'.$row['Descripcion'].'</option>';
	}

	return $options;
}
?>