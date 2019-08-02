<?php

class ViewUser extends View{
    
    private $module = null;
    
    public function __construct(){
        $this->module = "user";
        parent::__construct();
    }
    
    public function render(){
        
        parent::render();
        
    }
    
}