<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="network" />
<h4><?php _e('FlickR', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Choose all your networks you want to connect to.', TTO_I18N) ?></span>
</label>

<label class="label-edit-content">
    <span><?php _e('Networks', TTO_I18N) ?></span>
    <select name="tea_add_contents[<?php echo $number ?>][std][]" multiple="multiple" size="5" class="code">
        <?php
            foreach ($networks as $key => $itm):
                $selected = is_array($std) && in_array($key, $std) ? true : false;
        ?>
            <option value="<?php echo $key ?>" <?php echo $selected ? 'selected="selected"' : '' ?>><?php echo $itm ?></option>
        <?php endforeach ?>
    </select>
    <p><?php echo __('Press the <code>CTRL</code> or <code>CMD</code> button to select more than one option.', TTO_I18N) ?></p>
</label>