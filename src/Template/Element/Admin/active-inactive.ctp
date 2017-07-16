<div class="modal fade" id="change-status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?= __('YOU_ARE_GOING_CHANGE_STATUS'); ?>
            </div>
            <div class="modal-body" id="status-body">
                <?= __('ACTIVATE_USER') . ' '; ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirm-status" class="btn btn-success"><span class="spinner"><span class="glyphicon glyphicon-refresh spinning"></span></span> <?php echo __('CONFIRM'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('CANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>

<style>
.spinner {
  display: inline-block;
  opacity: 0;
  max-width: 0;

  -webkit-transition: opacity 0.25s, max-width 0.45s; 
  -moz-transition: opacity 0.25s, max-width 0.45s;
  -o-transition: opacity 0.25s, max-width 0.45s;
  transition: opacity 0.25s, max-width 0.45s; /* Duration fixed since we animate additional hidden width */
}

.has-spinner.active {
  cursor:progress;
}

.has-spinner.active .spinner {
  opacity: 1;
  max-width: 50px; /* More than it will ever come, notice that this affects on animation duration */
}
</style>