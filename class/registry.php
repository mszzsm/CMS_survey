<?php

class Registry{
    
    /*
     * 
     * Uprawnienia użytkownika:
     * 1 - redaktor ankiet -> może tworzyć i edytować swoje ankiety plus
     *     wypełnianie ankiet
     * 2 - redaktor naczelny -> może tworzyć i edytować wszystkie ankiety plus
     *     wypełnianie ankiet
     * 3 - administrator systemu -> pełna opcja
     * 
     */
    
    private static $instance = null;
    private $request;
    private $post;
    private $get;
    private $session;
    private $files;
    private $messages;
    private $messageCode = null;
    private $messageType = null;
    private $options;
    private $data = array();
    private $userPermission = 0; 
    
    private function __construct(){
        $this->post = $_POST;$_POST = null;
        $this->get = $_GET;$_GET = null;
        $this->session = $_SESSION;
        $this->files = $_FILES;
        $this->messages = simplexml_load_file('data/messages.xml');
        $this->options = simplexml_load_file('data/options.xml');
        $this->messageType = $this->getSession('messageType');
        $this->messageCode = $this->getSession('messageCode');
    }
    
    public static function instance(): self{
        
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
        
    }
    
    public function setData($name,$data){
        
        $this->data["$name"] = $data;
        
    }
    
    public function getData($name){
        
        if(isset($this->data["$name"])){return $this->data["$name"];}
        else{return null;}
        
    }
    
    public function getRequest(): Request{
        
        if(is_null($this->request)){
            $this->request = new Request();
        }
        return $this->request;   
    }
    
    public function getMainDir(){
        return $this->mainDir;
    }
    
    public function getPost($index=null){
        
        if($index==null){
            return $this->post;
        }
        
        if(isset($this->post["$index"])){
            return $this->post["$index"];
        }else{
            return null;
        }
        
    }
    
    public function clearPost(){
        
        $this->post = null;
        
    }
    
    public function getGet($index=null){
        
        if($index==null){
            return $this->get;
        }
        
        if(isset($this->get["$index"])){
            return $this->get["$index"];
        }else{
            return null;
        }
        
    }
    
    public function getSession($index){
        
        if(isset($this->session["$index"])){
            return $this->session["$index"];
        }else{
            return null;
        }
        
    }
    
    public function getFiles(){
        
        return $this->files;
        
    }
    
    public function getMessage(){
        
        $_SESSION['messageCode']=null;
        $text=$this->messages->xpath('//message[@name="'.(string)$this->messageCode.'"]');
        if(count($text)>0){
            return $text[0];
        }else{
            return $this->messages->xpath('//message[@name="IN_NO_MESSAGE"]')[0];
        }
        
    }
    
    public function getMessageType(){
        
        $_SESSION['messageType']=null;
        return $this->messageType;
        
    }
    
    public function setMessage($name=null){
        
        if($name<>null){
            $type=substr($name,0,2);
            if($type=='IN' or $type=='ER' or $type=='OK'){
                $this->messageType=$type;
                $_SESSION['messageType']=$type;
                $this->messageCode=$name;
                $_SESSION['messageCode']=$name;
            }
        }
        
    }
    
    public function getOption($name):string{
        
        $text=$this->options->xpath('//option[@name="'.(string)$name.'"]');
        if(count($text)>0){
            return $text[0];
        }else{
            return null;
        }
        
    }
    
    public function setUserPermission($permission){
        
        $this->userPermission = $permission;
        
    }
    
    public function getUserPermission(){
        
        return $this->userPermission;
        
    }

    
}