<?php foreach ($value3->answers as $key4 => $value4) : ?>
    <?php if ($value1->id == $value4->student_id) : ?>
        <?php if (($value3->question_type_id == 1) || ($value3->question_type_id == 2) || ($value3->question_type_id == 3)) : ?>
            <?php if (empty($value4->text)) : ?>
                <div class="text-danger"><?php echo __('NOT_ANSWERED'); ?></div>
            <?php else : ?>
                <!-- check correct and incorrect -->
                <?php if (isset($inline)) : ?>
                    <?php if ($value4->score > 0) : ?>
                        <span class="text-success"><?php echo $value4->text . ' <span class="score">' . ($value4->score+0) . '</span>'; ?></span>
                    <?php elseif ($value4->score == 0) : ?>
                        <span class="text-warning"><?php echo $value4->text; ?></span>
                    <?php else : ?>
                        <span class="text-danger"><?php echo $value4->text . ' <span class="score">' . ($value4->score+0) . '</span>'; ?></span>
                    <?php endif; ?>
                <?php else : ?>  
                    <?php if ($value4->score > 0) : ?>
                        <div class="text-success"><?php echo $value4->text . ' <span class="score">' . ($value4->score+0) . '</span><br/>'; ?></div>
                    <?php elseif ($value4->score == 0) : ?>
                        <div class="text-warning"><?php echo $value4->text . '<br/>'; ?></div>
                    <?php else : ?>
                        <div class="text-danger"><?php echo $value4->text . ' <span class="score">' . ($value4->score+0) . '</span><br/>'; ?></div>
                    <?php endif; ?>
                <?php endif; ?>   
            <?php endif; ?> 
        <!-- short manual scoring -->
        <?php elseif ($value3->question_type_id == 4) : ?>
            <?php if (empty($value4->text)) : ?>
                <div class="text-danger"><?php echo __('NOT_ANSWERED'); ?></div>
            <?php else : ?>
                <?php echo $value4->text; ?>
                <?php echo empty($value4->score) ? 0 : ($value4->score+0); ?>
                <?php if (!empty($value3->choices[0]->points)) : ?>
                    <?php echo '/' . ($value3->choices[0]->points+0); ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <!-- essay type -->
            <?php if (empty($value4->text)) : ?>
                <div class="text-danger"><?php echo __('NOT_ANSWERED'); ?></div>
            <?php else : ?>
                <?php echo $value4->text; ?>
                <span class="essay-points"><?php echo empty($value4->score) ? ' <span class="score">' . 0 . '</span>' : ' <span class="score">' . ($value4->score+0) . '</span>'; ?></span>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

<?php endforeach; ?>