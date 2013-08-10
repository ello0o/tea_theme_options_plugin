<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="select" />
<h4><?php _e('Select', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][title]" value="<?php echo isset($val['title']) ? $val['title'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][description]" class="code"><?php echo isset($val['description']) ? $val['description'] : '' ?></textarea>
</label>

<label class="label-edit-content">
    <span><?php _e('Default value', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][std]" value="<?php echo isset($val['std']) ? $val['std'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Options', TTO_I18N) ?></span>
</label>

<div class="label-edit-options" style="display:block;">
    <?php
        $optnum = 0;

        if (isset($val['options']) && !empty($val['options'])):
    ?>
        <?php
            foreach ($val['options'] as $k => $opts):
                if (empty($opts[0]))
                {
                    continue;
                }

                $vallabel = !empty($opts[1]) ? $opts[1] : $opts[0];
                $valvalue = $opts[0];
        ?>
            <label class="label-second">
                <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $k ?>][]" value="<?php echo $valvalue ?>" class="code" placeholder="<?php _e('Your value option', TTO_I18N) ?>" />
            </label>
            <label class="label-second">
                <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $k ?>][]" value="<?php echo $vallabel ?>" class="code" placeholder="<?php _e('Your label option', TTO_I18N) ?>" />
            </label>
            <div class="clearfix"></div>
            <?php $optnum++; ?>
        <?php endforeach ?>
    <?php endif ?>
    <label class="label-second">
        <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $optnum ?>][]" value="" class="code" placeholder="<?php _e('Your value option', TTO_I18N) ?>" />
    </label>
    <label class="label-second">
        <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $optnum ?>][]" value="" class="code" placeholder="<?php _e('Your label option', TTO_I18N) ?>" />
    </label>
    <div class="clearfix"></div>
</div>