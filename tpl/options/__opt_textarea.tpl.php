<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="textarea" />
<h4><?php _e('Textarea', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][title]" value="<?php echo isset($val['title']) ? $val['title'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][description]" class="code"><?php echo isset($val['description']) ? $val['description'] : '' ?></textarea>
</label>

<label class="label-edit-content">
    <span><?php _e('Placeholder', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][placeholder]" value="<?php echo isset($val['placeholder']) ? $val['placeholder'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Default value', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][std]" class="code"><?php echo isset($val['std']) ? $val['std'] : '' ?></textarea>
</label>