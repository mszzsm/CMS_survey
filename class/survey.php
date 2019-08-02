<?php

class Survey extends Object{
    
    private $surveyName = null;
    private $surveyProject = null;
    
    
    public function __construct(){
        
        $this->reg = Registry::instance();
        parent::__construct(__CLASS__);
        
    }
    
    public function main(){
        
     /*
      * 
      * Ankiety mają działać w ten sposób, że gdy osoba ma oczekującą ankietę
      * to ma się pokazać ich lista. Jesli nie, to jedynie komunikat typu
      * BRAK ANKIET DO WYPEŁNIENIA.
      * Jeśli natomiast osoba wchodząca to administrator, to domyślnie ma się
      * pojawić dodatkowe menu, Dodaj ankietę, etc., plus możliwość pokazania
      * listy aktywnych ankiet.
      * Kontrola uprawnień to pierwsza czynność w osobnej funkcji i stamtąd
      * program przjdzie do odpowiedniej czynności
      * 
      */
        
    /*
     * 
     * Uprawnienia użytkownika:
     * 1 - normalny użytkownik -> tylko wypełnianie i przeglądanie odpowiedzi
     * 2 - redaktor ankiet -> może tworzyć i edytować swoje ankiety plus
     *     wypełnianie ankiet
     * 3 - redaktor naczelny -> może tworzyć i edytować wszystkie ankiety plus
     *     wypełnianie ankiet
     * 4 - administrator systemu -> pełna opcja
     * 
     */
     
        switch($this->reg->getUserPermission()){
            case 1:
                $this->listOfSurveys();
                break;
            
            case 2:
                $this->listOfSurveys();
                break;
            
            case 3:
                $this->listOfSurveys();
                break;
            
            case 4:
                $this->listOfSurveys();
                break;
        }
        
    }
    
    
    private function listOfSurveys(){
        
        $listOfSurveys = $this->getModel()->getListOfSurveys();
        $this->reg->setData('listOfSurveys',$listOfSurveys);
        
        $this->getView()->showListOfSurveys();
        
    }
    
    private function saveNewSurvey(){
        
        if(         isset($this->reg->getPost()['surveyName']) and $this->reg->getPost()['surveyName']<>'' 
                and isset($this->reg->getPost()['surveyStart']) and DateTime::createFromFormat('Y-m-d',$this->reg->getPost()['surveyStart'])!==FALSE 
                and isset($this->reg->getPost()['surveyEnd']) and DateTime::createFromFormat('Y-m-d',$this->reg->getPost()['surveyEnd'])!==FALSE 
                and DateTime::createFromFormat('Y-m-d',$this->reg->getPost()['surveyStart']) <= DateTime::createFromFormat('Y-m-d',$this->reg->getPost()['surveyEnd'])) {
            
            $this->reg->setData('surveyName',(string)$this->reg->getPost()['surveyName']);
            $this->reg->setData('surveyStart',(string)$this->reg->getPost()['surveyStart']);
            $this->reg->setData('surveyEnd',(string)$this->reg->getPost()['surveyEnd']);
            if(isset($this->reg->getPost()['surveyName'])){
                $this->reg->setData('surveyDesc',(string)$this->reg->getPost()['surveyDesc']);
            }else{
                $this->reg->setData('surveyDesc','');
            }
            $this->reg->clearPost();
            
            if($this->getModel()->saveNewSurvey()){
                $this->reg->setMessage("OK_SURVEY_ADDED");
                $this->create();
            }else{
                $this->reg->setMessage("ER_SURVEY_NOT_ADDED");
                $this->create();
            }

        }else{
            $this->reg->clearPost();
            $this->reg->setMessage("ER_WRONG_DATA");
            $this->create();
        }
        exit;
    }
    
    public function create(){
        
        if(isset($this->reg->getPost()['surveyAddBtn'])){
            $this->saveNewSurvey();
            header("location: ".$this->reg->getOption('MAIN_DIR'));
        }else{
            $this->getView()->create();
        }
        
    }
    
