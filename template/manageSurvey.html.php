    <?php
        $title=$this->getTitle();
        $messageType=$this->get('messageType');
        $messageText=$this->get('messageText');
        $mainDir=$this->get("mainDir");
        $surveyDef=$this->get('surveyDef');
        $surveyDet=$this->get('surveyDet');
        require('template/header.html.php'); 
    ?>

    <?php  //var_dump($surveyDef)  ?>
    <?php  //var_dump($surveyDet['questions'])  ?>
    <script>
let Click = (x) => console.log(x);
    </script>


    <div class="wrapper">
        <!-- <nav id="sidebar">
            <div id="dismiss"><i class="fas fa-arrow-left"></i></div>
            <div class="sidebar-header">
                <h3>SMP Survey</h3>
            </div>
                <ul class="list-unstyled components">
            </ul>
        </nav> -->



        <div id="content" v-cloak>
            <div class="container">
                <div v-if="messages.mType == 'OK'" class="alert alert-success"> {{messages.mText}} </div>
                <div v-else-if="messages.mType == 'ER'" class="alert alert-danger"> {{messages.mText}} </div>


                <!-- Podstawowa informacja o ankiecie data /  autor możliwość aktywacji -->
                <h1>{{Title}}</h1>
                <hr>
                <section>
                    <div class="row">
                        <div class="col-9">
                            <span>Autor:</span> <strong>{{sAuthor}}</strong> <br>
                            <span>Stworzono: </span> <strong>{{sCreated}}</strong> <br>
                            <div v-show="dateEditing == false">
                                <span>Okres obowiązywania:</span>
                                <span> od <strong> {{sStart}} </strong> od <strong> {{sEnd}} </strong> </span>
                                <span v-show="dateEditing == false" class="far fa-edit"
                                    @click="dateEditing = !dateEditing" style="cursor: pointer;" />
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div v-show="this.sEnabled == 1" id="selector" @click="switchSurvey('deactivate')">
                                    <div class="d-flex">
                                        <small class="text-success">Ankieta jest aktywna </small>
                                    </div>
                                    <div class="d-flex">
                                        <label class="switch float-right">
                                            <input type="checkbox" checked>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>


                                <div v-show="this.sEnabled == 0" id="selector" @click="switchSurvey('activate')">
                                    <div class="d-flex">
                                        <small class="text-danger">Ankieta jest nieaktywna</small>
                                    </div>
                                    <div class="d-flex">
                                        <label class="switch float-right">
                                            <input type="checkbox">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div v-show="dateEditing == true" class="row">
                        <div class="col6">
                            <form id="date-change"
                                action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]['id']; ?>/changeValidity/"
                                method="post" id="main">
                                <div class="form-row">
                                    <div class="col">
                                        <input class="dateinput form-control" type="text" name="surveyStart"
                                            placeholder="yyyy/mm/dd" value="<?php echo $surveyDef[0]['start']; ?>">
                                    </div>
                                    <div class="col">
                                        <input class="dateinput form-control" type="text" name="surveyEnd"
                                            placeholder="yyyy/mm/dd" value="<?php echo $surveyDef[0]['end']; ?>">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-6">

                            <button name="changeValidityBtn" class="btn btn-danger btn-sm m-1"
                                value="odrzucz zmiane  zmiane" @click="dateEditing = !dateEditing"><i
                                    class="fas fa-window-close"></i>


                                <button name="changeValidityBtn" class="btn btn-success  btn-sm m-1" type="submit"
                                    value="Zaakceptuj zmiane" form="date-change"><i class="fas fa-check"></i>

                        </div>
                    </div>


                    </li>
                    </ul>
                </section>

                <!-- Actywacja / Deaktywacja ankiety z poziomu PHP -->

                <hr>
                <div class="row">
                    <h4>Sekcje ankiety</h4>
                    <button type="submit" title="dodaj nową sekcję" @Click="addSection"
                        class="add-btn  btn-success ml-2">
                        <i class="fas fa-plus"></i></button>
                    <small class="text-secondary">dodaj nową sekcje </small>
                </div>

                <section>
                    <div class="row">
                        <form class="col-10 offset-1 text-left"
                            action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]['id']; ?>/addSection/"
                            method="post" id="addSection">
                            <input type="text" :value="currentSection" name="sectionName" hidden>
                            <input type="text" name="addSectionBtn" hidden>
                        </form>
                    </div>

                    <div v-for="(section, i) in sectionsList">
                        <small>{{i+1}}: {{section}}</small>
                    </div>
                </section>
                <hr>


                <!-- Sekcja pytań  -->
                <section>
                    <div class="row">
                        <h4>Pytania w ankiecie </h4>
                        <button class="add-btn ml-2" data-toggle="modal" title="Dodaj nowe pytanie"
                            data-target="#new-question">
                            <i class="fas fa-plus"></i>
                        </button>
                        <small class="text-secondary">dodaj nowe pytanie </small>
                    </div>

                    <div v-for="(question,i) in questions">
                        <div class="row align-items-center question-block"
                            :class="{'active-block': question.enable == 1,  'inactive-block': question.enable == 0}">
                            <div class="col-10 col-sm-10 p-1 question">
                                <div class="row ml-3">
                                    <p class="mb-1">{{question.text}}</p> ({{question.id}})
                                    <span v-show="question.required == 1" class="fas fa-star-of-life ml-1"></span>
                                    <span v-show="question.commentsRequired == 1" class="comment-required"></span>
                                </div>


                                <div class="questionsectiontype">
                                    Sekcja:<strong> {{question.section}}</strong>, <br>
                                    Typ: <strong>{{questionType[question.type]}} </strong>
                                </div>
                                <span v-if="question.answer != ''"> Pytania: </span>
                                <div style="margin-left: 50px;">
                                    <small v-for="a in question.answer.trim().split('|').slice(0,-1)">
                                        <li>{{a}}</li>
                                    </small>
                                </div>
                                <div v-show="(question.type == 3 || question.type == 4) && question.commentsRequired == 1"
                                    class="questioncomment"><i class="far fa-comment"></i> TAK</div>
                            </div>
                            <div class="col-2 onoffquestion">
                                <label v-if="question.enable == 0" class="switch on">
                                    <input type="checkbox" @click="switchQuestion('enable', question.id,  i)">
                                    <span class="slider round"></span>
                                </label>
                                <label v-if="question.enable == 1" class="switch off">
                                    <input type="checkbox" checked @click="switchQuestion('disable', question.id, i)">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                </section>
                <hr>
                <section>
                    <div class="row">
                        <h2>Lista respondentów</h2>
                    </div>
                    <div class="row">
                        <form enctype="multipart/form-data"
                            action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]["id"]; ?>/addResponders/"
                            method="post">
                            <fieldset>
                                <label>Respondenci: </label>
                                <input name="respondersFile" type="file" accept=".csv" method="POST" />
                            </fieldset>
                            <fieldset>
                                <input type="submit" class="btn btn-info" value="Zapisz!" name="addRespondersBtn" />
                            </fieldset>
                        </form>
                        <p>* Plik powinien zawierać w każdej linii kolejno (oddzielone średnikiem):<br />
                            - numer Pracownika (niewymagane)<br />
                            - Nazwisko i Imię (kolejność obowiązkowa)<br />
                            - Dział Pracownika (niewymagane)<br />
                            - Grupa Pracownika (niewymagane)<br />
                            - Przełożony Pracownika (niewymagane)<br />
                            - Typ Pracownika (indirect/direct, etc., niewymagane)</p>
                    </div>
                </section>

                <!--    -   Po wyborze typu pytania, ma się pokazać/wykonać dodatkowo:
                        -   zamknięte jednokrotnego wyboru: jedno pole input + przycisk "Następna odpowiedź"
                        -   zamknięte wielokrotnego wyboru: j.w.
                        -   z zakresu: dwa pola input od-do
                        -   otwarte: nic - brak dodatkowych inputów
                        -   KAŻDE PYTANIE MUSI MIEĆ CHECK-BOXA, CZY JEST OBOWIĄZKOWE!!!-->

                <div class="modal fade" id="new-question" tabindex="-1" role="dialog"
                    aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">

                                <form class="col-12" id="addQuestionForm"
                                    action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]['id']; ?>/addQuestion/"
                                    method="post">
                                    <h3 class="font-weight-light">{{questionType[newQuestionType - 1]}}</h3>
                                    <div class="form-group">
                                        <input placeholder="Pytanie" type="text" v-model="currentQuestion"
                                            ref="QuestinOnSubmit" @focus="onFocus" class="form-control mr-3 lg">
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Sekcja</label>
                                            <select v-model="selectedSection" class="form-control"
                                                name="questionSection">
                                                <option v-for="s in sectionsList" :value="s"> {{s}}</option>
                                            </select>
                                        </div>

                                        <!-- Dodać komunikację z formularzem i zmienną qiestionType -->
                                        <div class="col">
                                            <label>Rodzaj</label>
                                            <select ref="questionType" class="form-control" v-model="newQuestionType"
                                                id="">
                                                <option v-for="(q, i) in questionType" :value="i + 1">{{q}}</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div v-for="(answ,i) in answers">
                                        <input type="text" :value="answ" :name="'answer' + (i+1)" hidden />
                                    </div>

                                    <input type="text" :value="newQuestionType" name="questionType" hidden>
                                    <input type="text" :value="currentQuestion" name="questionText" hidden>
                                    <input v-if="newQuestionType != 1 || newQuestionType != 2" type="number"
                                        :value="range.min" name="rangeMin" hidden>
                                    <input v-if="newQuestionType != 1 || newQuestionType != 2" type="number"
                                        :value="range.max" name="rangeMax" hidden>
                                    <input type="number" :value="required" name="isRequired" hidden>
                                    <input type="number" :value="comments" name="isCommRequired" hidden>
                                    <input type="text" value="Dodaj!" name="addQuestionBtn" hidden>

                            </div>

                            <!-- Ciało okna popup rodzaję -->
                            <!-- Input - pytania zamknięte -->
                            <div class="d-flex justify-content-center">

                            </div>
                            <div v-show="newQuestionType != 5" class="modal-body">
                                <div v-show="newQuestionType == 1 || newQuestionType == 2">
                                    <section>
                                        <div class="row">
                                            <div class="col-11">
                                                <input ref="currentAnswer" class="form-control-plaintext" type="text"
                                                    v-model="currentAnswer" :name="'answer' + (i+1)"
                                                    :placeholder="'odpowiedz ' + nextQuestion"
                                                    @click="$refs.currentAnswer.classList.remove('error')" />
                                            </div>
                                            <div class="col-1">
                                                <button class="switch float-right add-btn" @click="addAnswer"><i
                                                        class="fas fa-plus"></i></button><br />
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="list-group">
                                            <ol>
                                                <div v-for="(answ, i) in answers" class="list-group">
                                                    <li>
                                                        <a href="#" class="list-group-item list-group-item-action "
                                                            @click="consoleLog(i)">
                                                            {{answ}}
                                                            <button
                                                                class="btn btn-secondary-outline float-right btn-sm questiondelete"
                                                                @click="DelAnswer(i)">
                                                                <i class="fa fa-times"> </i>
                                                            </button>
                                                        </a>
                                                    </li>
                                            </ol>
                                        </div>
                                    </section>
                                </div>

                                <!-- Range - Wskaż z zakresu - całe pytanie -->
                                <div v-show="newQuestionType == 3">
                                    <section>
                                        <div class="row">
                                            <div class="col-6">
                                                <input type="number" id="min" class="form-control"
                                                    v-model.number="range.min" name="rangeMin" placeholder="Minimum"
                                                    value=9>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" id="max" class="form-control"
                                                    v-model.number="range.max" name="rangeMax" placeholder="Maksimum"
                                                    value=21>
                                            </div>
                                        </div>
                                        <br>
                                        <div v-for="(answ, i) in answers">
                                            <a href="#" class="list-group-item list-group-item-action "
                                                @click="consoleLog(i)">MIN: {{answ.minValue}} MAX: {{answ.maxValue}}
                                                <button
                                                    class="btn btn-secondary-outline float-right btn-sm questiondelete"
                                                    @click="DelAnswer(i)"> <i class="fa fa-times"></i> </button></a>
                                        </div>
                                    </section>
                                </div>

                                <div v-show="newQuestionType == 4">
                                    <div class="row">
                                        <div class="col-11">
                                            <input type="text" placeholder="Odpowiedź 1:" class="form-control"
                                                v-model="currentAnswer" name="answerMinMax1" />
                                        </div>
                                        <div class="col-1">
                                            <button class="switch add-btn" @click="addAnswerMinMax"><i
                                                    class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <label>Min:<input type="number" class="form-control m-2" v-model="range.min"
                                                    name="answer1rangeMin" /></label>
                                            <label>Max:<input type="number" class="form-control m-2" v-model="range.max"
                                                    name="answer1rangeMax" /></label>
                                        </div>

                                    </div>
                                    <section>
                                        <div class="row answer-block" v-for="(answ, i) in answers">

                                                <div class="col">
                                                    <span class="answer-title">{{ answ.answerText}}</span>
                                                    <span class="description"> Zakres: </span> <br>
                                                    <div>
                                                        <input type="number" :name="'answer' + (i+1) + 'rangeMin'"
                                                            hidden>
                                                        <span> Od: {{answ.minValue}} </span>
                                                    </div>
                                                    <div>
                                                        <input type="number" :name="'answer' + (i+1) + 'rangeMax'"
                                                            hidden>
                                                        <span> Do: {{answ.maxValue}} </span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <button class="btn btn-secondary float-right btn-sm questiondelete"
                                                        @click="DelAnswer(i)"> <i class="fa fa-times"></i> </button></a>
                                                </div>
                                        </div>
                                    </section>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <div class="col-10">
                                    <div>
                                        <label><strong>Czy obowiązkowe? </strong> <input type="checkbox"
                                                v-model="addOptions.required" name="isRequired"> </label>
                                    </div>
                                    <div v-if='newQuestionType == 3 || newQuestionType == 4'>
                                        <label><strong>Czy wymagany komentarz dla odpowiedzi MIN i MAX?</strong> <input
                                                type="checkbox" v-model="addOptions.comments" name="isCommRequired">
                                        </label>
                                    </div>
                                </div>

                                <div class="col-2">
                                    <button ref="submit" @click="Submit" class="btn  btn-success"> Dodaj
                                </div>
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
const sList = `<?php  
                    if ( count( $surveyDet['sections'] ) > 0 )  { 
                        foreach ( $surveyDet['sections'] as $s ) { 
                            echo $s.","; 
                        } 
                    }
                            else { echo "Brak sekcji"; }
                ?>`
