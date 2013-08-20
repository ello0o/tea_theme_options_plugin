<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="p" />
<h4><?php _e('Paragraph', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Content', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][content]" class="code"><?php echo $content ?></textarea>
</label>