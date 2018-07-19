<?php
extract($_GET);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>[S.I.G.C.A]</title>
	<link type="image/x-icon" rel="shortcut icon" href="imagenes/icono.ico" />
	<link type="text/css" rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
	<script type="text/javascript" src="js/jquery-1.7.min.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/funciones.js" charset="utf-8"></script>
	<script type="text/javascript" charset="utf-8">
		$(function() {
			var Usuario = $("#Usuario"),
				Clave = $("#Clave"),
				CodOrganismo = $("#CodOrganismo"),
				modulo = $("#modulo"),
				allFields = $([]).add(Usuario).add(Clave).add(CodOrganismo),
				tips = $(".validateTips"),
				IdUsuario = $("#IdUsuario"),
				ClaveNueva = $("#ClaveNueva"),
				ClaveConfirmar = $("#ClaveConfirmar"),
				allFields2 = $([]).add(ClaveNueva).add(ClaveConfirmar),
				tips2 = $(".validateTips2");
			
			$("#dialog-form").dialog({
				autoOpen: false,
				modal: true
			});
			
			$("#dialog-clave").dialog({
				autoOpen: false,
				modal: true
			});
			
			$(".link").click(function() {
				modulo.val($(this).attr('id'));
				dialog_form($(this).attr('id'), $(this).attr('title'));
			});
			
			allFields.change(function() {
				allFields.removeClass("ui-state-error");
			});
				
			function dialog_form(modulo, title) {
				$("#dialog-form").dialog({
					autoOpen: false,
					height: 200,
					width: 400,
					modal: true,
					title: title,
					buttons: {
						"Iniciar Sesión": function() {
							allFields.removeClass("ui-state-error");
							if (Usuario.val().trim() == "") {
								Usuario.addClass("ui-state-error");
								updateTips("Debe ingresar el Usuario");
							}
							else if (Clave.val().trim() == "") {
								Clave.addClass("ui-state-error");
								updateTips("Debe ingresar la Clave");
							}
							else if (CodOrganismo.val() == "") {
								CodOrganismo.addClass("ui-state-error");
								updateTips("Debe seleccionar el Organismo");
							}
							else {
								$.ajax({
									type: "POST",
									url: "fphp.php",
									data: "accion=VALIDAR&Usuario="+Usuario.val()+"&Clave="+Clave.val()+"&modulo="+$('#modulo').val()+"&CodOrganismo="+$('#CodOrganismo').val(),
									async: false,
									success: function(resp) {
										var partes = resp.split("|");
										if (partes[0].trim() != "") {
											updateTips(partes[0].trim());
										}
										else if (partes[1].trim() != "") {
											$('#IdUsuario').val($('#Usuario').val());
											updateTips(partes[1].trim());
											$("#dialog-form").dialog("close");
											dialog_clave();
										}
										else {
											window.open($('#modulo').val(), 'modulo'+$('#modulo').val(), 'toolbar=no, menubar=no, location=no, scrollbars=yes, height=1024, width=1280, left=0, top=0, resizable=yes');
											$("#dialog-form").dialog("close");
										}
									}
								});
							}
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					},
					close: function() {
						allFields.val("").empty().removeClass("ui-state-error");
						updateTips("Iniciar Sesión");
					}
				});
				$("#dialog-form").dialog("open");
			}
				
			function dialog_clave() {
				$("#dialog-clave").dialog({
					autoOpen: false,
					height: 315,
					width: 300,
					modal: true,
					title: 'Cambio de Clave',
					buttons: {
						"Aceptar": function() {
							allFields2.removeClass("ui-state-error");
							if (ClaveNueva.val().trim() == "") {
								ClaveNueva.addClass("ui-state-error");
								updateTips2("Debe ingresar la Clave");
							}
							else if (ClaveNueva.val() != ClaveConfirmar.val()) {
								ClaveConfirmar.addClass("ui-state-error");
								updateTips2("Confirmación de Clave Incorrecta");
							}
							else if (contarNumericoEntero($('#ClaveNueva').val()) < 3) {
								updateTips2("Debe contener por lo menos tres dígitos numéricos");
							}
							else if (contarLetras($('#ClaveNueva').val()) < 3) {
								updateTips2("Debe contener por lo menos tres dígitos alfabéticos");
							}
							else if ($('#ClaveNueva').val().length < 6) {
								updateTips2("Debe contener por lo menos 6 dígitos");
							}
							else {
								$.ajax({
									type: "POST",
									url: "fphp.php",
									data: "accion=cambiarClave&ClaveNueva="+ClaveNueva.val()+"&ClaveConfirmar="+ClaveConfirmar.val()+"&Usuario="+IdUsuario.val(),
									async: false,
									success: function(resp) {
										if (resp.trim() != "") updateTips2(resp.trim());
										else {
											//window.open($('#modulo').val(), 'modulo'+$('#modulo').val(), 'toolbar=no, menubar=no, location=no, scrollbars=yes, height=1024, width=1280, left=0, top=0, resizable=yes');
											//$("#dialog-clave").dialog("close");
											location.href = "index.php";
										}
									}
								});
							}
						},
						"Cancelar": function() {
							$(this).dialog("close");
						}
					},
					close: function() {
						allFields2.val("").empty().removeClass("ui-state-error");
						updateTips2("¡Clave Vencida!. Ingrese la nueva Clave.");
					}
				});
				$("#dialog-clave").dialog("open");
			}
			
			function updateTips(t) {
				tips
					.text(t)
					.addClass("ui-state-error");
			}
			
			function updateTips2(t) {
				tips2
					.text(t)
					.addClass("ui-state-error");
			}
		});

		function getOrganismos() {
			$.ajax({
				type: "POST",
				url: "fphp.php",
				data: "accion=getOrganismos&Usuario="+$('#Usuario').val()+"&modulo="+$('#modulo').val(),
				async: false,
				success: function(resp) {
					$('#CodOrganismo').empty().append(resp);
				}
			});
		}
		<?php
		if (isset($cerrar)) {
			?> window.close(); <?php
		}
		?>
	</script>

	<style type="text/css">
		input.text, select { margin-bottom:1px; width:95%; }
		input.text { padding: 3.5px; }
		select { padding: 2.5px; }
		.ui-dialog .ui-state-error { padding: .3em; }
		.validateTips, .validateTips2 { border: 1px solid transparent; padding: 0.3em; }
		
		body {
			margin:0;
			padding:0;
			background: #555555 url(imagenes/wideimage2.jpg) no-repeat;
		}
		body { font-size: 62.5%; }

		.titulo {
			filter:alpha(opacity=50); 
			opacity:0.5;
			margin:10px 50px;
			background-color: #1A252B;

			text-shadow: 
				0px 0px 20px rgba(0,0,0,1), 
				0px 0px 20px rgba(0,0,0,1), 
				0px 0px 20px rgba(0,0,0,1);

			font-weight: bold;
			color: #ddd;
			font-size: 24px;

			-webkit-box-shadow: 10px 10px 10px 10px rgba(0,0,0,0.75);
			-moz-box-shadow: 10px 10px 10px 10px rgba(0,0,0,0.75);
			box-shadow: 10px 10px 10px 10px rgba(0,0,0,0.75);

			border-radius: 5px 5px 5px 5px;
			-moz-border-radius: 5px 5px 5px 5px;
			-webkit-border-radius: 5px 5px 5px 5px;

			height: 30px;
			line-height: 30px;
			padding: 10px 5px;
		}

		.titulo .sub {
			font-size: 12px;
		}

		/* COLUMNAS */
		/* Control del flotado y margen */
		.col2  { float: left; margin: 50px 50px 0 0; overflow: hidden; }
		/* Importante: quita el margen extra a la ultima columna de cada fila para encajar */
		.final { margin-right: 0; }
		/* Usar estas clases para determinar los anchos y cantidad de columnas. Siempre en relacion al class .contenedor */
		.col2  { 
			width: 110px; 
			height: 110px;

			-webkit-box-shadow: 5px 5px 5px 5px rgba(0,0,0,0.25);
			-moz-box-shadow: 5px 5px 5px 5px rgba(0,0,0,0.25);
			box-shadow: 5px 5px 5px 5px rgba(0,0,0,0.25);

			border-radius: 5px 5px 5px 5px;
			-moz-border-radius: 5px 5px 5px 5px;
			-webkit-border-radius: 5px 5px 5px 5px;
			border: 3px solid #E6CB96;
		}
			.col2:hover { 
				-webkit-box-shadow: 5px 5px 5px 5px rgba(0,0,0,0);
				-moz-box-shadow: 5px 5px 5px 5px rgba(0,0,0,0);
				box-shadow: 5px 5px 5px 5px rgba(0,0,0,0);
				border-color: #F9FEC9;
				text-shadow: 
					0px 0px 20px rgba(0,0,0,0), 
					0px 0px 20px rgba(0,0,0,0), 
					0px 0px 20px rgba(0,0,0,0);
			}
			.col2:active {
				margin: 48px 48px 2px 2px;
			}
		.link { text-decoration: none; }
		.texto {
			position:relative;
			font-weight: bold;
			font-size: 14px;
			margin: -20px 0px;
			height: 20px;
			line-height: 18px;
			text-align: center;
			background-color: #E6CB96;
			width: 100%;
			color: #000;
			text-shadow: 
				0px 0px 20px rgba(0,0,0,0.25), 
				0px 0px 20px rgba(0,0,0,0.25), 
				0px 0px 20px rgba(0,0,0,0.25);
		}
		/*--------------------------------------------------------- FIN COLUMNAS*/
	</style>
</head>

<body>
	<div id="dialog-form">
		<form name="frmlogin" id="frmlogin" method="post" action="index.php" autocomplete='off'>
		<input type="hidden" name="modulo" id="modulo" />
	    <table width="100%">
	    	<tr>
	        	<td rowspan="5" align="center" valign="middle">
	            	<img src="imagenes/login.png" height="96" />
	            </td>
	        </tr>
	    	<tr>
	        	<td colspan="2">
	            	<div class="validateTips ui-widget-content ui-corner-all"><strong>Iniciar Sesi&oacute;n</strong></div>
	            </td>
	        </tr>
	    	<tr>
	        	<td width="80"><label for="Usuario">Usuario:</label></td>
	            <td><input type="text" name="Usuario" id="Usuario" style="width:175px;" class="text ui-widget-content ui-corner-all" onchange="getOrganismos();" /></td>
	        </tr>
	    	<tr>
	        	<td><label for="Clave">Clave</label></td>
	            <td><input type="password" name="Clave" id="Clave" style="width:175px;" class="text ui-widget-content ui-corner-all" /></td>
	        </tr>
	    	<tr>
	        	<td><label for="CodOrganismo">Organismo</label></td>
	            <td>
	                <select name="CodOrganismo" id="CodOrganismo" style="width:185px;" class="ui-widget-content ui-corner-all">
	                    <option value="">&nbsp;</option>
	                </select>
	            </td>
	        </tr>
	    </table>
		</form>
	</div>

	<div id="dialog-clave">
		<form name="frmclave" id="frmclave" method="post" action="index.php" autocomplete='off'>
	    <table width="100%">
	    	<tr>
	        	<td colspan="2">
	            	<div class="validateTips2 ui-widget-content ui-corner-all"><strong>¡Clave Vencida!. Ingrese la nueva Clave.</strong></div>
	            </td>
	        </tr>
	    	<tr>
	        	<td width="75"><label for="IdUsuario">Usuario</label></td>
	            <td><input type="text" name="IdUsuario" id="IdUsuario" style="width:175px;" class="text ui-widget-content ui-corner-all" disabled="disabled" /></td>
	        </tr>
	    	<tr>
	        	<td><label for="ClaveNueva">Clave</label></td>
	            <td><input type="password" name="ClaveNueva" id="ClaveNueva" style="width:175px;" class="text ui-widget-content ui-corner-all" /></td>
	        </tr>
	    	<tr>
	        	<td><label for="ClaveConfirmar">Confirme</label></td>
	            <td><input type="password" name="ClaveConfirmar" id="ClaveConfirmar" style="width:175px;" class="text ui-widget-content ui-corner-all" /></td>
	        </tr>
	    </table>
		</form>
	    <br />
	    
	    <div class="ui-widget">
	        <div class="ui-state-highlight ui-corner-all">
	            <p>
	                <ul>
		                <li type="square">La clave de contener al menos 3 digitos numericos.</li>
		                <li type="square">La clave de contener al menos 3 digitos alfabeticos.</li>
		                <li type="square">La clave de contener al menos 6 digitos.</li>
	                </ul>
	            </p>
	        </div>
	    </div>
	</div>

	<div class="titulo">
		SIGCA
		<span class="sub">Sistema Integral de Gesti&oacute;n y Control Administrativo</span>
	</div>

	<table style="margin:10px 50px;">
		<tr>
			<td>
		    	<div class="col2">
		            <a href="javascript:;" class="link" id="rh" title="M&oacute;dulo de Recursos Humanos">
		            	<img src="imagenes/menu_rrhh.jpg" alt="Entrar a Recursos Humanos" width="110" height="110" style="border-color:#999999" />
		            	<div class="texto">
		            		R. Humanos
		            	</div>
		            </a>
		        </div>
		    	<div class="col2">
		            <a href="javascript:;" class="link" id="nomina" title="M&oacute;dulo de N&oacute;mina">
		                <img src="imagenes/foto_rrhh.jpg" alt="Entrar a N&oacute;mina" width="110" height="110" style="border-color:#999999" />
		            	<div class="texto">
		            		N&oacute;mina
		            	</div>
		            </a>
		        </div>
		    	<div class="col2">
		            <a href="javascript:;" class="link" id="lg" title="M&oacute;dulo de Log&iacute;stica">
		                <img src="imagenes/menu_lg.jpg" alt="Entrar a Log&iacute;stica" width="110" height="110" style="border-color:#999999" />
		            	<div class="texto">
		            		Log&iacute;stica
		            	</div>
		            </a>
		        </div>
		    	<div class="col2">
		            <a href="javascript:;" class="link" id="ap" title="M&oacute;dulo de Cuentas por Pagar">
		                <img src="imagenes/menu_ap.png" alt="Entrar a Cuentas por Pagar" width="110" height="110" style="border-color:#999999" />
		            	<div class="texto">
		            		Ctas. x Pagar
		            	</div>
		            </a>
		        </div>
			</td>
		</tr>

		<tr>
			<td>
		    	<div class="col2">
		            <a href="javascript:;" class="link" id="co" title="M&oacute;dulo de Ventas">
		                <img src="imagenes/menu_co.jpg" alt="Entrar a Ventas" width="110" height="110" style="border-color:#999999" />
		            	<div class="texto">
		            		Ventas
		            	</div>
		            </a>
		        </div>
		    	<div class="col2">
		            <a href="javascript:;" class="link" id="ac" title="M&oacute;dulo de Contabilidad">
		                <img src="imagenes/menu_ac.png" alt="Entrar a Contabilidad" width="110" height="110" style="border-color:#999999" />
		            	<div class="texto">
		            		Contabilidad
		            	</div>
		            </a>
		        </div>
		    	<div class="col2">
		            <a href="javascript:;" class="link" id="pv" title="M&oacute;dulo de Presupuesto">
		                <img src="imagenes/menu_pv.png" alt="Entrar a Presupuesto" width="110" height="110" style="border-color:#999999" />
		            	<div class="texto">
		            		Presupuesto
		            	</div>
		            </a>
		        </div>
		    	<div class="col2">
		            <a href="javascript:;" class="link" id="af" title="M&oacute;dulo de Activos Fijos">
		                <img src="imagenes/menu_af.png" alt="Entrar a Activos Fijos" width="110" height="110" style="border-color:#999999" />
		            	<div class="texto">
		            		Activos Fijos
		            	</div>
		            </a>
		        </div>
			</td>
		</tr>
	</table>

</body>
</html>