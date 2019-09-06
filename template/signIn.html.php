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

<form action="<?php echo $mainDir; ?>user/signin/" method="post">
    <div id="form-container">
        <div class="inputs">
            <div class="row">
                <div class="col-12">
                    <h2><?php echo $title ?></h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="text" name="login" placeholder="nazwa użytkownika" style="width: 80%">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="password" name="password" placeholder="hasło" style="width: 80%">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <!--<input type="submit" value="zaloguj">-->
                    <input type="submit" name="signInBtn" class="btn btn-success" value="zaloguj" style="width: 80%" class="smp-info">
                </div>
            </div>
        </div>
    </div>
</form>
<?php 
    require('template/footer.html.php'); 
?>