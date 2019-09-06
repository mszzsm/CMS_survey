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
    
    public function getListOfSurveys($mode=0,$author=null){
        
        //mode=0 -> tylko ankiety do wypełnienia /dla użytkownika
        //mode=1 -> lista dla redaktora /autora ankiet
        //mode=2 -> cała lista /redaktor naczelny + administrator
        
        $sql = "select tbl_surveys.id,tbl_surveys.name,tbl_surveys.start,tbl_surveys.end,"
                . "tbl_surveys.author,tbl_surveys.enabled from tbl_surveys ";
        switch($mode){
            case 0: //pobrać tylko trwające
                $sql .= "
                    left join tbl_respondents on tbl_respondents.idSurvey = tbl_surveys.id
                    where tbl_surveys.enabled=1 and tbl_surveys.start<='".date('Y-m-d')."' 
                    and tbl_surveys.end>='".date('Y-m-d')."' 
                    and tbl_respondents.respondentName='".$this->reg->getSession('userName')."'
                ";
                break;
            
            case 1: //pobrać tylko ankiety właściciela
                $sql .= "where tbl_surveys.author='".$author."'";
                break;
            
            default: 
                //redaktor naczelny + administrator, nalezy pobrać wszystko
                break;
        }
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
        
        $sql = "select id,name,start,end,author,created,enabled,description from tbl_surveys"
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
                    $surveyDet['questions'][$key]['isCommReq'] = (string)$q->{'isCommReqRequired'};
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
    
    public function getSurveyDetailsToFill($id){
        
        /*
         * 
         * Funkcja pobiera szczgóły ankiety z pliku XML
         * celem wypełnienia ankiety
         * 
         */
        
        $file = file_get_contents('surveys_xml/'.$id.'.xml');
        $survey = simplexml_load_string($file);
        
        if($survey===FALSE){
            return false;
        }else{
            $surveyDet = array();
            
            //POBRANIE PYTAŃ
            $questions = $survey->{'questions'};
            if(count($questions->question)>0){
                
                $surveyDet = array();
                foreach($questions->question as $q){
                    if((string)$q->{'enabled'} == 1){
                        array_push($surveyDet,array());

                        end($surveyDet);
                        $key = key($surveyDet);

                        $surveyDet[$key]['id'] = (string)$q->{'id'};
                        $surveyDet[$key]['text'] = (string)$q->{'text'};
                        $surveyDet[$key]['type'] = (string)$q->{'type'};
                        $surveyDet[$key]['isRequired'] = (string)$q->{'isRequired'};
                        $surveyDet[$key]['isCommReq'] = (string)$q->{'isCommReqRequired'};
                        $surveyDet[$key]['section'] = (string)$q->{'section'};
                        $surveyDet[$key]['answers'] = (array)$q->answers->answer;
                    }
                }
                
                array_multisort( array_column($surveyDet, "section"), SORT_ASC, $surveyDet );
                
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
        if(isset($this->reg->getPost()['isCommRequired'])){
            $question->addChild('isCommRequired',1);
        }else{
            $question->addChild('isCommRequired',0);
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
                while(isset($this->reg->getPost()['answer'.$x.'RangeMin']) 
                    and isset($this->reg->getPost()['answer'.$x.'RangeMax'])
                    and isset($this->reg->getPost()['answerMinMax'.$x])){
                    $answers->addChild('answer',$this->reg->getPost()['answerMinMax'.$x]);
                    $answers->addChild('answer',$this->reg->getPost()['answer'.$x.'RangeMin']);
                    $answers->addChild('answer',$this->reg->getPost()['answer'.$x.'RangeMax']);
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
    
    public function enableQuestionA($idSurvey,$idQuestion){
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
    
    public function disableQuestionA($idSurvey,$idQuestion){
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
    
    public function saveResponders($data){
        try{
            $sql = 
                "
                    insert into tbl_respondents (idSurvey,respondentId,respondentName,respondentParam)
                    values ";
            
            $x = array_fill(0, count($data), "(?, ?, ?, ?)");
            $sql .=  implode(",",$x);
            
            $query = $this->pdo->prepare($sql);
            $i = 1;
            foreach($data as $d) { //bind the values one by one
                $query->bindValue($i++, $d['idSurvey']);
                $query->bindValue($i++, $d['respondentId']);
                $query->bindValue($i++, $d['respondentName']);
                $query->bindValue($i++, $d['respondentParam']);
            }
            
            $query->execute();
            
            return true;
            
        }catch(Exception $e){
            echo $e->getMessage();die();
            return false;	
        }
    }
    
    
}
