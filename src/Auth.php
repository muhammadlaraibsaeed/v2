<?php

class Auth{

    private int $user_id;

    public function __construct(private UserGateway $user_gateway,private JWTCodec $codec)
    {
        
    }

    public function authenticationAPIKey():bool
    {

        if(empty($_SERVER["HTTP_X_API_KEY"])){
            http_response_code(400);
            echo json_encode(["mesaage"=>"missing api key"]);
            return false;
         }
         
         $api_key= $_SERVER["HTTP_X_API_KEY"];
        //  this is because getBYAPIKEY function un usercontroller return associated array in $user id
         $user = $this->user_gateway->getBYAPIKEY($api_key);

        if($user === false)
            {
                http_response_code(401);
                echo json_encode(["message"=>"INVALID API KEY"]);
                return false;
            }
                 $this->user_id=$user["id"];
            return true;
         
    }

    public function getUserID(): int
    {
              return $this->user_id;
    }
    
    public function authenticateAccessToken()
    {
        if(!preg_match("/^Bearer\s+(.*)/",$_SERVER["HTTP_AUTHORIZATION"],$matches))
        {
            http_response_code(400);
            echo json_encode(["message"=>"incomplete authrization header"]);
            return false;
        }
        
        try{
            $data=$this->codec->decode($matches[1]);
        }
        catch(InvalidSignatureException)
        {
            http_response_code(401);
            echo json_encode(["message"=>"invalid signature"]);
            return false;
        }
        catch(TokenExpiredException)
        {
               http_response_code(401);
               echo json_encode(["message"=>"token has expired"]);
               return false;
        }
        catch(Exception $e)
        {
           http_response_code(400);
           echo json_encode(["message"=>$e->getMessage()]);
           return false;
        }

        $this->user_id = $data["sub"];
          
       return true;
    }
}

?>