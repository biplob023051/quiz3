<?php if (!empty($this->request->data['Student']['id'])) : ?>
    <script type="text/javascript">
        var student_id = '<?php echo $this->request->data['Student']['id']; ?>';
    </script>
<?php else : ?>
    <script type="text/javascript">
        var student_id = '';
    </script>
<?php endif; ?>
<?php
    $this->Html->script(array(
        'webquiz',
        'live',
            ), array('inline' => false)
    );

    $this->assign('title', __('Quiz: ') . $data['Quiz']['name']);

    echo $this->Session->flash('error'); 

    echo $this->Form->create('Student', array(
        'inputDefaults' => array(
            'label' => array('class' => 'sr-only'),
            'div' => array('class' => 'form-group'),
            'class' => 'form-control input-lg basic-info',
        ),
        'novalidate' => true,
        'url' => array('controller' => 'student', 'action' => 'submit', $data['Quiz']['random_id']),
        'name' => 'student_form'
    ));

?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <?php if (empty($data['Quiz']['anonymous'])) : ?>
            <?php // pr($this->request->data); ?>
            <div class="alert alert-danger" id="error-message" style="display: none;"></div>
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <?php
                    echo $this->Form->input('fname', array(
                        'placeholder' => __('First Name')
                    ));
                    ?>
                    <?php if (!empty($this->request->data['Student']['fname'])) : ?>
                        <span id="std-fname" class="glyphicon glyphicon-ok-sign text-success std-basic-info"></span>
                    <?php else : ?>
                        <span id="std-fname" class="glyphicon std-basic-info"></span>
                    <?php endif; ?>
                </div>
                <div class="col-xs-12 col-md-4">
                    <?php
                    echo $this->Form->input('lname', array(
                        'placeholder' => __('Last Name')
                    ));
                    ?>
                    <?php if (!empty($this->request->data['Student']['lname'])) : ?>
                        <span id="std-lname" class="glyphicon glyphicon-ok-sign text-success std-basic-info"></span>
                    <?php else : ?>
                        <span id="std-lname" class="glyphicon std-basic-info"></span>
                    <?php endif; ?>
                </div>
                <div class="col-xs-12 col-md-4">
                    <?php
                    echo $this->Form->input('class', array(
                        'placeholder' => __('Class')
                    ));
                    ?>
                    <?php if (!empty($this->request->data['Student']['class'])) : ?>
                        <span id="std-class" class="glyphicon glyphicon-ok-sign text-success std-basic-info"></span>
                    <?php else : ?>
                        <span id="std-class" class="glyphicon std-basic-info"></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <p><?php echo $data['Quiz']['description']; ?></p>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="ajax-loader col-xs-12 col-md-12" style="color: red; font-weight: bold"><?php echo __('Processing...'); ?></div>
        <table class="table table-condensed" id="questions">
            <tbody>
                <?php
                $i = 1;
                $othersQuestionType = array(6, 7, 8); // this categories for others type questions
                // If value exist
                if (!empty($this->request->data['Answer'])) { 
                    // if answer found
                    $temp = array();
                    $question_count = array();
                    foreach ($this->request->data['Answer'] as $key => $value) {
                        $temp[] = $value['question_id'];
                    }
                    $question_count = array_count_values($temp);
                    
                    foreach ($this->request->data['Answer'] as $key => $value) {
                        if ($question_count[$value['question_id']] < 2) { // Not multiple choice
                            $answered[$value['question_id']] = $value['text'];
                        } else {
                            $answered[$value['question_id']][] = $value['text'];
                        }
                    }

                } elseif ($this->Session->check($this->request->query['runningFor'])) { 
                    // if session found
                    $answered = $this->Session->read($this->request->query['runningFor']); 
                } else {
                    $answered = array();
                }


                foreach ($data['Question'] as $question) {
                    //pr($question);
                    // if answered previuosly and stored on session
                    if (isset($answered[$question['id']])) {
                        $question['given_answer'] = $answered[$question['id']];
                    } else {
                        $question['given_answer'] = '';
                    }

                    $choices_number = count($question['Choice']);
                    if (!$question['QuestionType']['multiple_choices'] && $choices_number > 1) {
                        for ($i = 1; $i < $choices_number; ++$i) {
                            unset($question['Choice'][$i]);
                        }
                    }

                    $question['number'] = $i;
                    echo $this->element('Quiz/live/question', $question);
                    if (!in_array($question['question_type_id'], $othersQuestionType)) { 
                        // only considered main question for numbering
                        // not others type questions
                        ++$i;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->element('Quiz/confirm_submit'); ?>
<div class="row">
    <div class="col-xs-12 col-md-4 pull-right">
        <span class="text-danger no-internet"><?php echo __('Sorry, you lost your internet connection.'); ?></span>
        <button type="submit" class="btn btn-primary btn-lg btn-block" id="std_form_submit"><?php echo __('Turn in your quiz') ?></button>
    </div>
</div>

<div style="display: none">
<input type="number" name="data[Student][id]" id="studentId" value="<?php echo !empty($this->request->data['Student']['id']) ? $this->request->data['Student']['id'] : 0; ?>">
</div>

<?php echo $this->Form->end(); ?>

<script id="app-data" type="application/json">
    <?php
    echo json_encode(array(
        'baseUrl' => $this->Html->url('/', true)
    ));
    ?>
</script>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
    var random_id = <?php echo $quizRandomId ?>;
    // Browser tab navigation
    var vis = (function(){
        var stateKey, eventKey, keys = {
            hidden: "visibilitychange",
            webkitHidden: "webkitvisibilitychange",
            mozHidden: "mozvisibilitychange",
            msHidden: "msvisibilitychange"
        };
        for (stateKey in keys) {
            if (stateKey in document) {
                eventKey = keys[stateKey];
                break;
            }
        }
        return function(c) {
            if (c) document.addEventListener(eventKey, c);
            return !document[stateKey];
        }
    })();

    vis(function(){
      if (!vis()) {
        alert(lang_strings['browser_switch']);
        // return;
      } else {
        window.btn_clicked = true;
        window.location.reload();
      }
    });
    // Leave page alert
    window.onbeforeunload = function(){
        if(!window.btn_clicked){
            return lang_strings['leave_quiz'];
        }
    };
</script>

<style type="text/css">
.modal {
  text-align: center;
  padding: 0!important;
}

.modal:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px; /* Adjusts for spacing */
}

.modal-dialog {
  display: inline-block;
  text-align: left;
  vertical-align: middle;
}

.glyphicon.spinning {
    animation: spin 1s infinite linear;
    -webkit-animation: spin2 1s infinite linear;
    color: red;
}

@keyframes spin {
    from { transform: scale(1) rotate(0deg); }
    to { transform: scale(1) rotate(360deg); }
}

@-webkit-keyframes spin2 {
    from { -webkit-transform: rotate(0deg); }
    to { -webkit-transform: rotate(360deg); }
}

.std-basic-info {
    top: -32px;
    float: right;
}
</style>