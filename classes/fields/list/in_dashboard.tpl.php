<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="list" />
<h4><?php _e('List', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Contents', TTO_I18N) ?></span>
</label>

<div class="label-edit-options" style="display:block;">
    <div class="label-model">
        <label class="label-option"></label>
        <label class="label-edit-content">
            <input type="text" name="tea_add_contents[<?php echo $number ?>][contents][__OPTNUM__]" value="" class="code" placeholder="<?php _e('Your text', TTO_I18N) ?>" />
        </label>
        <div class="clearfix"></div>
    </div>

    <?php
        $optnum = 0;

        if (!empty($contents)):
    ?>
        <?php
            foreach ($contents as $k => $opts):
                if (empty($opts))
                {
                    continue;
                }
        ?>
            <label class="label-option"></label>
            <label class="label-edit-content">
                <input type="text" name="tea_add_contents[<?php echo $number ?>][contents][<?php echo $k ?>]" value="<?php echo $opts ?>" class="code" placeholder="<?php _e('Your text', TTO_I18N) ?>" />
            </label>
            <div class="clearfix"></div>
            <?php $optnum++; ?>
        <?php endforeach ?>
    <?php endif ?>

    <input type="submit" name="add_option" class="button-secondary label-button" value="<?php _e('Add a new item', TTO_I18N) ?>" />
</div>