new Vue({
    el: '.wrapper',
    data: {
        messages: {
            mClass: ['success', 'danger', 'info', 'warning'],
            mType: '<?php echo $messageType ?>',
            mText: '<?php echo $messageText ?>',
        },

        Title: '<?php echo $title ?>',
        mDir: '<?php echo $mainDir ?>',
        sId: '<?php echo $surveyDef[0]['id']?>',
        sName: '<?php echo $surveyDef[0]['name']?>',
        sStart: '<?php echo $surveyDef[0]['start']?>',
        sEnd: '<?php echo $surveyDef[0]['end']?>',
        sAuthor: '<?php echo $surveyDef[0]['author']?>',
        sCreated: '<?php echo $surveyDef[0]['created']?>',
        sEnabled: '<?php echo $surveyDef[0]['enabled']?>',
        sSections: ``,
        currentSection: ``,
        activateURL: `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] . '/activate/' ?>`,
        diactivateURL: `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] . '/deactivate/' ?>`,

        // odpowiedż, podanie zakresu 
        range: {
            min: 0,
            max: 10
        },
        addOptions: {
            required: 0,
            comments: 1
        },

        questionType: [
            "Zamknięte jednokrotnego wyboru",
            "Zamknięte wielokrotnego wyboru",
            "Wskaż z zakresu - całe pytanie",
            "Wskaż z zakresu - poszczególne odpowiedzi",
            "Otwarte",
        ],

        selectedSection: `<?php $surveyDet['sections'] ?>`,
        i: '',
        newQuestionType: 1,
        currentQuestion: '',
        currentAnswer: '',
        useSection: [],
        answers: [],

        dateEditing: false,
    },

    computed: {
        required: function() {
            return this.addOptions.required * 1
        },
        comments: function() {
            return this.addOptions.comments * 1
        },


        sectionsList: function() {
            let sections = sList.trim().split(",")
            sections.pop()
            return [...new Set(sections)]
        },

        classObject: function() {
            return {
                active: this.isActive && !this.error,
                'text-danger': this.error && this.error.type === 'fatal'
            }
        },

        nextQuestion: function() {
            return this.answers.length + 1
        },

        //jest w computer properties ponieważ ma zmienną q która nadaje numer poszczególnym objektam w Array
        questions: function() {

            var q = [
                <?php   if ( count( $surveyDet['questions'] ) > 0 ) {   ?>
                <?php foreach ( $surveyDet['questions'] as $q ){ ?> {
                    id: `<?php echo $q['id'];?>`,
                    text: `<?php  echo  $q['text'];    ?>`,
                    type: `<?php  echo  $q['type'];    ?>`,
                    enable: `<?php  echo  $q['enabled'];  ?>`,
                    sequence: `<?php  echo  $q['sequence']; ?>`,
                    section: `<?php  echo  $q['section'];  ?>`,
                    required: `<?php  echo  $q['isRequired'];   ?>`,
                    commentsRequired: `<?php  echo  $q['isCommReq'];    ?>`,
                    OnOff: `<?php  echo ($q['enabled']==1)?("<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/disableQuestion/".$q['id']."/"):("<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/enableQuestion/".$q['id']."/\">Włącz</a>");?>`,
                    answer: `<?php  foreach ($q['answers'] as $a)  echo $a." | " ?>`
                },
                <?php ;} 
                } else { 
                            echo ''; 
                        }
                    ?>
            ]
            return q
        },
        //



        CurrentAnswersEmpty: function() {
            if (this.answers.length > 0) {
                this.$refs.questionType.disabled = 'true'
                console.log(this.$refs.questionType.disabled)
                console.log('1')
            } else {
                this.$refs.questionType.disabled = ''
                console.log(this.$refs.questionType.disabled)
                console.log('2')
            }
        }
    },
    watch: {},

    methods: {
        rand: function() {
            return Math.floor(Math.random() * (100 - 1 + 1)) + 1;
        },

        addSection: function() {
            var section = prompt("Dodaj nową sekcje", '');
            this.currentSection = section

            if (this.currentSection == null) {
                return console.info('msg', 'Nie wybrano sekcji')
            } else {
                setTimeout(() => {
                    document.forms["addSection"].submit()
                }, 50);
            }
        },

        onOffSurvey: function(state) {
            setTimeout(() => {
                window.location.href =
                    `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] ?>` + '/' + state +
                    '/';
            }, 500);
        },

        onOffQuestion: function(id, state) {
            setTimeout(() => {
                window.location.href = "/smp.survey/survey/manage/" + this.sId + "/" + state +
                    "Question/" + id + "/"
            }, 500);
        },

        addAnswer: function() {

            if (this.currentAnswer == '') {
                alert('Nie podałeś odpowiedzi')
                this.$refs.currentAnswer.classList.add("error")
            } else {
                this.answers.push(this.currentAnswer)
                this.currentAnswer = ''
                this.$refs.questionType.disabled = 'true'
            }
        },

        addAnswerMinMax: function() {
            this.$refs.questionType.disabled = 'true'
            if (this.currentAnswer != '') {
                if (this.newQuestionType == 4) {
                    this.answers.push({
                        answerText: this.currentAnswer,
                        minValue: this.range.min,
                        maxValue: this.range.max
                    })
                } else {
                    this.answers.push({
                        minValue: this.range.min,
                        maxValue: this.range.max,
                    })
                }
            } else {
                alert('Nie podałeś odpowiedzi')
            }
        },

        DelAnswer: function(x) {
            this.answers.splice(x, 1)
            if (this.answers.length < 1) {
                this.$refs.questionType.disabled = ''
            }
            console.log(x + ' Deleted')
        },

        consoleLog: function(x) {
            console.log(x + ' Clicked')
        },

        Submit: function() {
            if (this.newQuestionType == 3) {
                this.answer = [{
                    maxValue: this.range.min,
                    minValue: this.range.max
                }]
            }
            if (!this.currentQuestion) {
                alert('Nie podałeś pytania')
                console.log(this.$refs);
                this.$refs.QuestinOnSubmit.style.backgroundColor = '#e6808ad9'
            } else {
                document.forms["addQuestionForm"].submit()
            }
        },

        onFocus: function() {
            this.$refs.QuestinOnSubmit.style.backgroundColor = 'white'
            this.$refs.currentAnswer.style.backgroundColor = 'white'
        },

        switchQuestion: function(state, questionId, question) {
            var url = 'http://srvsmp0025/smp.survey/survey/manage/22/' + state + 'Question/' + questionId +
                '/';
            fetch(url).then((response) => {
                return response.json()
            });
            this.questions[question].enable = !this.questions[question].enable
            console.log(this.questions[question].enable)
            console.log(state, questionId)
        },

        switchSurvey: function(state) {
            var url = `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] ?>` + '/' + state + '/';
            fetch(url).then((response) => {
                return response.json()
            });
            console.log(state)
        },
    },

})
    </script>

    <script>
$('#exampleModalCenter').modal(show)
    </script>

    <script type="text/javascript">
$('.dateinput').datepicker({
    format: 'yyyy-mm-dd'
});
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

    <script>

    </script>
    <?php 
    require('template/footer.html.php'); 
