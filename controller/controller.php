<?php

class Controller{
    
    private $reg;
    private $user;
    private $request;
    private $object;
    
    
    private function __construct(){
        
        $this->reg = Registry::instance();
        $this->user = User::instance();
        
    }
    
    /* Funkcja run powinna najpierw wykonać logowanie do aplikacji */
    public static function run(){
    
        $instance = new Controller();
        
        //Kontrola, czy użytkownik jest zalogowany.
        if(!$instance->user->checkIfSignIn()){
            $instance->user->signIn();
            
            if($instance->user->checkIfSignIn() == true){
                $instance->reg->setMessage("OK_SIGNIN_OK");}
            else{$instance->reg->setMessage("ER_SIGNIN_FAILED");}
            
            header("location: ".$instance->reg->getOption('MAIN_DIR'));
            exit;
        }else{
            $instance->handleRequest(); // przygotowanie pod obsłużenie żądania
            return true;
        }
    }
    
    public function handleRequest(){
        
        $this->request = $this->reg->getRequest(); // przygotowuje informacje o żądaniu
        $moduleTemp = $this->request->getModule();
        $operationTemp = $this->request->getOperation();

        $this->object = new $moduleTemp;
        $this->object->$operationTemp();
        
                
    }
    
}