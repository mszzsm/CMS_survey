<?php

/* Rozważyć zrobienie z tego funkji abstrakcyjnej, z niej wywodziłyby się
 * funkcje, które obsługiwały by wywołanie z przeglądarki HTTP oraz 
 * z wiersza poleceń CLI. */

class Request{
    
    private $path = null;
    private $module = null;
    private $operation = null;
    private $parameters = null;
    private $reg = null;
    
    public function __construct(){
        
        $this->reg = Registry::instance();
        if($this->reg->getSession('signedIn')){
            if(isset($_SERVER['PATH_INFO'])){
                $this->path = $_SERVER['PATH_INFO'];
            }else{
                $this->path = $this->reg->getOption('MAIN_PAGE');
            }
        }else{
            $this->path = $this->reg->getOption('LOGIN_PAGE');
        }
        
        $this->path;
        $this->init();
        
    }
    
    private function init(){
        
        /* Funkcja rozbija $path na czynniki pierwsze */
        $temp = explode('/',$this->path);
        if(count($temp)>1){
            array_splice($temp,0,1);
            array_pop($temp);
            $this->module = $temp[0];//(isset($temp[0])?$temp[0]:'survey');
            array_splice($temp,0,1);
            $this->operation = $temp[0];//(isset($temp[0])?$temp[0]:'main');
            array_splice($temp,0,1);
            $this->parameters = (isset($temp)?$temp:null);
        }
        
    }
    
    public function getPath(){
        
        return $this->path;
        
    }
    
    public function getModule(){
        
        return $this->module;
        
    }
    
    public function getOperation(){
        
        return $this->operation;
        
    }
    
    public function getParameters($index=null){
        
        if($index===null){
            return $this->parameters;
        }
        
        if(isset($this->parameters[$index])){
            return $this->parameters[$index];
        }else{
            return null;
        }
        
    }

}