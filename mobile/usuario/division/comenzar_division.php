<?php
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_fechasHoras.php");
include_once ("../../funciones/f_inputs.php");
include_once ("../../../class/mysql.php");
include_once ("../../../class/liga.php");
include_once ("../../../class/division.php");
session_start();
$pagina = $_SESSION['pagina'];
if($pagina != 'gestion_division'){
	header ("Location: ../cerrar_sesion.php");
}
$opcion = $_SESSION['opcion'];
$liga = unserialize($_SESSION['liga']);
$id_liga = $liga->getValor('id_liga');
$tipo_pago = $liga->getValor('tipo_pago');
$idayvuelta = $liga->getValor('idayvuelta');
$division = unserialize($_SESSION['division']);
$id_division = $division->getValor('id_division');
//fechas para los calendarios. Si se ha accedido es porque hay al menos  2 equipos suscritos y si hay equipos es porque ha superado el periodo de suscripcion
$numEquipos = obten_consultaUnCampo('session','COUNT(id_equipo)','equipo','liga',$id_liga,'division',$id_division,'pagado','S','','','');
$numPartidos = obten_consultaUnCampo('session','COUNT(id_partido)','partido','division',$id_division,'','','','','','','');
if($numEquipos > 2 && $numPartidos == 0 && $opcion == 2 && $division->getValor('comienzo') == 'N'){
	//SE GUARDA EN SESSION
	$_SESSION['id_liga'] = $id_liga;
	$_SESSION['tipo_pago'] = $tipo_pago;
	$_SESSION['idayvuelta'] = $idayvuelta;
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>www.mitorneodepadel.es</title>
<link rel="stylesheet" type="text/css" href="css/comenzar_division.css" />
<link rel="stylesheet" type="text/css" href="../../css/estilo_info_texto.css" />
<link href="../../../jquery-ui/jquery-ui.css" rel="stylesheet">
<script src="../../javascript/info_texto.js" type="text/javascript"></script>
<script src="../../javascript/jquery-1.11.1.js" type="text/javascript"></script>
<script src="../../javascript/validaciones.js" type="text/javascript"></script>
<script src="javascript/comenzar_division.js" type="text/javascript"></script>
<script src="../../../jquery-ui/jquery-ui.js"></script>
<script language="javascript">
  $(function () {
		$.datepicker.setDefaults($.datepicker.regional["es"]);
		$("#datepicker").datepicker({
		firstDay: 1,
		minDate: "1D",
		changeMonth: true,
        changeYear: true
		});
	});
</script>
<style>
.horizontal {
	width:99% !important;
	/*border:1px #000 solid;*/
	float:left;
}
.horizontal div {
	width:80% !important;
	margin: 0 auto;
	font-size:80%;
	color: #006;
}
.columna1{
	width:55% !important;
	/*margin-left: 5%;*/
	margin-top:5%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna2{
	width:43% !important;
	margin-top:4.5%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna0{
	width:43% !important;
	margin-top:1%;
	float:left;
	/*border:1px black solid;*/
}
.columna12{
	width:35% !important;
	margin-top:0.5%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna3{
	width:15% !important;
	height:40% !important;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna4{
	width:15% !important;
	height:40% !important;
	margin-left:1%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna5{
	width:15% !important;
	height:40% !important;
	margin-left:1%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna6{
	width:15% !important;
	height:40% !important;
	margin-left:1%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna7{
	width:99% !important;
	margin-top:3%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna8{
	width:99% !important;
	margin-top:5%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna9{
	width:99% !important;
	margin-top:5%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna10{
	width:80% !important;
	margin-top:5%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.columna11{
	width:15% !important;
	margin-top:5%;
	float:left;
	/*border:1px black solid;*/
	display:none;
}
.numEquipo1{
	width:5%;
	font-size:80%;
	color:#003;
	text-align:center;
	/*border:1px black solid;*/
	float: left;
}
.equipo1{
	width:94%;
	font-size:80%;
	color:#003;
	text-align:left;
	/*border:1px black solid;*/
	float: left;
}
.pelota{
	width:5%;
	text-align:center;
	/*border:1px black solid;*/
	float: left;
}
.numEquipo2{
	margin-bottom:2%;
	width:5%;
	font-size:80%;
	color:#003;
	text-align:center;
	/*border:1px black solid;*/
	float: left;
}
.equipo2{
	margin-bottom:2%;
	width:94%;
	font-size:80%;
	color:#003;
	text-align:left;
	/*border:1px black solid;*/
	float: left;
}
.grupos1{
	width:99%;
	/*font-size:80%;
	color:#003;*/
	font-size:80%;
	text-align:left;
	/*border:1px black solid;*/
	float: left;
}
.grupos2{
	margin-bottom: 10%;
	width:99%;
	/*font-size:80%;
	color:#003;*/
	font-size:80%;
	text-align:left;
	/*border:1px black solid;*/
	float: left;
}
.cont_arbitros{
	width:99% !important;
	height:87% !important;
	float:right;
	overflow-x:hidden;
	overflow-y:auto;
	border:1px #d1d1d1 solid;
	box-shadow:5px 5px 7px rgba(0,0,0,0.3);
	border-radius:10px;
	display:none;
}
.cont_arbitros:hover{
	width:99% !important;
	height:87% !important;
	float:right;
	overflow-x:hidden;
	overflow-y:scroll;
	border:1px #181C83 solid;
	box-shadow:5px 5px 7px rgba(0,0,0,0.3);
	border-radius:10px;
}
.cont_pistas{
	width:99% !important;
	height:87% !important;
	float:right;
	overflow:auto;
	border:1px #d1d1d1 solid;
	box-shadow:5px 5px 7px rgba(0,0,0,0.3);
	border-radius:10px;
	display:none;
}
.cont_pistas:hover{
	width:99% !important;
	height:87% !important;
	float:right;
	overflow:auto;
	border:1px #181C83 solid;
	box-shadow:5px 5px 7px rgba(0,0,0,0.3);
	border-radius:10px;
}
.cuadroTexto{
	width:99%;
	height:70px !important;
	font-size:80%;
	text-align:right;
	color:#003;
	/*border:1px black solid;*/
	float:right;
}
.cuadroInputs{
	width:99%;
	height:70px !important;
	font-size:80%;
	text-align:left;
	/*border:1px black solid;*/
	float:right;
}

.input_text_liga {
	width:95%;
	height:90%;
	font-size:80%;
	color: #181C83;
	background-color: #EDEDFC;
	border-radius:10px;
	font-weight:bold;
	font-style:italic;
	border:2px #8989FE solid;
}
.input_select_liga {
	height:85%;
	font-size:78%;
	color: #181C83;
	background-color: #EDEDFC;
	border-radius:10px;
	font-style:italic;
	font-weight:bold;
	border:2px #8989FE solid;
}
.boton {
	background-color: #34495e;
	border-radius:10px;
	font-size:80%;
	border:3px #34495e solid;
	box-shadow:5px 5px 7px rgba(0,0,0,0.3);
	color:#FFF;
	font-weight:bold;
	margin-left:20%;
	float:left;
}
.mensaje {
	margin: 5%;
	width:80% !important;
	height:15% !important;
	/*border: 1px black solid;*/
	float:left;
}
.caja_pago {
	width:99% !important;
	height:99% !important;
	background-color:#c5fbc6;
	border-radius: 7px;
	margin-top:0.5%;
	border:1px #C1C1C1 solid;
	float:left;
}
.caja_pago img {
	margin-top:1%;
	margin-left:1%;
	height:70%;
	/*border:1px black solid;*/
	float:left;
}
.caja_pago label {
	width:80%;
	text-align:center;
	font-size:90%;
	margin-top:0.5%;
	margin-left:2%;
	color:#006;
	/*border:1px black solid;*/
	float:left;
}
#respuesta {
	color: #181C83;
}
#desdelunes1,#desdelunes2,#desdemartes1,#desdemartes2,#desdemiercoles1,#desdemiercoles2,#desdejueves1,#desdejueves2,#desdeviernes1,#desdeviernes2,#desdesabado1,#desdesabado2,#desdedomingo1,#desdedomingo2 {
	display:none;
}
#hastalunes1,#hastalunes2,#hastamartes1,#hastamartes2,#hastamiercoles1,#hastamiercoles2,#hastajueves1,#hastajueves2,#hastaviernes1,#hastaviernes2,#hastasabado1,#hastasabado2,#hastadomingo1,#hastadomingo2 {
	display:none;
}
.radio {
	width:40px;
	height:40px;
}


</style>
</head>
<body>
<?php
//CALCULAR EQUIPOS ELIMINATORIAS CON O SIN GRUPOS
if($tipo_pago == 0 || $tipo_pago == 1){$max_eliminatoria = 8;}
else if($tipo_pago == 2){$max_eliminatoria = 16;}
else{$max_eliminatoria = 32;}
$resto = $numEquipos % 2;//obtenemos el resto para saber si hay equipo pares o impares
//$crear_calendario = 'S';
/*
if($resto == 0){//equipos pares
	if($idayvuelta == 'S'){//grupos y eliminatorias
		if($numEquipos == 12 || $numEquipos == 24 || $numEquipos == 48){$crear_calendario = 'S';}//equipos exactos para que hayan grupos
		else{
			$advertencia = 'El Torneo est&aacute; configurado para disputarse con grupos y eliminatorias, actualmente tiene '.$numEquipos.' equipos inscritos, necesita la inscripci&oacute;n de '.(obten_equipos($tipo_pago)-$numEquipos).' equipos m&aacute;s para poder completar los grupos.';
			$crear_calendario = 'N';
		} 
	}
	else{//eliminatorias
		if($numEquipos == 8 || $numEquipos == 16 || $numEquipos == 32){$crear_calendario = 'S';}//equipos exactos para eliminatorias cuadradas
		else{
			$advertencia = 'El Torneo est&aacute; configurado para disputarse con eliminatorias, actualmente tiene '.$numEquipos.' equipos inscritos, necesita '.$max_eliminatoria-$numEquipos.' equipos m&aacute;s para poder completar las eliminatorias.';
			$crear_calendario = 'N';
		}
	}
}
else{//equipos impares
	$advertencia = 'Para poder crear el calendario el n&uacute;mero de equipos inscritos deben ser pares, '.$max_eliminatoria.' equipos en modo eliminatoria y '.obten_equipos($tipo_pago).' equipos para el modo grupos y eliminatorias.';
	$crear_calendario = 'N';
}
*/
	if($numEquipos > 2){//crear calendario
		if(obten_consultaUnCampo('session','COUNT(id_inscripcion)','inscripcion','liga',$id_liga,'division',$id_division,'pagado','N','','','') == 0){//filtra inscripciones sin pagar
?>
<div class="horizontal">&nbsp;</div>
<div class="horizontal"><div><b>CREAR CALENDARIO PARA LA DIVISION <?php echo $division->getValor('num_division');?></b></div></div>
<div class="horizontal"><div><b>Importante:</b> Una vez creado el calendario no podr&aacute;s inscribir ning&uacute;n equipo m&aacute;s. Podr&aacute;s modificar los partidos en el men&uacute; Partidos -> Ver/Modificar.</div></div>
<div class="horizontal">&nbsp;</div>
<div class="horizontal"><div><input type="radio" class="radio" name="calendario" value="automatico" checked onClick="comprueba(this)"><b>Autom&aacute;tico</b> (Crea el calendario de manera r&aacute;pida)</div></div>
<div id="flotante"></div>
<?php if($tipo_pago > 0){
		echo '<div class="horizontal"><div><input type="radio" class="radio" name="calendario" value="personalizado" onClick="comprueba(this)"><b>Personalizado</b> (Podr&aacute;s configurar d&iacute;as y horarios para generar el calendario)</div></div>';
}	?>
<div class="columna1">
	<div class="cuadroTexto" onMouseOver="showdiv(event,'Indica la fecha que quieres que comiencen los partidos.');" onMouseOut="hiddenDiv()" style='display:table;'>Fecha de inicio:</div>
    <div class="cuadroTexto" onMouseOver="showdiv(event,'Indica la duracion maxima o estimada por partido para el calculo del horario.');" onMouseOut="hiddenDiv()" style='display:table;'>Duraci&oacute;n estimada:</div>
    <div class="cuadroTexto" onMouseOver="showdiv(event,'Indica los sets por partido. Siempre se jugara al mejor de 3 o 5 sets.');" onMouseOut="hiddenDiv()" style='display:table;'>Sets por partido:</div>
    <div class="cuadroTexto" onMouseOver="showdiv(event,'Personaliza el horario para tu liga, si no deseas insertar horas djalas en blanco.');" onMouseOut="hiddenDiv()" style='display:table;'><span style="float:left;">Horario:</span></div>
</div>
<div class="columna2">
	<span><form id="formulario" action="#" method="post" name="formulario"></span>
    <span class="cuadroInputs"><input type="text" name="inicio" id="datepicker" class="input_text_liga" /></span>
	<input type="hidden" id="modo" name="modo" value="0">
    <span class="cuadroInputs"><?php duracion_partido('');?></span>
    <span class="cuadroInputs"><?php sets('');?></span>
    <!--<span class="cuadroInputs"><input type="checkbox" name="lunes" id="lunes" value="lunes" onClick="comprueba_linea(this)">Lunes</span>
    <span class="cuadroInputs"><input type="checkbox" name="martes" id="martes" value="martes" onClick="comprueba_linea(this)">Martes</span>
    <span class="cuadroInputs"><input type="checkbox" name="miercoles" id="miercoles" value="miercoles" onClick="comprueba_linea(this)">Mircoles</span>
    <span class="cuadroInputs"><input type="checkbox" name="jueves" id="jueves" value="jueves" onClick="comprueba_linea(this)">Jueves</span>
    <span class="cuadroInputs"><input type="checkbox" name="viernes" id="viernes" value="viernes" onClick="comprueba_linea(this)">Viernes</span>
    <span class="cuadroInputs"><input type="checkbox" name="sabado" id="sabado" value="sabado" onClick="comprueba_linea(this)">Sbado</span>
    <span class="cuadroInputs"><input type="checkbox" name="domingo" id="domingo" value="domingo" onClick="comprueba_linea(this)">Domingo</form></span>-->
    </form>
</div>
<div class="columna0">&nbsp;</div>
<div class="columna12">
	<form id="formulario2" action="#" method="post" name="formulario">
    <span class="cuadroInputs"><input type="checkbox" class="radio" name="lunes" id="lunes" value="lunes" onClick="comprueba_linea(this)">Lunes</span>
    <span class="cuadroInputs"><input type="checkbox" class="radio" name="martes" id="martes" value="martes" onClick="comprueba_linea(this)">Martes</span>
    <span class="cuadroInputs"><input type="checkbox" class="radio" name="miercoles" id="miercoles" value="miercoles" onClick="comprueba_linea(this)">Mi&eacute;rcoles</span>
    <span class="cuadroInputs"><input type="checkbox" class="radio" name="jueves" id="jueves" value="jueves" onClick="comprueba_linea(this)">Jueves</span>
    <span class="cuadroInputs"><input type="checkbox" class="radio" name="viernes" id="viernes" value="viernes" onClick="comprueba_linea(this)">Viernes</span>
    <span class="cuadroInputs"><input type="checkbox" class="radio" name="sabado" id="sabado" value="sabado" onClick="comprueba_linea(this)">S&aacute;bado</span>
    <span class="cuadroInputs"><input type="checkbox" class="radio" name="domingo" id="domingo" value="domingo" onClick="comprueba_linea(this)">Domingo</form></span>
    </form>
</div>
<div class="columna3">
	<span><form id="formulario_horas1" action="#" method="post" name="formulario_horas1"></span>
	<span class="cuadroInputs"><?php select_horas('desdelunes1','desdelunes1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdemartes1','desdemartes1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdemiercoles1','desdemiercoles1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdejueves1','desdejueves1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdeviernes1','desdeviernes1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdesabado1','desdesabado1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdedomingo1','desdedomingo1','M'); ?></span>
    <span></form></span>
</div>
<div class="columna4">
	<span><form id="formulario_horas2" action="#" method="post" name="formulario_horas2"></span>
	<span class="cuadroInputs"><?php select_horas('hastalunes1','hastalunes1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastamartes1','hastamartes1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastamiercoles1','hastamiercoles1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastajueves1','hastajueves1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastaviernes1','hastaviernes1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastasabado1','hastasabado1','M'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastadomingo1','hastadomingo1','M'); ?></span>
    <span></form></span>
</div>
<div class="columna5">
	<span><form id="formulario_horas3" action="#" method="post" name="formulario_horas3"></span>
	<span class="cuadroInputs"><?php select_horas('desdelunes2','desdelunes2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdemartes2','desdemartes2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdemiercoles2','desdemiercoles2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdejueves2','desdejueves2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdeviernes2','desdeviernes2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdesabado2','desdesabado2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('desdedomingo2','desdedomingo2','T'); ?></span>
    <span></form></span>
</div>
<div class="columna6">
	<span><form id="formulario_horas4" action="#" method="post" name="formulario_horas4"></span>
	<span class="cuadroInputs"><?php select_horas('hastalunes2','hastalunes2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastamartes2','hastamartes2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastamiercoles2','hastamiercoles2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastajueves2','hastajueves2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastaviernes2','hastaviernes2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastasabado2','hastasabado2','T'); ?></span>
    <span class="cuadroInputs"><?php select_horas('hastadomingo2','hastadomingo2','T'); ?></span>
    <span></form></span>
</div>
<div class="columna7">
	<span class="cuadroInputs"><input type="checkbox" class="radio" name="arbitros" id="arbitros" value="arbitros" onClick="comprueba_linea2(this)"><b>Asignar &Aacute;rbitros</b></span>
    <div class="cont_arbitros">
    <span><form id="formulario_arbitros" action="#" method="post" name="formulario_arbitros"></span>
	<?php check_arbitros($id_liga); ?>
    <span></form></span>
	</div>
</div>
<div class="columna8">
	<span class="cuadroInputs"><input type="checkbox" class="radio" name="pistas" id="pistas" value="pistas" onClick="comprueba_linea2(this)"><b>Asignar Pistas</b></span>
    <div class="cont_pistas">
    <span><form id="formulario_pistas" action="#" method="post" name="formulario_pistas"></span>
    <?php check_pistas($id_liga); ?>
    <span></form></span>
	</div>
</div>
<div class="columna9">
	<?php
    if($idayvuelta == 'S'){
        echo '<span class="cuadroInputs"><input type="checkbox" class="radio" name="grupos" id="grupos" value="grupos" onClick="comprueba_linea3(this)"><b>Crear grupos manual</b></span>';
    }
    ?>
</div>
<div class="columna10">
    	<form id="formulario_id_equipos" action="#" method="post" name="formulario_id_equipos">
        <?php
		$id_equipos = array();
		$cont = 0;
		$db = new MySQL('session');//LIGA PADEL
		$consulta = $db->consulta("SELECT * FROM inscripcion WHERE liga = '$id_liga' AND division = '$id_division' AND pagado = 'S'; ");
		//$consulta = $db->consulta("SELECT * FROM equipo WHERE liga = '$id_liga' AND division = '$id_division' AND pagado = 'S'; ");
		while($resultados = $consulta->fetch_array(MYSQLI_ASSOC)){
			if($resultados['id_jugador1'] > 0 && $resultados['id_jugador2'] > 0){//ambos jugadores son registrados
				$id_equipos[$cont] = obten_consultaUnCampo('session','id_equipo','equipo','liga',$id_liga,'division',$id_division,'jugador1',$resultados['id_jugador1'],'jugador2',$resultados['id_jugador2'],'');
			}
			else if($resultados['id_jugador1'] > 0 && $resultados['id_jugador2'] == 0){//jugador1 registrado y jugador2 rapido
				$id_equipos[$cont] = obten_consultaUnCampo('session','id_equipo','equipo','liga',$id_liga,'division',$id_division,'jugador1',$resultados['id_jugador1'],'seguro_jug2',$resultados['id_inscripcion'],'');
			}
			else if($resultados['id_jugador1'] == 0 && $resultados['id_jugador2'] > 0){//jugador1 rapido y jugador2 registrado
				$id_equipos[$cont] = obten_consultaUnCampo('session','id_equipo','equipo','liga',$id_liga,'division',$id_division,'seguro_jug1',$resultados['id_inscripcion'],'jugador2',$resultados['id_jugador2'],'');
			}
			else{//jugador1 y jugador2 rapidos
				$id_equipos[$cont] = obten_consultaUnCampo('session','id_equipo','equipo','liga',$id_liga,'division',$id_division,'seguro_jug1',$resultados['id_inscripcion'],'seguro_jug2',$resultados['id_inscripcion'],'');
			}
			echo '<div class="numEquipo1">'.($cont+1).'</div><div class="equipo1">'.substr($resultados['nombre1'].' '.$resultados['apellidos1'],0,35).'</div><div class="numEquipo2">'.($cont+1).'</div><div class="equipo2">'.substr($resultados['nombre2'].' '.$resultados['apellidos2'],0,35).'</div>';
			echo '<input type="hidden" name="cont_equipo'.$cont.'" value="'.$id_equipos[$cont].'">';
			$cont++;
		}//fin while
		echo '<input type="hidden" name="cont_total" id="cont_total" value="'.$cont.'">';
		?>
        </form>
</div>
<div class="columna11">
    	<form id="formulario_grupos_equipos" action="#" method="post" name="formulario_grupos_equipos">
    	<?php
			for($i=0; $i<$cont; $i++){
				//echo '<div class="grupos">'.num_grupos($valor,($cont/2)).'</div>';
				echo '<div class="grupos1"><select name="cont_grupo'.$i.'" id="cont_grupo'.$i.'" class="input_select_liga">';
				echo '<option selected="selected" value="0">Grupo</option>';
				for($x=1; $x<=$cont/2; $x++){
					echo '<option value="'.$x.'">'.$x.'</option>';
				}
				echo '</select></div><div class="grupos2">&nbsp;</div>';
			} 
			
		?>
        </form>
</div>

<div class="horizontal">&nbsp;</div>
<div class="horizontal"><input type="button" id="btn_enviar" value="Crear Calendario" class="boton"  /></div>
<div id="respuesta" class="horizontal"></div>
<div class="horizontal">&nbsp;</div>
<div class="horizontal">&nbsp;</div>
<?php
		}//fin comprobacion inscripciones sin pagar
		else{
			echo '<div class="mensaje">
						<div class="caja_pago"><img src="../../../images/error.png" /><label>Se ha detectado que dispone de inscripciones para este torneo y divisi&oacute;n que no han realizado el pago, por favor envie un e-mail desde el men&uacute; Inscripciones -> Ver/Modificar a los integrantes del equipo, y si no desean participar elim&iacute;nelas para continuar.</label></div>
					</div>';
		}
	}//fin comprobacion crear calendario
	else{
		echo '<div class="horizontal">&nbsp;</div><div class="horizontal">&nbsp;</div><div class="horizontal">&nbsp;</div><div class="horizontal"><div>'.$advertencia.'</div></div>';
		/*echo '<div class="mensaje">
						<div class="caja_pago"><img src="../../../images/error.png" /><label>Se ha detectado que el pago del servicio de ida y vuelta  no se ha realizado, si no desea este servicio puede eliminarlo desde el men Liga -> Ver/Modificar.</label></div>
					</div>';*/
		//mostrar mensaje de que elimine el pago o que espere un dia por la mensajeria IPN si ha realizado el pago que contacte con la web
	}
?>
</body>
</html>
<?php 
}//final de comprobar si hay equipos y no hay partidos 
?>