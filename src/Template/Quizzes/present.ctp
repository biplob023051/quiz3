<?php
    $this->assign('title', $quiz->name); 
    if (!empty($quiz->questions[0]->total)) : 
    $url = $this->Url->build('/' . $quiz->random_id , true);
?>
    <div class="row">
        <div class="col-md-10 col-xs-12">
            <ul class="present">
                <li><?php echo '1. ' . __('Read the QR code with your mobile service') ?></li>
                <li class="qr-image"><img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=<?php echo $url ?>" /></li>
                <li><?php echo __('OR') ?></li>
                <li><?php echo '2. ' . __('Surf to this web address:') ?></li>
                <li><p class="bg-info"><a href="javascript:void(0)" random-id="<?= $quiz->random_id; ?>" id="preview"><?php echo $url ?></a></p></li>
            </ul>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-xs-12 col-md-2 col-md-offset-10">
            <?php
            echo $this->Html->link(__('Back'), '/', array('class' => 'btn btn-primary btn-block'));
            ?>
        </div>
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <?= __('An empty quiz can\'t be shown. Please add at least one question to the quiz.'); ?>
        </div>
    </div>
<?php endif; ?>

<div class="modal fade" id="preview-quiz" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<?= $this->Html->script(['present'], ['inline' => false]); ?>