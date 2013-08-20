<?php if (isset($id)): ?><span class="content-id"><?php echo $id ?></span><?php endif ?>
<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="upload" />
<h4><?php _e('Upload', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][description]" class="code"><?php echo $description ?></textarea>
</label>

<label class="label-edit-content">
    <input type="checkbox" name="tea_add_contents[<?php echo $number ?>][multiple]" value="1" <?php echo $multiple ? 'checked="checked"' : '' ?> />
    <?php _e('Enable multi-upload?', TTO_I18N) ?>
</label>

<label class="label-edit-content">
    <span><?php _e('URL of your default image', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][std]" value="<?php echo $std ?>" class="code" />
</label>