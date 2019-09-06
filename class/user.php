<?php

class User extends ObjectAction{

    private static $instance = null;
    private $signedIn = false;
    private $userName = null;
    private $login = null;
    private $securityLevel = 0;

    public function __construct(){
        parent::__construct(__CLASS__);
        $this->userName = $this->reg->getSession('userName');
        $this->signedIn = $this->reg->getSession('signedIn');
        $this->login = $this->reg->getSession('login');
        $this->setUserPermission();
    }

    public function main(){
}

    private function authenticateWithAD($user,$password){

    $ldap_host = $this->reg->getOption("AD_SERVER");
	$ldap_dn = "DC=SMP";
	$ldap_usr_dom = 'SMP\\';
	$ldap = ldap_connect($ldap_host);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
	ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    if($bind = @ldap_bind($ldap, $ldap_usr_dom.$user, $password)) 
    {
        $filter = "(sAMAccountName=".$user.")";
        $attr = array("memberOf","displayName");
        $result = ldap_search($ldap, $ldap_dn, $filter, $attr);// or exit("Unable to search LDAP server");
        $entries = ldap_get_entries($ldap, $result);
        $displayName=$entries[0]['displayname'][0];
        ldap_unbind($ldap);
        return $displayName;
    } 
        else
    {
        return null;
    }
}
    
    public static function instance(): self{
        
        if(is_null(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function checkIfSignIn(): bool{
        return $this->signedIn;
    }
    
    public function signIn(): bool{
        if(!$this->reg->getPost("signInBtn")){
            $this->render();
            return $this->signedIn;
        }
        
        $login = $this->reg->getPost("login");
        $password = $this->reg->getPost("password");
        $userName=null;
        
        if(!is_null($login) and !is_null($password)){
            $userName = $this->authenticateWithAD($login,$password);
            
            if(!is_null($userName)){
                $this->userName = $userName;
                $this->securityLevel = $this->setUserPermission();
                $this->signedIn = true;
                $this->login = $login;
                $_SESSION['userName']=$this->userName;
                $_SESSION['login']=$this->login;
                $_SESSION['signedIn']=true;
                return $this->signedIn;
            }
            else
            {
                return $this->signedIn;
            }
                return $this->signedIn;
        }
            else
        {
                return $this->signedIn;
        }
                return $this->signedIn;
        
    }
    
    public function signOff(){
        $_SESSION['userName'] = null;
        $_SESSION['signedIn'] = false;
        $this->reg->setMessage("OK_SIGNOFF_OK");
        header("location: ".$this->reg->getOption('MAIN_DIR'));
    }
    
    private function setUserPermission(){
        $permission = $this->getModel()->getPermission($this->login);
        $this->reg->setUserPermission($permission);
    }
    
    private function render(){
        $this->view->render();
        exit;
    }
    
}