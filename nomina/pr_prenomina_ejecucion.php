<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fCodTipoNom = $_SESSION["NOMINA_ACTUAL"];
	$fPeriodo = "$AnioActual-$MesActual";
}
if ($fEstado == "") $filtro.=" AND (e.Estado = 'A')"; else $cEstado = "checked";
//	------------------------------------
$_titulo = "Control de Procesos (Pre-N&oacute;mina)";
$_width = 900;
$_sufijo = "prenomina_ejecucion";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="PreNomina" id="PreNomina" value="S" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="loadSelect($('#fCodTipoNom'), 'tabla=loadControlNominasPrenomina&CodOrganismo='+this.value, 1, destinos=['fCodTipoNom', 'fPeriodo', 'fCodTipoProceso']);">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">N&oacute;mina:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;" onChange="loadSelect($('#fPeriodo'), 'tabla=loadControlPeriodosPrenomina&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+this.value, 1, destinos=['fPeriodo', 'fCodTipoProceso']);">
				<?=loadControlNominasPrenomina($fCodOrganismo, $fCodTipoNom)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Periodo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
            <select name="fPeriodo" id="fPeriodo" style="width:75px;" onChange="loadSelect($('#fCodTipoProceso'), 'tabla=loadControlProcesosPrenomina&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+$('#fCodTipoNom').val()+'&Periodo='+this.value, 1, destinos=['fCodTipoProceso']);">
            	<option value="">&nbsp;</option>
				<?=loadControlPeriodosPrenomina($fCodOrganismo, $fCodTipoNom, $fPeriodo)?>
            </select>
		</td>
		<td align="right">Proceso:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodTipoProceso" id="fCodTipoProceso" style="width:250px;">
            	<option value="">&nbsp;</option>
				<?=loadControlProcesosPrenomina($fCodOrganismo, $fCodTipoNom, $fPeriodo, $fCodTipoProceso)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
            <input type="checkbox" name="fEstado" id="fEstado" value="A" <?=$cEstado?> /> Mostrar cesados
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center>
</form><br />

