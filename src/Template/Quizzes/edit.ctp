<script type="text/javascript">
    var c_quiz_id = '<?= $data['id']; ?>';
</script>
<style type="text/css">
    .subjects, .classes {
        width: 33% !important;
        float: left;
        margin-top: 0px;
    }   
    .subjects input[type=checkbox], .classes input[type=checkbox] {
        margin-right: 5px !important;
    }
    .placeholder {
        border: 1px solid green;
        background-color: white;
        -webkit-box-shadow: 0px 0px 10px #888;
        -moz-box-shadow: 0px 0px 10px #888;
        box-shadow: 0px 0px 10px #888;
        width: 50%;
    }
    .tile {
        height: 70px;
    }
    .grid {
        margin-top: 1em;
    }
    #settings {
        margin: 5px 0px;
    }
    #show-settings {
        text-align: left;
    }
    #settings .col-md-12 {
        padding: 0;
        margin: 0;
    } 
    .settings-options {
        padding: 0px 50px !important;
    }
    .settings-options label {
        padding: 0 5px !important;
    }
    .settings-options .form-group {
        margin-bottom: 0px;
    }
    .no-border {
        float: left;
        padding-right: 10px;
    }
    .caret-down {
        display: none;
    }

    .caret {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 4px solid #000000;
        display: inline-block;
        height: 0;
        opacity: 0.3;
        vertical-align: middle;
        width: 0;
    }

    .caret-right {
        border-bottom: 4px solid transparent;
        border-top: 4px solid transparent;
        border-left: 4px solid #000000;
        display: inline-block;
        height: 0;
        opacity: 0.3;
        vertical-align: middle;
        width: 0;
    }

    .settings-options label {
        font-weight: normal !important;
    }

    .ajax-file-upload, .ajax-file-upload:hover {
        font-weight: normal;
        color: #fff;
        background-color: #449d44;
        border-color: #398439;
        border: none;
        -moz-box-shadow: 0 0px 0 0 #398439;
        -webkit-box-shadow: 0 2px 0 0 #398439;
         box-shadow: 0 2px 0 0 #398439; 
        line-height: 16px;
    }

    .ajax-file-upload .upload-text {
        vertical-align: top;  
    }

    .ajax-upload-dragdrop {
        padding: 40px 0 40px 0px;
        width: 100% !important;
        text-align: center;
    }

    .ajax-file-upload-container {
        margin: 0px;
    }

    .ajax-file-upload-statusbar {
        width: 100% !important;
        text-align: center;
        margin: 5px 0px;
    }

    .ajax-file-upload-filename, .ajax-file-upload-abort {
        display: none !important;
    }

    #file-delete-button {
        position: absolute;
        right: 26px;
        margin-top: -5px;
    }

    #file-delete-existing {
        position: absolute;
        right: 26px;
        margin-top: -5px;
    }
</style>
<script type="text/javascript">
    var myChoices = {}, editing_q_type;
</script>
<?php
$data['Quiz']['id'] = $data['id'];
$data['Quiz']['user_id'] = $data['user_id'];
$data['Quiz']['name'] = $data['name'];
$data['Quiz']['description'] = $data['description'];
//$data['Quiz']['created'] = $data['created'];
//$data['Quiz']['modified'] = $data['modified'];
$data['Quiz']['student_count'] = $data['student_count'];
$data['Quiz']['status'] = $data['status'];
$data['Quiz']['random_id'] = $data['random_id'];
$data['Quiz']['show_result'] = $data['show_result'];
$data['Quiz']['anonymous'] = $data['anonymous'];
$data['Quiz']['subjects'] = $data['subjects'];
$data['Quiz']['classes'] = $data['classes'];
$data['Quiz']['shared'] = $data['shared'];
$data['Quiz']['is_approve'] = $data['is_approve'];
$data['Quiz']['comment'] = $data['comment'];
unset($data['id']);
unset($data['user_id']);
unset($data['name']);
unset($data['description']);
unset($data['created']);
unset($data['modified']);
unset($data['student_count']);
unset($data['status']);
unset($data['random_id']);
unset($data['show_result']);
unset($data['anonymous']);
unset($data['subjects']);
unset($data['classes']);
unset($data['shared']);
unset($data['is_approve']);
unset($data['comment']);


