<?php
require '../../verifier.php';
require '../../database.php';

$url =  $_SERVER['REQUEST_URI'];
$pattern = "/\/api\/v1\/users\/(.*)/";
preg_match($pattern, $url, $matches);
$userId =  $matches[1];

// ADD DRINK TO USER
$patternDrink = "/\/api\/v1\/users\/(.*)\/drink/";
preg_match($patternDrink, $url, $matches);
if(sizeof($matches) > 0){
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $userId =  $matches[1];
    $drink_ml = filter_var(getPostField('drink_ml'), FILTER_SANITIZE_NUMBER_INT);

    $sql = "UPDATE usuarios SET drink_counter = drink_counter + :drink_ml WHERE id = :id";
    $sql = $db->prepare($sql);
    $sql->bindParam(':id', $userId, PDO::PARAM_INT);
    $sql->bindParam(':drink_ml', $drink_ml, PDO::PARAM_INT);
    $sql->execute();

    $sql = "SELECT id, nome, email, drink_counter FROM usuarios WHERE id = :id";
    $sql = $db->prepare($sql);
    $sql->bindParam(':id', $userId, PDO::PARAM_INT);
    $sql->execute();
    $dados = $sql->fetchAll();

    echo json_encode($dados[0]);
    exit;
  }
};

// GET USER BY ID
if($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($userId)){
  $sql = "SELECT id, nome, email, drink_counter, token FROM usuarios WHERE id = :id";
  $sql = $db->prepare($sql);
  $sql->bindParam(':id', $userId, PDO::PARAM_INT);
  $sql->execute();
  $dados = $sql->fetchAll();

  if(sizeof($dados) === 0){
    header($_SERVER["SERVER_PROTOCOL"]." 404 User Found", true, 404);
    $retorno = array(
      "code" => "404",
      "error" => "User Not Found"
    );
    echo json_encode($retorno);
    exit;
  }else{
    // header('Token: '.$dados[0]->token);
    unset($dados[0]->token);
    echo json_encode($dados[0]);
    exit;
  }
}

// GET ALL USERS
if($_SERVER['REQUEST_METHOD'] === 'GET' && empty($userId)){
  $sql = "SELECT id, nome, email, drink_counter FROM usuarios ORDER BY id";
  $sql = $db->prepare($sql);
  $sql->bindParam(':id', $userId, PDO::PARAM_INT);
  $sql->execute();
  $dados = $sql->fetchAll();

  if(sizeof($dados) === 0){
    header($_SERVER["SERVER_PROTOCOL"]." 404 User Found", true, 404);
    $retorno = array(
      "code" => "404",
      "error" => "User Not Found"
    );
    echo json_encode($retorno);
    exit;
  }else{
    echo json_encode($dados);
    exit;
  }
}

$email = filter_var(getPostField('email'), FILTER_VALIDATE_EMAIL);
$name = filter_var(getPostField('name'), FILTER_SANITIZE_STRIPPED);
$password = filter_var(getPostField('password'), FILTER_SANITIZE_STRIPPED);

if(empty($email) || empty($name) || empty($password)) {
  header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request", true, 400);
  $retorno = array(
    "code" => "400",
    "error" => "Fields: name, email and password is required"
  );
  echo json_encode($retorno);
  exit;
}

// UPDATE USER BY ID -- Não esta funcionando
if($_SERVER['REQUEST_METHOD'] === 'PUT' && !empty($userId)){
  $sql = "UPDATE usuarios SET nome = :nome, email = :email, password = :password WHERE id = :id";
  $sql = $db->prepare($sql);
  $sql->bindParam(':email', $email, PDO::PARAM_STR);
  $sql->bindParam(':senha', $password, PDO::PARAM_STR);
  $sql->bindParam(':nome', $name, PDO::PARAM_STR);
  $sql->execute();

  $sql = "SELECT id, nome, email, drink_counter, token FROM usuarios WHERE id = :id";
  $sql = $db->prepare($sql);
  $sql->bindParam(':id', $userId, PDO::PARAM_INT);
  $sql->execute();
  $dados = $sql->fetchAll();

  unset($dados[0]->token);
  echo json_encode($dados[0]);
  exit;
}

// Adição do usuário
$sql = "SELECT * FROM usuarios WHERE email = :email";
$sql = $db->prepare($sql);
$sql->bindParam(':email', $email, PDO::PARAM_STR);
$sql->execute();
$dados = $sql->fetchAll();

if(sizeof($dados) === 0){
  $sql = "INSERT INTO usuarios (email, senha, nome, adm, token, confirmado) VALUES (:email, :senha, :nome, 0, 'abc123', 1)";
  $sql = $db->prepare($sql);
  $sql->bindParam(':email', $email, PDO::PARAM_STR);
  $sql->bindParam(':senha', $password, PDO::PARAM_STR);
  $sql->bindParam(':nome', $name, PDO::PARAM_STR);
  $sql->execute();

  header($_SERVER["SERVER_PROTOCOL"]." 200 OK", true, 200);
  $retorno = array(
    "code" => "200",
    "error" => "User Added"
  );
  echo json_encode($retorno);
  exit;
}else{
  header($_SERVER["SERVER_PROTOCOL"]." 400 User Found", true, 400);
  $retorno = array(
    "code" => "400",
    "error" => "User exists"
  );
  echo json_encode($retorno);
  exit;
}