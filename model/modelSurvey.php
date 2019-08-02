<?php
class ModelSurvey extends Model{
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
    
    public function getListOfSurveys(){
        
        $sql = "select id,name,start,end,author,enabled from tbl_surveys";
        $query=$this->pdo->prepare($sql);
        return $this->getData($query);
        
    }
    
    public function saveNewSurvey(){
     
        try{
            $sql="
                INSERT INTO tbl_surveys (name,start,end,description,enabled,author,created)
                VALUES (
                    :name,
                    :start,
                    :end,
                    :desc,
                    0,
                    '".$this->reg->getSession('userName')."',
                    '".date('Y-m-d H:i:s')."'    
                )
            ";
            
            $surveyName = $this->reg->getData('surveyName');
            $surveyStart = $this->reg->getData('surveyStart');
            $surveyEnd = $this->reg->getData('surveyEnd');
            $surveyDesc = $this->reg->getData('surveyDesc');
            
            $query=$this->pdo->prepare($sql);
            $query->bindParam(':name',$surveyName,PDO::PARAM_STR);
            $query->bindParam(':start',$surveyStart,PDO::PARAM_STR);
            $query->bindParam(':end',$surveyEnd,PDO::PARAM_STR);
            $query->bindParam(':desc',$surveyDesc,PDO::PARAM_STR);
            $query->execute();
            
            $sql="
                 select max(id) as id from tbl_surveys
                ";
            $query=$this->pdo->prepare($sql);
            $id = $this->getData($query);
            
            $survey = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><root></root>");
            
            $basic = $survey->addChild('basic_info');
            $basic->addChild('id', $id[0]['id']);
            $basic->addChild('questionQty', 0);
            
            $survey->addChild('sections');
            $survey->addChild('questions');
            
            $survey->saveXML('surveys_xml/'.$id[0]['id'].'.xml');
            
            return $id[0]['id'];
            
        }catch(Exception $e){
            return false;	
        }
        
    }
    
    public function changeValidity(){
        try{
            $sql="
                update tbl_surveys set
                    start=:start,
                    end=:end,
                    modifiedBy='".$this->reg->getSession('userName')."',
                    modifiedDate='".date('Y-m-d H:i:s')."'
                where id=:id
            ";
            
            $surveyId = $this->reg->getData('surveyId');
            $surveyStart = $this->reg->getData('surveyStart');
            $surveyEnd = $this->reg->getData('surveyEnd');
            
            $query=$this->pdo->prepare($sql);
            $query->bindParam(':id',$surveyId,PDO::PARAM_STR);
            $query->bindParam(':start',$surveyStart,PDO::PARAM_STR);
            $query->bindParam(':end',$surveyEnd,PDO::PARAM_STR);
            $query->execute();
            
            return true;
            
        }catch(Exception $e){
            echo $e->getMessage();die();
            return false;	
        }
    }
    
    public function getSurveyDefinition($id){
        /*
         * 
         * Funkcja pobiera z bazy dane nagłówkowe ankiety.
         * Reszta znajduje się w XML.
         * 
         */
        
        $sql = "select id,name,start,end,author,created,enabled from tbl_surveys"
            . " where id=:id";
        $query=$this->pdo->prepare($sql);
        $query->bindParam(':id',$id,PDO::PARAM_INT);
        return $this->getData($query);
        
    }
    
    public function getSurveyDetails($id){
        
        /*
         * 
         * Funkcja pobiera szczgóły ankiety z pliku XML
         * 
         */
        
        $file = file_get_contents('surveys_xml/'.$id.'.xml');
        $survey = simplexml_load_string($file);
        
        if($survey===FALSE){
            return false;
        }else{
            $surveyDet = array();
            
            //POBRANIE SEKCJI
            $sections = $survey->{'sections'};
            if(count($sections->section)>0){
                
                $surveyDet['sections'] = array();
                foreach($sections->section as $s){
                    array_push($surveyDet['sections'],(string)$s[0]);
                }
                
            }else{
                $surveyDet['sections']=null;
            }
            
            //POBRANIE PYTAŃ
            $questions = $survey->{'questions'};
            if(count($questions->question)>0){
                
                $surveyDet['questions'] = array();
                foreach($questions->question as $q){
                    array_push($surveyDet['questions'],array());
                    
                    end($surveyDet['questions']);
                    $key = key($surveyDet['questions']);

                    $surveyDet['questions'][$key]['id'] = (string)$q->{'id'};
                    $surveyDet['questions'][$key]['text'] = (string)$q->{'text'};
                    $surveyDet['questions'][$key]['type'] = (string)$q->{'type'};
                    $surveyDet['questions'][$key]['enabled'] = (string)$q->{'enabled'};
                    $surveyDet['questions'][$key]['isRequired'] = (string)$q->{'isRequired'};
                    $surveyDet['questions'][$key]['isCommReq'] = (string)$q->{'isCommReq'};
                    $surveyDet['questions'][$key]['sequence'] = (string)$q->{'sequence'};
                    $surveyDet['questions'][$key]['section'] = (string)$q->{'section'};
                    $surveyDet['questions'][$key]['answers'] = (array)$q->answers->answer;
                    
                }
                
            }else{
                $surveyDet['questions']=null;
            }

            return $surveyDet;
        }
        
    }
    