foreach ($data['QTypes'] as $key => $value) {
    $data['QuestionTypes'][$key]['QuestionType']['name'] = $value['name'];
    $data['QuestionTypes'][$key]['QuestionType']['template_name'] = $value['template_name'];
    $data['QuestionTypes'][$key]['QuestionType']['multiple_choices'] = $value['multiple_choices'];
    $data['QuestionTypes'][$key]['QuestionType']['id'] = $value['id'];
    $data['QuestionTypes'][$key]['QuestionType']['type'] = $value['type'];
}

$data['Question'] = $data['questions'];
unset($data['questions']);
foreach ($data['Question'] as $key => $question) {
    $data['Question'][$key]['QuestionType'] = $data['QTypes'][$question['question_type_id']-1];
    unset($data['Question'][$key]['question_type']);
    $data['Question'][$key]['Choice'] = $data['Question'][$key]['choices'];
    unset($data['Question'][$key]['choices']);

    unset($data['Question'][$key]['created']);
    unset($data['Question'][$key]['modified']);
}

unset($data['QTypes']);
// pr($data);
// exit;
use Cake\Routing\Router;
$this->assign('title', __('EDIT_QUIZ'));

if (!empty($data['Quiz']['subjects'])) {
    $selectedSubjects = json_decode($data['Quiz']['subjects'], true);
} else {
    $selectedSubjects = array_keys($subjectOptions); // By default all subjects
}

if (!empty($data['Quiz']['classes'])) {
    $selectedClasses = json_decode($data['Quiz']['classes'], true);
} else {
    $selectedClasses = array_keys($classOptions); // By default all classes
}

// pr($data);
// exit;
?>

<?= $this->Flash->render(); ?>

<?= $this->Form->create('Quiz', ['id' => 'QuizEditForm']); ?>
<!--<div id="qunit"></div>
<div id="qunit-fixture"></div>-->
<div class="row" id="settings">
    <div class="col-xs-12 col-md-12">
        <a href="javascript:void(0)" class="btn btn-default btn-block" id="show-settings">
            <span class="caret-down caret-icon">
                <span class="caret"></span>
            </span>
            <span class="caret caret-right caret-icon"></span>
            <?php echo __('QUIZ_SETTINGS'); ?>
        </a>
    </div>
    <div class="col-xs-12 col-md-12 settings-options" style="display: none;">
        <div class="form-group">
            <?php 
                echo $this->Form->checkbox('show_result', array('default' => $data['Quiz']['show_result'])); 
                echo $this->Form->label('show_result', __('SHOW_RESULTS_AFTER_QUIZ'));
            ?>
        </div>
        <div class="form-group">
            <?php 
                echo $this->Form->checkbox('anonymous', array('default' => $data['Quiz']['anonymous'])); 
                echo $this->Form->label('anonymous', __('ANONYMOUS_PARTICIPATION'));
            ?>
        </div>
    </div>
    <div class="col-xs-12 col-md-12 settings-options" style="display: none;">
        <hr>
    </div>
    <div class="col-md-12 settings-options" style="display: none;">
        <?php
            echo $this->Form->input('subjects', array(
                'templates' => [ 
                    'checkboxWrapper' => '<div class="subjects no-border">{{label}}</div>',
                ],
                'options' => $subjectOptions,
                'type' => 'select',
                'multiple' => 'checkbox',
                'value' => $selectedSubjects,
                'label' => false
            ));
        ?>
    </div>
    <div class="col-xs-12 col-md-12 settings-options" style="display: none;">
        <hr>
    </div>
    <div class="col-md-12 settings-options" style="display: none;">
        <?php
            echo $this->Form->input('classes', array(
                'templates' => [ 
                    'checkboxWrapper' => '<div class="classes no-border">{{label}}</div>',
                ],
                'options' => $classOptions,
                'type' => 'select',
                'multiple' => 'checkbox',
                'value' => $selectedClasses,
                'label' => false
            ));
        ?>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <?php
                if (isset($initial) && in_array($data['Quiz']['name'], ['Name the quiz', 'Nimeä testi', 'Namnge testet'])) {
                    echo $this->Form->input('name', array(
                        'label' => false,
                        'placeholder' => __('NAME_QUIZ'),
                        'class' => 'input-lg update-now',
                        'data-current-value' => '',
                    ));
                } else {
                    echo $this->Form->input('name', array(
                        'label' => false,
                        'default' => $data['Quiz']['name'],
                        'placeholder' => __('NAME_QUIZ'),
                        'class' => 'input-lg update-now',
                        'data-current-value' => $data['Quiz']['name']
                    ));
                }
                ?>
            </div>
            <div class="col-xs-12 col-md-6">
                <?php
                echo $this->Form->input('description', array(
                    'label' => false,
                    'default' => $data['Quiz']['description'],
                    'placeholder' => __('DESCRIBE_QUIZ'),
                    'class' => 'input-lg update-now',
                    'data-current-value' => $data['Quiz']['description']
                ));
                ?>
            </div>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
    <table class="table table-striped" id="questions">
        <tbody>
            <!--nocache-->
            <?php
            $i = 1;
            $othersQuestionType = array(6, 7, 8); // this categories for others type questions
            foreach ($data['Question'] as $question) {
                
                $choices_number = count($question['Choice']);
                if (!$question['QuestionType']['multiple_choices'] && $choices_number > 1) {
                    for ($i = 1; $i < $choices_number; ++$i) {
                        unset($question['Choice'][$i]);
                    }
                }

                $question['number'] = $i;
                echo $this->element('Quiz/edit/question', $question);
                if (!in_array($question['question_type_id'], $othersQuestionType)) { 
                    // only considered main question for numbering
                    // not others type questions
                    ++$i;
                }
            }
            ?>
            <!--/nocache-->
        </tbody>
    </table>

