<?php
include_once ("../../funciones/f_obten.php");
include_once ("../../funciones/f_general.php");
include_once ("../../funciones/f_calendario.php");
include_once ("../../funciones/f_fechasHoras.php");
include_once ("../../funciones/f_email.php");
include_once ("../../../class/mysql.php");
include_once ("../../../class/division.php");
include_once ("../../../class/partido.php");
include_once ("../../../class/noticia.php");
require_once ("../../../PHPMailer/PHPMailerAutoload.php");
require_once ("../../../PHPMailer/class.smtp.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<style type="text/css">
.actualizacion {
	border-radius:7px;
	background-color:#c5fbc6;
	text-align:center;
	font-size:80%;
	padding:12px;
	margin-left:5%;
	color:#006;
}
.actualizacion img{
	width:2%;
	margin-top:1%;
	margin-right:1%;
}
</style>
<?php
session_start();
$pagina = $_SESSION['pagina'];
$opcion = $_SESSION['opcion'];
$division = unserialize($_SESSION['division']);
$id_liga = $_SESSION['id_liga'];
$idayvuelta = $_SESSION['idayvuelta'];
if ( $pagina != 'gestion_division' && $opcion != 2 ){
	header ("Location: ../cerrar_sesion.php");
}
else {
	//para que existan todos
	if(!isset($lunes)){$lunes = '';}
	if(!isset($martes)){$martes = '';}
	if(!isset($miercoles)){$miercoles = '';}
	if(!isset($jueves)){$jueves = '';}
	if(!isset($viernes)){$viernes = '';}
	if(!isset($sabado)){$sabado = '';}
	if(!isset($domingo)){$domingo = '';}
	include_once ("../../funciones/f_recoger_post.php");
	$texto = 'El calendario se ha creado correctamente. Puede verlo en Partidos';
	$id_division = $division->getValor('id_division');
	$id_equipos = array();
	$grupos = array();
	$id_equipos = obtenEquiposDivision($id_liga,$id_division);//obtiene los ids de los equipos
	$num_equipos = count($id_equipos);
	if($idayvuelta == 'S'){//grupos
		if($cont_total > 0){//personalizados
			for($i=0; $i<$cont_total; $i++){
				$id_eq_tmp = 'cont_equipo'.$i;
				$id_equipos[$i] = $$id_eq_tmp;
				$grupo_tmp = 'cont_grupo'.$i;
				$grupos[$i] = $$grupo_tmp; 
			}
		}
		else{//automaticos
			$nuevo_grupo = false;
			$num_grupos = intval($num_equipos / 3);
			$resto = $num_equipos % 3;
			for($i=0; $i<$num_grupos; $i++){//creamos los grupos
				$grupos[$i*3] = $id_equipos[$i];
				$grupos[($i*3)+1] = $id_equipos[$i+$num_grupos];
				$grupos[($i*3)+2] = $id_equipos[$i+($num_grupos*2)];
			}
			$resul = obten_nombreEliminatoria($num_grupos);
			if($resto > 0){
				if($resto == 1){//añadimos al grupo 0 un equipo y queda con 4 equipos
					$grupos[$i*3] = $id_equipos[$num_equipos-1];
				}
				else if($resto == 2 && $resul != ''){//los grupos estan justos añado 2 equipos, 2 grupos de 4 equipos
					$grupos[$i*3] = $id_equipos[$num_equipos-2];
					$grupos[($i*3)+1] = $id_equipos[$num_equipos-1];
				}
				else{//creo nuevo grupo de 2
					$grupos[$i*3] = $id_equipos[$num_equipos-2];
					$grupos[($i*3)+1] = $id_equipos[$num_equipos-1];
					$num_grupos++;
					$nuevo_grupo = true;
				} 
			}//fin resto
		}//fin else grupos automaticos
	}//fin ida y vuelta
	if($modo == 0){//AUTOMATICO OK
		if($idayvuelta == 'S'){//grupos
			for($i=0; $i<$num_grupos; $i++){//grupos
				if($i == ($num_grupos-1) && $nuevo_grupo){$jornadas = 2;}
				else if(($i == 0 && $resto >= 1) || ($i == 1 && $resto == 2)){$jornadas = 4;}
				else{$jornadas = 3;}
				for($j=1; $j<=$jornadas; $j++){//jornadas
					if($jornadas == 2){//2 jornadas
						if($j == 1){//jornada1
							$local = $grupos[$i*3];
							$visitante = $grupos[($i*3)+1];
						}
						else{//jornada2
							$local = $grupos[($i*3)+1];
							$visitante = $grupos[$i*3];
						}
					}
					else if($jornadas == 4){//4 jornadas
								if($j == 1){//jornada1
									$local = $grupos[$i*3];
									$visitante = $grupos[($i*3)+1];
								}
								else if($j == 2){//jornada2
									$local = $grupos[($i*3)+2];
									if($i == 0){$visitante = $id_equipos[$num_equipos-$resto];}
									if($i == 1 && $resto == 2){$visitante = $id_equipos[$num_equipos-1];}
								}
								else if($j == 3){//jornada3
									$local = $grupos[($i*3)+1];
									$visitante = $grupos[($i*3)+2];
								}
								else{//jornada4
									if($i == 0){$local = $id_equipos[$num_equipos-$resto];}
									if($i == 1 && $resto == 2){$local = $id_equipos[$num_equipos-1];}
									$visitante = $grupos[$i*3];
								}
					}
					else{//normal de 3 jornadas
								if($j == 1){//jornada1
									$local = $grupos[$i*3];
									$visitante = $grupos[($i*3)+1];
								}
								else if($j == 2){//jornada2
									$local = $grupos[($i*3)+1];
									$visitante = $grupos[($i*3)+2];
								}
								else{//jornada3
									$local = $grupos[($i*3)+2];
									$visitante = $grupos[$i*3];
								}
					}//fin else 3 jornadas
					$grup = $i+1;
					$partido = new Partido(NULL,$j,NULL,NULL,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
					$partido->insertar();
					unset($partido);
				}//fin for jornadas
			}//fin for grupos
		}//fin grupos
		else{//eliminatorias
			$div_entera = intval($num_equipos / 2);
			$eliminatoria = $div_entera; //obtenemos la cantidad de eliminatorias
			$resto = $num_equipos % 2; //obtenemos el resto para generar eliminatoria ganadas
			if($eliminatoria >= 8){$eliminatoria = 16;}
			else if($eliminatoria >= 4){$eliminatoria = 8;}
			else if($eliminatoria >= 2){$eliminatoria = 4;}
			else if($eliminatoria >= 1){$eliminatoria = 2;}
			else{}
			if($resto == 0){$cuadros = $div_entera;}
			else{$cuadros = $div_entera + 1;}
			for($i=0; $i<$cuadros; $i++){
				if($i == $cuadros-1 && $resto > 0){//Si equipos impares, el ultimo local pasa directamente a la siguiente eliminatoria
					$local = $id_equipos[$num_equipos-1];
					$partido = new Partido(NULL,0,NULL,NULL,$local,0,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,$local,'N',1,0,$id_division,0,0,0,0,0,0,$id_liga,0,$eliminatoria,'');
				}
				else{//pares
					$local = $id_equipos[$i];
					$visitante = $id_equipos[$i+$div_entera];
					$partido = new Partido(NULL,0,NULL,NULL,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,0,$eliminatoria,'');
				}
				$partido->insertar();
				unset($partido);
			}
		}//fin else
	}//FIN IF AUTOMATICO
	else{//PERSONALIZADO
		//Me aseguro que estan los días aunque sea vacío
		//hay que controlar que aunque esté marcado el día, si no hay horario se ponga a vacío
		if(isset($num_id_pista) && $num_id_pista != 0){//hay pistas
			$id_pistas = array();//ARRAY PISTAS
			for($i=0; $i<$num_id_pista; $i++){
				$pistas = 'id_pista'.$i;
				$id_pistas[$i] = $$pistas;
			}
		}
		if(isset($num_id_arbitro) && $num_id_arbitro != 0){//hay arbitros
			$id_arbitros = array();//ARRAY ARBITROS
			for($i=0; $i<$num_id_arbitro; $i++){
				$arbitros = 'id_arbitro'.$i;
				$id_arbitros[$i] = $$arbitros;
			}
		}
		$array_desde1 = array();
		$array_hasta1 = array();
		$array_desde2 = array();
		$array_hasta2 = array();
		for($i=0; $i<8; $i++){//vaciamos
			$array_desde1[$i] = 0;
			$array_hasta1[$i] = 0;
			$array_desde2[$i] = 0;
			$array_hasta2[$i] = 0;
		}
		$cont = 1;//LUNES
		if($desdelunes1 != ''){$array_desde1[$cont] = $desdelunes1;}
		else{$array_desde1[$cont] = 0;}
		if($hastalunes1 != ''){$array_hasta1[$cont] = $hastalunes1;}
		else{$array_hasta1[$cont] = 0;}
		if($desdelunes2 != ''){$array_desde2[$cont] = $desdelunes2;}
		else{$array_desde2[$cont] = 0;}
		if($hastalunes2 != ''){$array_hasta2[$cont] = $hastalunes2;}
		else{$array_hasta2[$cont] = 0;}
		$cont++;//MARTES
		if($desdemartes1 != ''){$array_desde1[$cont] = $desdemartes1;}
		else{$array_desde1[$cont] = 0;}
		if($hastamartes1 != ''){$array_hasta1[$cont] = $hastamartes1;}
		else{$array_hasta1[$cont] = 0;}
		if($desdemartes2 != ''){$array_desde2[$cont] = $desdemartes2;}
		else{$array_desde2[$cont] = 0;}
		if($hastamartes2 != ''){$array_hasta2[$cont] = $hastamartes2;}
		else{$array_hasta2[$cont] = 0;}
		$cont++;//MIERCOLES
		if($desdemiercoles1 != ''){$array_desde1[$cont] = $desdemiercoles1;}
		else{$array_desde1[$cont] = 0;}
		if($hastamiercoles1 != ''){$array_hasta1[$cont] = $hastamiercoles1;}
		else{$array_hasta1[$cont] = 0;}
		if($desdemiercoles2 != ''){$array_desde2[$cont] = $desdemiercoles2;}
		else{$array_desde2[$cont] = 0;}
		if($hastamiercoles2 != ''){$array_hasta2[$cont] = $hastamiercoles2;}
		else{$array_hasta2[$cont] = 0;}
		$cont++;//JUEVES
		if($desdejueves1 != ''){$array_desde1[$cont] = $desdejueves1;}
		else{$array_desde1[$cont] = 0;}
		if($hastajueves1 != ''){$array_hasta1[$cont] = $hastajueves1;}
		else{$array_hasta1[$cont] = 0;}
		if($desdejueves2 != ''){$array_desde2[$cont] = $desdejueves2;}
		else{$array_desde2[$cont] = 0;}
		if($hastajueves2 != ''){$array_hasta2[$cont] = $hastajueves2;}
		else{$array_hasta2[$cont] = 0;}
		$cont++;//VIERNES
		if($desdeviernes1 != ''){$array_desde1[$cont] = $desdeviernes1;}
		else{$array_desde1[$cont] = 0;}
		if($hastaviernes1 != ''){$array_hasta1[$cont] = $hastaviernes1;}
		else{$array_hasta1[$cont] = 0;}
		if($desdeviernes2 != ''){$array_desde2[$cont] = $desdeviernes2;}
		else{$array_desde2[$cont] = 0;}
		if($hastaviernes2 != ''){$array_hasta2[$cont] = $hastaviernes2;}
		else{$array_hasta2[$cont] = 0;}
		$cont++;//SABADO
		if($desdesabado1 != ''){$array_desde1[$cont] = $desdesabado1;}
		else{$array_desde1[$cont] = 0;}
		if($hastasabado1 != ''){$array_hasta1[$cont] = $hastasabado1;}
		else{$array_hasta1[$cont] = 0;}
		if($desdesabado2 != ''){$array_desde2[$cont] = $desdesabado2;}
		else{$array_desde2[$cont] = 0;}
		if($hastasabado2 != ''){$array_hasta2[$cont] = $hastasabado2;}
		else{$array_hasta2[$cont] = 0;}
		$cont++;//DOMINGO
		if($desdedomingo1 != ''){$array_desde1[$cont] = $desdedomingo1;}
		else{$array_desde1[$cont] = 0;}
		if($hastadomingo1 != ''){$array_hasta1[$cont] = $hastadomingo1;}
		else{$array_hasta1[$cont] = 0;}
		if($desdedomingo2 != ''){$array_desde2[$cont] = $desdedomingo2;}
		else{$array_desde2[$cont] = 0;}
		if($hastadomingo2 != ''){$array_hasta2[$cont] = $hastadomingo2;}
		else{$array_hasta2[$cont] = 0;}
		for($i=0; $i<8; $i++){//para evitar que se entre en el horario con todo vacio o parejas incorrectas
			if($i != 0){//el 0 no es ningun dia y entra en el else y pone domingo a ''
				//si es todo a 0 la variable la pongo a vacio
				if($array_desde1[$i] == 0 && $array_hasta1[$i] == 0 && $array_desde2[$i] == 0 && $array_hasta2[$i] == 0){
					if($i == 1){$lunes = '';}
					else if($i == 2){$martes = '';}
					else if($i == 3){$miercoles = '';}
					else if($i == 4){$jueves = '';}
					else if($i == 5){$viernes = '';}
					else if($i == 6){$sabado = '';}
					else{$domingo = '';}
				}
				//si alguna de las parejas es 0 y la otra no, pongo ambas a 0 porque son datos erroneos
				if($array_desde1[$i] == 0 && $array_hasta1[$i] != 0){//
					$array_desde1[$i] = 0;
					$array_hasta1[$i] = 0;
				}
				if($array_desde1[$i] != 0 && $array_hasta1[$i] == 0){
					$array_desde1[$i] = 0;
					$array_hasta1[$i] = 0;
				}
				if($array_desde2[$i] == 0 && $array_hasta2[$i] != 0){
					$array_desde2[$i] = 0;
					$array_hasta2[$i] = 0;
				}
				if($array_desde2[$i] != 0 && $array_hasta2[$i] == 0){
					$array_desde2[$i] = 0;
					$array_hasta2[$i] = 0;
				}
			}
		}
		$inicio = insercion_fecha($inicio);//formato correcto aaaa-mm-dd
		//echo 'inicial '.$inicio;
		$cont_horas = $duracion_partido;
		$num_dia_semana = obten_numDiaSemana($inicio);//numero día semana 1=lunes 7=domingo
		$sumar_dias = dias_comienzo($num_dia_semana,$lunes,$martes,$miercoles,$jueves,$viernes,$sabado,$domingo);
		if($sumar_dias != 0){
			$inicio = fecha_suma($inicio,'','',$sumar_dias,'','','');
		}
		//echo 'Despues de suma '.$inicio;
		$num_dia_semana = obten_numDiaSemana($inicio);//numero día semana 1=lunes 7=domingo
		if(isset($id_arbitros)){$num_arbitros = count($id_arbitros);}
		else{$arbitro_insertar = NULL;}
		if(isset($id_pistas)){$num_pistas = count($id_pistas);}
		else{$pista_insertar = NULL;}
		if($idayvuelta == 'S'){//grupos
			if($cont_total > 0){//personalizados
				//ELIMINAR ESTO SON 2 JORNADAS, 2 PARTIDOS POR EQUIPO
				$num_grupos = max($grupos);
				$jornadas = 100;
				for($i=0; $i<$num_grupos; $i++){
					$temp = 0;
					for($z=0; $z<$cont_total; $z++){
						if($grupos[$z] == $i){$temp++;}//si es el mismo grupo sumo
					}
					if($temp < $jornadas){$jornadas = $temp;}//obtenemos el grupo con menos equipos para obtener jornadas
				}
				if($jornadas > 2){$jornadas--;}//jornadas es numero menor de equipos -1 con equipos mayor que dos
			}//fin if personalizados
			for($i=1; $i<=$num_grupos; $i++){//grupos
				if(isset($id_pistas)){//GESTION PISTAS
					if($num_pistas > 0){//entro decremento y no sumo dias
						$num_pistas--;
					}
					else{//vuelvo a restablecer las pistas
						$num_pistas = count($id_pistas);
						$num_pistas--;
					}
					$pista_insertar = $id_pistas[$num_pistas];
				}
				if(isset($num_arbitros)){//GESTION ARBITROS
					if($num_arbitros == 0){
						$num_arbitros = count($id_arbitros);
					}
					$num_arbitros--;
					$arbitro_insertar = $id_arbitros[$num_arbitros];
				}
				if($cont_total > 0){//personalizados
					$equipos_temp = array();
					$a = 0;
					$impar = false;
					for($z=0; $z<$cont_total; $z++){
						if($grupos[$z] == $i){
							$equipos_temp[$a] = $id_equipos[$z];
							$a++;
						}//si es el mismo grupo sumo
					}
					if($a % 2 == 0){$num_partidos = $a / 2;}//obtenemos los partidos por jornada
					else{//si son impartes sumamos un partidos
						$impar = true;
						$num_partidos = ($a / 2) + 1;
					}
					//SOLO 2 JORNADAS FIJAS CON X PARTIDOS
					for($j=1; $j<=2; $j++){//jornadas
						echo '-j '.$j.'-num part '.$num_partidos;
						for($x=0; $x<$num_partidos; $x++){//partidos por jornada que varia en función de la cantidad de equipos por grupo
							$continuar = true;
							if($j == 1){//jornada1
								$local = $equipos_temp[$x*2];
								if($x == $num_partidos-1 && $impar){$visitante = $equipos_temp[0];}//
								else{$visitante = $equipos_temp[($x*2)+1];}
								echo '-l '.$local.' -v '.$visitante;
							}
							else{//jornada2
								if($x == $num_partidos-1 && $impar){$continuar = false;}//no creamos el ultimo partido
								else{
									//$local = $equipos_temp[($x*2)+1];
									//$visitante = $equipos_temp[($x*2)+2];
									$local = $equipos_temp[0];
									$visitante = $equipos_temp[1];
									echo '-l '.$local.' -v '.$visitante;
							}
							if($continuar){
								$grup = $i;
								if( $lunes != '' || $martes != '' || $miercoles != '' || $jueves != '' || $viernes != '' || $sabado != '' || $domingo != '' ){//si entra aquí es porque se han puesto horarios
									$res = 0;
									//0=CONTINUAR BUCLE, 1=CAMBIO DE DIA, >1 HORA A INSERTAR
									while( $res == 0 ){//mientras sea 0 error no hay hora
										if($array_desde1[$num_dia_semana] != 0 || $array_hasta1[$num_dia_semana] != 0 || $array_desde2[$num_dia_semana] != 0 || $array_hasta2[$num_dia_semana] != 0){//si no hay datos cambio de dia
											//AQUI LA VARIABLE $res ES 0.5 COMO ERROR, YA QUE EL 1 PUEDE SER UNA HORA
											$res = comprueba_entreHorario($cont_horas,$duracion_partido,$array_desde1[$num_dia_semana],$array_hasta1[$num_dia_semana],$array_desde2[$num_dia_semana],$array_hasta2[$num_dia_semana]);
											if($res > 0.5){//inserto en el mismo día
												$hora = '';
												$hora = crear_hora($res).':00';
												$fecha = substr($inicio,0,10);
												if($sets == 3){//partido a 3 sets
													$partido = new Partido(NULL,$j,$fecha,$hora,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,$pista_insertar,$id_division,$arbitro_insertar,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
												}
												else{//partido a 5 sets
													$partido = new Partido(NULL,$j,$fecha,$hora,$local,$visitante,0,0,0,0,0,0,0,0,0,0,NULL,NULL,0,$pista_insertar,$id_division,$arbitro_insertar,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
												}
												$partido->insertar();
												unset($partido);
											}
										}
										else{
											$res = 0.5;
										}
										if($res == 0.5){//cambio de dia
											$cont_horas = $duracion_partido;
											$inicio = fecha_suma($inicio,'','',1,'','','');//sumo un dia
											//echo 'suma '.$inicio.'-';
											$num_dia_semana = obten_numDiaSemana($inicio);//numero día semana 1=lunes 7=domingo
											$sumar_dias = dias_comienzo($num_dia_semana,$lunes,$martes,$miercoles,$jueves,$viernes,$sabado,$domingo);
											if($sumar_dias != 0){
												$inicio = fecha_suma($inicio,'','',$sumar_dias,'','','');
											}
											$num_dia_semana = obten_numDiaSemana($inicio);//numero día semana 1=lunes 7=domingo
											$res = 0;//SE PONE A 0 PARA QUE CONTINUE EL BUCLE HASTA CONSEGUIR UN 1 E INSERTAR LA HORA
										}
									}//fin while
									//AQUI SE COMPRUEBAN LAS PISTAS PARA INCREMENTAR O NO LA HORA
									if(isset($num_pistas)){
										if($num_pistas == 0){//si es cero incremento la hora
											$cont_horas += $duracion_partido;
										}
									}
									else{
										$cont_horas += $duracion_partido;
									}
								}//fin if con horarios
								else{//sin horarios
									if($sets == 3){//partido a 3 sets
										$partido = new Partido(NULL,$j,NULL,NULL,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
									}
									else{//partido a 5 sets
										$partido = new Partido(NULL,$j,NULL,NULL,$local,$visitante,0,0,0,0,0,0,0,0,0,0,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
									}
									$partido->insertar();
									unset($partido);
								}//fin sin horarios
							}//fin if de continuar
						}//fin for numero de partidos por jornada
						//unset($equipos_temp);
					}//fin for jornadas
				}//fin if personalizados
				else{//automaticos
					for($j=1; $j<=3; $j++){//jornadas
						if($j == 1){//jornada1
							$local = $grupos[$i*3];
							$visitante = $grupos[($i*3)+1];
						}
						else if($j == 2){//jornada2
							$local = $grupos[($i*3)+1];
							$visitante = $grupos[($i*3)+2];
						}
						else{//jornada3
							$local = $grupos[($i*3)+2];
							$visitante = $grupos[$i*3];
						}
						$grup = $i+1;
						if( $lunes != '' || $martes != '' || $miercoles != '' || $jueves != '' || $viernes != '' || $sabado != '' || $domingo != '' ){//si entra aquí es porque se han puesto horarios
							$res = 0;
							//0=CONTINUAR BUCLE, 1=CAMBIO DE DIA, >1 HORA A INSERTAR
							while( $res == 0 ){//mientras sea 0 error no hay hora
								if($array_desde1[$num_dia_semana] != 0 || $array_hasta1[$num_dia_semana] != 0 || $array_desde2[$num_dia_semana] != 0 || $array_hasta2[$num_dia_semana] != 0){//si no hay datos cambio de dia
									//AQUI LA VARIABLE $res ES 0.5 COMO ERROR, YA QUE EL 1 PUEDE SER UNA HORA
									$res = comprueba_entreHorario($cont_horas,$duracion_partido,$array_desde1[$num_dia_semana],$array_hasta1[$num_dia_semana],$array_desde2[$num_dia_semana],$array_hasta2[$num_dia_semana]);
									if($res > 0.5){//inserto en el mismo día
										$hora = '';
										$hora = crear_hora($res).':00';
										$fecha = substr($inicio,0,10);
										if($sets == 3){//partido a 3 sets
											$partido = new Partido(NULL,$j,$fecha,$hora,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,$pista_insertar,$id_division,$arbitro_insertar,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
										}
										else{//partido a 5 sets
											$partido = new Partido(NULL,$j,$fecha,$hora,$local,$visitante,0,0,0,0,0,0,0,0,0,0,NULL,NULL,0,$pista_insertar,$id_division,$arbitro_insertar,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
										}
										//echo '-f_partido '.$fecha.' '.$hora.'--';
										//echo 'fecha '.$fecha.'---';
										$partido->insertar();
										unset($partido);
									}
								}
								else{
									$res = 0.5;
								}
								if($res == 0.5){//cambio de dia
									$cont_horas = $duracion_partido;
									$inicio = fecha_suma($inicio,'','',1,'','','');//sumo un dia
									//echo 'suma '.$inicio.'-';
									$num_dia_semana = obten_numDiaSemana($inicio);//numero día semana 1=lunes 7=domingo
									$sumar_dias = dias_comienzo($num_dia_semana,$lunes,$martes,$miercoles,$jueves,$viernes,$sabado,$domingo);
									if($sumar_dias != 0){
										$inicio = fecha_suma($inicio,'','',$sumar_dias,'','','');
									}
									$num_dia_semana = obten_numDiaSemana($inicio);//numero día semana 1=lunes 7=domingo
									$res = 0;//SE PONE A 0 PARA QUE CONTINUE EL BUCLE HASTA CONSEGUIR UN 1 E INSERTAR LA HORA
								}
							}//fin while
							//AQUI SE COMPRUEBAN LAS PISTAS PARA INCREMENTAR O NO LA HORA
							if(isset($num_pistas)){
								if($num_pistas == 0){//si es cero incremento la hora
									$cont_horas += $duracion_partido;
								}
							}
							else{
								$cont_horas += $duracion_partido;
							}
						}//fin if con horarios
						else{//sin horarios
							if($sets == 3){//partido a 3 sets
								$partido = new Partido(NULL,$j,NULL,NULL,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
							}
							else{//partido a 5 sets
								$partido = new Partido(NULL,$j,NULL,NULL,$local,$visitante,0,0,0,0,0,0,0,0,0,0,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,$grup,0,'');
							}
							$partido->insertar();
							unset($partido);
						}//fin sin horarios
					}//fin for jornadas
				}//fin else automaticos
			}//fin for grupos
		}//fin grupos
		else{//eliminatorias
			$eliminatoria = $num_equipos / 2; //obtenemos la cantidad de eliminatorias
			for($i=0; $i<$eliminatoria; $i++){
				if(isset($id_pistas)){//GESTION PISTAS
					if($num_pistas > 0){//entro decremento y no sumo dias
						$num_pistas--;
					}
					else{//vuelvo a restablecer las pistas
						$num_pistas = count($id_pistas);
						$num_pistas--;
					}
					$pista_insertar = $id_pistas[$num_pistas];
				}
				if(isset($num_arbitros)){//GESTION ARBITROS
					if($num_arbitros == 0){
						$num_arbitros = count($id_arbitros);
					}
					$num_arbitros--;
					$arbitro_insertar = $id_arbitros[$num_arbitros];
				}
				$local = $id_equipos[$i];
				$visitante = $id_equipos[$i+$eliminatoria];
				if( $lunes != '' || $martes != '' || $miercoles != '' || $jueves != '' || $viernes != '' || $sabado != '' || $domingo != '' ){//si entra aquí es porque se han puesto horarios
						$res = 0;
						//0=CONTINUAR BUCLE, 1=CAMBIO DE DIA, >1 HORA A INSERTAR
						while( $res == 0 ){//mientras sea 0 error no hay hora
							if($array_desde1[$num_dia_semana] != 0 || $array_hasta1[$num_dia_semana] != 0 || $array_desde2[$num_dia_semana] != 0 || $array_hasta2[$num_dia_semana] != 0){//si no hay datos cambio de dia
								$res = comprueba_entreHorario($cont_horas,$duracion_partido,$array_desde1[$num_dia_semana],$array_hasta1[$num_dia_semana],$array_desde2[$num_dia_semana],$array_hasta2[$num_dia_semana]);
								if($res > 0.5){//inserto en el mismo día
									$hora = '';
									$hora = crear_hora($res).':00';
									$fecha = substr($inicio,0,10);
									if($sets == 3){//partido a 3 sets
										$partido = new Partido(NULL,0,$fecha,$hora,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,$pista_insertar,$id_division,$arbitro_insertar,NULL,NULL,NULL,NULL,0,$id_liga,0,$eliminatoria,'');
									}
									else{//partido a 5 sets
										$partido = new Partido(NULL,0,$fecha,$hora,$local,$visitante,0,0,0,0,0,0,0,0,0,0,NULL,NULL,0,$pista_insertar,$id_division,$arbitro_insertar,NULL,NULL,NULL,NULL,0,$id_liga,0,$eliminatoria,'');
									}
									//echo 'f_partido '.$fecha.' '.$hora.'--';
									//echo 'fecha '.$fecha.'---';
									$partido->insertar();
									unset($partido);
								}
							}
							else{
								$res = 0.5;
							}
							if($res == 0.5){//cambio de dia
								$cont_horas = $duracion_partido;
								$inicio = fecha_suma($inicio,'','',1,'','','');//sumo un dia
								//echo 'suma '.$inicio.'-';
								$num_dia_semana = obten_numDiaSemana($inicio);//numero día semana 1=lunes 7=domingo
								$sumar_dias = dias_comienzo($num_dia_semana,$lunes,$martes,$miercoles,$jueves,$viernes,$sabado,$domingo);
								if($sumar_dias != 0){
									$inicio = fecha_suma($inicio,'','',$sumar_dias,'','','');
								}
								$num_dia_semana = obten_numDiaSemana($inicio);//numero día semana 1=lunes 7=domingo
								$res = 0;//SE PONE A 0 PARA QUE CONTINUE EL BUCLE HASTA CONSEGUIR UN 1 E INSERTAR LA HORA
							}
						}//fin while
						//AQUI SE COMPRUEBAN LAS PISTAS PARA INCREMENTAR O NO LA HORA
						if(isset($num_pistas)){
							if($num_pistas == 0){//si es cero incremento la hora
								$cont_horas += $duracion_partido;
							}
						}
						else{
							$cont_horas += $duracion_partido;
						}
					}//fin if con horarios
					else{//sin horarios
						if($sets == 3){//partido a 3 sets
							$partido = new Partido(NULL,0,NULL,NULL,$local,$visitante,0,0,0,-1,-1,0,0,0,-1,-1,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,0,$eliminatoria,'');
						}
						else{//partido a 5 sets
							$partido = new Partido(NULL,0,NULL,NULL,$local,$visitante,0,0,0,0,0,0,0,0,0,0,NULL,NULL,0,NULL,$id_division,NULL,NULL,NULL,NULL,NULL,0,$id_liga,0,$eliminatoria,'');
						}
						$partido->insertar();
						unset($partido);
					}//fin sin horarios
			}//for eliminatorias
		}//fin else eliminatoria
	}//FIN ELSE PERSONALIZADO
	 //ENVIAR CORREO AL PAGADOR
	 $nombre = utf8_encode(obten_consultaUnCampo('session','nombre','liga','id_liga',$id_liga,'','','','','','',''));
	 $email_admin = $_SESSION['email'];
	 include_once ("../../funciones/f_conexion_email.php");//Set who the message is to be sent from
	$mail->setFrom('info@mitorneodepadel.es', 'mitorneodepadel.es');//Set an alternative reply-to address
	$mail->addReplyTo('info@mitorneodepadel.es', 'mitorneodepadel.es');//Set who the message is to be sent to
	$mail->AddBCC($email_admin);
	$db = new MySQL('session');//LIGA PADEL
	$consulta = $db->consulta("SELECT email1,email2 FROM inscripcion WHERE liga = '$id_liga' AND division = '".$division->getValor('id_division')."' AND pagado = 'S' ; ");
	while($resultados = $consulta->fetch_array(MYSQLI_ASSOC)){
		$mail->AddBCC($resultados['email1']);
		$mail->AddBCC($resultados['email2']);
	}
	$asunto = utf8_decode('Ha comenzado el Torneo de Padel <'.$nombre.' división '.$division->getValor('num_division').'>.');
	$mail->Subject = $asunto;
	$cuerpo = '<br><br>El calendario de tu Torneo de Padel '.$nombre.' división '.$division->getValor('num_division').' ya se ha generado, y por lo tanto ya está disponible toda la información de tu torneo en www.mitorneodepadel.es<br><br>';
	$cuerpo .= 'Ahora a Ganar!!<br><br>';
	$body = email_jugadorAdmin("<br>¡Gracias por utilizar nuestros servicios en www.mitorneodepadel.es!<br>",$cuerpo);
	$mail->msgHTML($body);
	$mail->AltBody = 'This is a plain-text message body';//Replace the plain text body with one created manually
	$mail->send();
	$division->setValor('comienzo','S');//COMIENZA LA DIVISION
	$division->modificar();
	$resumen_noticia = utf8_decode('Sección: División -> Comenzar.');
	$descripcion_noticia = utf8_decode('Se ha generado el calendario con los partidos, puede consultarlos en el Calendario. ');
	$noticia = new Noticia(NULL,$id_liga,$id_division,$resumen_noticia,$descripcion_noticia,obten_fechahora(),'');
	$noticia->insertar();
	unset($liga,$division,$noticia,$partido);
	echo '<span class="actualizacion"><img src="../../../images/ok.png" />'.utf8_decode($texto).'</span>';
}//fin else

?>