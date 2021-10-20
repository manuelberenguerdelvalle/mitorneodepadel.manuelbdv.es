<?php
session_start();
function obtenGanador($local,$visitante,$set1_l,$set2_l,$set3_l,$set4_l,$set5_l,$set1_v,$set2_v,$set3_v,$set4_v,$set5_v){
	$error = 0;
	$cont_l = 0;
	$cont_v = 0;
	$array_local = array($set1_l,$set2_l,$set3_l,$set4_l,$set5_l);
	$array_visitante = array($set1_v,$set2_v,$set3_v,$set4_v,$set5_v);
	if($set4_l == -1 && $set4_v == -1){$tam = 3;}
	else{$tam = 5;}
	for($i=0; $i<$tam; $i++){
		if($array_local[$i] >= 6){
			$cont_l++;
		}
		if($array_visitante[$i] >= 6){
			$cont_v++;
		}
	}
	if($tam == 3){//al mejor de 3
		if($cont_l == 2 && $cont_v <= 1){//gana local con 2 sets
			$retorno = $local;
		}
		else if($cont_l <= 1 && $cont_v == 2){//gana visitante con 2 sets
			$retorno = $visitante;
		}
		else{
			$retorno = -1;
		}
	}
	else{//al mejor de 5
		if($cont_l == 3 && $cont_v <= 2){//gana local con 2 sets
			$retorno = $local;
		}
		else if($cont_l <= 2 && $cont_v == 3){//gana visitante con 2 sets
			$retorno = $visitante;
		}
		else{
			$retorno = -1;
		}
	}
	return $retorno;
}

function hayTiebreak($set1_l,$set2_l,$set3_l,$set4_l,$set5_l,$set1_v,$set2_v,$set3_v,$set4_v,$set5_v){
	$retorno = 'N';
	$array_local = array($set1_l,$set2_l,$set3_l,$set4_l,$set5_l);
	$array_visitante = array($set1_v,$set2_v,$set3_v,$set4_v,$set5_v);
	if($set4_l == -1 && $set4_v == -1){$tam = 3;}
	else{$tam = 5;}
	for($i=0; $i<$tam; $i++){
		if( ($array_local[$i] == 7 && $array_visitante[$i] == 6) || ($array_local[$i] == 6 && $array_visitante[$i] == 7) ){
			$retorno = 'S';
		}
	}
	return $retorno;
}

function grupoPartidosFinalizados($id_division,$grupo){//obtiene el numero de partidos con ESTADO = 1 finalizados para la jornada indicada
	$db = new MySQL('session');//LIGA PADEL
	$consulta = $db->consulta("SELECT COUNT(id_partido) AS num FROM partido WHERE division = '$id_division' AND grupo = '$grupo' AND estado >= 1 ; ");
	$resultado = $consulta->fetch_array(MYSQLI_ASSOC);
	return $resultado['num'];
}

function eliminatoriaPartidosFinalizados($id_division,$eliminatoria){//obtiene el numero de partidos con ESTADO = 1 finalizados para la jornada indicada
	$db = new MySQL('session');//LIGA PADEL
	$consulta = $db->consulta("SELECT COUNT(id_partido) AS num FROM partido WHERE division = '$id_division' AND eliminatoria = '$eliminatoria' AND estado >= 1 ; ");
	$resultado = $consulta->fetch_array(MYSQLI_ASSOC);
	return $resultado['num'];
}

function liguillaPartidosFinalizados($id_division){//obtiene el numero de partidos con ESTADO = 1 finalizados para la jornada indicada
	$db = new MySQL('session');//LIGA PADEL
	$consulta = $db->consulta("SELECT COUNT(id_partido) AS num FROM partido WHERE division = '$id_division' AND jornada > 0 AND estado >= 1 ; ");
	$resultado = $consulta->fetch_array(MYSQLI_ASSOC);
	return $resultado['num'];
}

function hayPartidoDescanso($id_division,$jornada){//obtiene si la jornada tiene partido de descanso
	$db = new MySQL('session');//LIGA PADEL
	$consulta = $db->consulta("SELECT COUNT(id_partido) AS num FROM partido WHERE division = '$id_division' AND jornada = '$jornada' AND (set1_local = -1  AND set1_visitante = -1 ); ");
	$resultado = $consulta->fetch_array(MYSQLI_ASSOC);
	return $resultado['num'];
}

function obten_letraGrupo($i){
	$letras = 'ABCDEFGHIJKLMNOP';
	return $letras[$i-1];
}

function obtenGanadoresEliminatorias($id_division,$eliminatoria){//obtiene el id de partidos ganadores si es 0 liguilla si no eliminatoria
	$id_p = array();
	$i = 0;
	$db = new MySQL('session');//UNICAS
	//va a faltar la bd
	$c = $db->consulta("SELECT ganador FROM partido WHERE division = '$id_division' AND eliminatoria = '$eliminatoria' ; ");
	while($r = $c->fetch_array(MYSQLI_ASSOC)){
		$id_p[$i] = $r['ganador'];
		$i++;
	}
	return $id_p;
}

?>