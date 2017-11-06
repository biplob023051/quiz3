<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="radio">
            <label>
                <input type="radio" class="form-input tick-mark" <?php if ($choice->text == $choice->given_answer) echo 'checked'; ?> value="<?= htmlentities($choice->text) ?>" name="data[Answer][<?= $choice->number ?>][text]" />
                <?= $choice->text; ?>
            </label>
        </div>
    </div>
</div>