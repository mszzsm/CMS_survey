<?php

abstract class Object{
    
    protected $view = null;
    protected $model = null;
    protected $reg = null;
    protected $classView = null;
    protected $classModel = null;
    
    protected function __construct($class){
        $this->reg = Registry::instance();
        
        $this->classView='View'.$class;
        $this->view = new $this->classView();
        
        $this->classModel='Model'.$class;
        $this->model = new $this->classModel();
        
    }
    
    protected function getView(){
        
        return $this->view;
        
    }
    
    protected function getModel(){
        
        return $this->model;
        
    }
    
    //określa akcję domyślną dla danego modułu
    abstract public function main();
    
}