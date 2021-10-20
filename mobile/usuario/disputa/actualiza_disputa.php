<?php
include_once ("../../../class/mysql.php");
include_once ("../../../class/liga.php");
include_once ("../../../class/usuario.php");
include_once ("../../../class/disputa.php");
include_once ("../../../class/partido.php");
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_secundarias.php");
include_once ("../../funciones/f_email.php");
require_once ("../../../PHPMailer/PHPMailerAutoload.php");
require_once ("../../../PHPMailer/class.smtp.php");
header("Content-Type: text/html;charset=ISO-8859-1");
session_start();
$pagina = $_SESSION['pagina'];
$opcion = $_SESSION['opcion'];
$liga = unserialize($_SESSION['liga']);
$usuario = unserialize($_SESSION['usuario']);
$num_division = $_SESSION['num_division'];
if ( $pagina != 'gestion_disputa' || $opcion != 0 ){
	header ("Location: ../cerrar_sesion.php");
}
else {
	include_once ("../../funciones/f_recoger_post.php");//SIEMPRE TIENE QUE ESTAR F_GENERAL MELOOOOONNNNNNNN 2 TARDES CON ESTO
	$disputa = new Disputa($id_disputa,'','','','','','');
	$id_partido = $disputa->getValor('partido');
	$partido = new Partido($id_partido,'','','','','','','','','','','','','','','','','','','','','','','','','');
	include_once ("../../funciones/f_conexion_email.php");
	//Set who the message is to be sent from
	$mail->setFrom('info@mitorneodepadel.es', 'mitorneodepadel.es');
	//Set an alternative reply-to address
	$mail->addReplyTo('info@mitorneodepadel.es', 'mitorneodepadel.es');
	//Set who the message is to be sent to
	
	if(isset($l_j1)){$email_l_j1 = $mail->AddBCC(obten_consultaUnCampo('unicas','email','jugador','id_jugador',$l_j1,'','','','','','',''));}
	if(isset($l_j2)){$email_l_j2 = $mail->AddBCC(obten_consultaUnCampo('unicas','email','jugador','id_jugador',$l_j2,'','','','','','',''));}
	if(isset($v_j1)){$email_v_j1 = $mail->AddBCC(obten_consultaUnCampo('unicas','email','jugador','id_jugador',$v_j1,'','','','','','',''));}
	if(isset($v_j2)){$email_v_j2 = $mail->AddBCC(obten_consultaUnCampo('unicas','email','jugador','id_jugador',$v_j2,'','','','','','',''));}
	$mail->addCC($usuario->getValor('email'));//en copia al usuario
	
	//$mail->addAddress('manu_oamuf@hotmail.com', 'Manuel');
	//Set the subject line
	$asunto = utf8_decode('El Administrador de tu Torneo de Padel <'.$liga->getValor("nombre").'> División '.$num_division.', en relación con una disputa abierta en el partido de la jornada '.$partido->getValor("jornada").'.');
	$mail->Subject = $asunto;
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	if($partido->getValor("jornada") != 0){
		$cuerpo .= '<br>Jornada: '.$partido->getValor("jornada");
	}
	else{
		$cuerpo .= '<br>Eliminatoria: '.obten_nombreEliminatoria($partido->getValor("eliminatoria"));
	}
	$cuerpo .= '<br>Equipo Local: ('.utf8_encode(obtenNombreJugador($partido->getValor("local"),"jugador1")).' - '.utf8_encode(obtenNombreJugador($partido->getValor("local"),"jugador2")).')<br>Equipo Visitante: ('.utf8_encode(obtenNombreJugador($partido->getValor("visitante"),"jugador1")).' - '.utf8_encode(obtenNombreJugador($partido->getValor("visitante"),"jugador2")).')';
	$cuerpo .= '<br>Estado del Partido: '.obten_estadoPartido($partido->getValor("estado"));
	if($partido->getValor('fecha') != '0000-00-00'){
		$cuerpo .= '<br>Fecha: '.$partido->getValor("fecha");
	}
	
	$cuerpo .= '<br><br>El Administrador ha escrito:<br>'.utf8_encode($texto).'.<br>&nbsp;';
	//$cuerpo .= '</div>';
	$body = email_jugadorAdmin("<br>El Administrador de tu Torneo de Padel te envia este e-m@il:<br>",$cuerpo);
	$mail->msgHTML($body);
	//Replace the plain text body with one created manually
	$mail->AltBody = 'This is a plain-text message body';
	//Attach an image file
	
	//send the message, check for errors
	if ($mail->send()) {
		$disputa->setValor('respuesta','S');
		$disputa->modificar();
		//echo "Mailer Error: " . $mail->ErrorInfo;
	}
	
}//fin else

?>