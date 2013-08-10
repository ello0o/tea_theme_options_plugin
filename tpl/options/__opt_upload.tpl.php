<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="upload" />
<h4><?php _e('Upload', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][title]" value="<?php echo isset($val['title']) ? $val['title'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][description]" class="code"><?php echo isset($val['description']) ? $val['description'] : '' ?></textarea>
</label>

<label class="label-edit-content">
    <input type="checkbox" name="tea_add_contents[<?php echo $num ?>][multiple]" value="1" <?php echo isset($val['multiple']) ? 'checked="checked"' : '' ?> />
    <?php _e('Enable multi-upload?', TTO_I18N) ?>
</label>

<label class="label-edit-content">
    <span><?php _e('URL of your default image', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][std]" value="<?php echo isset($val['std']) ? $val['std'] : '' ?>" class="code" />
</label>