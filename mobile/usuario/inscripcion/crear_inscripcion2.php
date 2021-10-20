<?php
include_once ("../../../class/mysql.php");
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_fechasHoras.php");
include_once ("../../../class/division.php");
include_once ("../../../class/noticia.php");
include_once ("../../../class/inscripcion.php");
include_once ("../../../class/notificacion.php");
include_once ("../../../class/jugador.php");
include_once ("../../../class/equipo.php");
include_once ("../../../class/pago_admin.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<style type="text/css">
.actualizacion {
	border-radius:7px;
	background-color:#c5fbc6;
	text-align:center;
	font-size:80%;
	padding:12px;
	margin-left:15%;
	color:#006;
}
.actualizacion img{
	width:2%;
}
</style>
<?php
session_start();
$pagina = $_SESSION['pagina'];
$opcion = $_SESSION['opcion'];
$id_liga = $_SESSION['id_liga'];
$tipo_pago = $_SESSION['tipo_pago'];
$division = unserialize($_SESSION['division']);
$id_division = $division->getValor('id_division');
$precio = $division->getValor('precio');
$comienzo = $division->getValor('comienzo');
$suscripcion = $division->getValor('suscripcion');
$id_usuario = $_SESSION['id_usuario'];
if($_SESSION['cuenta_paypal'] != ''){$email_ins = $_SESSION['cuenta_paypal'];}
else{$email_ins = $_SESSION['email'];}
$bd = $_SESSION['bd'];
if ( $pagina != 'gestion_inscripcion' || $opcion != 1 ){
	header ("Location: ../cerrar_sesion.php");
}
else {
	include_once ("../../funciones/f_recoger_post.php");
	$texto = 'La Inscripción se ha realizado correctamente.';
	$descripcion_noticia = '';
	if(isset($id_jugador1)){//si existe el id_jugador del jugador1
		$jugador1 = new Jugador($id_jugador1,'','','','','','','','','','','','','','','');
		$dni1 = $jugador1->getValor('dni');
		$nombre1 = $jugador1->getValor('nombre');
		$apellidos1 = $jugador1->getValor('apellidos');
		$password1 = $jugador1->getValor('password');
		$direccion1 = $jugador1->getValor('direccion');
		$fec_nac1 = $jugador1->getValor('fec_nac');
		$ciudad = $jugador1->getValor('ciudad');
		$provincia = $jugador1->getValor('provincia');
		$pais = $jugador1->getValor('pais');
		$telefono1 = $jugador1->getValor('telefono');
		$email1 = $jugador1->getValor('email');
		$genero1 = $jugador1->getValor('genero');
		//echo 'seleccionado1';
	}
	else{//datos introducidos del jugador 1
		$jugador_encontrado = buscar_jugador($nombre1,$apellidos1,$email1,$telefono1,$dni1);
		if($jugador_encontrado != 0){//se ha encontrado
			$jugador1 = new Jugador($jugador_encontrado,'','','','','','','','','','','','','','','');
			$id_jugador1 = $jugador1->getValor('id_jugador');
			$dni1 = $jugador1->getValor('dni');
			$nombre1 = $jugador1->getValor('nombre');
			$apellidos1 = $jugador1->getValor('apellidos');
			$password1 = $jugador1->getValor('password');
			$direccion1 = $jugador1->getValor('direccion');
			$fec_nac1 = $jugador1->getValor('fec_nac');
			$ciudad = $jugador1->getValor('ciudad');
			$provincia = $jugador1->getValor('provincia');
			$pais = $jugador1->getValor('pais');
			$telefono1 = $jugador1->getValor('telefono');
			$email1 = $jugador1->getValor('email');
			$genero1 = $jugador1->getValor('genero');
			//echo 'encontrado1';
		}
		else{$fec_nac1 = $anyo1.'-'.$mes1.'-'.$dia1;}
	}
	if(isset($id_jugador2)){//si existe el dni del jugador2
		$jugador2 = new Jugador($id_jugador2,'','','','','','','','','','','','','','','');
		$dni2 = $jugador2->getValor('dni');
		$nombre2 = $jugador2->getValor('nombre');
		$apellidos2 = $jugador2->getValor('apellidos');
		$password2 = $jugador2->getValor('password');
		$direccion2 = $jugador2->getValor('direccion');
		$fec_nac2 = $jugador2->getValor('fec_nac');
		$ciudad2 = $jugador2->getValor('ciudad');
		$provincia2 = $jugador2->getValor('provincia');
		$pais2 = $jugador2->getValor('pais');
		$telefono2 = $jugador2->getValor('telefono');
		$email2 = $jugador2->getValor('email');
		$genero2 = $jugador2->getValor('genero');
		//echo 'seleccionado2';
	}
	else{//datos introducidos del jugador 2
		$jugador_encontrado = buscar_jugador($nombre2,$apellidos2,$email2,$telefono2,$dni2);
		if($jugador_encontrado != 0){//se ha encontrado
			$jugador2 = new Jugador($jugador_encontrado,'','','','','','','','','','','','','','','');
			$id_jugador2 = $jugador2->getValor('id_jugador');
			$dni2 = $jugador2->getValor('dni');
			$nombre2 = $jugador2->getValor('nombre');
			$apellidos2 = $jugador2->getValor('apellidos');
			$password2 = $jugador2->getValor('password');
			$direccion2 = $jugador2->getValor('direccion');
			$fec_nac2 = $jugador2->getValor('fec_nac');
			$ciudad2 = $jugador2->getValor('ciudad');
			$provincia2 = $jugador2->getValor('provincia');
			$pais2 = $jugador2->getValor('pais');
			$telefono2 = $jugador2->getValor('telefono');
			$email2 = $jugador2->getValor('email');
			$genero2 = $jugador2->getValor('genero');
			//echo 'encontrado2';
		}
		else{$fec_nac2 = $anyo2.'-'.$mes2.'-'.$dia2;}
	}
	//verificar obligatorios nombre apellidos email
	if(!empty($nombre1) && !empty($nombre2) && !empty($apellidos1) && !empty($apellidos2) && !empty($email1) && !empty($email2)){//si entra aqui todo bien
		if(isset($id_jugador1)){$jug1_enc = busca_jugadorEnOtroEquipo($id_liga,$id_jugador1);}//si existe es porque el jugador1 va por login
		if(isset($id_jugador2)){$jug2_enc = busca_jugadorEnOtroEquipo($id_liga,$id_jugador2);}//si existe es porque el jugador2 va por login
		if(!isset($jugador1)){//si existe es buscado o encontrado NO INSERTO
			if($jug2_enc == ''){//si el jugador 2 no está inscrito
				$jugador1 = new Jugador(NULL,$dni1,ucwords($nombre1),ucwords($apellidos1),$password1,$direccion1,$fec_nac1,$ciudad,$provincia,$pais,$telefono1,$email1,$genero1,0,'A');
				$jugador1->insertar();
                $id_jugador1 = obten_consultaUnCampo('unicas','id_jugador','jugador','nombre',ucwords($nombre1),'apellidos',ucwords($apellidos1),'email',$email1,'','','');
			}
		}
		if(!isset($jugador2)){//si existe es buscado o encontrado NO INSERTO
			if($jug1_enc == ''){//si el jugador 1 no está inscrito
				$jugador2 = new Jugador(NULL,$dni2,$nombre2,$apellidos2,$password2,$direccion2,$fec_nac2,$ciudad2,$provincia2,$pais2,$telefono2,$email2,$genero2,0,'A');
				$jugador2->insertar();
                $id_jugador = obten_consultaUnCampo('unicas','id_jugador','jugador','nombre',ucwords($nombre2),'apellidos',ucwords($apellidos2),'email',$email2,'','','');
			}
		}
		if($jug1_enc == '' && $jug2_enc == ''){//si estan vacios, jugador nuevo o no está registrado en la torneo
			$inscripcion = new Inscripcion(NULL,$id_division,$id_liga,'M',$precio,'S',$id_jugador1,$dni1,$nombre1,$apellidos1,$password1,$direccion1,$fec_nac1,$ciudad,$provincia,$pais,$telefono1,$email1,$genero1,$id_jugador2,$dni2,$nombre2,$apellidos2,$password2,$direccion2,$fec_nac2,$ciudad2,$provincia2,$pais2,$telefono2,$email2,$genero2);
			$inscripcion->insertar();//inserto inscripcion
			$equipo = new Equipo(NULL,$id_jugador1,NULL,$id_jugador2,NULL,$id_liga,$id_division,'S',0,obten_fechaHora());///AÑADIDO CAMPO FEC_CREACION
			$equipo->insertar();//inserto equipo
			$notificacion = new Notificacion('',$id_usuario,$id_liga,$id_division,'modificar_inscripcion.php',date('Y-m-d H:i:s'),'N');
			$notificacion->insertar();
			//cambiar por tipo pago
			if($tipo_pago > 0){//si no es gratis
                $id_equipo = obten_consultaUnCampo('session','id_equipo','equipo','liga',$id_liga,'division',$id_division,'jugador1',$id_jugador1,'jugador2',$id_jugador2,'');
				$pago_admin = new Pago_admin(NULL,$id_liga,$id_division,$bd,$id_equipo,$precio,'M','S',$email_ins,$id_usuario,$email1,obten_fechaHora(),'','','',$id_jugador1,$id_jugador2,'R');
				$pago_admin->insertar();//inserto el pago
			}
			//SI NO HAY FECHA DE SUSCRIPCION, ACTIVO LA SUSCRIPCION
			if($suscripcion == '0000-00-00 00:00:00' || empty($suscripcion)){
				$division->setValor('suscripcion',obten_fechaHora());
				$division->modificar();
			}
			$descripcion_noticia .= utf8_decode('El administrador ha inscrito un nuevo equipo compuesto por '.$nombre1.' '.substr($apellidos1,0,1).'. y '.$nombre2.' '.substr($apellidos2,0,1).'.');
		}
		else{//si no está duplicado
			$texto = 'Error de inscripción, el jugador ';
			if($jug1_enc != ''){$texto .= '1 ya está registrado en este torneo.';}
			else{$texto .= '2 ya está registrado en este torneo.';}
		}
	}//fin if campos obligatorios
	if(!empty($descripcion_noticia)){
		$resumen_noticia = utf8_decode('Sección: Inscripcion -> Crear Nueva.');
		$fecha_noticia = obten_fechahora();
		$noticia = new Noticia(NULL,$id_liga,$id_division,$resumen_noticia,$descripcion_noticia,$fecha_noticia,'');
		$noticia->insertar();
		unset($noticia);
	}
	unset($inscripcion,$equipo,$pago_admin,$notificacion);
	echo '<span class="actualizacion"><img src="../../../images/ok.png" />'.utf8_decode($texto).'</span>';
	
}

?>