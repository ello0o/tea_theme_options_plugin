<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="list" />
<h4><?php _e('List', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Contents', TTO_I18N) ?></span>
</label>

<div class="label-edit-options" style="display:block;">
    <?php
        $optnum = 0;

        if (isset($val['contents']) && !empty($val['contents'])):
    ?>
        <?php
            foreach ($val['contents'] as $k => $opts):
                if (empty($opts))
                {
                    continue;
                }
        ?>
            <label class="label-edit-content">
                <input type="text" name="tea_add_contents[<?php echo $num ?>][contents][<?php echo $k ?>]" value="<?php echo $opts ?>" class="code" placeholder="<?php _e('Your text', TTO_I18N) ?>" />
            </label>
            <?php $optnum++; ?>
        <?php endforeach ?>
    <?php endif ?>
    <label class="label-edit-content">
        <input type="text" name="tea_add_contents[<?php echo $num ?>][contents][<?php echo $optnum ?>]" value="" class="code" placeholder="<?php _e('Your text', TTO_I18N) ?>" />
    </label>
    <div class="clearfix"></div>
</div>