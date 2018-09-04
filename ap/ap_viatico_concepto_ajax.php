<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	CONCEPTOS DE VIATICOS (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "viatico_concepto") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$CodConcepto = codigo("ap_conceptogastoviatico", "CodConcepto", 3);
		$sql = "INSERT INTO ap_conceptogastoviatico
				SET
					CodConcepto = '".$CodConcepto."',
					Descripcion = '".changeUrl($Descripcion)."',
					Categoria = '".$Categoria."',
					Articulo = '".$Articulo."',
					Numeral = '".$Numeral."',
					ValorUT = '".setNumero($ValorUT)."',
					FlagMonto = '".$FlagMonto."',
					FlagCantidad = '".$FlagCantidad."',
					cod_partida = '".$cod_partida."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE ap_conceptogastoviatico
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					Categoria = '".$Categoria."',
					Articulo = '".$Articulo."',
					Numeral = '".$Numeral."',
					ValorUT = '".setNumero($ValorUT)."',
					FlagMonto = '".$FlagMonto."',
					FlagCantidad = '".$FlagCantidad."',
					cod_partida = '".$cod_partida."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodConcepto = '".$CodConcepto."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM ap_conceptogastoviatico WHERE CodConcepto = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>