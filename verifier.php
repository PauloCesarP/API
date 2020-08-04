<?php
header('Content-Type: application/json; charset=utf-8');

$headers = '';
$query = '';
$ip = '';
$body = file_get_contents('php://input');

foreach (getallheaders() as $name => $value) { 
    $headers .= "$name: $value\r\n";
}

foreach ($_GET as $name => $value) { 
    $query .= "$name: $value\r\n";
}

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$authorization = getallheaders();

if(empty($authorization['Authorization'])){
	header($_SERVER["SERVER_PROTOCOL"]." 401 Unauthorized", true, 401);
	$retorno = array(
		"code" => "401",
		"error" => "Unauthorized"
	);
	echo json_encode($retorno);
	exit;
}

$authorization = explode(' ', $authorization['Authorization']);

if($authorization[0] !== 'Bearer'){
	header($_SERVER["SERVER_PROTOCOL"]." 401 Unauthorized", true, 401);
	$retorno = array(
		"code" => "401",
		"error" => "Bearer token malformed"
	);
	echo json_encode($retorno);
	exit;
}

// Token da requisição
$idUsuario = $authorization[1];

function getPostField($nomeCampo) {
  if(isset($_POST[$nomeCampo]) && !empty($_POST[$nomeCampo])){
		return $_POST[$nomeCampo];
	}else{
		return null;
	}
}