<?php
//	------------------------------------
include("../lib/fphp.php");
include("lib/fphp.php");
list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('', $concepto);
//	------------------------------------
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
//	------------------------------------
$_SESSION["fBuscar"] = "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
<link type="text/css" rel="stylesheet" href="../css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../css/estilo.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" src="../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.formatCurrency-1.4.0.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.formatCurrency.all.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.timeentry.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.numeric.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/jquery.mask.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" src="js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" src="js/form.js" charset="utf-8"></script>
</head>

<body>
<!-- ui-dialog -->
<div id="cajaModal"></div>
<!-- pretty -->
<span class="gallery clearfix"></span>
<!-- progress-bar -->
<div class="div-progressbar"><div class="progressbar"></div></div>

<!-- INICIO -->
<?php if (file_exists("$anz.php")) include("$anz.php"); ?>
<!-- FIN -->

<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		<?php
		if (isset($rows_total)) {
			?> totalRegistros(parseInt(<?=$rows_total?>), "<?=$_ADMIN?>", "<?=$_INSERT?>", "<?=$_UPDATE?>", "<?=$_DELETE?>"); <?php
		}
		if (isset($focus)) {
			?> $("#<?=$focus?>").focus(); <?php
		}
		?>
		$(".div-progressbar").css("display", "none");

		var widthWindow = $(window).width();
		var heightWindow = $(window).height();
		var heightFiltro = $('.tblFiltro').height();
		var height = heightWindow - heightFiltro - 150;
		$('.scroll').css('height', height);
	});
</script>
</body>
</html>