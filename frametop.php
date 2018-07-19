<?php
include("lib/fphp.php");
if ($_SESSION['MODULO'] == 'rh') {
	$titulo = 'Recursos Humanos';
	$img = 'menu_rrhh.jpg';
}
elseif ($_SESSION['MODULO'] == 'nomina') {
	$titulo = 'N&oacute;mina';
	$img = 'foto_rrhh.jpg';
}
elseif ($_SESSION['MODULO'] == 'lg') {
	$titulo = 'Log&iacute;stica';
	$img = 'menu_lg.jpg';
}
elseif ($_SESSION['MODULO'] == 'ap') {
	$titulo = 'Cuentas x Pagar';
	$img = 'menu_ap.png';
}
elseif ($_SESSION['MODULO'] == 'at') {
	$titulo = 'Activos Tecnol&oacute;gicos';
	$img = 'menu_pv.png';
}
elseif ($_SESSION['MODULO'] == 'ac') {
	$titulo = 'Contabilidad';
	$img = 'menu_ac.png';
}
elseif ($_SESSION['MODULO'] == 'pv') {
	$titulo = 'Presupuesto';
	$img = 'menu_pv.png';
}
elseif ($_SESSION['MODULO'] == 'ob') {
	$titulo = 'Obras';
	$img = 'menu_ob.jpg';
}
elseif ($_SESSION['MODULO'] == 'ha') {
	$titulo = 'Hacienda Municipal';
	$img = 'menu_ha3.jpg';
}
elseif ($_SESSION['MODULO'] == 'af') {
	$titulo = 'Activos Fijos';
	$img = 'menu_pv.png';
}
elseif ($_SESSION['MODULO'] == 'co') {
	$titulo = 'Ventas';
	$img = 'menu_co.jpg';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
	<style type="text/css">
		body {
			margin:0;
			padding:0;
			background: url(imagenes/wideimage2.jpg);
		}
		body { font-size: 62.5%; }

		.img  { 
			float: left; 
			margin: 2px 1px 1px 1px; 
			overflow: hidden; 

			border-radius: 2px 2px 2px 2px;
			-moz-border-radius: 2px 2px 2px 2px;
			-webkit-border-radius: 2px 2px 2px 2px;
			border: 2px solid #373D40;
		}

		.titulo {
			float: left; 
			margin: 5px 2.5px 2.5px 2.5px; 
			overflow: hidden; 

			filter:alpha(opacity=50); 
			opacity:0.5;
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

			line-height: 30px;
			padding: 0px 5px;
		}

		.foto  { 
			float: right; 
			margin: 2px 1px 1px 1px; 
			overflow: hidden; 

			border-radius: 2px 2px 2px 2px;
			-moz-border-radius: 2px 2px 2px 2px;
			-webkit-border-radius: 2px 2px 2px 2px;
			border: 2px solid #373D40;
		}

		.usuario {
			float: right; 
			margin: 2.5px 2.5px 2.5px 2.5px; 
			overflow: hidden; 

			filter:alpha(opacity=75); 
			opacity:0.75;
			background-color: #1A252B;

			text-shadow: 
				0px 0px 20px rgba(0,0,0,1), 
				0px 0px 20px rgba(0,0,0,1), 
				0px 0px 20px rgba(0,0,0,1);

			font-weight: bold;
			color: #ddd;
			font-size: 10px;

			-webkit-box-shadow: 10px 10px 10px 10px rgba(0,0,0,0.15);
			-moz-box-shadow: 10px 10px 10px 10px rgba(0,0,0,0.15);
			box-shadow: 10px 10px 10px 10px rgba(0,0,0,0.15);

			border-radius: 2px 2px 2px 2px;
			-moz-border-radius: 2px 2px 2px 2px;
			-webkit-border-radius: 2px 2px 2px 2px;
			border: 2px solid #1A252B;

			line-height: 15px;
			padding: 2px 5px;
		}
			.usuario img {
				vertical-align: middle;
				height: 12px;
			}
			.usuario a {
				color: #fff;
				text-decoration: none;
				float: right;
			}
			.usuario a:hover {
				color: #ddd;
			}
	</style>
</head>
<body style="margin-top:0px; margin-left:0px;">
	<div class="img">
        <img src="imagenes/<?=$img?>" width="68" height="68" style="border-color:#999999" />
    </div>

    <div class="titulo">
		M&oacute;dulo de <?=$titulo?>
	</div>

	<div class="foto">
		<?php
		if (isset($_SESSION["FOTO"])) {
			?> <img src="imagenes/fotos/<?=$_SESSION["FOTO"]?>" width="68" height="68" /> <?php
		}
		?>
	</div>

	<div class="usuario">
		&nbsp;<?=$_SESSION["USUARIO_ACTUAL"]?>
		<br />
		&nbsp;<?=$_SESSION["NOMBRE_USUARIO_ACTUAL"]?>
		<br />
		&nbsp;<?=$_SESSION["NOMBRE_ORGANISMO_ACTUAL"]?>
		<br />
		<a href="cerrar_sesion.php" target="_parent"><img src="imagenes/off.png" /> Cerrar Sessión</a>
	</div>
</body>
</html>