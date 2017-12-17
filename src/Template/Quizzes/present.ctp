<?php
    $this->assign('title', $quiz->name); 
    if (!empty($quiz->questions[0]->total)) : 
    $url = $this->Url->build('/' . $quiz->random_id , true);
?>
    <div class="row">
        <div class="col-md-10 col-xs-12">
            <ul class="present">
                <li><?php echo '1. ' . __('READ_QR_CODE') ?></li>
                <li class="qr-image"><img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=<?php echo $url ?>" /></li>
                <li><?php echo __('OR') ?></li>
                <li><?php echo '2. ' . __('SURF_WEB_ADDRESS') ?></li>
                <li>
                <p class="bg-info"><a href="<?php echo $url ?>" random-id="<?= $quiz->random_id; ?>" id="preview">
                <?php 
                if (strpos($url, 'https://www.') !== false) {
                    echo str_replace("https://www.","",$url);
                } elseif (strpos($url, 'https://') !== false) {
                    echo str_replace("https://","",$url);
                } else {
                    echo $url;
                }
                    
                    
                ?>
                </a></p></li>
            </ul>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-xs-12 col-md-2 col-md-offset-10">
            <?= $this->Html->link(__('BACK'), '/', array('class' => 'btn btn-primary btn-block'));?>
        </div>
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <?= __('EMPTY_QUIZ_CANT_SHOW_ADD_QUESTION'); ?>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="preview-quiz" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<?= $this->Html->script(['present'.$minify], ['inline' => false]); ?>