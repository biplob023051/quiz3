<div class="row">
    <div class="col-xs-6 col-md-6">
        <div class="checkbox">
            <label>
                <input type="checkbox" value="" name="choices[]" />
                <?php echo $choice->text ?>
            </label>
        </div>
    </div>
    <?php if (empty($class_preview)) : ?>
        <div class="col-xs-6 col-md-6">
            <?php echo $choice->points; ?>
        </div>
    <?php endif; ?>
</div>