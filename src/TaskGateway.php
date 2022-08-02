<?php

class TaskGateway{
    private PDO $_conn;
        public function __construct(Database $database)
        {
            // getConnection() is come from database classs by passing the object of database into
            //  class taskcontroller __construct from main 
            // controller(index.php)
            $this->_conn = $database->getConnection();
        }
        // select all data from database  api_db in table task
    
        public function getAllforUser(int $user_id):array
        {
            $sql ="SELECT * 
                FROM task
                WHERE user_id=:user_id
                order by name";
                $stmt =$this->_conn->prepare($sql);
                $stmt->bindValue(":user_id",$user_id,PDO::PARAM_STR);
                $stmt->execute();
                $data = [];
                while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                    // 
                    $row['is_completed']=(bool)  $row['is_completed'];
                    $data[] = $row;   
                }

                return $data;

        }

        //    for getting individual record from database table task
        public function getforUser(int $user_id,string $id):array|false
        {
            $sql ="SELECT * 
                FROM task
                WHERE id = :id AND user_id=:user_id";
                
                $stmt =$this->_conn->prepare($sql);
                $stmt->bindValue(":id",$id,PDO::PARAM_INT);
                $stmt->bindValue(":user_id",$user_id,PDO::PARAM_INT);
                
                $stmt->execute();
            $data =$stmt->fetch(PDO::FETCH_ASSOC);
            if($data!==false){
                $data["is_completed"]=(bool) $data["is_completed"];
            }
            return $data;
        }

        // for inserting into table (task) of database api_db
        public function createforUser(int $user_id,array $data):string
        {
           $sql="INSERT INTO task(name,priority,is_completed,user_id) 
                VALUES(:name,:priority,:is_completed,:user_id)";
            $stmt=$this->_conn->prepare($sql);
            $stmt->bindValue(":name",$data["name"],PDO::PARAM_STR);
            if(empty($data["priority"]))
            {
                $stmt->bindValue(":priority",null,PDO::PARAM_NULL);
            }else{
                $stmt->bindValue("priority",$data["priority"],PDO::PARAM_INT);
            }

            $stmt->bindValue(":is_completed",$data["is_completed"] ?? false,PDO::PARAM_BOOL); 
            $stmt->bindValue(":user_id",$user_id,PDO::PARAM_INT);
            $stmt->execute();

           return $this->_conn->lastInsertId();
        }

        // use for updating the data in table task of database api_db

       public function updateforUser(int $user_id,string $id,array $data):int|array
       {
          $field=[];
        //  if name is set then append user name into  index(name) of array empty field
          if(!empty($data["name"]))
          {
            $field["name"]=[
                $data["name"],
                PDO::PARAM_STR
            ];
          }

        //  if priority is set then append user priority into  index(priority) of array empty field

          if(array_key_exists("priority",$data))
          {
            $field["priority"]=[
                $data["priority"],
                $data["priority"] === null ? PDO::PARAM_NULL:PDO::PARAM_INT
            ];
          } 

        //  if is_completed is set then append user is_completed into  index(is_completed) of array empty field
          if(array_key_exists("is_completed",$data))
          {
            $field["is_completed"]=[
                $data["is_completed"],
                PDO::PARAM_BOOL
            ];
          }

          $set = array_map(function($value){
            return "$value=:$value";
          },array_keys($field));
          
          if(empty($field)){
            return 0;
        }else{

          $sql ="UPDATE task"
                 ." SET ".implode(",",$set)
                 ."  WHERE id = :id"
                 ." AND user_id=:user_id";
                 $stmt = $this->_conn->prepare($sql);
                 $stmt->bindValue(":id",$id,PDO::PARAM_INT);                   
                 $stmt->bindValue(":user_id",$user_id,PDO::PARAM_INT);                   
                //  use field array for binding value into $sql statement
                 foreach($field as $name=>$value){

                    $stmt->bindValue(":$name",$value[0],$value[1]);
                }

                 $stmt->execute();
                $row_count = $stmt->rowCount();
                if($row_count!==0){
                    return $id;
                }else{
                    return 0;
                }
             
            }
       }
   
       public function deleteforUser(int $user_id,string $id):int
       {
            $sql = "DELETE FROM task WHERE id = :id AND user_id=:user_id";
            $stmt = $this->_conn->prepare($sql);

            $stmt->bindvalue(":id",$id,PDO::PARAM_INT);
            $stmt->bindvalue(":user_id",$user_id,PDO::PARAM_INT);
            $stmt->execute();

            $row_count = $stmt->rowCount();
                if($row_count!==0){
                    return $id;
                }else{
                    return 0;
                }
       }


}
?>