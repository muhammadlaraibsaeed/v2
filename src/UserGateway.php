<?php

class UserGateway{
    private PDO $_conn;
    public function __construct(Database $database)
    {
        // getConnection() is come from database classs by passing the object of database into
        //  class taskcontroller __construct from main 
        // controller(index.php)
        $this->_conn = $database->getConnection();
    }

    public function getBYAPIKEY(string $key)
    {
        $sql = "SELECT *
               FROM user 
            WHERE  api_key =:api_key";
            $stmt=$this->_conn->prepare($sql);
                
                $stmt->bindValue(":api_key",$key,PDO::PARAM_STR);
                $stmt->execute();

            
                return $stmt->fetch(PDO::FETCH_ASSOC);


    }

    public function getByUsername(string $username):array|false
    {
        $sql="SELECT * FROM
              user  WHERE username=:username ";
        $stmt =$this->_conn->prepare($sql);
        $stmt->bindValue(":username",$username,PDO::PARAM_STR);
        $stmt->execute();
        
       return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $sql = "SELECT *  
                    FROM user 
                        WHERE id=:id";

        $stmt = $this->_conn->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}


?>