<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//
list($CodOrganismo, $Periodo, $Voucher, $CodContabilidad) = split("[_]", $registro);
//	datos generales
$sql = "SELECT
			v.CodOrganismo,
			v.ComentariosVoucher,
			v.FechaVoucher,
			v.PreparadoPor,
			v.FechaPreparacion,
			v.CodVoucher,
			v.NroVoucher,
			v.AprobadoPor,
			v.FechaAprobacion,
			v.CodLibroCont,
			v.CodContabilidad,
			p1.NomCompleto AS NomPreparadoPor,
			p2.NomCompleto AS NomAprobadoPor
		FROM
			ac_vouchermast v
			LEFT JOIN mastpersonas p1 ON (p1.CodPersona = v.PreparadoPor)
			LEFT JOIN mastpersonas p2 ON (p2.CodPersona = v.AprobadoPor)
		WHERE
			v.CodOrganismo = '".$CodOrganismo."' AND
			v.Periodo = '".$Periodo."' AND
			v.Voucher = '".$Voucher."' AND
			v.CodContabilidad = '".$CodContabilidad."'";
$query = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
//
if ($accion == "ver") $disabled_ver = "disabled";
?>
<form name="frmentrada" id="frmentrada" method="POST" onsubmit="return false;">
<table align="center">
	<tr>
    	<td valign="top">
            <table width="400" class="tblBotones">
                <tr><td align="right">&nbsp;</td></tr>
            </table>
            
            <table><tr><td><div style="overflow:scroll; width:400px; height:100px;">
            <table width="500" class="tblLista">
            	<thead>
                <tr>
                    <th width="75" scope="col">Periodo</th>
                    <th width="75" scope="col">Voucher</th>
                    <th width="75" scope="col">Fecha</th>
                    <th width="75" scope="col">Status</th>
                    <th scope="col">Organismo</th>
                </tr>
                </thead>
                
                <tbody id="lista1">
                </tbody>
            </table>
            </div></td></tr></table>
        </td>
        
        <td valign="top">
            <table width="550" class="tblBotones">
                <tr><td align="right">&nbsp;</td></tr>
            </table>
            
            <table><tr><td><div style="overflow:scroll; width:550px; height:100px;">
            <table width="700" class="tblLista">
            	<thead>
                <tr>
                    <th width="50" scope="col">Linea</th>
                    <th scope="col">Errores Encontrados</th>
                    <th width="75" scope="col">Periodo</th>
                    <th width="75" scope="col">Voucher</th>
                    <th width="75" scope="col">Organismo</th>
                </tr>
                </thead>
                
                <tbody id="lista_errores">
                </tbody>
            </table>
            </div></td></tr></table>
        </td>
    </tr>
    
    <tr>
    	<td colspan="2">
            <table width="960" class="tblForm">
                <tr>
                    <td class="tagForm" width="125">* Organismo:</td>
                    <td>
                        <select id="CodOrganismo" style="width:300px;" <?=$disabled_ver?>>
                            <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 1)?>
                        </select>
                    </td>
                    <td class="tagForm">Descripci&oacute;n:</td>
                    <td><input type="text" id="ComentariosVoucher" style="width:305px;" value="<?=htmlentities($field['ComentariosVoucher'])?>" <?=$disabled_ver?> /></td>
                </tr>
                <tr>
                    <td class="tagForm">* Fecha:</td>
                    <td><input type="text" id="FechaVoucher" value="<?=formatFechaDMA($field['FechaVoucher'])?>" style="width:75px;" disabled /></td>
                    <td class="tagForm">Preparado Por:</td>
                    <td>
                        <input type="hidden" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
                        <input type="text" style="width:235px;" value="<?=htmlentities($field['NomPreparadoPor'])?>" disabled />
                        <input type="text" id="FechaPreparacion" style="width:60px;" value="<?=formatFechaDMA($field['FechaPreparacion'])?>" disabled />
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">Voucher:</td>
                    <td>
						<input type="text" id="Periodo" value="<?=$field['Periodo']?>" style="width:50px;" <?=$disabled_ver?> />
                        <select id="CodVoucher" <?=$disabled_ver?>>
                            <?=loadSelect("ac_voucher", "CodVoucher", "CodVoucher", $field['CodVoucher'], 1)?>
                        </select>
                        <input type="text" id="NroVoucher" value="<?=$field['NroVoucher']?>" style="width:50px;" disabled="disabled" />
                    </td>
                    <td class="tagForm">Aprobado Por:</td>
                    <td>
                        <input type="hidden" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
                        <input type="text" style="width:235px;" value="<?=htmlentities($field['NomAprobadoPor'])?>" disabled />
                        <input type="text" id="FechaAprobacion" style="width:60px;" value="<?=formatFechaDMA($field['FechaAprobacion'])?>" disabled />
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">* Libro Contable:</td>
                    <td>
                        <select id="CodLibroCont" style="width:150px;" <?=$disabled_ver?>>
                            <?=loadSelect("ac_librocontable", "CodLibroCont", "Descripcion", $field['CodLibroCont'], 0)?>
                        </select>
                    </td>
                    <td class="tagForm">* Contabilidad:</td>
                    <td>
                        <select id="CodContabilidad" style="width:150px;" <?=$disabled_ver?>>
                            <?=loadSelect("ac_contabilidades", "CodContabilidad", "Descripcion", $field['CodContabilidad'], 1)?>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
	<tr>
    	<td valign="top" colspan="2">
            <table width="960" class="tblBotones">
                <tr>
                    <td align="right">
                        <input type="submit" class="btLista" value="Aceptar" id="btAceptar" <?=$disabled_ver?> />
                        <input type="button" class="btLista" value="Rechazar" <?=$disabled_ver?> onclick="parent.$.prettyPhoto.close();" />
                    </td>

                </tr>
            </table>
            
            <table><tr><td><div style="overflow:scroll; width:960px; height:175px;">
            <table width="1100" class="tblLista">
            	<thead>
                <tr>
                    <th scope="col" width="30">#</th>
                    <th scope="col" width="110">Cuenta</th>
                    <th scope="col">Descripci&oacute;n</th>
                    <th scope="col" width="125">Monto</th>
                    <th scope="col" width="75">Persona</th>
                    <th scope="col" colspan="2">Documento</th>
                    <th scope="col" width="75">C.Costo</th>
                    <th scope="col" width="75">Fecha</th>
                </tr>
                </thead>
                
                <tbody>
                <?php
				$sql = "SELECT
							Linea,
							CodCuenta,
							Descripcion,
							MontoVoucher,
							CodPersona,
							ReferenciaTipoDocumento,
							ReferenciaNroDocumento,
							CodCentroCosto,
							FechaVoucher
						FROM ac_voucherdet vd
						WHERE
							CodOrganismo = '".$CodOrganismo."' AND
							Periodo = '".$Periodo."' AND
							Voucher = '".$Voucher."' AND
							CodContabilidad = '".$CodContabilidad."'
						ORDER BY MontoVoucher, CodCuenta";
				$query_det = mysql_query($sql) or die ($sql.mysql_error());
				while($field_det = mysql_fetch_array($query_det)) {
					if ($field_det['MontoVoucher'] < 0) {
						$style = "color:red;";
						$Debitos += $field_det['MontoVoucher'];
					} else {
						$style = "";
						$Creditos += $field_det['MontoVoucher'];
					}
					?>
					<tr class="trListaBody">
                    	<td>
                        	<input type="text" name="Linea" value="<?=++$Linea?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodCuenta" value="<?=$field_det['CodCuenta']?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="Descripcion" value="<?=htmlentities($field_det['Descripcion'])?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="MontoVoucher" value="<?=number_format($field_det['MontoVoucher'], 2, ',', '.')?>" class="cell2" style="text-align:right; <?=$style?>" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodPersona" value="<?=$field_det['CodPersona']?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td width="25">
                        	<input type="text" name="ReferenciaTipoDocumento" value="<?=$field_det['ReferenciaTipoDocumento']?>" class="cell2" readonly />
                        </td>
                    	<td width="125">
                        	<input type="text" name="ReferenciaNroDocumento" value="<?=$field_det['ReferenciaNroDocumento']?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodCentroCosto" value="<?=$field_det['CodCentroCosto']?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="FechaVoucher" value="<?=formatFechaDMA($field_det['FechaVoucher'])?>" class="cell2" style="width:75px; text-align:center;" readonly />
                        </td>
					</tr>
					<?php
				}
				?>
                </tbody>
            </table>
            </div></td></tr></table>
            
            <table>
                <tr>
                    <th scope="col" width="140">Nro Lineas: <input type="text" id="Lineas" value="<?=$Linea?>" class="cell2" style="text-align:center; font-weight:bold; font-size:12px; width:20px;" readonly /></th>
                    <th scope="col" width="75">&nbsp;</th>
                    <th scope="col" width="150">&nbsp;</th>
                    <th scope="col" width="75">Total:</th>
                    <th scope="col" width="125">
                    	<input type="text" id="Creditos" value="<?=number_format($Creditos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px;" readonly />
                    </th>
                    <th scope="col" width="125">
                    	<input type="text" id="Debitos" value="<?=number_format($Debitos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px; color:red;" readonly />
					</th>
                    <th scope="col" width="125">&nbsp;</th>
                </tr>
			</table>
            
        </td>
    </tr>
</table>
</form>

<!-- JS	-->
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	//validarErroresVoucher();
});
</script>