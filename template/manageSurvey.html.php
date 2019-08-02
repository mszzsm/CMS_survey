<?php
    $title=$this->getTitle();
    $messageType=$this->get('messageType');
    $messageText=$this->get('messageText');
    $mainDir=$this->get("mainDir");
    $surveyDef=$this->get('surveyDef');
    $surveyDet=$this->get('surveyDet');
    require('template/header.html.php'); 
    if($messageType<>null){
        echo $messageText."<br/><br/>";
    }
?>


<?php  //var_dump($surveyDef)  ?>
<?php  var_dump($surveyDet['questions'])  ?>



<div class="wrapper">
    <nav id="sidebar">
        <div id="dismiss"><i class="fas fa-arrow-left"></i></div>
        <div class="sidebar-header">
            <h3>SMP Survey</h3>
        </div>
        <ul class="list-unstyled components">
        </ul>
    </nav>


    <div id="content">
        <div v-cloak class="container">
            <!-- <button type="button" id="sidebarCollapse" class="btn btn-info m-3">
                <i class="fas fa-align-left pr-2"></i><span>Toggle Sidebar</span>
            </button> -->
            <!-- Podstawowa informacja o ankiecie data /  autor możliwość aktywacji -->
            {{questions}}
            <section>
                <ul>
                    <li>Autor: <strong>{{sAuthor}}</strong> <br> </li>
                    <li>Stworzono: <strong>{{sCreated}}</strong> </li>

                    <li>okres obowiązywania: <strong> {{sStart}} - {{sEnd}}</strong><br>
                        <small class="text-warning">Zmienić format daty na yyyy/mm/dd</small>
                        <div class="row">
                            <div class="d-flex col-8">
                                <form
                                    action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]['id']; ?>/changeValidity/"
                                    method="post">
                                    <label>Od:</label> <input type="date" name="surveyStart"
                                        value="<?php echo $surveyDef[0]['start']; ?>" />
                                    <label>Do:</label> <input type="date" name="surveyEnd"
                                        value="<?php echo $surveyDef[0]['end']; ?>" />
                                    <input name="changeValidityBtn" type="submit" value="->">
                                </form>
                            </div>
                            <div v-cloak class="col-4">
                                <div v-show="this.sEnabled == 1" id="selector" @click="goto('deactivate')">
                                    <label class="switch float-right">
                                        <input type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </a>
                                </div>
                                <div v-show="this.sEnabled == 0" id="selector" @click="goto('activate')">
                                    <label class="switch float-right">
                                        <input type="checkbox">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </section>


            <!-- Actywacja / Deaktywacja ankiety z poziomu PHP -->

            <?php   
                // echo ($surveyDef[0]['enabled']==0?
                // "Nieaktywna (<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/activate/\">Aktywuj</a>)":
                // "Aktywna (<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/deactivate/\">Dezaktywuj</a>)"); 
            ?>

            <hr>

            <h2>Sekcje ankiety</h2>
            <small class="text-warning">Lista sekcij</small>
            <form action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]['id']; ?>/addSection/"
                method="post">
                <label>Nazwa sekcji</label><input type="text" name="sectionName">
                <button name="addSectionBtn" class="btn btn-success btn-sm" type="submit">+</button>
            </form>

            <div v-for="(section, i) in sectionsList">
                <small>{{i+1}}: {{section}}</small>
            </div>
            <hr>




            <h2>Pytania w ankiecie <button class="btn btn-success btn-sm" data-toggle="modal"
                    data-target="#new-question">+</button></h2>
            <?php 
    /*
    * 
    * Jeśli pytanie ma status enabled=0, to musi pojawić się link do aktywowania pytania
    * Jeśli enabled=1, to musi być możliwość dezaktywacji pytania
    * 
    */

    if(count($surveyDet['questions'])>0){
        foreach($surveyDet['questions'] as $q){
            echo "<b>".$q['text']."</b> (id:".$q['id']."), typ: ".$q['type'].", włączone: ".$q['enabled'].", sekwencja: ".$q['sequence'].", sekcja: ".$q['section'].", pyt. obowiązkowe: ".$q['isRequired'].", czy komentarz obowiązkowy: ".$q['isCommReq']." -> ";
            echo ($q['enabled']==1)?("<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/disableQuestion/".$q['id']."/\">Wyłącz</a>"):("<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/enableQuestion/".$q['id']."/\">Włącz</a>");    
            echo "<br />Pytania: ";
            foreach($q['answers'] as $a){
                echo $a." | ";
            }
            echo "<br /><br />";
        }
    }else{
        echo "Brak pytań";
    }
