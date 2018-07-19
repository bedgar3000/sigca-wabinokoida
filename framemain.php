<?php
include("lib/fphp.php");
include($_SESSION['MODULO']."/lib/fphp.php");
//	------------------------------------
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
	<link type="text/css" rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
	<link type="text/css" rel="stylesheet" href="css/estilo.css" charset="utf-8" />
	<link type="text/css" rel="stylesheet" href="css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
	<link type="text/css" rel="stylesheet" href="assets/Semantic-UI/semantic.min.css" charset="utf-8" />
</head>

<body style="margin-top:0px; margin-left:0px;">
	<?php
	if ($_SESSION['MODULO'] == 'rh') {
		?>
		<div class="ui mini labeled icon menu" style="margin-left: 11px; background-color: #F0EFEE; font-size:9px;">
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0001')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>empleados/gehen.php?anz=empleados_lista&filtrar=default&concepto=01-0001&_APLICACION=RH">
				<i class="users icon"></i>
				Empleados
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0016')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>rh/gehen.php?anz=rh_permisos_form&opcion=nuevo&concepto=01-0016&_APLICACION=RH">
				<i class="flag icon"></i>
				Permisos
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0025')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>rh/gehen.php?anz=rh_vacaciones_form&opcion=nuevo&concepto=01-0025&_APLICACION=RH">
				<i class="suitcase icon"></i>
				Vacaciones
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','05-0004')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>rh/gehen.php?anz=rh_bono_periodos_registrar_lista&filtrar=default&concepto=01-0025&_APLICACION=RH&return=framemain">
				<i class="calendar icon"></i>
				Eventos
			</a>
		</div>
		<?php
	}
	elseif ($_SESSION['MODULO'] == 'nomina') {
		?>
		<div class="ui mini labeled icon menu" style="margin-left: 11px; background-color: #F0EFEE; font-size:9px;">
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0001')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>empleados/gehen.php?anz=empleados_lista&filtrar=default&concepto=01-0001&_APLICACION=NOMINA">
				<i class="users icon"></i>
				Empleados
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','05-0004')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>nomina/gehen.php?anz=pr_procesos_control_lista&filtrar=default&lista=todos&concepto=05-0004&_APLICACION=NOMINA">
				<i class="add to calendar icon"></i>
				Control Procesos
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0006')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>nomina/gehen.php?anz=pr_procesos_control_ejecucion&filtrar=default&concepto=05-0006&_APLICACION=NOMINA">
				<i class="tags icon"></i>
				Ejecutar Nómina
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','05-0013')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>nomina/gehen.php?anz=pr_interfase_cuentas_por_pagar&filtrar=default&concepto=05-0013&_APLICACION=NOMINA">
				<i class="file text icon"></i>
				Generar Obigación
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','05-0027')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>nomina/gehen.php?anz=pr_interfase_cuentas_por_pagar_bono&filtrar=default&concepto=05-0027&_APLICACION=NOMINA">
				<i class="shop icon"></i>
				Bono Alimentación
			</a>
		</div>
		<?php
	}
	elseif ($_SESSION['MODULO'] == 'lg') {
		?>
		<div class="ui mini labeled icon menu" style="margin-left: 11px; background-color: #F0EFEE; font-size:9px;">
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0001')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>lg/gehen.php?anz=lg_requerimiento_form&opcion=nuevo&origen=&concepto=01-0001&_APLICACION=LG">
				<i class="browser icon"></i>
				Nuevo Requerimiento
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0006')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>lg/gehen.php?anz=lg_cotizaciones_items_invitar_lista&filtrar=default&concepto=01-0006&_APLICACION=LG">
				<i class="map outline icon"></i>
				Invitaciones
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0007')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>lg/gehen.php?anz=lg_cotizaciones_proveedores_invitar_lista&filtrar=default&concepto=01-0007&_APLICACION=LG">
				<i class="bar chart icon"></i>
				Cotizaciones
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0009')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>lg/gehen.php?anz=lg_orden_compra_lista&lista=todos&filtrar=default&concepto=01-0009&_APLICACION=LG">
				<i class="in cart icon"></i>
				Ordenes Compras
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0013')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>lg/gehen.php?anz=lg_orden_servicio_lista&lista=todos&filtrar=default&concepto=01-0013&_APLICACION=LG">
				<i class="shop icon"></i>
				Ordenes Servicios
			</a>
		</div>
		<?php
	}
	elseif ($_SESSION['MODULO'] == 'ap') {
		?>
		<div class="ui mini labeled icon menu" style="margin-left: 11px; background-color: #F0EFEE; font-size:9px;">
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0005')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>ap/gehen.php?anz=ap_facturacion_lista&lista=todos&filtrar=default&concepto=01-0005&_APLICACION=AP">
				<i class="cubes icon"></i>
				Facturación
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0004')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>ap/gehen.php?anz=ap_obligacion_form&opcion=nuevo&_APLICACION=AP">
				<i class="cube icon"></i>
				Nueva Obligación
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','02-0001')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>ap/gehen.php?anz=ap_orden_pago_lista&lista=todos&filtrar=default&concepto=02-0001&_APLICACION=AP">
				<i class="file text outline icon"></i>
				Orden Pago
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','02-0002')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>ap/gehen.php?anz=ap_orden_pago_prepago_lista&lista=prepago&filtrar=default&concepto=02-0002&_APLICACION=AP">
				<i class="thumbs outline up icon"></i>
				Prepago
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','02-0003')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>ap/gehen.php?anz=ap_orden_pago_transferir_lista&filtrar=default&concepto=02-0003&_APLICACION=AP">
				<i class="thumbs up icon"></i>
				Transferir
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','02-0006')[0]?'':'none'; ?>;" href="<?php echo $_PARAMETRO["PATHSIA"]; ?>ap/gehen.php?anz=ap_pago_lista&filtrar=default&concepto=02-0006&_APLICACION=AP">
				<i class="file text icon"></i>
				Pagos
			</a>
		</div>
		<?php
	}
	elseif ($_SESSION['MODULO'] == 'co') {
		?>
		<div class="ui mini labeled icon menu" style="margin-left: 11px; background-color: #F0EFEE; font-size:9px;">
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0001')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_cotizacion_form&opcion=nuevo&origen=framemain&concepto=01-0001&_APLICACION=CO">
				<i class="cube icon"></i>
				Nueva Cotización
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0003')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_cotizacion_lista&filtrar=default&lista=aprobar&concepto=01-0003&_APLICACION=CO">
				<i class="cubes icon"></i>
				Aprobar Cotización
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0004')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_cotizacion_generar_lista&filtrar=default&lista=generar&concepto=01-0004&_APLICACION=CO">
				<i class="cubes icon"></i>
				Generar Pedidos
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0005')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_pedidos_form&opcion=nuevo&origen=framemain&concepto=01-0005&_APLICACION=CO">
				<i class="cube icon"></i>
				Nuevo Pedido
			</a>
			<?php if ($_PARAMETRO['PEDAAUTOAP'] <> 'S') { ?>
				<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0007')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_pedidos_lista&filtrar=default&lista=aprobar&concepto=01-0007&_APLICACION=CO">
					<i class="cubes icon"></i>
					Aprobar Pedidos
				</a>
			<?php } ?>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0008')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_pedidos_lista&filtrar=default&lista=facturar&concepto=01-0008&_APLICACION=CO">
				<i class="cubes icon"></i>
				Facturar Pedidos
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','01-0009')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_documento_form&opcion=nuevo&origen=framemain&concepto=01-0009&_APLICACION=CO">
				<i class="cube icon"></i>
				Nuevo Documento
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','02-0001')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_cobranza_form&opcion=nuevo&origen=framemain&concepto=02-0001&_APLICACION=CO">
				<i class="cube icon"></i>
				Nueva Cobranza
			</a>
			<a class="item" style="width: 70px; display: <?php echo opcionesPermisos('','02-0004')[0]?'':'none'; ?>;" href="<?=$_PARAMETRO["PATHSIA"]?>co/gehen.php?anz=co_cierrecaja_form&opcion=nuevo&origen=framemain&concepto=02-0004&_APLICACION=CO">
				<i class="cube icon"></i>
				Nuevo Cierre Caja
			</a>
		</div>
		<?php
	}
	?>

	<!-- ui-dialog -->
	<div id="cajaModal"></div>

	<?php
	if ($_SESSION["PRIMERA_VEZ"] && $_SESSION['MODULO'] == 'rh') {
		$_SESSION["PRIMERA_VEZ"] = false;
		list ($Ejecutar) = opcionesPermisos('', '01-0032');
		if ($Ejecutar == "S") avisoPeriodosVacacionales();
	}
	?>
	<div style="position:absolute; top:20%; left:35%;">
		<img src="imagenes/fondo_main.png" width="40%" height="40%" />
	</div>


	<script type="text/javascript" src="js/jquery-1.7.min.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/fscript.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.prettyPhoto.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.formatCurrency-1.4.0.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.formatCurrency.all.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.timeentry.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.numeric.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/funciones.js" charset="utf-8"></script>
	<script type="text/javascript" src="assets/Semantic-UI/semantic.min.js" charset="utf-8"></script>
</body>
</html>