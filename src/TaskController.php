<?php

class TaskController{

    // use object of class TaskGateway in below for access all properties of class TaskGateway and method 
    public function __construct(private TaskGateway $gateway,private int $user_id)
    {
   
    }

    public function processRequest(string $method,?string $id):void
    {
        if($id == null){
            if($method == "GET")
            {
            //  getall() is come from class taskgateway by using gateway object as constructor
             echo json_encode($this->gateway->getAllforUser($this->user_id));
            }
            elseif($method == "POST")
            {  
               $data = (array) json_decode(file_get_contents("php://input"),true);

               $error = $this->getValidationError($data);
               if(!empty($error))
               {
                $this->respondUnprocessableEntity($error);
                exit;  
               }
               $id = $this->gateway->createforUser($this->user_id,$data);
            //  call respondcreated privated method for showing status code 201
            $this->respondCreated($id);
             }
             else{
                $this->responseMethodNotAllowed("GET,POST");
            }
        }
        else
        {     
            $task = $this->gateway->getforUser($this->user_id,$id);
            if($task===false){
               $this->respondNotFound($id);
               exit;
            }
            switch($method){
                case "GET":
                    echo json_encode($task);
                    break;            
                case "PATCH":     
                    $data = (array) json_decode(file_get_contents("php://input"),true);

                    $error = $this->getValidationError($data,false);
                    if(!empty($error))
                    {
                        $this->respondUnprocessableEntity($error);
                        exit;  
                    }
                    // returning integer from class gateway through funtion update by varification if the if
                    // the record update or not if record is updated then use get function of gateway class to show the current updated
                    $row = $this->gateway->updateforUser($this->user_id,$id,$data);
                    if($row ===  0){
                        echo json_encode(["message"=>"Task Already updated at row $row"]);

                    }else{
                        // use get function for showing individual record is updated
                        
                        echo json_encode($this->gateway->getforUser($this->user_id,$row));
                    }
                  
                break; 
                    case "DELETE":
                        $row = $this->gateway->deleteforUser($this->user_id,$id);
                        if($row ===  0){
                            echo json_encode(["message"=>"Task Already NOT EXIST"]);
    
                        }else{
                            // use get function for showing individual record is updated
                            echo json_encode(["message"=>" Task Deleted","Id"=>$row]);
                        }
                        break;   
                    default:
                        $this->responseMethodNotAllowed("GET,PATCH,UPDATE");

            }
        }
        
    }
                //  use for 405 status code that message is not allow if user enter request method rather than GET, POST ,DELETE
                // AND PATCH , PUT
    private function responseMethodNotAllowed(string $allow_method)
    {
              http_response_code(405);
              header("Allow:".$allow_method);
    }
            //   use for 404 status code if user enter invalid id which is not occure in my table database
    private function respondNotFound(string $id):void
    {
       http_response_code(404);
       echo json_encode(["message"=>"Task with ID ".$id." not found"]);
    }
    // use for 201 status code
    private function respondCreated(string $id):void
    {
       http_response_code(201);
       echo json_encode(["message"=>"Task Created","id"=>$id]);
    }
    
    //  use for status code 422
    private function respondUnprocessableEntity(array $error):void
    {
        http_response_code(422);
        echo json_encode(["error"=>$error]); 
    }


    // this function is used for get validation of input name and priority

    private function getValidationError(array $data,bool $is_new = true):array
    {
            $error = [];
            if($is_new && empty($data["name"]))
            {       
                // here appending the string if name is empty
                $error[]="name  is required";
            }
           else if(!empty($data["priority"]))
            {
                    if(!filter_var($data["priority"],FILTER_VALIDATE_INT))
                    {
                               $error[]="priority  must be integer"; 
                    }
            }

            return $error;  
    }
}

?>