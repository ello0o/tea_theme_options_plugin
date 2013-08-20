<?php if (isset($id)): ?><span class="content-id"><?php echo $id ?></span><?php endif ?>
<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="wordpress" />
<h4><?php _e('Wordpress', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][description]" class="code"><?php echo $description ?></textarea>
</label>

<label class="label-edit-content">
    <span><?php _e('Content', TTO_I18N) ?></span>
    <select name="tea_add_contents[<?php echo $number ?>][mode]" class="code">
        <?php
            foreach ($wordpress as $key => $itm):
                $selected = $mode == $key ? 'selected="selected"' : '';
        ?>
            <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $itm ?></option>
        <?php endforeach ?>
    </select>
</label>

<label class="label-edit-content">
    <input type="checkbox" name="tea_add_contents[<?php echo $number ?>][multiple]" value="1" <?php echo $multiple ? 'checked="checked"' : '' ?> />
    <?php _e('Enable multiple selection?', TTO_I18N) ?>
</label>