</div>

<div class="row">
    <div class="col-xs-12 col-md-3 col-md-offset-6">
        <div class="form-group">
            <button type="button" class="btn btn-primary btn-lg btn-block" id="add-question"><?php echo __('ADD_NEW_QUESTION') ?></button>

        </div>
    </div>
    <div class="col-xs-12 col-md-3">
        <div class="form-group">
            <input type="submit" class="btn btn-default btn-lg btn-block" id="submit-quiz" value="<?php echo __('FINISH_EDIT'); ?>" />
        </div>
    </div>
</div>

<script id="app-data" type="application/json">
<?php
echo json_encode(array(
    'baseUrl' => Router::url('/', true),
    'questionTypes' => $data['QuestionTypes'],
    'quizId' => $data['Quiz']['id']
));
?>
</script>

<script id="question-preview-template" type="text/x-handlebars-template">
<?php echo $this->element('Quiz/edit/Handlebars/question.preview'); ?>
</script>

<script id="question-edit-template" type="text/x-handlebars-template">
<?php echo $this->element("Quiz/edit/Handlebars/question.edit", $data); ?>
</script>


<?php foreach ($data['QuestionTypes'] as $qt): ?>

    <script id="choice-<?php echo $qt['QuestionType']['template_name'] ?>-edit-template" type="text/x-handlebars-template">
    <?php echo $this->element("Quiz/edit/Handlebars/choice.{$qt['QuestionType']['template_name']}.edit"); ?>
    </script>

    <script id="choice-<?php echo $qt['QuestionType']['template_name'] ?>-preview-template" type="text/x-handlebars-template">
        <?php echo $this->element("Quiz/edit/Handlebars/choice.{$qt['QuestionType']['template_name']}.preview"); ?>
    </script>

<?php endforeach; ?>

<script type="text/javascript">
    <?php if (!empty($initial)) : ?>
        var initial = true;
    <?php else : ?>
        var initial = false;
    <?php endif; ?>
    <?php if (!empty($no_question)) : ?>
        var no_question = true;
    <?php else : ?>
        var no_question = false;
    <?php endif; ?>
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>

<?php 
    echo $this->Html->script([
            'jquery.uploadfile.min',
            /* production */
            'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/2.0.0/handlebars.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery.serializeJSON/2.8.1/jquery.serializejson.min.js',
            /* local */
            //'jquery-ui',
            // 'handlebars.min',
            // 'jquery.serializejson.min',
            'webquiz'.$minify,
            'edit'.$minify,
        ], ['inline' => false]
    );
    echo $this->Html->css([
            'uploadfile'.$minify,
            'jquery-ui'.$minify
        ], ['inline' => false]
    );
?>
