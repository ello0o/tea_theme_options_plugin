<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="include" />
<h4><?php _e('Include PHP file', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Path to your PHP file', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][file]" value="<?php echo $file ?>" class="code" />
</label>