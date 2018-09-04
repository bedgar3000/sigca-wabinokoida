<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Presupuesto
##############################################################################/
if ($modulo == "formulario") {
	//	anular
	if ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if ($Estado == 'AN') die('No puede anular un presupuesto <strong>'.printValores('presupuesto-estado',$Estado).'</strong>');
		else {
			$sql = "SELECT *
					FROM pv_presupuestodet
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						CodPresupuesto = '".$CodPresupuesto."' AND
						MontoCompromiso > 0.00";
			$field_compromisos = getRecords($sql);
			if (count($field_compromisos)) die("No puede anular un presupuesto con partidas presupuestarias con Compromisos");
		}
		##	
		$Estado = 'AN';
		##	actualizar
		$sql = "UPDATE pv_presupuesto
				SET
					Estado = '".$Estado."',
					AnuladoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaAnulado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodPresupuesto = '".$CodPresupuesto."'";
		execute($sql);
		##	detalle
		$sql = "UPDATE pv_presupuestodet
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodPresupuesto = '".$CodPresupuesto."'";
		execute($sql);
		##	proyecto
		##	actualizar
		$sql = "UPDATE pv_proyectopresupuesto
				SET
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	detalle
		$sql = "UPDATE pv_proyectopresupuestodet
				SET
					Estado = 'PR',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	anular
	if($accion == "anular") {
		list($CodOrganismo, $CodPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_presupuesto WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$CodPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado == 'AN') die('No puede anular un presupuesto <strong>'.printValores('presupuesto-estado',$Estado).'</strong>');
		else {
			$sql = "SELECT *
					FROM pv_presupuestodet
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						CodPresupuesto = '".$CodPresupuesto."' AND
						MontoCompromiso > 0.00";
			$field_compromisos = getRecords($sql);
			if (count($field_compromisos)) die("No puede anular un presupuesto con partidas presupuestarias con Compromisos");
		}
	}
}
?>