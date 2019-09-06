<?php
    $messageType=$this->get('messageType');
    $messageText=$this->get('messageText');
    $title=$this->getTitle();
    $listOfSurveys=$this->get('listOfSurveys');
    $mainDir=$this->get("mainDir");
    $mode=$this->get("mode");

    require('template/header.html.php');
?>

<div id="app" v-cloak class="container">
    <div v-if="mType == 'OK'"       class="alert alert-success"> {{ mText }} </div>
        <div v-else-if="mType == 'ER'"  class="alert alert-danger">  {{ mText }} </div>

    <h1 class="tytul"> {{Title}}</h1>
    <div class="row">
        <div  class="card-deck col-4 ml-2"  v-for="(survey, i) in sortedSlist"  >
            <div class="card col-sm-10  offset-sm-1 bg-survey my-3 border-sanden shadow card_1">
                <div    class="card-title p-1" 
                        :class="{SandenCard : survey.enabled == '1'}"> <strong>{{ survey.name }}<strong></div>
                    <img class="grayimg"
                        :src="'https://picsum.photos/id/'+ rand() + '/300/200'" alt="Card image"
                        :class="{imgInactive : survey.enabled == '0'}">
                    <div class="date border-left-sanden mx-3 mb-4 ">
                        <strong>OD:</strong> {{ survey.start }}<br>
                        <strong>DO:</strong> {{ survey.end }}
                    </div>
                        <button v-if="survey.enabled == '1'"
                            class="btn btn-outline-success"
                            style=" cursor: pointer;
                                    position: absolute;
                                    right: 10px;
                                    margin-top: 15px;
                                    font-size: 12px;"
                            @click="startSurvey(survey.id)"> 
                            Rozpocznij 
                        </button>
                    <div class="author px-3 py-1"><strong>{{ survey.author }}</strong></div>
                    <div v-if="access == 1" class="manage">
                        <a class="text-white" :href="mDir + 'survey/manage/' + survey.id + '/' ">
                            <i  class="fas fa-cog" 
                                :class="['opt', ( survey.enabled == '0' ) ?  'deactive' : '']" 
                                :id="'options' + survey.id" ></i>
                        </a>
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
   new Vue({
    el: '#app',
    data: {
        mType:  '<?php echo $messageType ?>',
        msgType: {
                'OK':   'success',
                'ER':   'danger',
                'IN':   'info'},

        mText:  '<?php echo $messageText ?>',
        Title:  '<?php echo $title ?>',
        sList:  [   
                    <?php foreach ($listOfSurveys as $key => $item) { ?>
                        <?php echo ' { 
                            id:     "'  .$item['id'].       '", 
                            name:   "'  .$item['name'] .    '",
                            start:  "'  .$item['start'].    '",
                            end:    "'  .$item['end'].      '",
                            author: "'  .$item['author'].   '",
                            enabled: "' .$item['enabled'].  '",
                        },' ?>
                    <?php } ?> 
        ],
        mDir:   '<?php echo $mainDir ?>',
        currentMode: ["enable", "disable"],
        mode: '<?php echo $mode ?>'

    },

    methods: {

        msgType: function(mType) {
           if (mType == 'OK') {
            return this.msgType == 'success'
            }
        },

        startSurvey: function(id){
          setTimeout(() => {
                window.location.href = "/smp.survey/survey/fill/" + id + "/"
            }, 100);
        },

         rand: function(){
            return Math.floor(Math.random() * (10 - 1)) + 1;
        }
    },

    computed: {
        access: function(){
          switch (this.mode) {
                case 'manage':
                    return 1
                    break;
                case 'fill':
                    return 2
                default:
                    break;
            }
        },
       
        sortedSlist: function(){
            return this.sList.sort((a,b) => (a.enabled < b.enabled) ? 1 : ((b.enabled < a.enabled) ? -1 : 0));
        },
        enable: function(){       
             return this.sList.filter(f => f.enabled == 1) 
        },

        disable: function(){       
            return this.sList.filter(f => f.enabled == 0) 
        }
    }

   
    })
</script>


<?php 
    include('template/footer.html.php');