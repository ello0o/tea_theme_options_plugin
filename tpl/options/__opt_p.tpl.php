<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="p" />
<h4><?php _e('Paragraph', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Content', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][content]" class="code"><?php echo isset($val['content']) ? $val['content'] : '' ?></textarea>
</label>