<?php
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_secundarias.php");
include_once ("../../../class/mysql.php");
include_once ("../../../class/pago_web.php");
include_once ("../../../class/usuario.php");
include_once ("../../../class/liga.php");
include_once ("../../../class/division.php");
include_once ("../../../class/datos.php");
require_once ("../../../fpdf/fpdf.php");
session_start();
$pagina = $_SESSION['pagina'];
if($pagina != 'gestion_pago'){
	header ("Location: ../cerrar_sesion.php");
}
$id_pago_web = limpiaTexto($_GET['id_pago_web']);
$estado = limpiaTexto($_GET['estado']);
//echo $id_pago_web.'-'.$estado;
	$pago = new Pago_web($id_pago_web,'','','','','','','','','','','','','','','',''); 
	$usuario = unserialize($_SESSION['usuario']);
	$liga = unserialize($_SESSION['liga']);
	$nom_liga = utf8_encode($liga->getValor('nombre'));
	$division = unserialize($_SESSION['division']);
	$datos = new Datos(2,'','','','','','');
	
	$nom_yo = '   '.$datos->getValor('c1');
	$nom_cli = '   '.utf8_encode(ucwords($usuario->getValor('nombre').' '.$usuario->getValor('apellidos')));
	$dir_yo = '   '.$datos->getValor('c2');
	$dir_cli = '   '.utf8_encode(ucwords($usuario->getValor('direccion')));
	$loc_yo = '   '.$datos->getValor('c3');
	$loc_cli = '   '.ucwords( obtenLocalizacion(3,$usuario->getValor('ciudad')).' ('.obtenLocalizacion(2,$usuario->getValor('provincia')).')' );
	$doc_yo = '   '.$datos->getValor('c4');
	$doc_cli = '   '.$usuario->getValor('dni').'-'.letraNIF($usuario->getValor('dni'));
	if($pago->getValor('modo_pago') == 'P'){$modo_pago = 'Online';}
	else{$modo_pago = 'Presencial';}
	$tipo = $pago->getValor('tipo');
	$iva = $precio_sin = $pago->getValor('precio')*0.21;
	$precio_sin = $pago->getValor('precio')-$iva;
	$euro = utf8_encode(' EUR');
	if($estado == 'devolucion'){
		$descrip_pago = 'Devoluci�n ';
		$signo = '-';
	}
	else{$signo = '';}
	if($tipo == 'T'){
			$descrip_pago .= 'Pack Torneo '.obten_equipos($liga->getValor('tipo_pago')).': '.$nom_liga;
	}//pago de liga
	else if($tipo == 'D'){
			$descrip_pago .= 'Pack Divisi�n extra: n�'.$division->getValor('num_division').' en el torneo '.$nom_liga;
	}//division extra
	/*else if($tipo == 'I'){
			$descrip_pago .= 'Pack Ida y vuelta: en la liga '.$nom_liga;
	}//division extra*/
	else{
			$descrip_pago .= 'Publicidad: Posici�n '.$pago->getValor('posicion_publi').' en el torneo '.$nom_liga;
	}
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>www.mitorneodepadel.es</title>
<link rel="stylesheet" type="text/css" href="../../css/facturas_mobile.css" />
<style>
/*.contenedor_principal {
	margin: 0 auto;
	height: 1200px !important;
	background-image:url(../../../images/diseno_factura.jpg);
	background-repeat: no-repeat;
	background-position:center;
	background-size: contain;
}
.datos{
	float:left;
	width:375px;
	height:225px;
	margin-top:300px;
	margin-left:75px;
	font-size:24px;
	color:#003;
}
.cliente{
	float:left;
	width:375px;
	height:225px;
	margin-top:300px;
	margin-left:60px;
	font-size:24px;
	color:#003;
}
.num_factura{
	float:left;
	width:150px;
	height:35px;
	margin-top:110px;
	margin-left:180px;
	font-size:24px;
	color:#003;
}
.fecha{
	float:left;
	width:150px;
	height:35px;
	margin-top:110px;
	margin-left:152px;
	font-size:24px;
	color:#003;
}
.modo{
	float:left;
	width:150px;
	height:35px;
	margin-top:110px;
	margin-left:150px;
	font-size:24px;
	color:#003;
}
.articulo{
	float:left;
	width:40px;
	height:150px;
	margin-top:200px;
	margin-left:75px;
	font-size:24px;
	color:#003;
}
.descripcion{
	float:left;
	width:500px;
	height:150px;
	margin-top:200px;
	margin-left:15px;
	font-size:24px;
	color:#003;
}
.cantidad{
	float:left;
	width:40px;
	height:150px;
	margin-top:200px;
	margin-left:75px;
	font-size:24px;
	color:#003;
}
.precio_sin{
	float:left;
	width:120px;
	height:150px;
	margin-top:200px;
	margin-left:40px;
	font-size:24px;
	color:#003;
	text-align:center;
}
.iva{
	float:left;
	width:120px;
	height:40px;
	margin-top:65px;
	margin-left:790px;
	font-size:24px;
	color:#003;
	text-align:center;
}
.total{
	float:left;
	width:120px;
	height:40px;
	margin-top:20px;
	margin-left:790px;
	font-size:24px;
	color:#003;
	text-align:center;
}*/
</style>
</head>
<body>
	<a href="#" onClick="window.history.back();">
	<div class="contenedor_principal">
    	<div class="datos"><?php echo substr($nom_yo,0,56).'<br>'.substr($dir_yo,0,56).'<br>'.substr($loc_yo,0,56).'<br>'.$doc_yo;?></div>
        <div class="cliente"><?php echo substr($nom_cli,0,56).'<br>'.substr($dir_cli,0,56).'<br>'.substr($loc_cli,0,56).'<br>'.$doc_cli;?></div>
        <div class="num_factura"><?php echo $id_pago_web;?></div>
        <div class="fecha"><?php echo datepicker_fecha(substr($pago->getValor('fecha_limite'),0,10));?></div>
        <div class="modo"><?php echo $modo_pago;?></div>
        <div class="articulo"><?php echo obten_tipoArticulo($tipo);?></div>
        <div class="descripcion"><?php echo $descrip_pago;?></div>
        <div class="cantidad"><?php echo '1';?></div>
        <div class="precio_sin"><?php echo $signo.$precio_sin.$euro;?></div>
        <div class="iva"><?php echo $signo.$iva.$euro;?></div>
        <div class="total"><?php echo $signo.$pago->getValor('precio').$euro;?></div>
    </div>
    </a>
</body>
</html>