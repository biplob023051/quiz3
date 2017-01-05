<div class="row">
    <div class="col-xs-6 col-md-6">
        <div class="radio">
            <label>
                <input type="radio" disabled value="<?php echo $choice->id ?>" name="questions[<?php echo $choice->question_id ?>][correct_choice]" />
                <?php echo $choice->text ?>
            </label>
        </div>
    </div>
    <div class="col-xs-6 col-md-6">
        <?php echo $choice->points; ?>
    </div>
</div>