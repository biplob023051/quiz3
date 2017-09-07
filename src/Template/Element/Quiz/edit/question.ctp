<?php 
    $templateOptions = array('header', 'youtube_video', 'image_url');
?>
<tr id="q<?php echo $id ?>"<?php if (in_array($QuestionType['template_name'], $templateOptions)) : ?> class="others_type<?php if ($QuestionType['template_name'] == 'header') : ?> header_type<?php endif; ?>"<?php endif; ?>>
<script type="application/json">
<?php
if (($question_type_id == 2) && !empty($case_sensitive)) {
    $Choice[0]['case_sensitive'] =  true;
}
echo json_encode(array(
    'id' => $id,
    'text' => $text,
    'explanation' => $explanation,
    'max_allowed' => $max_allowed,
    'QuestionType' => $QuestionType,
    'Choice' => $Choice
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
                <?php if ($QuestionType['template_name'] == 'header') : ?>
                    <span class="h4 header"><?php echo $text; ?></span>
                    <br />
                <?php elseif ($QuestionType['template_name'] == 'youtube_video') : ?>
                    
                <?php elseif ($QuestionType['template_name'] == 'image_url') : ?>
                    
                <?php else : ?>
                    <span class="h4"><?php echo '<span class="question_number">' . $number . '</span>. ' .  $text; ?></span>
                    <br />
                <?php endif; ?>
                <span class="text-muted"><?php echo $explanation ?></span>
                <?php if (!empty($max_allowed)) : ?>
                    <p>
                        <span class="text-muted">
                            <strong>
                                <?php echo __('CHOOSE_MOST'); ?>
                            </strong>
                            <?php echo $max_allowed; ?>
                        </span>
                    </p>
                <?php endif; ?>
                <?php if (!empty($case_sensitive)) : ?>
                    <p>
                        <span class="text-muted">
                            <strong>
                                <?php echo __('DEMAND_UPPER_LOWERCASE'); ?>
                            </strong>
                        </span>
                    </p>
                <?php endif; ?>
            </p>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="btn-group preview-btn">
                <button type="button" class="btn btn-default btn-sm edit-question" id="edit-q<?php echo $id ?>" title="<?php echo __('EDIT_QUESTION'); ?>">
                    <i class="glyphicon pencil"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm delete-question" id="delete-q<?php echo $id ?>" title="<?php echo __('REMOVE_QUESTION'); ?>">
                    <i class="glyphicon trash"></i>
                </button>
                <button type="button" class="btn btn-success btn-sm duplicate-question" id="duplicate-q<?php echo $id ?>" title="<?php echo __('DUPLICATE_QUESTION'); ?>">
                    <i class="glyphicon duplicate"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="preview"> <!-- before it was choices -->
        <?php
        foreach ($Choice as $c) {
            echo $this->element("Quiz/edit/choice.{$QuestionType['template_name']}", $c);
        }
        ?>
    </div>  
</td>                      
</tr>

<?php if ($question_type_id == 2) : ?>
    <?php $Choice[0]['case_sensitive'] = $case_sensitive; ?>
<?php endif; ?>
<?php if ($question_type_id != 6) : ?>
    <script type="text/javascript">
        myChoices[<?php echo $id ?>] = <?php echo json_encode($Choice); ?>;
    </script>
<?php endif; ?>