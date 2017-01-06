<?php
$this->Html->script('jquery.colorbox-min', array(
    'inline' => false
));
?>
<?php echo $this->Session->flash('notification'); ?>
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
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?php echo $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0"  class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center col-md-1"><?php echo __('id'); ?></th>
                        <th class="text-center"><?php echo __('Title'); ?></th>
                        <th class="text-center"><?php echo __('Sub Title'); ?></th>
                        <th class="text-center"><?php echo __('Display Page'); ?></th>
                        <th class="text-center"><?php echo __('Url'); ?></th>
                        <th class="text-center"><?php echo __('Created'); ?></th>
                        <th class="text-center col-md-1"><?php echo __('Sort'); ?></th>
                        <th class="text-center col-md-1"><?php echo __('Status'); ?></th>
                        <th class="text-center col-md-1"><?php echo __('Action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($helps as $help): ?>
                        <tr>
                            <td class="text-center"><?php echo h($help['Help']['id']); ?></td>
                            <td class="text-center"><a href="javascript:void jQuery.colorbox({html:'<iframe width=420 height=315 src=https://www.youtube.com/embed/<?php echo $help['Help']['url_src']; ?>?autoplay=1 frameborder=0 allowfullscreen></iframe>'})"><?php echo $help['Help']['title']; ?></a></td>
                            <td class="text-center"><?php echo h($help['Help']['sub_title']); ?></td>
                            <td class="text-center">
                                <?php echo $siteOptions[$help['Help']['type']] ?>
                            </td>
                            <td class="text-center"><?php echo h($help['Help']['url']); ?></td>
                            <td class="text-center"><?php echo h($help['Help']['created']); ?></td>
                            <td class="text-center" nowrap="nowrap">
                                    <?php echo $this->Form->postLink('<span class="glyphicon glyphicon-arrow-up"></span>', array('action' => 'moveup', $help['Help']['id'],'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))), array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                                    <?php echo $this->Form->postLink('<span class="glyphicon glyphicon-arrow-down"></span>', array('action' => 'movedown', $help['Help']['id'],'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))), array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                            </td>
                             <td class="text-center" nowrap="nowrap">
                                <?php if($help['Help']['status']):?>
                                    <?php echo $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs active">'.__('On').'</button><button type="button" class="btn btn-default btn-xs inactive">'.__('Off').'</button></div>', array('action' => 'active', $help['Help']['id'],'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('escape'=>false), __('Confirm inactive title %s?', trim($help['Help']['id']))); ?>
                                <?php else :?>
                                    <?php echo $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs inactive">'.__('On').'</button><button type="button" class="btn btn-default btn-xs active">'.__('Off').'</button></div>', array('action' => 'active', $help['Help']['id'],1,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('escape'=>false), __('Confirm active title %s?', trim($help['Help']['id']))); ?>
                                <?php endif;?>
                            </td>
                            <td class="text-center" nowrap="nowrap">
                                <?php echo $this->Html->link(__('Edit'), array('action' => 'create', $help['Help']['id'],'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                                <?php if(!$help['Help']['status']):?>
                                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $help['Help']['id'],'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-danger btn-xs','escape'=>false), __('Confirm delete of title %s?', trim($help['Help']['id']))); ?>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>