    public function addSection(){
        $newSection = $this->reg->getPost()['sectionName'];
        $idSurvey = $this->reg->getRequest()->getParameters(0);
        
        $file = file_get_contents('surveys_xml/'.$idSurvey.'.xml');
        $survey = simplexml_load_string($file);
        
        $sections = $survey->{'sections'};
        $sections->addChild('section',$newSection);
        
        if($survey->saveXML('surveys_xml/'.$idSurvey.'.xml')){
            return true;
        }else{
            return false;
        }
    }
    
    public function addQuestion(){//var_dump($this->reg->getPost());die();
        
        $idSurvey = $this->reg->getRequest()->getParameters(0);
        $questionText = $this->reg->getPost()['questionText'];
        $questionSection = $this->reg->getPost()['questionSection'];
        $questionType = $this->reg->getPost()['questionType'];
        
        $file = file_get_contents('surveys_xml/'.$idSurvey.'.xml');
        $survey = simplexml_load_string($file);
        
        $basic = $survey->{'basic_info'};
        $questionQty = $basic->{'questionQty'};
        $questionQty = $questionQty + 1;
        $basic->{'questionQty'} = $questionQty;
        
        $questions = $survey->{'questions'};
        $question = $questions->addChild('question');
        $question->addChild('id',$questionQty);
        $question->addChild('type',$questionType);
        $question->addChild('text',$questionText);
        $question->addChild('section',$questionSection);
        $question->addChild('enabled',0);
        $question->addChild('sequence',0);
        if(isset($this->reg->getPost()['isRequired'])){
            $question->addChild('isRequired',1);
        }else{
            $question->addChild('isRequired',0);
        }
        if(isset($this->reg->getPost()['isCommReq'])){
            $question->addChild('isCommReq',1);
        }else{
            $question->addChild('isCommReq',0);
        }
        $answers = $question->addChild('answers');
        
        switch($questionType){
            case 1: //pytanie zamknięte, jednokrotny wybór
            case 2: //pytanie zamknięte, wielokrotny wybór
                $x=1;
                while(isset($this->reg->getPost()['answer'.$x])){
                    $answers->addChild('answer',$this->reg->getPost()['answer'.$x]);
                    $x++;
                }
                break;
            
            case 3: //pytanie z zakresu I
                $rangeMin = $this->reg->getPost()['rangeMin'];
                $rangeMax = $this->reg->getPost()['rangeMax'];
                $answers->addChild('answer',$rangeMin);
                $answers->addChild('answer',$rangeMax);
                break;
            
            case 4: //pytanie z zakresu II - dla poszczególnych odpowiedzi
                $x=1;
                while(isset($this->reg->getPost()['answer'.$x.'rangeMin']) 
                    and isset($this->reg->getPost()['answer'.$x.'rangeMax'])
                    and isset($this->reg->getPost()['answerMinMax'.$x])){
                    $answers->addChild('answer',$this->reg->getPost()['answerMinMax'.$x]);
                    $answers->addChild('answer',$this->reg->getPost()['answer'.$x.'rangeMin']);
                    $answers->addChild('answer',$this->reg->getPost()['answer'.$x.'rangeMax']);
                    $x++;
                }    
                break;
            
            case 5: //pytanie otwarte, brak wariantów
                break;
        }
        
        //die();
        
        if($survey->saveXML('surveys_xml/'.$idSurvey.'.xml')){
            return true;
        }else{
            return false;
        }
        
    }
    
    public function deleteSection(){
        /*Najpierw należy sprawdzić, czy któreś z pytań nie należy już do usuwanej sekcji.*/
    }
    
    public function enableQuestion($idSurvey,$idQuestion){
        $file = file_get_contents('surveys_xml/'.$idSurvey.'.xml');
        $survey = simplexml_load_string($file);
        
        $questions = $survey->questions;
        foreach($questions->children() as $q){
            if($q->{'id'} == $idQuestion){
                $q->{'enabled'}=1;
                if($survey->saveXML('surveys_xml/'.$idSurvey.'.xml')){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;
    }
    
    public function disableQuestion($idSurvey,$idQuestion){
        $file = file_get_contents('surveys_xml/'.$idSurvey.'.xml');
        $survey = simplexml_load_string($file);
        
        $questions = $survey->questions;
        foreach($questions->children() as $q){
            if($q->{'id'} == $idQuestion){
                $q->{'enabled'}=0;
                if($survey->saveXML('surveys_xml/'.$idSurvey.'.xml')){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;
    }
    
    public function activateSurvey($idSurvey){
        try{
            $sql="
                update tbl_surveys set
                    enabled=1,
                    modifiedBy='".$this->reg->getSession('userName')."',
                    modifiedDate='".date('Y-m-d H:i:s')."'
                where id=:id
            ";
            
            $query=$this->pdo->prepare($sql);
            $query->bindParam(':id',$idSurvey,PDO::PARAM_STR);
            $query->execute();
            
            return true;
            
        }catch(Exception $e){
            echo $e->getMessage();die();
            return false;	
        }
    }
    
    public function deactivateSurvey($idSurvey){
        try{
            $sql="
                update tbl_surveys set
                    enabled=0,
                    modifiedBy='".$this->reg->getSession('userName')."',
                    modifiedDate='".date('Y-m-d H:i:s')."'
                where id=:id
            ";
            
            $query=$this->pdo->prepare($sql);
            $query->bindParam(':id',$idSurvey,PDO::PARAM_STR);
            $query->execute();
            
            return true;
            
        }catch(Exception $e){
            echo $e->getMessage();die();
            return false;	
        }
    }
    
    
}