<center>
<table align="center" cellpadding="0" cellspacing="0" width="<?=$_width?>">
	<tr>
    	<td>
        	<form name="frm_disponibles" id="frm_disponibles">
            <table width="430" class="tblBotones">
                <tr>
                    <td align="right" style="height:25px;"></td>
                </tr>
            </table>
            <div style="overflow:scroll; width:430px; height:350px;">
            <table width="1600" class="tblLista">
                <thead>
                <tr>
                    <th scope="col" width="65">Nro. Documento</th>
                    <th scope="col" align="left">Empleado</th>
                    <th scope="col" width="550" align="left">Dependencia</th>
                    <th scope="col" width="550" align="left">Cargo</th>
                    <th scope="col" width="65">Estado</th>
                </tr>
                </thead>
                
                <tbody id="lista_disponibles">
                <?php
                //	consulto lista
				if ($fCodTipoProceso != "") {
					$sql = "SELECT
								e.CodEmpleado,
								e.Estado,
								p.CodPersona,
								p.NomCompleto,
								p.Ndocumento,
								d.Dependencia,
								pu.DescripCargo
							FROM
								mastpersonas p
								INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
								INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
								INNER JOIN rh_puestos pu ON (pu.CodCargo = e.CodCargo)
							WHERE
								e.CodOrganismo = '".$fCodOrganismo."' AND
								e.CodTipoNom = '".$fCodTipoNom."' AND
								p.Estado = 'A' AND
								p.CodPersona NOT IN (SELECT CodPersona
													 FROM pr_tiponominaempleadoprenomina
													 WHERE
														CodOrganismo = '".$fCodOrganismo."' AND
														CodTipoNom = '".$fCodTipoNom."' AND
														Periodo = '".$fPeriodo."' AND
														CodTipoProceso = '".$fCodTipoProceso."')
								$filtro
							ORDER BY LENGTH(Ndocumento), Ndocumento";
					$query_disponibles = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
					$rows_disponibles = mysql_num_rows($query_disponibles);	$i=0;
					while ($field_disponibles = mysql_fetch_array($query_disponibles)) {
						$id = "$field_disponibles[CodPersona]";
						$tr = "tr_$field_disponibles[CodPersona]";
						?>
						<tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');" id="<?=$tr?>">
							<td align="right">
				            	<input type="checkbox" name="personas" id="<?=$id?>" value="<?=$id?>" style="display:none;" />
								<?=number_format($field_disponibles['Ndocumento'], 0, '', '.')?>
                            </td>
							<td><?=htmlentities($field_disponibles['NomCompleto'])?></td>
							<td><?=htmlentities($field_disponibles['Dependencia'])?></td>
							<td><?=htmlentities($field_disponibles['DescripCargo'])?></td>
							<td align="center"><?=printValoresGeneral("ESTADO", $field_disponibles['Estado'])?></td>
						</tr>
						<?php
					}
				}
                ?>
                </tbody>
            </table>
            </div>
            </form>
        </td>
        
        <td width="100" valign="middle" align="center">
        	<input type="button" value="&gt;" style="width:30px; cursor:pointer;" onclick="control_agregar('>');" />
            <br />
            <br />
        	<input type="button" value="&lt;" style="width:30px; cursor:pointer;" onclick="control_quitar('<');" />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
        	<input type="button" value="&gt;&gt;" style="width:30px; cursor:pointer;" onclick="control_agregar('>>');" />
            <br />
            <br />
        	<input type="button" value="&lt;&lt;" style="width:30px; cursor:pointer;" onclick="control_quitar('<<');" />
        </td>
        
        <td>
        	<form name="frm_aprobados" id="frm_aprobados">
            <table width="430" class="tblBotones">
                <tr>
                    <td>
                        <input type="button" value="Payrrol" style="width:75px;" onclick="procesos_control_payroll('payroll');" /> |
                        <input type="button" value="N&oacute;mina" style="width:75px;" onclick="procesos_control_payroll('nomina');" />
                    </td>
                    <td align="right">
                        <input type="button" value="Generar" style="width:75px;" onclick="procesos_control_ejecucion();" />
                    </td>

                </tr>
            </table>
            <div style="overflow:scroll; width:430px; height:350px;">
            <table width="1600" class="tblLista">
                <thead>
                <tr>
                    <th scope="col" width="65">Nro. Documento</th>
                    <th scope="col" align="left">Empleado</th>
                    <th scope="col" width="550" align="left">Dependencia</th>
                    <th scope="col" width="550" align="left">Cargo</th>
                    <th scope="col" width="65">Estado</th>
                </tr>
                </thead>
                
                <tbody id="lista_aprobados">
                <?php
                //	consulto lista
				$sql = "SELECT
							e.CodEmpleado,
							e.Estado,
							p.CodPersona,
							p.NomCompleto,
							p.Ndocumento,
							d.Dependencia,
							pu.DescripCargo
						FROM
							pr_tiponominaempleadoprenomina tne
							INNER JOIN mastpersonas p ON (p.CodPersona = tne.CodPersona)
							INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
							INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
							INNER JOIN rh_puestos pu ON (pu.CodCargo = e.CodCargo)
						WHERE
							tne.CodOrganismo = '".$fCodOrganismo."' AND
							tne.CodTipoNom = '".$fCodTipoNom."' AND
							tne.Periodo = '".$fPeriodo."' AND
							tne.CodTipoProceso = '".$fCodTipoProceso."'
						ORDER BY LENGTH(Ndocumento), Ndocumento";
				$query_aprobados = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				$rows_aprobados = mysql_num_rows($query_aprobados);	$i=0;
				while ($field_aprobados = mysql_fetch_array($query_aprobados)) {
					$id = "$field_aprobados[CodPersona]";
					$tr = "tr_$field_aprobados[CodPersona]";
					?>
					<tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');" id="<?=$tr?>">
						<td align="right">
                            <input type="checkbox" name="personas" id="<?=$id?>" value="<?=$id?>" style="display:none;" />
                            <?=number_format($field_aprobados['Ndocumento'], 0, '', '.')?>
                        </td>
                        <td><?=htmlentities($field_aprobados['NomCompleto'])?></td>
                        <td><?=htmlentities($field_aprobados['Dependencia'])?></td>
                        <td><?=htmlentities($field_aprobados['DescripCargo'])?></td>
                        <td align="center"><?=printValoresGeneral("ESTADO", $field_aprobados['Estado'])?></td>
					</tr>
					<?php
				}
                ?>
                </tbody>
            </table>
            </div>
            </form>
        </td>
    </tr>
    <tr>
        <td style="padding:5px;">
            <a class="link" href="#" onclick="selTodos('disponibles', 'personas');">Todos</a> |
            <a class="link" href="#" onclick="selNinguno('disponibles', 'personas');">Ninguno</a>
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Nro. Disponibles: &nbsp; <span style="font-weight:bold;" id="rows_disponibles"><?=$rows_disponibles?></span>
        </td>
        <td>&nbsp;</td>
        <td style="padding:5px;">
            <a class="link" href="#" onclick="selTodos('aprobados', 'personas');">Todos</a> |
            <a class="link" href="#" onclick="selNinguno('aprobados', 'personas');">Ninguno</a>
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Nro. Aprobados: &nbsp; <span style="font-weight:bold;" id="rows_aprobados"><?=$rows_aprobados?></span>
        </td>
    </tr>
</table>
</center>

<div class="gallery clearfix">
    <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_reporte"></a>
</div>