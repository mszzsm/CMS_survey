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

<div class="container">
    <!-- <form action="<?php echo $mainDir; ?>survey/create/" method="post">
        <fieldset><label>Nazwa ankiety</label><input type="text" name="surveyName"></fieldset>
        <fieldset><label>Początek ankiety:</label><input type="date" name="surveyStart"></fieldset>
        <fieldset><label>Koniec ankiety:</label><input type="date" name="surveyEnd"></fieldset>
        <fieldset><label>Opis ankiety (widoczny na stronie):</label><input type="text" max="10" name="surveyDesc"></fieldset>
        <fieldset><input name="surveyAddBtn" type="submit" value="Utwórz!"></fieldset>
    </form> -->

    <div id="form-container">
	<h2>Dodaj nową ankietę</h2>
	<form action="<?php echo $mainDir; ?>survey/create/" method="post">
		<input type="text" name="surveyName"    placeholder="nazwa ankiety" onfocus="this.placeholder=''" onblur="this.placeholder='nazwa ankiety'">
		<input type="date" name="surveyStart"   placeholder="DD.MM.YYYY">
		<input type="date" name="surveyEnd">
        <input type="text" name="surveyDesc" lim="10">
		<!--<input type="submit" value="Dodaj">-->
	    <input type="submit" value="Utwórz!" name="surveyAddBtn"></a>
	</form>
</div>
</div>

<?php 
    require('template/footer.html.php'); 
