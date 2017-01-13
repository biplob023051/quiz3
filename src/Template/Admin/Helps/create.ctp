<?= $this->Flash->render(); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?php echo $this->Html->link(__('Site Videos'),array('controller'=>'helps','action'=>'videos'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?php echo $this->Html->link(__('Create Videos'),array('controller'=>'helps','action'=>'create'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
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
        )); ?>
    
            <?php
                echo $this->Form->hidden('photo');
                echo $this->Form->input('type', array('label'=>array('text'=>__('Display Page')),'options' => $siteOptions, 'empty' => __('Select One') ));
                echo $this->Form->input('title', array('label'=>array('text'=>__('Title')), 'placeholder' => __('Please insert video title')));
                echo $this->Form->input('sub_title', array('label'=>array('text'=>__('Sub Title')), 'placeholder' => __('Please insert video sub title')));
                echo $this->Form->input('url', array('label'=>array('text'=>__('Youtube Video Url')), 'placeholder' => __('Please insert youtube video url here')));
                echo $this->Form->input('body', array('label'=>array('text'=>__('Description')), 'placeholder' => __('Description here'), 'type' => 'textarea'));
                
            ?>
            <div class="form-group">
                <label><?php echo __('Photo'); ?></label>
                <img src="<?php echo $this->Quiz->getHelpPicture($this->request->data, 'videos'); ?>" id="item-avatar" class="img_wrapper">
                <div id="select-0" style="margin: 10px 0px 0px 210px;"></div>
            </li>
            <div class="form-group">
                <?php 
                    echo $this->Form->button(__('SAVE'), ['class' => 'btn btn-primary btn-xlarge']) . ' ';
                    echo empty($this->request->query['redirect_url']) ? $this->Html->link(__('BACK'),array('controller' => 'helps','action' => 'videos', 'admin' => true),array('class'=>'btn btn-danger')) : $this->Html->link(__('BACK'),urldecode($this->request->query['redirect_url']),array('class'=>'btn btn-danger')); 
                ?>
            </div>
            
        <?php echo $this->Form->end(); ?>
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
<?php
    echo $this->Html->script(array('tinymce/tinymce.min', 'jquery.fineuploader', 'admin-insert-help'), array(
        'inline' => false
    ));
    echo $this->Html->css('fineuploader', array(
        'inline' => false
    ));
?>
