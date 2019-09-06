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

    <div class="wrapper">
        <div id="content" v-cloak class="grid-container">
                <div v-if="messages.mType !== ''">
                    <msg-alert  :type="messages.mType"  :text="messages.mText" > </msg-alert>
                </div>
                <div class="header">
                    <h3>{{Title}}</h3>
                    <hr>

                    <main-information   
                                    :author="sAuthor" 
                                    :created="sCreated"
                                    :editingdate="dateEditing"
                                    :start="sStart"
                                    :end="sEnd"
                                    @edit-date="editDate"> 
                    </main-information>

                    <toggle-survey :enable="sEnabled"> </toggle-survey>

                    <edit-period    
                                v-if="dateEditing == true" 
                                :enable="dateEditing" 
                                :url="addQuestionURL"
                                :begin="sStart"
                                :end="sEnd"
                                :action="addQuestionURL"
                                @edit-date="editDate"
                                @edit-begin="editBegin"
                                @edit-end="editEnd">
                    </edit-period> 
                </div>

                <div class="section">
                    

                    <form 
                        action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]['id']; ?>/addSection/"
                        method="post" id="addSection">
                        <h4>Sekcje ankiety</h4>
                        <input type="text" :value="currentSection" name="sectionName" hidden>
                        <input type="text" name="addSectionBtn" hidden>
                       
                    </form>

                    <div v-for="(section, i) in sectionsList">
                        <div class="sectionList"> {{i+1}}: <span >{{section}}</span> </div>
                    </div>

                    <button type="submit" 
                            title="dodaj nową sekcję" 
                            data-toggle="dropdown"
                            data-target="dataTargetForm" 
                            aria-haspopup="true" 
                            class="add-btn btn-success">
                            <i class="fas fa-plus"></i>
                    </button>

                    <div class="dropdown-menu p-3" id="dataTargetForm">
                        <div class="form-group">
                            <label  for="addNewSection">Dodaj nową sekcje</label><br>
                        </div>
                        <div class="form-group">
                            <textarea  ref="newSectionName" type="textarea" id="addNewSection" rows="4" cols="20"> </textarea> <br><hr>
                        </div>
                        <div class="form-group">
                            <input  type="button" value="Dodaj" name="addNewSection" class="btn btn-success"  @Click="addSection"> 
                        </div>
                    </div>

                    <small class="text-secondary"> </small>
                </div>



                <div class="listOfUser">
                    <h4>Lista respondentów</h4>
                    <hr>
                
                    <form enctype="multipart/form-data"
                            action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]["id"]; ?>/addResponders/"
                            method="post">
                        <fieldset>
                            <label>Respondenci: </label>
                            <input name="respondersFile" type="file" accept=".csv" method="POST" />
                        </fieldset>
                        <hr>
                        <fieldset>
                            <input type="submit" class="btn btn-info" value="Zapisz!" name="addRespondersBtn" />
                        </fieldset>
                    </form>
                    <hr>
                    <p style="font-size: 10px">* Plik powinien zawierać w każdej linii kolejno (oddzielone średnikiem):<br />
                        - numer Pracownika (niewymagane)<br />
                        - Nazwisko i Imię (kolejność obowiązkowa)<br />
                        - Dział Pracownika (niewymagane)<br />
                        - Grupa Pracownika (niewymagane)<br />
                        - Przełożony Pracownika (niewymagane)<br />
                        - Typ Pracownika (indirect/direct, etc., niewymagane)
                    </p>
                </div>
                 
                <!-- Sekcja pytań  -->
                <div class="question" id="questions">
                    <h4>Pytania w ankiecie</h4>
                    <div class="row answersSectionTitle">
                        <button class="add-btn ml-2" 
                                data-toggle="modal" 
                                title="Dodaj nowe pytanie"
                                data-target="#new-question">
                            <i class="fas fa-plus"></i>
                        </button>
                        <small class="text-secondary">dodaj nowe pytanie </small>
                        <span  @click="activeQuestions = !activeQuestions" 
                                class="show-question"
                                @hover="console.log('hovered')">  
                                Pokaz 
                                    <span v-if="activeQuestions"> wyłączonę </span> 
                                    <span v-else="activeQuestions"> aktywne </span> 
                                pytania 
                        </span>
                    </div>

                    <div v-for="(question,i) in questions">
                        <div  class="align-items-center question-block" 
                                :class="{'active-block': question.enable == 1,  'inactive-block': question.enable == 0}"
                                :key="question.id">
                                <div class="row ml-3">
                                    <p class="mb-1">{{question.text}} (<i><small style="color: grey;"> {{questionType[question.type - 1]}}  </i> | Sekcja: <i> {{question.section}} </i></small>)</p>
                                    <small><span v-if="question.required == 1" class="fas fa-star-of-life ml-1" ></span></small>
                                    <small><span v-if="question.commentsRequired == 1" class="comment-required"></span></small>
                                </div>

                                <div   v-if="question.answer != ''"> <hr> </div> 
                        
                                <span v-for="(a, index) in question.answer.trim().split('|').slice(0,-1)">
                                    <small>{{index + 1}}:</small>{{ a }}<br>
                                </span>

                                <div v-show="(question.type == 3 || question.type == 4) && question.commentsRequired == 1"
                                     class="questioncomment">
                                     <i class="far fa-comment"></i> TAK
                                </div>
                            <div class="onoffquestion">
                                <label v-if="question.enable == 0" class="switch on">
                                    <input type="checkbox" @click="switchQuestion('enable', question.id, i)">
                                    <span class="slider round"></span>
                                </label>
                                <label v-if="question.enable == 1" class="switch off">
                                    <input type="checkbox" checked @click="switchQuestion('disable', question.id, i)">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                <!--                                    -->
                <!--    Add new question modal window   -->
                <!--                                    -->

                <div    class="modal fade"
                        id="new-question"
                        tabindex="-1"
                        role="dialog"
                        aria-labelledby="myLargeModalLabel"
                        aria-hidden="true">

                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <form   class="col-12" id="addQuestionForm"
                                        action="<?php echo $mainDir; ?>survey/manage/<?php echo $surveyDef[0]['id']; ?>/addQuestion/"
                                        method="post">
                                    <h3 class="font-weight-light">{{questionType[selectedType]}}</h3>

                                    <div class="form-group">
                                        <input  placeholder="Pytanie" 
                                                type="text"
                                                v-model="newQuestion.questionTitle" 
                                                ref="QuestinOnSubmit" 
                                                @focus="onFocus" 
                                                class="form-control mr-3 lg">
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Sekcja</label>
                                            <select     
                                                    v-model="newQuestion.selectedSection"
                                                    class="form-control"  
                                                    name="questionSection">
                                                <option     
                                                        v-for="s in sectionsList" 
                                                        :value="s"> {{s}} 
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Dodać komunikację z formularzem i zmienną qiestionType -->
                                        <div class="col">
                                            <label>Rodzaj</label>
                                            <select ref="questionType" 
                                                    class="form-control" 
                                                    v-model="newQuestion.selectedType">
                                                <option v-for="(q, i) in questionType" 
                                                        :value="i + 1"> {{q}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div v-for="(answ,i) in newQuestion.answers">
                                        <div  v-if="newQuestion.selectedType == 4"> 
                                            <input type="text"      :value="answ.title"     :name="'answerMinMax' + (i+1)" hidden />
                                            <input type="text"      :value="answ.minValue"  :name="'answer' + (i+1) + 'RangeMin'" hidden />
                                            <input type="text"      :value="answ.maxValue"  :name="'answer' + (i+1) + 'RangeMax'" hidden />
                                        </div>
                                        <div  v-else-if="newQuestion.selectedType == 1 || newQuestion.selectedType == 2"> 
                                            <input type="text" :value="answ.title" :name="'answer' + (i+1)" hidden />
                                        </div>
                                    </div>


                                        <!--                                    -->
                                        <!--   inputy wysyłki danych            -->
                                        <!--                                    -->
                                    <input v-if=" newQuestion.selectedType == 4 || 
                                                    newQuestion.selectedType == 3"
                                            type="number"    
                                            :value="comments"       
                                            name="isCommRequired" hidden>

                                    <div v-if=" newQuestion.selectedType == 3">
                                        <input  type="number" 
                                                :value="newQuestion.range.min" 
                                                name="rangeMin" 
                                                hidden>

                                        <input  type="number" 
                                                :value="newQuestion.range.max" 
                                                name="rangeMax" 
                                                hidden>
                                    </div>

                                    <input type="text"      :value="newQuestion.selectedType"   name="questionType" hidden>
                                    <input type="text"      :value="newQuestion.questionTitle"  name="questionText" hidden>
                                    <input type="text"      value="Dodaj!"                      name="addQuestionBtn" hidden>
                                    <input type="number"    :value="required"                   name="isRequired" hidden>
                                   
                                    



                                 </form>
                                </div>

                                <!-- Input - pytania zamknięte -->
                                <div    v-if="newQuestion.selectedType != 5" class="modal-body">
                                    <div class="row" v-if="newQuestion.selectedType == 1 || 
                                                    newQuestion.selectedType == 2 ||
                                                    newQuestion.selectedType == 4">
                                        <div class="col-11">
                                            <input 
                                                ref="currentAnswer" 
                                                class="form-control-plaintext" 
                                                style=" border: solid; border-width: 0px 0px 1px 0px;"
                                                type="text"
                                                v-model="newQuestion.currentAnswer" :name="'answer' + (i+1)"
                                                :placeholder="'odpowiedz ' + nextQuestion"
                                                @click="$refs.currentAnswer.classList.remove('error')" 
                                                @keyup.enter="addAnswer"/>
                                        </div>
                                        <div v-if="newQuestion.selectedType == '4'" class="col-1" >
                                            <button     
                                                class="switch float-right add-btn" 
                                                @click="addAnswerMinMax">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div v-else>
                                            <button    
                                                class="switch float-right add-btn" 
                                                @click="addAnswer">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div> 

                                    <div class="row" v-show="newQuestion.selectedType == 3">
                                        <div class="col-6">
                                            <input  type="number" 
                                                    id="min" 
                                                    class="form-control"
                                                    v-model.number="newQuestion.range.min" 
                                                    name="rangeMin" 
                                                    placeholder="Minimum"
                                                    value=9>
                                        </div>
                                        <div class="col-6">
                                            <input  type="number" 
                                                    id="max" 
                                                    class="form-control"
                                                    v-model.number="newQuestion.range.max" 
                                                    name="rangeMax" 
                                                    placeholder="Maksimum"
                                                    value=21>
                                        </div>
                                    </div>
                                    <div class="row" v-show="newQuestion.selectedType == 4">
                                        <div class="col-6">
                                            <label>Min:
                                                <input type="number" 
                                                    class="form-control" 
                                                    v-model="newQuestion.range.min"
                                                    name="answer1rangeMin" />
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <label>Max:
                                                <input type="number" 
                                                    class="form-control" 
                                                    v-model="newQuestion.range.max"
                                                    name="answer1rangeMax" />
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Added answers  -->
                                    <div class="list-group">
                                        <div v-for="(answ, i) in newQuestion.answers" class="list-group">
                                            <a href="#" class="list-group-item list-group-item-action " @click="consoleLog(i)">
                                                <span v-if="newQuestion.selectedType == '4'"> 
                                                    <small>{{i+1}}</small> : <strong>{{answ.title}}</strong> | oceń od {{answ.minValue}} do {{answ.maxValue}} </span>
                                                <span v-else> <small>{{i + 1}}</small>: <strong>{{answ.title}}</strong> </span>
                                                <button
                                                    class="btn btn-secondary-outline float-right btn-sm questiondelete"
                                                    @click="DelAnswer(i)">
                                                    <i class="fa fa-times"> </i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Range - Wskaż z zakresu - całe pytanie -->
                              
                                <div class="modal-footer">
                                    <div class="col-10">
                                        <label><strong>Czy obowiązkowe? </strong> 
                                        <input type="checkbox" v-model="newQuestion.required" name="isRequired"> </label>
                                        <div v-if='newQuestion.selectedType == 3 || newQuestion.selectedType == 4'>

                                            <label><strong>Czy wymagany komentarz dla odpowiedzi MIN i MAX?</strong> 
                                                <input type="checkbox" v-model="newQuestion.comments" name="isCommRequired">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button ref="submit" @click="Submit" class="btn  btn-success"> Dodaj
                                    </div>
                                </div>
                              </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
    <script>
   
//Components
    const sList = `<?php if ( count( $surveyDet['sections'] ) > 0 )  {  foreach ( $surveyDet['sections'] as $s ) {  echo $s.","; } } else { echo "Brak sekcji"; }?>`;
    const EditPeriod = Vue.component('edit-period',{
            props: ['enable', 'begin', 'end', 'action'],
            methods: {
                changeBegin(item){
                    return  this.$emit('edit-begin', event.target.value)
                },

                changeEnd(item){
                    return  this.$emit('edit-end', event.target.value)
                },

                sendChanges(){
                    return this.$refs.dateEditForm.submit()
                }

                
            },
            data () {
                return {
                    picker: new Date().toISOString().substr(0, 10),
                    period: {begin: '', end: ''}
                }
            },
            template: `   <div class="EditPeriod">
                            <form id="date-change" :action="this.$props.action" v-on:submit.prevent="sendChanges" ref="dateEditForm"  method="post">
                                <div class="form-row">
                                    <div class="col-4">
                                        <input  class="dateinput form-control"  
                                            type="date" 
                                            name="surveyStart"
                                            placeholder="yyyy/mm/dd" 
                                            @change="changeBegin()"
                                            :value="this.$props.begin">
                                    </div>

                                    <div class="col-4">
                                        <input  class="dateinput form-control" 
                                            type="date" 
                                            name="surveyEnd"
                                            placeholder="yyyy/mm/dd" 
                                            @change="changeEnd()"
                                            :value="this.$props.end">
                                    </div>
                                    <input type="text" name="changeValidityBtn" value="Zmień!" hidden>
                                </div>
                            </form>

                         
                                <button name="changeValidityBtn" 
                                        class="btn btn-danger btn-sm m-1"
                                        value="odrzucz zmiane" 
                                        @click="$emit('edit-date', this)"><i 
                                        class="fas fa-window-close"></i>
                                </button>
                                <button name="changeValidityBtn" 
                                        class="btn btn-success  btn-sm m-1" 
                                        type="submit"
                                        value="Zaakceptuj zmiane"
                                        form="date-change"><i w
                                        class="fas fa-check"></i>
                                </button>
                     
                        </div>
                    `
    });
    const ShowSections= null;
    const ToggleSurvey = {
        props: ['enable'],
        methods: {
            //W przy kliknienciu dana funkcja wykonuję sie 2 razy a powinno 1 raz
            switchSurvey(state) {
                 var url = `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] ?>` + '/' + state + '/';
                    fetch(url).then((response) => {
                        return response.json()
                    });
                    console.log(state)
            }   
        },
        template: ` <div v-if="this.$props.enable == 1" id="selector" @click="switchSurvey('deactivate')">
                        <label class="switch float-right">
                            <input type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div v-else id="selector" @click="switchSurvey('activate')">
                        <label class="switch float-right">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>`
        }


    const MsgAlert = {  props: [ 'type', 'text'],
                        template: ` <div v-if="this.$props.type == 'OK'"        class="alert alert-success"> {{ this.$props.text }} </div>
                                    <div v-else-if="this.$props.type == 'ER'"   class="alert alert-danger"> {{ this.$props.text }} </div>` }
    
    
    
    const MainInformation = Vue.component('main-information', {
            props: [ 'author', 'created', 'editingdate', 'start', 'end' ],
            template: ` <div>
                            <span>Autor:</span> <strong>{{this.$props.author}}</strong><br>
                            <span>Stworzono: </span> <strong>{{this.$props.created}}</strong><br>
                            <div v-show="editingdate == false">
                                <span>Okres obowiązywania:</span>
                                <span> od <strong> {{this.$props.start}} </strong> od <strong> {{this.$props.end}} </strong> </span>
                                <span v-show="editingdate == false" class="far fa-edit"
                                        @click="$emit('edit-date', this)" 
                                        style="cursor: pointer; color: green; font-size: 25px;" 
                                        data-toggle="tooltip" 
                                        data-placement="right" 
                                        title="Zmień Datę">
                                </span>
                            </div>
                        </div>
                    ` 
            })






//Vue instance
new Vue({
    el: '.wrapper',
    components: {
        MsgAlert: MsgAlert,
        MainInformation: MainInformation,
        ToggleSurvey: ToggleSurvey,
        EditPeriod: EditPeriod,
    },

    data: {
        show: true,
        messages: {
            mClass: ['success', 'danger', 'info', 'warning'],
            mType: '<?php echo $messageType ?>',
            mText: '<?php echo $messageText ?>',
        },

        Title:          '<?php echo $title ?>',
        mDir:           '<?php echo $mainDir ?>',
        sId:            '<?php echo $surveyDef[0]['id']?>',
        sName:          '<?php echo $surveyDef[0]['name']?>',
        sStart:         '<?php echo $surveyDef[0]['start']?>',
        sEnd:           '<?php echo $surveyDef[0]['end']?>',
        sAuthor:        '<?php echo $surveyDef[0]['author']?>',
        sCreated:       '<?php echo $surveyDef[0]['created']?>',
        sEnabled:       '<?php echo $surveyDef[0]['enabled']?>',
        sSections:      ``,
        currentSection: ``,
        activateURL:    `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] . '/activate/' ?>`,
        diactivateURL:  `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] . '/deactivate/' ?>`,

        // odpowiedż, podanie zakresu 
        range: {
            min: 0,
            max: 10 },


        questionType: [
            "Zamknięte jednokrotnego wyboru",
            "Zamknięte wielokrotnego wyboru",
            "Wskaż z zakresu - całe pytanie",
            "Wskaż z zakresu - poszczególne odpowiedzi",
            "Otwarte", ],

        newQuestion: {
            'selectedSection':  ``,
            'selectedType' : '',
            'questionTitle' : '',
            'currentAnswer' : '',
            'answers' : [], 
            'range' : {
                'min' : 1,
                'max' : 10
            },
            'required' : 0,
            'comments' : 1
        },

        allQuestions:  <?php echo json_encode($surveyDet, JSON_PRETTY_PRINT)?>,

        i: '',

        dateEditing: false,
        activeQuestions: true,
    },

    computed: {
        selectedType: function() {return this.newQuestion.selectedType - 1},
        required: function() { return this.newQuestion.required * 1 },
        comments: function() { return this.newQuestion.comments * 1 },

        addQuestionURL: function() { return `<?php echo $mainDir; ?>` + `survey/manage/` + `<?php echo $surveyDef[0]['id']; ?>` + '/changeValidity/' },

        sectionsList: 
                function() { let sections = sList.trim().split(",") 
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
            return this.newQuestion.answers.length + 1
        },

        //jest w computer properties ponieważ ma zmienną q która nadaje numer poszczególnym objektam w Array
        questions: function() {
            var q = [
                <?php   if ( count( $surveyDet['questions'] ) > 0 ) {   ?>
                <?php foreach ( $surveyDet['questions'] as $q ){ ?> {
                    id:         `<?php echo $q['id'];                   ?>`,
                    text:       `<?php  echo  $q['text'];               ?>`,
                    type:       `<?php  echo  $q['type'];               ?>`,
                    enable:     this.allQuestions.questions.find(x => x.id == `<?php echo $q['id'];?>`).enabled,
                    sequence:   `<?php  echo  $q['sequence'];           ?>`,
                    section:    `<?php  echo  $q['section'];            ?>`,
                    required:   `<?php  echo  $q['isRequired'];         ?>`,
                    commentsRequired: `<?php  echo  $q['isCommReq'];    ?>`,
                    OnOff:      `<?php  echo ($q['enabled']==1)?("<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/disableQuestion/".$q['id']."/"):("<a href=\"".$mainDir."survey/manage/".$surveyDef[0]['id']."/enableQuestion/".$q['id']."/\">Włącz</a>");?>`,
                    answer:     `<?php  foreach ($q['answers'] as $a)  echo $a." | " ?>`,
                },

                <?php ;} 
                    } else { 
                        echo ''; 
                    }
                    ?>
            ]
            return q
        },

        CurrentAnswersEmpty: function() {
            if (this.newQuestion.answers.length > 0) {
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
        
        editBegin: function(e){
            return this.sStart = e
        },

        editEnd: function(e){
            return this.sEnd = e
        },

        editDate: function() { 
            console.log(this)
            return  this.dateEditing = !this.dateEditing 
        },

        toggleEdit: function(item){
             return this.dateEditing = !this.dateEditing
        },

        rand: function() {
            return Math.floor(Math.random() * (100 - 1 + 1)) + 1;
        },

        addSection: function() {
            this.currentSection = this.$refs.newSectionName.value;

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
            console.log('addAnswer')
            if (this.newQuestion.currentAnswer == '') {
                alert('Nie podałeś odpowiedzi')
                this.$refs.currentAnswer.classList.add("error")
                this.$refs.currentAnswer.focus()
            } else {
                this.newQuestion.answers.push({title: this.newQuestion.currentAnswer})
                this.newQuestion.currentAnswer = ''
                this.$refs.questionType.disabled = 'true'
                this.$refs.currentAnswer.focus()
            }
        },

        addAnswerMinMax: function() {
            console.log('addAnswerMinMax')
            if (this.newQuestion.currentAnswer != '') {
                if (this.newQuestion.selectedType == 4) {
                    this.newQuestion.answers.push({
                        title: this.newQuestion.currentAnswer,
                        minValue: this.newQuestion.range.min * 1,
                        maxValue: this.newQuestion.range.max * 1,
                    })
                        this.$refs.questionType.disabled = 'true',
                        this.newQuestion.currentAnswer = '',
                        this.$refs.currentAnswer.focus()
                } else {
                    this.$refs.questionType.disabled = 'true'
                    this.newQuestion.answers.push({
                        minValue: this.newQuestion.range.min * 1,
                        maxValue: this.newQuestion.range.max * 1,
                    })
                }
            } else {
                    this.$refs.currentAnswer.style.backgroundColor = '#e6808ad9'
                setTimeout(() => {
                    this.$refs.QuestinOnSubmit.style.backgroundColor = '#e6898ad9'  
                }, 1000);
            }
        },

        DelAnswer: function(x) {
            this.newQuestion.answers.splice(x, 1)
            if (this.newQuestion.answers.length < 1) {
                this.$refs.questionType.disabled = ''
            }
            console.log(x + ' Deleted')
        },

        consoleLog: function(x) { console.log(x + ' Clicked') },

        Submit: function() {
            if (this.newQuestion.selectedType == 3) {
                this.answer = [{
                    maxValue: this.newQuestion.range.min,
                    minValue: this.range.max
                }]
            }
            if (!this.newQuestion.questionTitle) {
                alert('Nie podałeś pytania')
                console.log(this.$refs);
                this.$refs.QuestinOnSubmit.style.backgroundColor = '#e6808ad9'
            } else {
                document.forms["addQuestionForm"].submit()
            }
        },

        onFocus: function() {
            this.$refs.QuestinOnSubmit.style.backgroundColor = 'white'
        },

        switchQuestion: function(state, questionId, question) {
            //http://srvsmp0025/smp.survey/survey/enableQuestion/35/9/ 
            //http://srvsmp0025/smp.survey/survey/enableQuestion/22/224/

            var url = `<?php echo $mainDir. 'survey/'  ?>` + state + 'Question/' + `<?php echo $surveyDef[0]["id"] ?>`+ '/' +  questionId + '/';
                fetch(url)
                    .then((resp) => { return resp.json() })
                    .then((x) => { this.allQuestions.questions.find(x => x.id == questionId).enabled = x});
            },

        switchSurvey: function(state) {
            var url = `<?php echo $mainDir. 'survey/manage/' .$surveyDef[0]["id"] ?>` + '/' + state + '/';
            fetch(url)
                .then((response) => { return response.json() });
            
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

            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        </script>
    <?php 
    require('template/footer.html.php'); 