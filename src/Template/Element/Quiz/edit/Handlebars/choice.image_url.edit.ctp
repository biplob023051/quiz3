{{#if text}}
    <div class="row" id="web-panel" style="display: none;">
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
                <div id="fileuploader"><?= __('CHOOSE_FILE'); ?></div>
                <?= $this->Form->input('data.Choice.{{id}}.temp', ['type' => 'hidden', 'id' => 'temp_photo']); ?>
            </div>
        </div>
    </div>

    <div class="row" id="preview-panel">
        <div class="col-xs-12 col-md-6">
            <div class="well well-sm">
                <div class="ajax-file-upload-container">
                    <div class="ajax-file-upload-statusbar" style="width: 400px;">
                        <button data-img="{{text}}" type="button" class="btn btn-default" id="file-delete-existing" title="Delete image"><i class="glyphicon close"></i></button>
                        <img class="ajax-file-upload-preview" src="{{text}}" style="width: 150px; height: 150px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
{{else}}
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
                <div id="fileuploader"><?= __('CHOOSE_FILE'); ?></div>
                <?= $this->Form->input('data.Choice.{{id}}.temp', ['type' => 'hidden', 'id' => 'temp_photo']); ?>
            </div>
        </div>
    </div>
{{/if}}
