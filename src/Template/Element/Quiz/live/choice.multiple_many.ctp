<?php 
	$disabled_print = '';
	$disabled_class = '';
	$checked = '';
	if ((is_array($choice->given_answer) && in_array($choice->text, $choice->given_answer)) || ($choice->text == $choice->given_answer)) {
		$checked = 'checked';
	} else { 
		if (!empty($disabled)) {
			$disabled_class = 'max_allowed_disabled';
			$disabled_print = 'disabled'; 
		}
	} 
?>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="checkbox">
            <label>
                <input type="checkbox" class="form-input tick-mark <?php echo $disabled_class; ?>" <?php echo $checked . ' ' . $disabled_print; ?> value="<?php echo $choice->text ?>" name="data[Answer][<?php echo $choice->number ?>][text][]" />
                <?php echo $choice->text ?>
            </label>
        </div>
    </div>
</div>