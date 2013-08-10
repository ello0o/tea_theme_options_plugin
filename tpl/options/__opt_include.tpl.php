<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="include" />
<h4><?php _e('Include PHP file', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][title]" value="<?php echo isset($val['title']) ? $val['title'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Path to your PHP file', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][file]" value="<?php echo isset($val['file']) ? $val['file'] : '' ?>" class="code" />
</label>