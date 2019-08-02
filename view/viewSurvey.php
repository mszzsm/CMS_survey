<?php

class ViewSurvey extends View{
    
    private $module = null;
    
    public function __construct(){
        $this->module = "survey";
        parent::__construct();
    }
    
    public function render(){
        
        
        parent::render();
        
    }
    
    public function showListOfSurveys(){
        
        $listOfSurveys = $this->reg->getData('listOfSurveys');
        
        $title="Lista ankiet";
        $this->setTitle($title);
        $this->set('listOfSurveys',$listOfSurveys);
        $this->render();
        
    }
    
    public function create(){
        
        $title="Tworzenie nowej ankiety";
        $this->setTitle($title);
        $this->render();
        
    }
    
    public function manage(){
        
        $surveyDef = $this->reg->getData('surveyDef');
        $surveyDet = $this->reg->getData('surveyDet');
        
        $title="Ankieta ".$surveyDef[0]['name']." - zarządzanie";
        $this->setTitle($title);
        $this->set('surveyDef',$surveyDef);
        $this->set('surveyDet',$surveyDet);
        $this->render();
        
    }
    
    
    
}