?>
            <!-- 
    Po wyborze typu pytania, ma się pokazać/wykonać dodatkowo:
        - zamknięte jednokrotnego wyboru: jedno pole input + przycisk "Następna odpowiedź"
        - zamknięte wielokrotnego wyboru: j.w.
        - z zakresu: dwa pola input od-do
        - otwarte: nic - brak dodatkowych inputów
    KAŻDE PYTANIE MUSI MIEĆ CHECK-BOXA, CZY JEST OBOWIĄZKOWE!!!
-->


            <div class="modal fade" id="new-question" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                        <form
                            action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]['id']; ?>/addQuestion/"
                            method="post">

                            <label>Pytanie</label><input type="text" name="questionText">
                            <fieldset>
                                <legend>Sekcja</legend>
                                <select name="questionSection">
                                        <?php
                                            if(count($surveyDet['sections'])>0){
                                            echo "<option value=\"No Section\">Bez sekcji</option>";
                                            foreach($surveyDet['sections'] as $s){
                                                echo "<option value=\"".$s."\">".$s."</option>";
                                            }
                                        }else{
                                            echo "<option value=\"No Section\">Bez sekcji</option>";
                                        }
                                        ?>
                                </select>
                            </fieldset>

                            <fieldset>
                                <legend>Rodzaj</legend>
                                <label><input type="radio" name="questionType" v-model="newQuestionType" value="1"
                                        checked> Zamknięte jednokrotnego wyboru</label><br>
                                <label><input type="radio" name="questionType" v-model="newQuestionType" value="2">
                                    Zamknięte wielokrotnego wyboru</label><br>
                                <label><input type="radio" name="questionType" v-model="newQuestionType" value="3">
                                    Wskaż z zakresu - całe pytanie</label><br>
                                <label><input type="radio" name="questionType" v-model="newQuestionType" value="4">
                                    Wskaż z zakresu - poszczególne odpowiedzi</label> <br>
                                <label><input type="radio" name="questionType" v-model="newQuestionType" value="5">
                                    Otwarte</label>
                            </fieldset>
                            </div>
                            <div class="modal-body">
                            <div v-show="newQuestionType == 1 || newQuestionType == 2">
                                <fieldset>
                                    <legend>Opcja 1 i 2</legend><br />
                                    <label>Odpowiedź 1:</label><input type="text" name="answer1" /> <br />
                                    <label>Odpowiedź 2:</label><input type="text" name="answer2" /> <br />
                                    <label>Odpowiedź 3:</label><input type="text" name="answer3" /> <br />
                                    <label>Czy obowiązkowe? <input type="checkbox" name="isRequired"></label><br />
                                    <br /><br />
                            </div>

                            <div v-show="newQuestionType == 3">
                                <legend>Zakres</legend><br />
                                <label>Min:</label><input type="number" name="rangeMin" /><br />
                                <label>Max:</label><input type="number" name="rangeMax" /><br />
                                <label>Czy obowiązkowe? <input type="checkbox" name="isRequired"></label><br />
                                <label>Czy wymagany komentarz dla odpowiedzi MIN i MAX? <input type="checkbox"
                                        name="isCommReq"></label><br />
                                <br /><br />
                            </div>

                            <div v-show="newQuestionType == 4">
                                <legend>Opcja 4</legend><br />
                                <label>Odpowiedź 1:</label><input type="text" name="answerMinMax1" /> <br />
                                <label>Min:</label><input type="number" name="answer1rangeMin" /><br />
                                <label>Max:</label><input type="number" name="answer1rangeMax" /><br />

                                <label>Odpowiedź 2:</label><input type="text" name="answerMinMax2" /> <br />
                                <label>Min:</label><input type="number" name="answer2rangeMin" /><br />
                                <label>Max:</label><input type="number" name="answer2rangeMax" /><br />

                                <label>Odpowiedź 3:</label><input type="text" name="answerMinMax3" /> <br />
                                <label>Min:</label><input type="number" name="answer3rangeMin" /><br />
                                <label>Max:</label><input type="number" name="answer3rangeMax" /><br />

                                <label>Czy obowiązkowe? <input type="checkbox" name="isRequired"></label><br />
                                <label>Czy wymagany komentarz dla odpowiedzi MIN i MAX? <input type="checkbox"
                                        name="isCommRequired"></label><br />
                            </div>
                            </div>
                            <div class="modal-footer">
                                <fieldset><input name="addQuestionBtn" type="submit" value="Dodaj!"></fieldset>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
