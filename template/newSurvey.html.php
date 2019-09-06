<?php
    $title=$this->getTitle();
    $messageType=$this->get('messageType');
    $messageText=$this->get('messageText');
    $mainDir=$this->get("mainDir");
    require('template/header.html.php'); 

    if($messageType<>null){
        if($messageType == 'OK'){
            echo " <div class='alert alert-success'>" . $messageText. "</div>";
        } else {
            echo " <div class='alert alert-warning'>" . $messageText. "</div>";
        }
    }
?>

<div class="container">
    <!-- <form action="<?php echo $mainDir; ?>survey/create/" method="post">
        <fieldset><label>Nazwa ankiety</label><input type="text" name="surveyName"></fieldset>
        <fieldset><label>Początek ankiety:</label><input type="date" name="surveyStart"></fieldset>
        <fieldset><label>Koniec ankiety:</label><input type="date" name="surveyEnd"></fieldset>
        <fieldset><label>Opis ankiety (widoczny na stronie):</label><input type="text" max="10" name="surveyDesc"></fieldset>
        <fieldset><input name="surveyAddBtn" type="submit" value="Utwórz!"></fieldset>
    </form> -->

        <div id="form-container" class="flex-container">
            <h2>Dodaj nową ankietę</h2>
        <form action="<?php echo $mainDir; ?>survey/create/" method="post">
            <input type="text"
                    name="surveyName"
                    class="title"
                    placeholder="Nazwa ankiety"
                    onfocus="this.placeholder=''"
                    onblur="this.placeholder='Nazwa ankiety'">
           
            <h2>Okres obowiązywania</h2>
                <div class="d-flex">
                    <input class="date" type="date" name="surveyStart"  id="begin" placeholder="Od">
                </div>
                <div class="d-flex">
                    <input class="date" type="date" name="surveyEnd"  placeholder="Do" id="untill">
                </div>

                <input type="text"
                        class="title"
                        name="surveyDesc"
                        onfocus="this.placeholder=''"
                        onblur="this.placeholder='Opis ankiety'"
                        placeholder="Opis Ankiety">

                <input type="submit" class="btn btn-success" value="Utwórz!" name="surveyAddBtn">
        </form>
    </div>
</div>

<style>
    .flex-container {
        display: flex;
        flex-direction: column;
        margin: 10px;
        text-align: center;
        font-size: 15px;
        }
    .title {
        width: 100%;   
        height: 50px;
        border-top: none;
        border-right: none;
        border-left: none;
        border-radius: 0px;
        font-size: 20px;
    }

    .date {
        width: 100%;  
        height: 30px;
        border-top: none;
        border-right: none;
        border-left: none;
        border-radius: 0px;
        font-size: 20px;
    },

    .description{
        width: 100%;   
        height: 50px;
        border-top: none;
        border-right: none;
        border-left: none;
        border-radius: 0px;
        font-size: 20px;
    }
</style>

<?php 
    require('template/footer.html.php'); 
