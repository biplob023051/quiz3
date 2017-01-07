<?php 
    $templateOptions = array('header', 'youtube_video', 'image_url');
    // pr($question);
    // exit;
?>
<tr id="q<?php echo $question->id ?>"<?php if (in_array($question->question_type->template_name, $templateOptions)) : ?> class="others_type<?php if ($question->question_type->template_name == 'header') : ?> header_type<?php endif; ?>"<?php endif; ?>>
<script type="application/json">
<?php
echo json_encode(array(
    'id' => $question->id,
    'text' => $question->text,
    'explanation' => $question->explanation,
    'max_allowed' => $question->max_allowed,
    'case_sensitive' => $question->case_sensitive,
    'QuestionType' => $question->question_type,
    'Choice' => $question->choices
));
?>
</script>
<td>
    <div class="pull-right shorter-arrow">
        <i class="glyphicon glyphicon-resize-vertical"></i>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-6">            
            <p>
                <?php if ($question->question_type->template_name == 'header') : ?>
                    <span class="h4 header"><?php echo $question->text; ?></span>
                    <br />
                <?php elseif ($question->question_type->template_name == 'youtube_video') : ?>
                    
                <?php elseif ($question->question_type->template_name == 'image_url') : ?>
                    
                <?php else : ?>
                    <span class="h4"><?php echo '<span class="question_number">' . $question->number . '</span>. ' .  $question->text; ?></span>
                    <br />
                <?php endif; ?>
                <span class="text-muted"><?php echo $question->explanation ?></span>
                <?php if (!empty($question->max_allowed)) : ?>
                    <p>
                        <span class="text-muted">
                            <strong>
                                <?php echo __('Choose at most'); ?>
                            </strong>
                            <?php echo $question->max_allowed; ?>
                        </span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($question->case_sensitive)) : ?>
                    <p>
                        <span class="text-muted">
                            <strong>
                                <?php echo __('Demand exact upper- and lowercase letters'); ?>
                            </strong>
                        </span>
                    </p>
                <?php endif; ?>
            </p>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="btn-group preview-btn">
                <button type="button" class="btn btn-default btn-sm edit-question" id="edit-q<?php echo $question->id ?>" title="<?php echo __('Edit question'); ?>">
                    <i class="glyphicon pencil"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm delete-question" id="delete-q<?php echo $question->id ?>" title="<?php echo __('Remove question'); ?>">
                    <i class="glyphicon trash"></i>
                </button>
                <button type="button" class="btn btn-success btn-sm duplicate-question" id="duplicate-q<?php echo $question->id ?>" title="<?php echo __('Duplicate question'); ?>">
                    <i class="glyphicon duplicate"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="preview"> <!-- before it was choices -->
        <?php
        foreach ($question->choices as $choice) {
            echo $this->element("Quiz/edit/choice.{$question->question_type->template_name}", array('choice' => $choice));
        }
        ?>
    </div>  
</td>                      
</tr>