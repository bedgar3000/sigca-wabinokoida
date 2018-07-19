<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
##############################################################################/
##	PROCESOS (NUEVO, MODIFICAR, ELIMINAR)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$CodDependencia = codigo('mastdependencias', 'CodDependencia', 4);
		##	inserto
		$sql = "INSERT INTO mastdependencias
				SET
					CodDependencia = '".$CodDependencia."',
					CodOrganismo = '".$CodOrganismo."',
					Dependencia = '".$Dependencia."',
					Telefono1 = '".$Telefono1."',
					Telefono2 = '".$Telefono2."',
					Extencion1 = '".$Extencion1."',
					Extencion2 = '".$Extencion2."',
					CodPersona = '".$CodPersona."',
					CodInterno = '".$CodInterno."',
					FlagControlFiscal = '".$FlagControlFiscal."',
					FlagPrincipal = '".$FlagPrincipal."',
					Nivel = '".$Nivel."',
					CodCargo = '".$CodCargo."',
					Mision = '".$Mision."',
					Vision = '".$Vision."',
					EntidadPadre = '".$EntidadPadre."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	matriz
		for ($i=0; $i < count($matriz_Columna); $i++) {
			##	valido
			if (!trim($matriz_Columna[$i]) || !trim($matriz_Descripcion[$i])) die("Debe llenar los campos obligatorios en la Ficha FODA");
			##	inserto
			$CodMatriz = codigo('po_matrizfoda', 'CodMatriz', 4);
			$sql = "INSERT INTO po_matrizfoda
					SET
						CodMatriz = '".$CodMatriz."',
						CodDependencia = '".$CodDependencia."',
						Columna = '".$matriz_Columna[$i]."',
						Descripcion = '".$matriz_Descripcion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	designacion
		for ($i=0; $i < count($designacion_Designacion); $i++) {
			##	valido
			if (!trim($designacion_Designacion[$i])) die("Debe llenar los campos obligatorios en la Ficha Designaciones Especiales");
			elseif (!validateDate($designacion_FechaDesde[$i],'d-m-Y') && trim($designacion_FechaDesde[$i])) die("Formato Fecha Desde en la Ficha Designaciones Especiales");
			elseif (!validateDate($designacion_FechaHasta[$i],'d-m-Y') && trim($designacion_FechaHasta[$i])) die("Formato Fecha Desde en la Ficha Designaciones Especiales");
			##	inserto
			$CodDesignacion = codigo('mastpersonasdesignacion', 'CodDesignacion', 4, ['CodPersona','CodDependencia'], [$CodPersona, $CodDependencia]);
			$sql = "INSERT INTO mastpersonasdesignacion
					SET
						CodPersona = '".$CodPersona."',
						CodDependencia = '".$CodDependencia."',
						CodDesignacion = '".$CodDesignacion."',
						Designacion = '".$designacion_Designacion[$i]."',
						Descripcion = '".$designacion_Descripcion[$i]."',
						FechaDesde = '".formatFechaAMD($designacion_FechaDesde[$i])."',
						FechaHasta = '".formatFechaAMD($designacion_FechaHasta[$i])."',
						FlagDesignacionEspecial = '".($designacion_FlagDesignacionEspecial[$i]=='S'?'S':'N')."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		##	actualizar
		$sql = "UPDATE mastdependencias
				SET
					CodOrganismo = '".$CodOrganismo."',
					Dependencia = '".$Dependencia."',
					Telefono1 = '".$Telefono1."',
					Telefono2 = '".$Telefono2."',
					Extencion1 = '".$Extencion1."',
					Extencion2 = '".$Extencion2."',
					CodPersona = '".$CodPersona."',
					CodInterno = '".$CodInterno."',
					FlagControlFiscal = '".$FlagControlFiscal."',
					FlagPrincipal = '".$FlagPrincipal."',
					Nivel = '".$Nivel."',
					CodCargo = '".$CodCargo."',
					Mision = '".$Mision."',
					Vision = '".$Vision."',
					EntidadPadre = '".$EntidadPadre."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodDependencia = '".$CodDependencia."'";
		execute($sql);
		##	matriz
		$sql = "DELETE FROM po_matrizfoda WHERE CodDependencia = '".$CodDependencia."'";
		execute($sql);
		for ($i=0; $i < count($matriz_Columna); $i++) {
			##	valido
			if (!trim($matriz_Columna[$i]) || !trim($matriz_Descripcion[$i])) die("Debe llenar los campos obligatorios en la Ficha FODA");
			##	inserto
			$CodMatriz = codigo('po_matrizfoda', 'CodMatriz', 4);
			$sql = "INSERT INTO po_matrizfoda
					SET
						CodMatriz = '".$CodMatriz."',
						CodDependencia = '".$CodDependencia."',
						Columna = '".$matriz_Columna[$i]."',
						Descripcion = '".$matriz_Descripcion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	designacion
		$sql = "DELETE FROM mastpersonasdesignacion WHERE CodPersona = '".$CodPersona."' AND CodDependencia = '".$CodDependencia."'";
		execute($sql);
		for ($i=0; $i < count($designacion_Designacion); $i++) {
			##	valido
			if (!trim($designacion_Designacion[$i])) die("Debe llenar los campos obligatorios en la Ficha Designaciones Especiales");
			elseif (!validateDate($designacion_FechaDesde[$i],'d-m-Y') && trim($designacion_FechaDesde[$i])) die("Formato Fecha Desde en la Ficha Designaciones Especiales");
			elseif (!validateDate($designacion_FechaHasta[$i],'d-m-Y') && trim($designacion_FechaHasta[$i])) die("Formato Fecha Desde en la Ficha Designaciones Especiales");
			##	inserto
			$CodDesignacion = codigo('mastpersonasdesignacion', 'CodDesignacion', 4, ['CodPersona','CodDependencia'], [$CodPersona, $CodDependencia]);
			$sql = "INSERT INTO mastpersonasdesignacion
					SET
						CodPersona = '".$CodPersona."',
						CodDependencia = '".$CodDependencia."',
						CodDesignacion = '".$CodDesignacion."',
						Designacion = '".$designacion_Designacion[$i]."',
						Descripcion = '".$designacion_Descripcion[$i]."',
						FechaDesde = '".formatFechaAMD($designacion_FechaDesde[$i])."',
						FechaHasta = '".formatFechaAMD($designacion_FechaHasta[$i])."',
						FlagDesignacionEspecial = '".($designacion_FlagDesignacionEspecial[$i]=='S'?'S':'N')."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		$sql = "DELETE FROM mastdependencias WHERE CodDependencia = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	insertar linea
	if ($accion == "matriz_insertar") {
		$id = $nro_detalle;
		?>
            <tr class="trListaBody" onclick="clk($(this), 'matriz', 'matriz_<?=$id?>');" id="matriz_<?=$id?>">
                <th><?=$id?></th>
                <td>
                    <select name="matriz_Columna[]" class="cell">
                    	<option value="">&nbsp;</option>
                    	<?=loadSelectValores('columna-foda')?>
                    </select>
                </td>
                <td>
                    <textarea name="matriz_Descripcion[]" class="cell" style="height:30px;"></textarea>
                </td>
            </tr>
        <?php
	}
	//	insertar linea
	elseif ($accion == "designacion_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'designacion', 'designacion_<?=$id?>');" id="designacion_<?=$id?>">
            <th><?=$id?></th>
            <td>
                <textarea name="designacion_Designacion[]" class="cell" style="height:16px;"></textarea>
            </td>
			<td>
				<input type="text" name="designacion_Descripcion[]" class="cell" maxlength="50" />
			</td>
            <td>
            	<input type="text" name="designacion_FechaDesde[]" class="cell datepicker" style="text-align:center;" />
            </td>
            <td>
            	<input type="text" name="designacion_FechaHasta[]" class="cell datepicker" style="text-align:center;" />
            </td>
			<td align="center">
				<input type="checkbox" name="designacion_FlagDesignacionEspecial[]" value="S" />
			</td>
        </tr>
        <?php
	}

}
?>