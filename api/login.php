<?php

    declare(strict_types=1);
    require __DIR__."/boostrap.php";
    if($_SERVER["REQUEST_METHOD"]!=="POST")
    {
        http_response_code(405);
        header("Allow:POST");
        exit;
    }

    $data = (array) json_decode(file_get_contents("php://input"),true);

    if(!array_key_exists("username",$data)||!array_key_exists("password",$data))
    {
        http_response_code(400);
        echo json_encode(["message"=>"missing login credential"]);
        exit;
    }

    $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
    $user_gateway = new UserGateway($database);

    $user = $user_gateway->getByUsername($data["username"]);
    if($user === false)
    {
        http_response_code(401);
        echo json_encode(["message"=>"Inavlid authentication"]);
    }
  if(! password_verify($data["password"],$user["password_hash"]))
  {
    http_response_code(401);
    echo json_encode(["message"=>"Inavlid authentication"]);
    exit;
  }
  $codec = new JWTCodec($_ENV["SECRET_KEY"]);

  require __DIR__ ."/token.php";

  $refresh_token_gateway = new RefreshTokenGatway($database,$_ENV["SECRET_KEY"]);

  $refresh_token_gateway->create($refresh_token,$refresh_token_expiry);
  

?>