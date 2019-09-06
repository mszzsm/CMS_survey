<?php
class ModelUser extends Model{
    protected $pdo;
    protected $sql;

    public function __construct(){
        try{
            parent::__construct();
	}
        catch (DBException $e){
            echo $e->getMessage();
        }
    }
    
    public function getPermission($login){
        try{
            $this->sql="
                select role from tbl_permissions where login=:login
                        ";
            $query=$this->pdo->prepare($this->sql);
            $query->bindParam(':login',$login,PDO::PARAM_STR);
            $query->execute();
            $result=$query->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>0){return $result[0]['role'];}
            else{return 0;}
        }catch(Exception $e){
            return -1;	
        }
    }
    
}
