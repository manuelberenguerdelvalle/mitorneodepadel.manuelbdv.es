<?php
include_once ("../../../class/mysql.php");
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_fechasHoras.php");
include_once ("../../funciones/f_email.php");
include_once ("../../../class/liga.php");
include_once ("../../../class/division.php");
include_once ("../../../class/noticia.php");
include_once ("../../../class/puntuacion.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<style type="text/css">
.actualizacion {
	border-radius:7px;
	background-color:#c5fbc6;
	text-align:center;
	font-size:80%;
	padding:12px;
	color:#006;
}
.actualizacion img{
	width:10%;
	margin-top:1%;
	margin-right:1%;
}
</style>
<?php
session_start();
$pagina = $_SESSION['pagina'];
$liga = unserialize($_SESSION['liga']);
$division = unserialize($_SESSION['division']);
$opcion = $_SESSION['opcion'];
$id_usuario = $_SESSION['id_usuario'];

$bd_usuario = $_SESSION['bd'];
$tipo_pago = $liga->getValor('tipo_pago');

if ( $pagina != 'gestion_puntuacion' ){
	header ("Location: ../cerrar_sesion.php");
}
else {
	include_once ("../../funciones/f_recoger_post.php");
	$descripcion_noticia = '';
	$modif = false;
	if($tipo_pago > 0){//si es gratis compruebo 
		$id_liga = $liga->getValor('id_liga');
		$id_division = $division->getValor('id_division');
		if(obten_consultaUnCampo('session','COUNT(id_puntuacion)','puntuacion','liga',$id_liga,'usuario',$id_usuario,'division',$id_division,'','','') > 0){
			$id_puntuacion = obten_consultaUnCampo('session','id_puntuacion','puntuacion','liga',$id_liga,'usuario',$id_usuario,'division',$id_division,'','','');
		}
		else{$id_puntuacion = '';}
		if($id_puntuacion == '' && ($inscripcion > 0 || $victoria_amistoso > 0 || $victoria > 0 || $dieciseisavos > 0 || $octavos > 0 || $cuartos > 0 || $semifynal > 0 || $fynal > 0 || $primero > 0 || $segundo > 0 || $tercero > 0 || $cuarto) ){//si no hay puntuacion hay que crearla
			$puntuacion = new Puntuacion('','',$id_liga,$id_division,'T',0,$inscripcion,$victoria_amistoso,$victoria,$dieciseisavos,$octavos,$cuartos,$semifynal,$fynal,$primero,$segundo,$tercero,$cuarto);
			$puntuacion->setValor('usuario',$id_usuario);//lo seteamos aqui para que no entre en consulta
			$puntuacion->insertar();
			$descripcion_noticia .= 'La puntuación del Torneo es: ';
			if($inscripcion > 0){$descripcion_noticia .= 'Por inscribirse =  '.$inscripcion.' ptos. ';}
			if($victoria > 0){$descripcion_noticia .= 'Por victoria en torneo =  '.$victoria.' ptos. ';}
			if($dieciseisavos > 0){$descripcion_noticia .= 'Por pasar a 16avos =  '.$dieciseisavos.' ptos. ';}
			if($octavos > 0){$descripcion_noticia .= 'Por pasar a octavos =  '.$octavos.' ptos. ';}
			if($cuartos > 0){$descripcion_noticia .= 'Por pasar a cuartos =  '.$cuartos.' ptos. ';}
			if($semifynal > 0){$descripcion_noticia .= 'Por pasar a la semifinal =  '.$semifynal.' ptos. ';}
			if($fynal > 0){$descripcion_noticia .= 'Por pasar a la final =  '.$fynal.' ptos. ';}
			if($primero > 0){$descripcion_noticia .= 'Por ganar el torneo =  '.$primero.' ptos. ';}
			if($segundo > 0){$descripcion_noticia .= 'Por quedar segundo/a =  '.$segundo.' ptos. ';}
			$texto = 'La puntuación se ha creado correctamente.';
			$modif = true;
		}
		else{
			$texto = 'La actualización no se ha realizado, la puntuación está a ceros.';
		}
		if($id_puntuacion > 0){//hay puntuacion la modificamos
			$texto = 'Actualizaci&oacute;n correcta.';
			$descripcion_noticia .= 'Se ha modificado la puntuación del Torneo: ';
			$puntuacion = new Puntuacion($id_puntuacion,'','','','','','','','','','','','','','','','','');
			if($inscripcion > 0 && $inscripcion != $puntuacion->getValor('inscripcion')){
				$puntuacion->setValor('inscripcion',$inscripcion);
				$modif = true;
				$descripcion_noticia .= 'Por inscribirse =  '.$inscripcion.' ptos. ';
			}
			if($victoria > 0 && $victoria != $puntuacion->getValor('victoria')){
				$puntuacion->setValor('victoria',$victoria);
				$modif = true;
				$descripcion_noticia .= 'Por victoria en torneo =  '.$victoria.' ptos. ';
			}
			if($dieciseisavos > 0 && $dieciseisavos != $puntuacion->getValor('dieciseisavos')){
				$puntuacion->setValor('dieciseisavos',$dieciseisavos);
				$modif = true;
				$descripcion_noticia .= 'Por pasar a 16avos =  '.$dieciseisavos.' ptos. ';
			}
			if($octavos > 0 && $octavos != $puntuacion->getValor('octavos')){
				$puntuacion->setValor('octavos',$octavos);
				$modif = true;
				$descripcion_noticia .= 'Por pasar a octavos =  '.$octavos.' ptos. ';
			}
			if($cuartos > 0 && $cuartos != $puntuacion->getValor('cuartos')){
				$puntuacion->setValor('cuartos',$cuartos);
				$modif = true;
				$descripcion_noticia .= 'Por pasar a cuartos =  '.$cuartos.' ptos. ';
			}
			if($semifynal > 0 && $semifynal != $puntuacion->getValor('semifynal')){
				$puntuacion->setValor('semifynal',$semifynal);
				$modif = true;
				$descripcion_noticia .= 'Por pasar a la semifinal =  '.$semifynal.' ptos. ';
			}
			if($fynal > 0 && $fynal != $puntuacion->getValor('fynal')){
				$puntuacion->setValor('fynal',$fynal);
				$modif = true;
				$descripcion_noticia .= 'Por pasar a la final =  '.$fynal.' ptos. ';
			}
			if($primero > 0 && $primero != $puntuacion->getValor('primero')){
				$puntuacion->setValor('primero',$primero);
				$modif = true;
				$descripcion_noticia .= 'Por ganar el torneo =  '.$primero.' ptos. ';
			}
			if($segundo > 0 && $segundo != $puntuacion->getValor('segundo')){
				$puntuacion->setValor('segundo',$segundo);
				$modif = true;
				$descripcion_noticia .= 'Por quedar segundo/a =  '.$segundo.' ptos. ';
			}
		}//fin if existe puntuacion
		if($modif){
			$puntuacion->modificar();
		}
	}//fin tipo_pago
	if($modif){
		$resumen_noticia = utf8_decode('Sección: Puntuación -> Ver/Modificar.');
		$descripcion_noticia = utf8_decode($descripcion_noticia);
		$fecha_noticia = obten_fechahora();
		$noticia = new Noticia(NULL,$id_liga,$id_division,$resumen_noticia,$descripcion_noticia,$fecha_noticia,'');
		$noticia->insertar();
		unset($noticia);
	}
	unset($liga,$division);
	echo '<span class="actualizacion"><img src="../../../images/ok.png" />'.utf8_decode($texto).'</span>';
}

?>