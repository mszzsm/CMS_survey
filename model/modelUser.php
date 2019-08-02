<?php
class ModelUSer extends Model{
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
    
    public function checkAccess($login){
        try{
            $this->sql="
                        ";
            $query=$this->pdo_mysql->prepare($this->sql);
            $query->bindParam(':login',$login,PDO::PARAM_STR);
            $query->execute();
            $count=$query->fetchAll(PDO::FETCH_ASSOC);
            return $count;
        }catch(Exception $e){
            return -1;	
        }
    }
    
}
