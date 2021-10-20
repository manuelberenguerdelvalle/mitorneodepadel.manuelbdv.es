<?php
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_partidos.php");
include_once ("../../funciones/f_inputs.php");
include_once ("../../funciones/f_secundarias.php");
include_once ("../../funciones/f_fechasHoras.php");
include_once ("../../../class/mysql.php");
include_once ("../../../class/partido.php");
include_once ("../../../class/liga.php");
include_once ("../../../class/division.php");
session_start();
$pagina = $_SESSION['pagina'];
if($pagina != 'gestion_partido'){
	header ("Location: ../cerrar_sesion.php");
}
$opcion = $_SESSION['opcion'];
$liga = unserialize($_SESSION['liga']);
$id_liga = $liga->getValor('id_liga');
$tipo_pago = $liga->getValor('tipo_pago');
$idayvuelta = $liga->getValor('idayvuelta');
$division = unserialize($_SESSION['division']);
$id_division = $division->getValor('id_division');
//$num_partidos = obten_consultaUnCampo('session','COUNT(id_partido)','partido','division',$id_division,'','','','','','','');
//siguiente fase 
$texto = '';
$min_eliminatoria = obten_consultaUnCampo('session','MIN(eliminatoria)','partido','division',$id_division,'jornada',0,'','','','','');
if($min_eliminatoria > 1){//hay eliminatorias
	if(obten_consultaUnCampo('session','COUNT(id_partido)','partido','division',$id_division,'eliminatoria',$min_eliminatoria,'','','','','') == eliminatoriaPartidosFinalizados($id_division,$min_eliminatoria)){//eliminatoria completada
		$texto = 'La eliminatoria '.obten_nombreEliminatoria($min_eliminatoria). ' se ha completado correctamente, revisa que los equipos ganadores son correctos, desea generar la nueva eliminatoria '.obten_nombreEliminatoria($min_eliminatoria/2).'?';
	}
}//fin if
else if($min_eliminatoria == 1){}
else{//liguilla
	if(obten_consultaUnCampo('session','COUNT(id_partido)','partido','division',$id_division,'eliminatoria',0,'','','','','') == liguillaPartidosFinalizados($id_division)){//liguilla completa
		$texto = 'La liguilla ha finalizado, va a comenzar la fase de eliminatorias, revisa que los equipos ganadores sean correctos, posteriormente no se podr&aacute; modificar los datos, <br>desea continuar?';
	}
	
}//fin else
if($opcion == 1 && $texto != ''){//modificacion
	//SE GUARDA EN SESSION
	$_SESSION['id_liga'] = $id_liga;
	$_SESSION['id_division'] = $id_division;
	
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>www.mitorneodepadel.es</title>
<link rel="stylesheet" type="text/css" href="css/modificar_partido.css" />
<link rel="stylesheet" type="text/css" href="../../../sweetalert-master/lib/sweet-alert.css" />
<script src="../../javascript/jquery-1.11.1.js" type="text/javascript"></script>
<script src="../../../sweetalert-master/lib/sweet-alert.min.js" type="text/javascript"></script>
<script src="javascript/siguiente_fase.js" type="text/javascript"></script>
</head>
<body>
<div class="cont_principal">
	<div class="horizontal">&nbsp;</div>
    <div class="horizontal">&nbsp;</div>
    <div class="horizontal">&nbsp;</div>
    <div class="horizontal">&nbsp;</div>
	<div class="horizontal"><div class="titulo"><?php echo $texto;?></div></div>
     <div class="horizontal">&nbsp;</div>
    <div class="si"><a href="#" class="textoResp" onClick="generar('S')">Si</a></div>
    <div class="horizontal" id="respuesta">&nbsp;</div>
</div><!--fin div cont principal -->
</body>
</html>
<?php
}//fin de if opcion tipo numPartidos

?>