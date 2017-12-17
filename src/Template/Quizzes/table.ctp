<?php
echo $this->Html->css(['answer-table'.$minify], ['inline' => false]);

$this->assign('title', __('ANSWER_TABLE'));
?>
<?= $this->Form->create('', ['id' => 'answer-table-filter']);  ?>
    <div class="row">
        <div class="alert" id="ajax-message" style="display: none"></div>
        <div class="col-md-3 col-xs-12">
            <?php
            echo $this->Form->input('Filter.daterange', array(
                'options' => array(
                    'all' => __('ALL_TIME'),
                    'today' => __('TODAY'),
                    'this_year' => __('THIS_YEAR'),
                    'this_month' => __('THIS_MONTH'),
                    'this_week' => __('THIS_WEEK'),
                ),
                'div' => array('class' => 'form-group'),
                'default' => $filter['daterange'],
                'class' => 'form-control',
                'label' => false
            ));
            ?>
        </div>
        <div class="col-md-3 col-xs-12">
            <?php 
                if (empty($quizDetails->anonymous)) {
                    echo $this->Form->input('Filter.class', array(
                        'options' => $classes,
                        'div' => array('class' => 'form-group'),
                        'default' => $filter['class'],
                        'class' => 'form-control',
                        'label' => false
                    ));
                }
            ?>
        </div>
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <div class="btn-group btn-group-justified">
                    <a href="javascript:void(0);" class="btn btn-default" id="answer-table-overview"><?= __('OVERVIEW'); ?></a>
                    <a href="javascript:void(0);" class="btn btn-primary" id="answer-table-show"><?= __('ANSWERS'); ?></a>
                </div>
            </div>
        </div>
    </div>
<?= $this->Form->end(); ?>

<div class="panel panel-default">
    <?= $this->element('Quiz/table_extension', array('quizDetails' => $quizDetails)); ?>    
</div>
<?= $this->element('Answer/confirm_delete'); ?>
<div id="prev_data" style="display : none;"><?php echo $studentIds; ?></div>
<div id="quizId" style="display : none;"><?php echo $quizId; ?></div>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-8">
        <button type="button" class="btn btn-primary btn-block" id="print"><?= __('PRINT'); ?></button>
    </div>
    <div class="col-xs-12 col-md-2">
        <?= $this->Html->link(__('BACK'), '/', array('class' => 'btn btn-primary btn-block'));?>
    </div>
</div>
<div id="print_div" style="display: none;"></div>
<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
    var onlineStds = <?php echo json_encode($onlineStds) ?>;
</script>

<?= $this->Html->script([
        'tableHeadFixer'.$minify, 
        /* production */
        'https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.29.2/js/jquery.tablesorter.min.js',
        /*local*/
        //'jquery.tablesorter.min', 
        'answer-table'.$minify,
    ], ['inline' => false]
);?>
