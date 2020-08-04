<?php
require '../../verifier.php';
require '../../database.php';

$email = filter_var(getPostField('email'), FILTER_VALIDATE_EMAIL);
$password = filter_var(getPostField('password'), FILTER_SANITIZE_STRIPPED);

$sql = "SELECT token, id, email, nome, drink_counter FROM usuarios WHERE email = :email and senha = :password";
$sql = $db->prepare($sql);
$sql->bindParam(':email', $email, PDO::PARAM_STR);
$sql->bindParam(':password', $password, PDO::PARAM_STR);
$sql->execute();
$dados = $sql->fetchAll();

if(sizeof($dados) === 0){
  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
	$retorno = array(
		"code" => "404",
		"error" => "User Not Found"
	);
	echo json_encode($retorno);
	exit;
}

echo json_encode($dados[0]);
exit;