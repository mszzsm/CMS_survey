<?php 
    session_name("survey");
    session_start();


    if(!isset($_SESSION['signedIn'])){$_SESSION['signedIn'] = false;}
    if(!isset($_SESSION['userName'])){$_SESSION['userName'] = null;}
    if(!isset($_SESSION['login'])){$_SESSION['login'] = null;}
    if(!isset($_SESSION['messageCode'])){$_SESSION['messageCode'] = null;}
    if(!isset($_SESSION['messageType'])){$_SESSION['messageType'] = null;}

    //Główny kontroler
    require_once('controller/controller.php');

    //Klasy pomocnicze
    require_once('class/registry.php');
    require_once('class/request.php');

    //Kontrolery obiektów akcji
    require_once('class/objectAction.php');
    require_once('class/user.php');
    require_once('class/survey.php');

    //Modele obiektów akcji
    require_once('model/model.php');
    require_once('model/modelSurvey.php');
    require_once('model/modelUser.php');
    
    //Widoki
    require_once('view/view.php');
    require_once('view/viewUser.php');
    require_once('view/viewSurvey.php');
    
    //Pozostałe klasy
    //require_once('class/question');

    //$_SESSION['signedIn'] = false;
    Controller::run();
