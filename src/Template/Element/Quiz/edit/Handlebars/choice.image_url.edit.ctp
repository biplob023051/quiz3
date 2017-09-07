<div class="row" id="web-panel">
    <div class="col-xs-12 col-md-6">
        <div class="well well-sm">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <?php
                    echo $this->Form->input('data.Choice.{{id}}.text', array(
                        'default' => '{{text}}',
                        'div' => array('class' => 'form-group'),
                        'class' => 'form-control youtube-url',
                        'label' => false,
                        'placeholder' => __('ENTER_IMAGE_URL'),
                        'id' => 'Choice{{id}}Text'
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="upload-panel" style="display: none;">
    <div class="col-xs-12 col-md-6">
        <div class="well well-sm">
            <div id="fileuploader"><?= __('CHOOSE_FILE_1'); ?></div>
            <?= $this->Form->input('data.Choice.{{id}}.temp', ['type' => 'hidden', 'id' => 'temp_photo']); ?>
        </div>
    </div>
</div>