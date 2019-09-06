    <?php
        $title=$this->getTitle();
        $messageType=$this->get('messageType');
        $messageText=$this->get('messageText');
        $mainDir=$this->get("mainDir");
        $surveyDef=$this->get('surveyDef');
        $surveyDet=$this->get('surveyDet');
        require('template/header.html.php'); 
    ?>

   

    <!DOCTYPE html>
    <html lang="en">
    <head>  <title>Document</title> </head>
        <body>
            <div class="app">
                <progress-bar :progress="progress" 
                              :allquestions="allQuestions">
                </progress-bar>
                <div class="container jumbotron" style="margin-top: 2em;">
                <transition name="slide-fade">
                    <head-component 
                        v-if="!started"
                        :title="title" 
                        :id="surveyDef[0].id"
                        :author="surveyDef[0].author"
                        :description="surveyDef[0].description"
                        :end="surveyDef[0].end"
                        :start="surveyDef[0].start"
                    > 
                    </head-component>
                </transition>
                 <transition name="slide-fade">
                <question-card 
                    v-if="started"
                    :answers="surveyDet[progress].answers"
                    :gonext="goNext"
                    :text="surveyDet[progress].text"
                    :id="surveyDet[progress].id"
                    :commreq="surveyDet[progress].isCommReq"
                    :required="surveyDet[progress].isRequired"
                    :section="surveyDet[progress].section"
                    :type="surveyDet[progress].type"
                    @next-question="nextQuestion"
                    > 
                </question-card>
                <button name="start"
                        v-if="!started" 
                        ref="nextButton"
                        @click="startSurvey"
                        class="btn btn-lg btn-success"
                        style="position:absolute; position: absolute; right: 9%;"
                        :style=""
                        >Rozpocznij!
                </button>
                 </transition>
                 </div>
            </div>
        </body>
    </html>


    <script>
        const HeadComponent = Vue.component('head-component', {
            props: [ 'id', 'title', 'author', 'description', 'end', 'start' ],
            template: ` <div class="main-card">
                           <h1 style="text-align: center;">{{this.$props.title}}</h1>
                            <h4 style="color: #5d5151; padding: 2em;">
                                {{this.$props.description}}
                            </h4> 
                        <hr>
                            <p>
                                strworzona przez: <strong> {{this.$props.author}} </strong><br>
                                od: <strong> {{this.$props.start}} </strong> <br>
                                do: <strong> {{this.$props.end}} </strong>
                            <p/>
                        </div>` 
            });

        const QuestionCard  = Vue.component('question-card' , {
            props: ['id', 'commreq', 'required', 'section', 'text', 'type', 'answers', 'gonext'],
            template: ` <div class="question-card"> 
                            {{this.$props}}
                            <button 
                                name="next"
                                ref="nextButton"
                                @click="nextQuestion"
                                class="btn btn-lg btn-success"
                                :class="{disabled: disabled == true}"
                                style="position:absolute; position: absolute; right: 9%;"
                                > Prejdź dalej!
                            </button>
                        </div>`,

            methods: {
                nextQuestion: function(){
                    return this.$emit('next-question')
                },
            },
            computed: {
                disabled:  function(){
                    return  !this.$props.gonext
                }
            }
           
        })
        const ProgressBar = Vue.component('progress-bar', {
            props: ['progress', 'allquestions'],
            template: `<div>  {{ this.$props.progress + 1 }} / {{this.$props.allquestions}} </div> `
        })

        new Vue({
            el: ".app",
            components: {
                HeadComponent: HeadComponent,
                QuestionCard: QuestionCard
            },
            data: {
                title: `<?php echo $title ?>`,
                messageType: `<?php echo $messageType ?>`,
                messageText: `<?php echo $messageText ?>`,
                mainDir: `<?php echo $mainDir ?>`,
                surveyDet: <?php echo json_encode($surveyDet, JSON_PRETTY_PRINT)?>,
                surveyDef: <?php echo json_encode($surveyDef, JSON_PRETTY_PRINT)?>,
                progress: 0,
                started: false,
            },
            methods: {
                  nextQuestion: function(){
                    console.log(this.progress)
                    if(  this.progress >= this.surveyDet.length ) {
                        return console.log('To jest ostanie pytanie') 
                    } else {
                        return  this.progress = this.progress + 1;
                    }
                  },

                  startSurvey: function(){
                    console.log('Survey is started')
                    return this.started = true
                  }
            },
            computed: {

                allQuestions :  function(){
                    return this.surveyDet.length
                }, 

                goNext: function(){
                    let progress  = this.progress
                    let all = this.allQuestions - 1
                    if (progress == all) { 
                       return false
                    } else { return true} 
                }
          
            }
        })
    </script>

    <style>
       .slide-fade-enter-active {
        transition: all .3s ease;
        }
        .slide-fade-leave-active {
        transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
        }
        .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */ {
        transform: translateX(10px);
        opacity: 0;
        }
    </style>



    <?php 
    require('template/footer.html.php'); 