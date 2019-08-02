<?php
abstract class Model{
    protected $pdo;
    protected $sql;
    protected $reg;

    protected function __construct(){
        try{
            $hostname = "172.20.1.33";
            $dbname = "survey";
            $username = "survey";
            $pw = 'QWUuNRZxY5rkwNmo';
            $this->pdo = new PDO('mysql:host='.$hostname.';dbname='.$dbname.';charset=utf8', $username, $pw);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);  
            
            $this->reg = Registry::instance();
	}
        catch (DBException $e){
            echo $e->getMessage();
        }
    }
    
    protected function getData($query){
        /*
         * Przyjmuje wstępnie utworzoną zmienną query
         */
        try{
            $query->execute();
            $result=$query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch(Exception $e){
            echo $e->getMessage();
            return -1;	
        }
    }
    
}
