<?php
include("fphp.php");
//	-------------------------------------------------------------
if ($accion == "getPresupuestoxDependencia") {
	##	presupuesto
	$sql = "SELECT p.*
			FROM pv_presupuesto p
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
			INNER JOIN pv_unidadejecutora ue On (ue.CodUnidadEjec = cp.CodUnidadEjec)
			INNER JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = ue.CodCentroCosto)
			WHERE p.CodOrganismo = '".$CodOrganismo."' AND p.Ejercicio = '".$Ejercicio."' AND cc.CodDependencia = '".$CodDependencia."'
			LIMIT 0, 1";
	$field_presupuesto = getRecord($sql);
	##	
	$jsondata = [
		'CodPresupuesto' => $field_presupuesto['CodPresupuesto'],
		'CategoriaProg' => $field_presupuesto['CategoriaProg'],
		'Ejercicio' => $field_presupuesto['Ejercicio'],
	];
    echo json_encode($jsondata);
    exit();
}
?>