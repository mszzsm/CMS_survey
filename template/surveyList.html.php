<?php
    $messageType=$this->get('messageType');
    $messageText=$this->get('messageText');
    $title=$this->getTitle();
    $listOfSurveys=$this->get('listOfSurveys');
    $mainDir=$this->get("mainDir");
    require('template/header.html.php');
?>

<?php //var_dump($listOfSurveys); 

    if($messageType<>null){
        echo $messageText."<br /><br />";
    }

?>

<div id="app" v-cloak class="container">
    <div class="row">
        <div  class="card-deck col-4 ml-2"  v-for="survey in sList" >
            <div class="card col-sm-10  offset-sm-1 bg-survey my-3 border-sanden shadow card_1">
                <div class="title-bg p-1"><strong>{{survey.name}}<strong></div>
                <div class="card-title p-1"><strong>{{survey.name}}<strong></div>
                <img class="grayimg" :src="'https://picsum.photos/id/'+ rand() + '/500/300'" alt="Card image">
                <div class="date border-left-sanden mx-3 mb-4 "><strong>OD:</strong> {{survey.start}}<br><strong>DO:</strong> {{survey.end}}</div>
                <div class="author px-3 py-1"><strong>{{survey.author}}</strong></div>
                <div class="manage"><a class="text-white" :href="mDir + 'survey/manage/' + survey.id + '/' "><i class="fas fa-cog"></i></a></div>
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
                'IN':   'info'
        },
        mText:  '<?php echo $messageText ?>',
        Title:  '<?php echo $title ?>',

        sList:  {
                    <?php foreach ($listOfSurveys as $key => $item) { ?>
                    <?php echo 'Ankieta_'. $key . ' : { 
                        id:     "' .$item['id'].     '", 
                        name:   "' .$item['name'] .  '",
                        start:  "' .$item['start'].  '",
                        end:    "' .$item['end'].    '",
                        author: "' .$item['author']. '",
                    },' ?>
                    <?php } ?> 
                },


        mDir:   '<?php echo $mainDir ?>',
       
       
    },
    computed: {
        
    },
    methods: {
        msgType: function(mType) {
           if (mType == 'OK') {
            return this.msgType == 'success'
            }
        },

         rand: function(){
            return Math.floor(Math.random() * (10 - 1)) + 1;
        }
      
        }
    })
</script>


<?php 
    include('template/footer.html.php');