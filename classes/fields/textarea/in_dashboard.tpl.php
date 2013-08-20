<?php if (isset($id)): ?><span class="content-id"><?php echo $id ?></span><?php endif ?>
<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="textarea" />
<h4><?php _e('Textarea', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][description]" class="code"><?php echo $description ?></textarea>
</label>

<label class="label-edit-content">
    <span><?php _e('Placeholder', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][placeholder]" value="<?php echo $placeholder ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Default value', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][std]" class="code"><?php echo $std ?></textarea>
</label>