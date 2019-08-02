<?php

abstract class View{
    
    protected $reg = null;
    protected $proporties = array();
    protected $title = null;
    protected $routing = null;
    
    protected function __construct(){
        $this->title = "SMP Survey";
        $this->reg = Registry::instance();
        $this->routing = simplexml_load_file('data/routing.xml');
    }
    
    protected function render() {
        
        try{
            $this->set("messageType",$this->reg->getMessageType());
            $this->set("messageText",$this->reg->getMessage());
            $path = 'template/'.$this->getTemplate(
                    $this->reg->getRequest()->getModule(),
                    $this->reg->getRequest()->getOperation());
            
            if(is_file($path)) {
                $this->set("mainDir",$this->reg->getOption("MAIN_DIR"));
                require_once($path); 
                exit;
            }else{
                throw new Exception("Wystąpił błąd podczas otwierania tego pliku.");
            }
        }catch(Exception $e) {
            echo $e->getMessage();
            exit;
        }
        exit;
        
    }
    
    protected function menuGenerator(){
        
        /*
         * 
         * Na podstawie userPermission w Registry, trzeba ułożyć menu główne
         * i je przekazać do widoku, aby na templatce nie manipulować
         * uprawnieniami zalogowanego użytkownika.
         * 
         */
        
    }

    protected function set($name, $value) {
        $this->proporties["$name"]=$value;
    }

    protected function get($name) {
        return $this->proporties["$name"];
    }
    
    private function getTemplate($module,$operation){
        $text=$this->routing->xpath('//module[@name="'.(string)$module.'"]/operation[@name="'.(string)$operation.'"]');
        if(count($text)>0){
            return $text[0];
        }else{
            return null;
        }
    }
    
    protected function setTitle($title){
        $this->title = $title;
    }
    
    protected function getTitle(){
        return $this->title;
    }
    
    protected function checkRouting(){
        
    }
    
    
}