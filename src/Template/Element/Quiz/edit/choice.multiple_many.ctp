<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="checkbox">
            <label>
                <input type="checkbox" disabled value="<?php echo $choice->id ?>" name="questions[<?php echo $choice->question_id ?>][correct_choice]" />
                <?php echo $choice->text ?>
            </label>
        </div>
    </div>
</div>