<script type="text/javascript">
	var _A = "PV";
	var menuItems = [
			
		["Presupuesto", , , , , , "0", , , , , ],
			["|Anteproyecto &nbsp;", , , , , , , , , , , ],
				["||Nuevo Anteproyecto &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_proyectopresupuesto_form&opcion=nuevo&action=framemain&concepto=01-0001", , , , d['01-0001'], , , , , , ],
				["||Listar Anteproyectos &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_proyectopresupuesto_lista&lista=listar&filtrar=default&concepto=01-0002", , , , d['01-0002'], , , , , , ],
				["||Revisar Anteproyectos &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_proyectopresupuesto_lista&lista=revisar&filtrar=default&concepto=01-0003", , , , d['01-0003'], , , , , , ],
				["||Aprobar Anteproyectos &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_proyectopresupuesto_lista&lista=aprobar&filtrar=default&concepto=01-0004", , , , d['01-0004'], , , , , , ],
				["||Generar Presupuesto &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_proyectopresupuesto_lista&lista=generar&filtrar=default&concepto=01-0005", , , , d['01-0005'], , , , , , ],
			
			["|Presupuesto &nbsp;", , , , , , , , , , , ],
				["||Listar Presupuesto &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_presupuesto_lista&lista=listar&filtrar=default&concepto=01-0006", , , , d['01-0006'], , , , , , ],

			["|Reformulaci&oacute;n &nbsp;", , , , , , , , , , , ],
				["||Nueva Reformulaci&oacute;n &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reformulacion_form&opcion=nuevo&action=framemain&concepto=01-0007", , , , d['01-0007'], , , , , , ],
				["||Listar Reformulaciones &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reformulacion_lista&lista=listar&filtrar=default&concepto=01-0008", , , , d['01-0008'], , , , , , ],
				["||Aprobar Reformulaciones &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reformulacion_lista&lista=aprobar&filtrar=default&concepto=01-0009", , , , d['01-0009'], , , , , , ],
			
			["|Ajustes &nbsp;", , , , , , , , , , , ],
				["||Nuevo Ajuste &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_ajustes_form&opcion=nuevo&action=framemain&concepto=01-0010", , , , d['01-0010'], , , , , , ],
				["||Listar Ajustes &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_ajustes_lista&lista=listar&filtrar=default&concepto=01-0011", , , , d['01-0011'], , , , , , ],
				["||Aprobar Ajustes &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_ajustes_lista&lista=aprobar&filtrar=default&concepto=01-0012", , , , d['01-0012'], , , , , , ],

		["Formulaci&oacute;n", , , , , , "0", , , , , ],
			["|Formulación por Metas &nbsp;", , , , , , , , , , , ],
				["||Nueva Formulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_formulacionmetas_form&opcion=nuevo&action=framemain&concepto=02-0001&FlagContinuar=true", , , , d['02-0001'], , , , , , ],
				["||Listar Formulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_formulacionmetas_lista&lista=listar&filtrar=default&concepto=02-0002", , , , d['02-0002'], , , , , , ],
				["||Aprobar Formulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_formulacionmetas_lista&lista=aprobar&filtrar=default&concepto=02-0003", , , , d['02-0003'], , , , , , ],
				["||Generar Anteproyecto &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_proyectopresupuesto_generar&filtrar=default&concepto=02-0004", , , , d['02-0004'], , , , , , ],
				["||Generar Reformulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_formulacionmetas_lista&lista=generar-reformulacion&filtrar=default&concepto=02-0005", , , , d['02-0005'], , , , , , ],

			["|Reformulación por Metas &nbsp;", , , , , , , , , , , ],
				["||Nueva Reformulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reformulacionmetas_form&opcion=nuevo&action=framemain&concepto=02-0006&FlagContinuar=true", , , , d['02-0006'], , , , , , ],
				["||Listar Reformulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reformulacionmetas_lista&lista=listar&filtrar=default&concepto=02-0007", , , , d['02-0007'], , , , , , ],
				["||Aprobar Reformulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reformulacionmetas_lista&lista=aprobar&filtrar=default&concepto=02-0008", , , , d['02-0008'], , , , , , ],
				["||Generar Presupuesto &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_proyectopresupuesto_presupuesto&filtrar=default&concepto=02-0009", , , , d['02-0009'], , , , , , ],

			["|Formulaci&oacute;n por Obras &nbsp;", , , , , , , , , , , ],
				["||Nueva Formulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_presupuestoobra_form&opcion=nuevo&action=framemain&concepto=02-0010&FlagContinuar=true", , , , d['02-0010'], , , , , , ],
				["||Listar Formulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_presupuestoobra_lista&lista=listar&filtrar=default&concepto=02-0011", , , , d['02-0011'], , , , , , ],
				["||Aprobar Formulación &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_presupuestoobra_lista&lista=aprobar&filtrar=default&concepto=02-0012", , , , d['02-0012'], , , , , , ],

		["Reportes", , , , , , "0", , , , , ],
			["|Instructivo Presupuestario # 21 &nbsp;", , , , , , , , , , , ],
				["||Presupuesto de Ingresos (F.2102)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2102&concepto=70-0001", , , , d['70-0001'], , , , , , ],
				["||Categorías Programáticas (F.2103)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2103&concepto=70-0002", , , , d['70-0002'], , , , , , ],
				["||Resumen Ppto. x Sec., Pro. y F.F (F.2104)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2104&concepto=70-0003", , , , d['70-0003'], , , , , , ],
				["||Resumen Ppto. x Par. y F.F (F.2105)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2105&concepto=70-0004", , , , d['70-0004'], , , , , , ],
				["||Resumen Ppto. x Par. y Sector (F.2106)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2106&concepto=70-0005", , , , d['70-0005'], , , , , , ],
				["||Relación RR.HH x Tipo de Cargo y Género (F.2107)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2107&concepto=70-0012", , , , d['70-0012'], , , , , , ],
				["||RR.HH x Escala de Sueldos (F.2108)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2108&concepto=70-0013", , , , d['70-0013'], , , , , , ],
				["||RR.HH x Escala de Salarios (F.2109)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2109&concepto=70-0014", , , , d['70-0014'], , , , , , ],
				["||Gastos de Inversión (F.2111)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2111&concepto=70-0006", , , , d['70-0006'], , , , , , ],
				["||Transferencias y Donaciones (F.2112)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2112&concepto=70-0011", , , , d['70-0011'], , , , , , ],
				["||Descripción del Programa (F.2113)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2113&concepto=70-0009", , , , d['70-0009'], , , , , , ],
				["||Metas del Programa (F.2114)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2114&concepto=70-0010", , , , d['70-0010'], , , , , , ],
				["||Resumen Ppto. x Sec., Pro., Par. y F.F (F.2119)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2119&concepto=70-0018", , , , d['70-0018'], , , , , , ],
				["||Relación de Obras (F.2120)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2120&concepto=70-0019", , , , d['70-0019'], , , , , , ],
				["||Relación de Obras (F.2122)", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_f2122&concepto=70-0020", , , , d['70-0020'], , , , , , ],

			["|Formulación Presupuestaria &nbsp;", , , , , , , , , , , ],
				["||Consolidado x Partidas", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_poa_consolidado_partidas&concepto=70-0007", , , , d['70-0007'], , , , , , ],
				["||Consolidado x Actividad &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_poa_consolidado_actividad&concepto=70-0008", , , , d['70-0008'], , , , , , ],
				["||Métas y Volúmenes de Trabajo &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_poa_metas&concepto=70-0015", , , , d['70-0015'], , , , , , ],
				["||Matriz Lógica &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_poa_matriz&concepto=70-0016", , , , d['70-0016'], , , , , , ],
				["||Vinculación Plan Presupuesto &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_poa_vinculacion&concepto=70-0017", , , , d['70-0017'], , , , , , ],

			["|Presupuesto &nbsp;", , , , , , , , , , , ],
				["||Ejecución Presupuestaria", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_presupuesto_ejecucion&concepto=70-0021", , , , d['70-0021'], , , , , , ],
				["||Ejecución Presupuestaria por Periodo", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_presupuesto_ejecucion_periodo&concepto=70-0021", , , , d['70-0021'], , , , , , ],
				["||Resumen Estadístico de Partidas Consolidado", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_resumen_consolidado&concepto=70-0022", , , , d['70-0022'], , , , , , ],
				["||Resumen Estadístico de Partidas Consolidado por Organismo", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_resumen_consolidado_organismo&concepto=70-0023", , , , d['70-0023'], , , , , , ],
				["||Resumen Estadístico de Partidas por Sector", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_resumen_sector&concepto=70-0024", , , , d['70-0024'], , , , , , ],
				["||Resumen Estadístico de Partidas por Actividades", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_resumen_actividades&concepto=70-0025", , , , d['70-0025'], , , , , , ],
				["||Ejecución Detallada por Partidas", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_ejecucion_detallada_partidas&concepto=70-0026", , , , d['70-0026'], , , , , , ],
				["||Movimiento Detallado por Partidas", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_movimiento_detallado_partidas&concepto=70-0026", , , , d['70-0026'], , , , , , ],

			["|Ajustes &nbsp;", , , , , , , , , , , ],
				["||Consolidado por Partidas", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_ajustes_consolidado&concepto=70-0021", , , , d['70-0021'], , , , , , ],
				["||Resumen Detallado", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_reporte_ajustes_detallado&concepto=70-0022", , , , d['70-0022'], , , , , , ],

		["Maestros", , , , , , "0", , , , , ],
			["|Del Sistema SIA", , , , , , , , , , , ],
				["||Propios del Sistema", , , , , , , , , , , ],
					["|||Aplicaciones", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=aplicaciones_lista&filtrar=default&concepto=80-0001", , , , d['80-0001'], , , , , , ],
					["|||Par&aacute;metros", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=parametros_lista&filtrar=default&concepto=80-0002", , , , d['80-0002'], , , , , , ],
				["||Relacionados a Personas", , , , , , , , , , , ],
					["|||Personas", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=personas_lista&filtrar=default&concepto=80-0003", , , , d['80-0003'], , , , , , ],
					["|||Organismos", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=organismos_lista&filtrar=default&concepto=80-0004", , , , d['80-0004'], , , , , , ],
					["|||Dependencias", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=dependencias_lista&filtrar=default&concepto=80-0005", , , , d['80-0005'], , , , , , ],
				["||Relacionados a Contabilidad", , , , , , , , , , , ],
					["|||Plan de Cuentas", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=plan_cuentas_lista&filtrar=default&concepto=80-0006", , , , d['80-0006'], , , , , , ],
					["|||Plan de Cuentas (Pub. 20)", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=plan_cuentas_pub20&filtrar=default&concepto=80-0007", , , , d['80-0007'], , , , , , ],
					["|||Grupos de Centros de Costos", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=grupo_centro_costos_lista&filtrar=default&concepto=80-0008", , , , d['80-0008'], , , , , , ],
					["|||Centros de Costos", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=centro_costos_lista&filtrar=default&concepto=80-0009", , , , d['80-0009'], , , , , , ],
				["||Relacionados a Presupuesto", , , , , , , , , , , ],
					["|||Tipos de Cuenta", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=tipo_cuenta_lista&filtrar=default&concepto=80-0010", , , , d['80-0010'], , , , , , ],
					["|||Clasificador Presupuestario", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=clasificador_presupuestario_lista&filtrar=default&concepto=80-0011", , , , d['80-0011'], , , , , , ],
				["||Otros Maestros", , , , , , , , , , , ],
					["|||Paises", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=paises_lista&filtrar=default&concepto=80-0012", , , , d['80-0012'], , , , , , ],
					["|||Estados", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=estados_lista&filtrar=default&concepto=80-0013", , , , d['80-0013'], , , , , , ],
					["|||Municipios", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=municipios_lista&filtrar=default&concepto=80-0014", , , , d['80-0014'], , , , , , ],
					["|||Ciudades", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=ciudades_lista&filtrar=default&concepto=80-0015", , , , d['80-0015'], , , , , , ],
					["|||Parroquias", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=parroquias_lista&filtrar=default&concepto=80-0016&_APLICACION="+_A, , , , d['80-0016'], , , , , , ],
					["|||Comunidades", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=comunidades_lista&filtrar=default&concepto=80-0017&_APLICACION="+_A, , , , d['80-0017'], , , , , , ],
					["|||Tipos de Pago", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=tipos_pago_lista&filtrar=default&concepto=80-0018", , , , d['80-0018'], , , , , , ],
					["|||Bancos", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=bancos_lista&filtrar=default&concepto=80-0019", , , , d['80-0019'], , , , , , ],
					["|||Unidad Tributaria", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=unidad_tributaria_lista&filtrar=default&concepto=80-0020", , , , d['80-0020'], , , , , , ],
        			["|||Unidad Aritmetica Umbral", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=unidadaritmetica_lista&filtrar=default&concepto=80-0035", , , , d['80-0035'], , , , , , ],
			["|Relacionados a Presupuesto", , , , , , , , , , , ],
				["||Sectores &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_sector_lista&filtrar=default&concepto=80-0022", , , , d['80-0022'], , , , , , ],
				["||Sub-Sectores &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_subsector_lista&filtrar=default&concepto=80-0023", , , , d['80-0023'], , , , , , ],
				["||Programas &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_programas_lista&filtrar=default&concepto=80-0024", , , , d['80-0024'], , , , , , ],
				["||Sub-Programas &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_subprogramas_lista&filtrar=default&concepto=80-0025", , , , d['80-0025'], , , , , , ],
				["||Proyectos &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_proyectos_lista&filtrar=default&concepto=80-0026", , , , d['80-0026'], , , , , , ],
				["||Actividades &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_actividades_lista&filtrar=default&concepto=80-0027", , , , d['80-0027'], , , , , , ],
				["||Unidades Ejecutoras &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_unidadejecutora_lista&filtrar=default&concepto=80-0028", , , , d['80-0028'], , , , , , ],
				["||Categorias Programaticas &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_categoriaprog_lista&filtrar=default&concepto=80-0030", , , , d['80-0030'], , , , , , ],
				["||Fuentes de Financiamiento &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_fuentefinanciamiento_lista&filtrar=default&concepto=80-0029", , , , d['80-0029'], , , , , , ],
			["|Relacionados al POA", , , , , , , , , , , ],
				["||Objetivos &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_objetivospoa_lista&filtrar=default&concepto=80-0031", , , , d['80-0031'], , , , , , ],
				["||Metas &nbsp; &nbsp; &nbsp;", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_metaspoa_lista&filtrar=default&concepto=80-0032", , , , d['80-0032'], , , , , , ],
			["|Otros", , , , , , , , , , , ],
				["||Miscel&aacute;neos", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=miscelaneos_lista&filtrar=default&concepto=80-0021", , , , d['80-0021'], , , , , , ],
				["||Financiamientos Aprobados", "<?=$_PARAMETRO["PATHSIA"]?>pv/gehen.php?anz=pv_financiamiento_lista&filtrar=default&concepto=80-0033", , , , d['80-0033'], , , , , , ],
			
		["Admin.", , , , , , "0", , , , , ],
			["|Seguridad", , , , , , , , , , , ],
				["||Maesto de Usuarios", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=usuarios_lista&lista=usuarios&filtrar=default&concepto=90-0001", , , , d['90-0001'], , , , , , ],
				["||Dar Autorizaciones a Usuarios", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=usuarios_lista&lista=autorizaciones&filtrar=default&concepto=90-0002", , , , d['90-0002'], , , , , , ],
			["|Seguridad Alterna", , , , , , , , , , , ],
				["||Dar Autorizaciones a Usuarios", "<?=$_PARAMETRO["PATHSIA"]?>comunes/gehen.php?anz=usuarios_lista&lista=alterna&filtrar=default&concepto=90-0003", , , , d['90-0003'], , , , , , ],
	];
	dm_initFrame("frmSet", 0, 1, 0);
</script>