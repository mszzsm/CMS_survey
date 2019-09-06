<?php

class Survey extends ObjectAction{
    
    private $surveyName = null;
    private $surveyProject = null;
    private $isProcessing = false;
    
    
    public function __construct(){
        
        //$this->reg = Registry::instance();
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
     * 0 - normalny użytkownik -> tylko wypełnianie i przeglądanie odpowiedzi
     * 1 - redaktor ankiet -> może tworzyć i edytować swoje ankiety plus
     *     wypełnianie ankiet
     * 2 - redaktor naczelny -> może tworzyć i edytować wszystkie ankiety plus
     *     wypełnianie ankiet
     * 3 - administrator systemu -> pełna opcja
     * 
     */

        $permission=$this->reg->getUserPermission();
        switch($permission){
            case 0:
                $this->toFillSurveys($permission);
                break;
            
            case 1:
                $this->listOfMySurveys($permission);
                break;
            
            case 2:
                $this->listOfAllSurveys($permission);
                break;
            
            case 3:
                $this->listOfAllSurveys($permission);
                break;
        }
        
    }
    
    
    private function listOfAllSurveys($mode){

        $listOfSurveys = $this->getModel()->getListOfSurveys($mode);
        $this->reg->setData('listOfSurveys',$listOfSurveys);
        
        $this->reg->setData('mode','manage');
        
        $this->getView()->showListOfSurveys();
        
    }
    
    private function listOfMySurveys($mode){

        $listOfSurveys = $this->getModel()->getListOfSurveys($mode,$this->reg->getSession('userName'));
        $this->reg->setData('listOfSurveys',$listOfSurveys);
        
        $this->reg->setData('mode','manage');
        
        $this->getView()->showListOfSurveys();
        
    }
    
    private function toFillSurveys($mode){
        
        $listOfSurveys = $this->getModel()->getListOfSurveys($mode);
        $this->reg->setData('listOfSurveys',$listOfSurveys);
        
        $this->reg->setData('mode','fill');
        
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
    
    public function enableQuestion(){
        if(isset($this->reg->getRequest()->getParameters()[0]) and is_numeric($this->reg->getRequest()->getParameters()[0]) and 
           isset($this->reg->getRequest()->getParameters()[1]) and is_numeric($this->reg->getRequest()->getParameters()[1])){
            if($this->getModel()->enableQuestionA($this->reg->getRequest()->getParameters(0),$this->reg->getRequest()->getParameters(1))){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }
        exit;
    }
    
    public function disableQuestion(){
        if(isset($this->reg->getRequest()->getParameters()[0]) and is_numeric($this->reg->getRequest()->getParameters()[0]) and 
           isset($this->reg->getRequest()->getParameters()[1]) and is_numeric($this->reg->getRequest()->getParameters()[1])){
            if($this->getModel()->disableQuestionA($this->reg->getRequest()->getParameters(0),$this->reg->getRequest()->getParameters(1))){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }
        exit;
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
                //var_dump($this->reg->getPost());die();
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
        
        if(isset($this->reg->getRequest()->getParameters()[1]) and $this->reg->getRequest()->getParameters(1)=='addResponders'){
            if(isset($this->reg->getRequest()->getParameters()[0]) and is_numeric($this->reg->getRequest()->getParameters()[0])){
                $this->addResponders();
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
    
    public function addResponders(){
        $file = $this->reg->getFiles();
        
        if(
            $file['respondersFile']['error']==0 and 
            $file['respondersFile']['size']>0 and 
            $file['respondersFile']['type']=='application/vnd.ms-excel' and
            substr($file['respondersFile']['name'],-4)=='.csv'
        ){
            
            $respondersList = null;
            $fileName=$file['respondersFile']['tmp_name'];
            if (($handle = fopen($fileName, 'r')) !== FALSE) {
                $i=1;
                while (($data = fgetcsv($handle, 0, ',')) !== FALSE) {
                    $temp = explode(';',iconv( mb_detect_encoding($data[0], mb_detect_order(), TRUE), "UTF-8", $data[0]));
                    
                    $respondersList[$i]['idSurvey']=$this->reg->getRequest()->getParameters()[0];
                    
                    $respondersList[$i]['respondentId']=$temp[0];
                    array_splice($temp,0,1);
                    
                    $respondersList[$i]['respondentName']=mb_convert_case(mb_strtolower($temp[0]),MB_CASE_TITLE);
                    array_splice($temp,0,1);
                    
                    $temp[2]=mb_convert_case(mb_strtolower($temp[2]),MB_CASE_TITLE);
                    $respondersList[$i]['respondentParam']=json_encode($temp,JSON_UNESCAPED_UNICODE);
                    
                    $i++;
                }
                fclose($handle);
                
                if(count($respondersList)<=0){
                    $this->reg->setMessage("ER_FILE_EMPTY");
                    return false;
                }
                
                if($this->getModel()->saveResponders($respondersList)){
                    $this->reg->setMessage("OK_ADD_DATA");
                    return true;
                }else{
                    $this->reg->setMessage("ER_ADD_DATA");
                    return false;
                }
                
            }else{
                $this->reg->setMessage("ER_FILE_OPEN");
                return false;
            }
            
        }else{
            $this->reg->setMessage("ER_FILE_EMPTY");
            return false;
        }
        
        $this->reg->setMessage("ER_ACTION_UNDEF");
        return false;
    }
    
    public function fill(){
        $surveyId = $this->reg->getRequest()->getParameters(0);
        
        if($surveyId===null){
            $this->reg->setMessage("ER_WRONG_SURVEY_ID");
            header("location: ".$this->reg->getOption('MAIN_DIR'));
            exit;
        }
        
        $surveyDef = $this->getModel()->getSurveyDefinition($surveyId);
        if(count($surveyDef)==0){
            $this->reg->setMessage("ER_WRONG_SURVEY_ID");
            header("location: ".$this->reg->getOption('MAIN_DIR'));
            exit;
        }
        
        $surveyDet = $this->getModel()->getSurveyDetailsToFill($surveyId);
        
        $this->reg->setData('surveyDef',$surveyDef);
        $this->reg->setData('surveyDet',$surveyDet);
        
        $this->getView()->fill();
        exit;
    }
    
}