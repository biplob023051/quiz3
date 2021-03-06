<?php $othersQuestionType = array(6, 7, 8); // this categories for others type questions ?>
<?php 
    $col = 3;
    $headerCol = []; 
?>
<div id="answer-table">
    <table class="table table-hover table-responsive table-striped table-fixed" id="fixTable">
        <thead>
            <tr>
                <th class="serial sortable cusFixedTh">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;#&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                <?php if (empty($quizDetails->anonymous)) : $col++; ?>
                    <th class="sortable std-name fixedName cusFixedTh"><?php echo __('NAME'); ?></th>
                <?php endif; ?>
                <th class="sortable timestamp"><?php echo __('TIMESTAMP'); ?></th>
                <?php if (empty($quizDetails->anonymous)) : $col++; ?>
                    <th class="sortable class-th"><?php echo __('CLASS'); ?></th>
                <?php endif; ?>
                <th class="sortable point-th"><?php echo __('TOTAL_POINTS'); ?></th>
                <th class="sortable progress-section"><?php echo __('PROGRESS'); ?></th>
                <?php $i = 1; foreach ($quizDetails->questions as $question): ?>
                    <?php if (!in_array($question->question_type_id, $othersQuestionType)) : ?>
                        <th class="sortable question-collapse">
                            <?php echo $i; ?>
                            . &nbsp;
                            <span class="more"><?php echo $question->text; ?></span>
                        </th>
                    <?php 
                        ++$i; 
                        $col++;
                        if ($question->question_type_id == 2) {
                            $headerCol['STAR'][] = $col;
                        } else if ($question->question_type_id == 3) {
                            $headerCol['MCMC'][] = $col;
                        } else if ($question->question_type_id == 4) {
                            $headerCol['STMR'][] = $col;
                        } else if ($question->question_type_id == 5) {
                            $headerCol['ESSAY'][] = $col;
                        }
                    endif; 
                    ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $sl = 0; if (!empty($quizDetails)) : ?>
                <?php $question_count = count($quizDetails->questions); ?>
                <?php foreach ($quizDetails->students as $key1 => $value1) : $sl++;  ?>
                    <?php //pr($value1); ?>
                    <tr id="student-<?php echo $value1->id; ?>">
                        <td class="serial cusFixedTd">
                            <?php if (in_array($value1->id, $onlineStds)) : ?><?php $paddingClass = 'small-padding-false'; else : $paddingClass = 'small-padding-true'; ?><?php endif; ?>
                            <span class="number-fix question-serial <?php echo $paddingClass; ?>"><?php if (in_array($value1->id, $onlineStds)) : ?><i class="glyphicon online"></i><?php endif; ?><?php echo $sl; ?></span>
                            <button type="button" class="btn btn-danger btn-sm delete-answer" id="<?php echo $value1['id']; ?>" title="<?php echo __('REMOVE_ANSWER'); ?>">
                                <i class="glyphicon trash"></i>
                            </button>
                            <span class="ajax-loader"><img src="<?php echo $this->request->webroot; ?>img/ajax-loader.gif" /></span>
                        </td>
                        <?php if (empty($quizDetails->anonymous)) : ?>
                            <td class="std-name cusFixedTd">
                                <span class="std-info" style="margin-right: 5px;"><?php echo !empty($value1->lname) ? $value1->lname : __('LAST_NAME'); ?> <i class="glyphicon pencil-small"></i></span><input type="text" placeholder="<?php echo __('ENTER_LAST_NAME'); ?>" class="form-control update-std" name="lname" data-rel="lname-<?php echo $value1->id; ?>" value="<?php echo $value1->lname; ?>">
                                
                                <span class="std-info"><?php echo !empty($value1->fname) ? $value1->fname : __('FIRST_NAME'); ?> <i class="glyphicon pencil-small"></i></span><input type="text" placeholder="<?php echo __('ENTER_FIRST_NAME'); ?>" class="form-control update-std" name="fname" data-rel="fname-<?php echo $value1->id; ?>" value="<?php echo $value1->fname; ?>">
                            </td>
                        <?php endif; ?>
                        <td class="timestamp"><?php echo !empty($value1->submitted) ? date('d.m.Y, H:m', $value1->submitted->timestamp) : ''; ?></td>
                        <?php if (empty($quizDetails->anonymous)) : ?>
                            <td class="class-th"><span class="std-info"><?php echo !empty($value1->class) ? $value1->class : __('CLASS'); ?> <i class="glyphicon pencil-small"></i></span><input type="text" placeholder="<?php echo __('ENTER_CLASS'); ?>" class="form-control update-std" name="class" data-rel="class-<?php echo $value1->id; ?>" value="<?php echo $value1->class; ?>"></td>
                        <?php endif; ?>
                        
                        <td class="point-th">
                            <span id="studentscr1-<?php echo $value1->id; ?>"><?php echo ($value1->ranking->score+0); ?></span>/<?php echo ($value1->ranking->total+0); ?>
                        </td>
                        <?php
                            $answer_array = array();
                            $answer_count = 0;
                            foreach ($value1->answers as $answer) {
                                if (!in_array($answer->question_id, $answer_array)) {
                                    $answer_array[] = $answer->question_id;
                                    $answer_count++;
                                }
                            }
                            $progress = number_format((float)($answer_count/$question_count)*100, 2, '.', '')+0; 
                            if ($progress == '100' && $value1->status == '1') {
                                $color = 'green';
                                $table_sorter = $progress+1;
                            } else {
                                $color = '#337ab7';
                                $table_sorter = $progress;
                            }
                        ?>
                        <td class="progress-section" data-value="<?= $table_sorter; ?>">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $progress; ?>%; background-color: <?= $color; ?>">
                                    <span<?php if (empty($progress)) : ?> class="empty-progress-text"<?php endif; ?>><?php echo $progress; ?>%</span>
                                </div>
                            </div>
                        </td>
                        <?php foreach ($quizDetails->questions as $key3 => $value3): ?>
                            <?php if (!in_array($value3->question_type_id, $othersQuestionType)) : ?>
                                <td class="question-collapse">
                                    <?php echo $this->element('Quiz/table', array('value3' => $value3, 'value1' => $value1)); ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td><?php echo __('Quiz not taken yet!'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    var headerCol = <?= json_encode($headerCol); ?>;
</script>