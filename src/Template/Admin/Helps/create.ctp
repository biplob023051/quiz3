<link href="https://hayageek.github.io/jQuery-Upload-File/4.0.10/uploadfile.css" rel="stylesheet">
<script src="https://hayageek.github.io/jQuery-Upload-File/4.0.10/jquery.uploadfile.min.js"></script>
<?php $this->assign('title',$title_for_layout); ?>
<?= $this->Flash->render(); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?php echo $this->Html->link(__('SITE_VIDEOS'),array('controller'=>'helps','action'=>'videos'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li class="active"><?php echo $this->Html->link(__('CREATE_VIDEOS'),array('controller'=>'helps','action'=>'create'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <?php echo $this->Form->create($video, array(
            'type' => 'file',
            'novalidate'=>'novalidate'
        )); 
        ?>
    
            <?php
                echo $this->Form->hidden('photo');
                echo $this->Form->hidden('temp_photo');
                echo $this->Form->input('type', array('label'=>array('text'=>__('DISPLAY_PAGE')),'options' => $siteOptions, 'empty' => __('SELECT_ONE') ));
                echo $this->Form->input('language', [
                    'label' => ['text' => __('LANGUAGE')],
                    'options' => $this->Quiz->allLanguages(),
                    'empty' => __('SELECT_LANGUAGE'),
                    'default' => false
                ]);
                echo $this->Form->input('title', array('label'=>array('text'=>__('Title')), 'placeholder' => __('INSERT_VIDEO_TITLE')));
                echo $this->Form->input('sub_title', array('label'=>array('text'=>__('SUB_TITLE')), 'placeholder' => __('INSERT_VIDEO_SUB_TITLE')));
                echo $this->Form->input('url', array('label'=>array('text'=>__('YOUTUBE_URL')), 'placeholder' => __('INSERT_YOUTUBE_URL_HERE')));
                echo $this->Form->input('body', array('label'=>array('text'=>__('DESCRIPTION')), 'placeholder' => __('Description here'), 'type' => 'textarea'));
                
            ?>
            <div class="form-group">
                <label><?php echo __('Photo'); ?></label>
                <?php if ($video->errors() && $video->temp_photo) : ?>
                    <img src="<?php echo $this->Quiz->getHelpPictureNew($video->temp_photo, 'tmp'); ?>" id="item-avatar" style="width: 200px; height: auto;">
                <?php elseif ($video->photo) : ?>
                    <img src="<?php echo $this->Quiz->getHelpPictureNew($video->photo, 'videos'); ?>" id="item-avatar" style="width: 200px; height: auto;">
                <?php else : ?>
                <?php endif; ?>
                <div id="fileuploader"><?= __('BROWSE_PHOTO'); ?></div>
            </li>
            <div class="form-group">
                <?php 
                    echo $this->Form->button(__('SAVE'), ['class' => 'btn btn-primary btn-xlarge']) . ' ';
                    echo empty($this->request->query['redirect_url']) ? $this->Html->link(__('BACK'),array('controller' => 'helps','action' => 'videos'),array('class'=>'btn btn-danger')) : $this->Html->link(__('BACK'),urldecode($this->request->query['redirect_url']),array('class'=>'btn btn-danger')); 
                ?>
            </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
<script type="text/javascript">
    <?php if (!empty($video->id)) : ?>
        var video_id = <?php echo $video->id; ?>;
    <?php else: ?>
        var video_id = '';
    <?php endif; ?>
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>
<?= $this->Html->script(['tinymce/tinymce.min', 'admin-insert-help'.$minify], ['inline' => false]); ?>