    public function manage(){
        
        $surveyId = $this->reg->getRequest()->getParameters(0);
        //var_dump($this->reg->getRequest()->getParameters());
        //var_dump($this->reg->getPost());
        
        if($surveyId===null){
            $this->reg->setMessage("ER_WRONG_SURVEY_ID");
            header("location: ".$this->reg->getOption('MAIN_DIR'));
            exit;
        }
        
        if(isset($this->reg->getRequest()->getParameters()[1]) and $this->reg->getRequest()->getParameters(1)=='addSection'){
            if(isset($this->reg->getPost()['addSectionBtn']) and $this->reg->getPost()['sectionName']<>''){
                if($this->getModel()->addSection()){
                    $this->reg->setMessage("OK_ADD_DATA");
                }else{
                    $this->reg->setMessage("ER_ADD_DATA");
                }
            }else{
                $this->reg->setMessage("ER_WRONG_DATA");
            }
        }
        
        if(isset($this->reg->getRequest()->getParameters()[1]) and $this->reg->getRequest()->getParameters(1)=='addQuestion'){
            if(isset($this->reg->getPost()['addQuestionBtn']) and $this->reg->getPost()['questionText']<>''){
                if($this->getModel()->addQuestion()){
                    $this->reg->setMessage("OK_ADD_DATA");
                }else{
                    $this->reg->setMessage("ER_ADD_DATA");
                }
            }else{
                $this->reg->setMessage("ER_WRONG_DATA");
            }
        }
        
        if(isset($this->reg->getRequest()->getParameters()[1]) and $this->reg->getRequest()->getParameters(1)=='changeValidity'){
            if(isset($this->reg->getPost()['changeValidityBtn']) and $this->reg->getPost()['surveyStart']<>'' and
                    $this->reg->getPost()['surveyEnd']<>''){
                $this->reg->setData('surveyId',$this->reg->getRequest()->getParameters(0));
                $this->reg->setData('surveyStart',(string)$this->reg->getPost()['surveyStart']);
                $this->reg->setData('surveyEnd',(string)$this->reg->getPost()['surveyEnd']);
                $this->reg->clearPost();
                
                if($this->getModel()->changeValidity()){
                    $this->reg->setMessage("OK_ADD_DATA");
                }else{
                    $this->reg->setMessage("ER_ADD_DATA");
                }
            }else{
                $this->reg->setMessage("ER_WRONG_DATA");
            }
        }
        
        if(isset($this->reg->getRequest()->getParameters()[1]) and $this->reg->getRequest()->getParameters(1)=='enableQuestion'){
            if(isset($this->reg->getRequest()->getParameters()[2]) and is_numeric($this->reg->getRequest()->getParameters()[2])){
                if($this->getModel()->enableQuestion($this->reg->getRequest()->getParameters(0),$this->reg->getRequest()->getParameters(2))){
                    $this->reg->setMessage("OK_ADD_DATA");
                }else{
                    $this->reg->setMessage("ER_ADD_DATA");
                }
            }else{
                $this->reg->setMessage("ER_WRONG_DATA");
            }
        }
        
        if(isset($this->reg->getRequest()->getParameters()[1]) and $this->reg->getRequest()->getParameters(1)=='disableQuestion'){
            if(isset($this->reg->getRequest()->getParameters()[2]) and is_numeric($this->reg->getRequest()->getParameters()[2])){
                if($this->getModel()->disableQuestion($this->reg->getRequest()->getParameters(0),$this->reg->getRequest()->getParameters(2))){
                    $this->reg->setMessage("OK_ADD_DATA");
                }else{
                    $this->reg->setMessage("ER_ADD_DATA");
                }
            }else{
                $this->reg->setMessage("ER_WRONG_DATA");
            }
        }
        
        if(isset($this->reg->getRequest()->getParameters()[1]) and $this->reg->getRequest()->getParameters(1)=='activate'){
            if(isset($this->reg->getRequest()->getParameters()[0]) and is_numeric($this->reg->getRequest()->getParameters()[0])){
                if($this->getModel()->activateSurvey($this->reg->getRequest()->getParameters(0))){
                    $this->reg->setMessage("OK_ADD_DATA");
                }else{
                    $this->reg->setMessage("ER_ADD_DATA");
                }
            }else{
                $this->reg->setMessage("ER_WRONG_DATA");
            }
        }
        
        if(isset($this->reg->getRequest()->getParameters()[1]) and $this->reg->getRequest()->getParameters(1)=='deactivate'){
            if(isset($this->reg->getRequest()->getParameters()[0]) and is_numeric($this->reg->getRequest()->getParameters()[0])){
                if($this->getModel()->deactivateSurvey($this->reg->getRequest()->getParameters(0))){
                    $this->reg->setMessage("OK_ADD_DATA");
                }else{
                    $this->reg->setMessage("ER_ADD_DATA");
                }
            }else{
                $this->reg->setMessage("ER_WRONG_DATA");
            }
        }
        
        $surveyDef = $this->getModel()->getSurveyDefinition($surveyId);
        if(count($surveyDef)==0){
            $this->reg->setMessage("ER_WRONG_SURVEY_ID");
            header("location: ".$this->reg->getOption('MAIN_DIR'));
            exit;
        }
        
        $surveyDet = $this->getModel()->getSurveyDetails($surveyId);
        
        $this->reg->setData('surveyDef',$surveyDef);
        $this->reg->setData('surveyDet',$surveyDet);
        
        $this->getView()->manage();
        exit;
    }
    
}