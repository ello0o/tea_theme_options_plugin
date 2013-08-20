<?php if (isset($id)): ?><span class="content-id"><?php echo $id ?></span><?php endif ?>
<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="text" />
<h4><?php _e('Text', TTO_I18N) ?></h4>

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
    <input type="text" name="tea_add_contents[<?php echo $number ?>][std]" value="<?php echo $std ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Max length', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][maxlength]" value="<?php echo $maxlength ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Content', TTO_I18N) ?></span>
    <select name="tea_add_contents[<?php echo $number ?>][options][type]" class="code select-options">
        <?php
            foreach ($texts as $key => $itm):
                $selected = $key == $options['type'] ? 'selected="selected"' : '';
                $class = 'number' == $key || 'range' == $key ? 'class="display-options"' : '';
        ?>
            <option value="<?php echo $key ?>" <?php echo $class ?> <?php echo $selected ?>><?php echo $itm ?></option>
        <?php endforeach ?>
    </select>
</label>

<div class="label-edit-options">
    <label class="label-edit-content">
        <?php _e('Options', TTO_I18N) ?>
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Min value', TTO_I18N) ?></span>
        <input type="number" name="tea_add_contents[<?php echo $number ?>][options][min]" value="<?php echo $options['min'] ?>" class="code" />
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Max value', TTO_I18N) ?></span>
        <input type="number" name="tea_add_contents[<?php echo $number ?>][options][max]" value="<?php echo $options['max'] ?>" class="code" />
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Step', TTO_I18N) ?></span>
        <input type="number" name="tea_add_contents[<?php echo $number ?>][options][step]" value="<?php echo $options['step'] ?>" class="code" />
    </label>
    <div class="clearfix"></div>
</div>