</div>
</main>

<script>
new Vue({
    el: '.wrapper',
    data: {
        mType: '<?php echo $messageType ?>',
        mClass: ['success', 'danger', 'info', 'warning'],

        mText:      '<?php echo $messageText ?>',
        Title:      '<?php echo $title ?>',
        mDir:       '<?php echo $mainDir ?>',
        sId:        '<?php echo $surveyDef[0]['id']?>',
        sName:      '<?php echo $surveyDef[0]['name']?>',
        sStart:     '<?php echo $surveyDef[0]['start']?>',
        sEnd:       '<?php echo $surveyDef[0]['end']?>',
        sAuthor:    '<?php echo $surveyDef[0]['author']?>',
        sCreated:   '<?php echo $surveyDef[0]['created']?>',
        sEnabled:   '<?php echo $surveyDef[0]['enabled']?>',
        sSections:  ``,
        activateURL:    `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] . '/activate/' ?>`,
        diactivateURL:  `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] . '/deactivate/' ?>`,

        
        newQuestionType: '',


    },
    computed: {
        sectionsList: function() {
            var s = `<?php  if ( count( $surveyDet['sections'] ) > 0 )  { 
                                foreach ( $surveyDet['sections'] as $s ) { 
                                    echo $s.","; 
                                } 
                            }
                            else { 
                                    echo "Brak sekcji"; 
                                    }?>`
            var sections = s.split(",")
            sections.pop()
            //this.sSections = sections
            return sections
        }, 

        questions: function() {
            let x = 0
             `<?php 
                                if ( count( $surveyDet['questions'] ) > 0 ) { 
                               ?>`
            var q =  { x : [`<?php foreach ( $surveyDet['questions'] as $q ){  echo $q['id'];?>`,
                            `<?php  echo  $q['text'];    ?>`,
                            `<?php  echo  $q['type'];    ?>`,
                            `<?php  echo  $q['enabled'];  ?>`,
                            `<?php  echo  $q['sequence']; ?>`,
                            `<?php  echo  $q['section'];  ?>`,
                            `<?php  echo  $q['isRequired'];   ?>`,
                            `<?php  echo  $q['isCommReq'];    ?>`,
                            `<?php  echo ($q['enabled']==1)?("<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/disableQuestion/".$q['id']."/"):("<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/enableQuestion/".$q['id']."/\">Włącz</a>");?>`],
                            `<?php  foreach ($q['answers'] as $a)  echo $a." | ";} 
                        } 
                    else { 
                        echo ']'; ?>`]`<?php
                    }
                ?>` }
        return q
       
    },
    methods: {
        rand: function() {
            return Math.floor(Math.random() * (100 - 1 + 1)) + 1;
        },
        goto: function(state) {
            setTimeout(() => {
                window.location.href =
                    `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] ?>` + '/' + state +
                    '/';
            }, 500);
        }
    }
})
</script>
<script>
$('#exampleModalCenter').modal(show)
</script>
<script type="text/javascript">
$(document).ready(function() {
    $("#sidebar").mCustomScrollbar({
        theme: "minimal"
    });

    $('#dismiss, .overlay').on('click', function() {
        $('#sidebar').removeClass('active');
        $('.overlay').removeClass('active');
    });

    $('#sidebarCollapse').on('click', function() {
        $('#sidebar').addClass('active');
        $('.overlay').addClass('active');
        $('.collapse.in').toggleClass('in');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    });
});
</script>
<?php 
    require('template/footer.html.php'); 