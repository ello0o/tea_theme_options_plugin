<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="wordpress" />
<h4><?php _e('Wordpress', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][title]" value="<?php echo isset($val['title']) ? $val['title'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][description]" class="code"><?php echo isset($val['description']) ? $val['description'] : '' ?></textarea>
</label>

<label class="label-edit-content">
    <span><?php _e('Content', TTO_I18N) ?></span>
    <select name="tea_add_contents[<?php echo $num ?>][mode]" class="code">
        <?php
            $wp = isset($val['mode']) ? $val['mode'] : '';
            foreach ($wordpress as $key => $itm):
                $selected = $wp == $key ? 'selected="selected"' : '';
        ?>
            <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $itm ?></option>
        <?php endforeach ?>
    </select>
</label>