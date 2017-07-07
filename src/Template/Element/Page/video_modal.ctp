<div class="modal fade" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <?= $home_video['title']; ?>
            </div>
            <div class="modal-body"> 
                 <iframe width="100%" height="315" src="" frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="close"><?= __('CLOSE'); ?></button>
            </div>
        </div>
    </div>
</div>