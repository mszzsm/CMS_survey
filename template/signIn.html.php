<?php
    $title=$this->getTitle();
    $messageType=$this->get('messageType');
    $messageText=$this->get('messageText');
    $mainDir=$this->get("mainDir");
    require('template/header.html.php'); 
    
    if($messageType<>null){
        echo $messageText."<br /><br />";
    }
?>
    
    <form action="<?php echo $mainDir; ?>user/signin/" method="post">
        <div id="form-container">
	        <h2><?php echo $title ?></h2>
                <input type="text" name="login"         placeholder="nazwa użytkownika" style="width: 80%"><br>
                <input type="password" name="password"  placeholder="hasło" style="width: 80%"><br>
                <!--<input type="submit" value="zaloguj">-->
                <input type="submit" name="signInBtn" value="zaloguj" style="width: 80%">
        </div>
    </form>
<?php 
    require('template/footer.html.php'); 
?>

