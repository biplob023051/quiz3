<?php use Cake\Routing\Router; ?>
<?= $this->Html->script('jquery.colorbox-min', array('inline' => false)); ?>
<?= $this->Flash->render(); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?= $this->Html->link(__('Site Videos'),array('controller'=>'helps','action'=>'videos'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?= $this->Html->link(__('Create Videos'),array('controller'=>'helps','action'=>'create'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0"  class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center col-md-1"><?= __('id'); ?></th>
                        <th class="text-center"><?= __('Title'); ?></th>
                        <th class="text-center"><?= __('Sub Title'); ?></th>
                        <th class="text-center"><?= __('Display Page'); ?></th>
                        <th class="text-center"><?= __('Url'); ?></th>
                        <th class="text-center"><?= __('Created'); ?></th>
                        <th class="text-center col-md-1"><?= __('Sort'); ?></th>
                        <th class="text-center col-md-1"><?= __('Status'); ?></th>
                        <th class="text-center col-md-1"><?= __('Action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($helps as $help): ?>
                        <tr>
                            <td class="text-center"><?= $help->id; ?></td>
                            <td class="text-center"><a href="javascript:void jQuery.colorbox({html:'<iframe width=420 height=315 src=https://www.youtube.com/embed/<?= $help->url_src; ?>?autoplay=1 frameborder=0 allowfullscreen></iframe>'})"><?= $help->title; ?></a></td>
                            <td class="text-center"><?= h($help->sub_title); ?></td>
                            <td class="text-center"><?= $siteOptions[$help->type]; ?></td>
                            <td class="text-center"><?= h($help->url); ?></td>
                            <td class="text-center"><?= h($help->created); ?></td>
                            <td class="text-center" nowrap="nowrap">
                                <?= $this->Form->postLink('<span class="glyphicon glyphicon-arrow-up"></span>', array('action' => 'moveup', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))), array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                                <?= $this->Form->postLink('<span class="glyphicon glyphicon-arrow-down"></span>', array('action' => 'movedown', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))), array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                            </td>
                             <td class="text-center" nowrap="nowrap">
                                <?php if($help->status):?>
                                    <?= $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs active">'.__('On').'</button><button type="button" class="btn btn-default btn-xs inactive">'.__('Off').'</button></div>', array('action' => 'active', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('escape'=>false, 'confirm' => __("Confirm inactive title ''{0}''?", trim($help->title)))); ?>
                                <?php else :?>
                                    <?= $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs inactive">'.__('On').'</button><button type="button" class="btn btn-default btn-xs active">'.__('Off').'</button></div>', array('action' => 'active', $help->id,1,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('escape'=>false, 'confirm' => __("Confirm active title ''{0}''?", trim($help->title)))); ?>
                                <?php endif;?>
                            </td>
                            <td class="text-center" nowrap="nowrap">
                                <?= $this->Html->link(__('Edit'), array('action' => 'create', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                                <?php if(!$help->status):?>
                                    <?= $this->Form->postLink(__('Delete'), array('action' => 'delete', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-danger btn-xs','escape'=>false, 'confirm' => __("Confirm delete of title ''{0}''?", trim($help->title)))); ?>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>


