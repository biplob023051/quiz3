<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?php echo __('Delete quiz “Chemical Elements”?'); ?>
            </div>
            <div class="modal-body">
                <?php echo __('There are 14 answers for this quiz. This can’t be undone. Are you sure you want to delete the quiz and all the answers?'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('CANCEL'); ?></button>
                <?php echo $this->Html->link(__('DELETE'),array('controller'=>'quiz', 'action'=>'delete'),array("role"=>"button", "class"=>"btn btn-danger btn-ok"));?>
            </div>
        </div>
    </div>
</div>