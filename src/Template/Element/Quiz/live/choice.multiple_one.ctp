<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="radio">
            <label>
                <input type="radio" class="form-input tick-mark" <?php if ($choice->text == $choice->given_answer) echo 'checked'; ?> value="<?php echo $choice->text ?>" name="data[Answer][<?php echo $choice->number ?>][text]" />
                <?php echo $choice->text; ?>
            </label>
        </div>
    </div>
</div>