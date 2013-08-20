<!-- Content checkbox <?php echo $id ?> -->
<div id="<?php echo $id ?>_checkbox_content" class="checkboxes stuffbox">
    <h3>
        <label><?php echo $title ?></label>
        <?php if (2 < count($options)): ?>
            <label for="checkall" class="checkall">
                <?php _e('Un/select all options') ?>
                <input type="checkbox" id="checkall" />
            </label>
        <?php endif ?>
    </h3>

    <div class="inside checkbox">
        <fieldset>
            <?php
                foreach ($options as $option):
                    if (empty($option[0]))
                    {
                        continue;
                    }

                    $selected = is_array($vals) && in_array($option[0], $vals) ? true : false;
                    $for = $id . '_' . $option[0];
                ?>
                <p>
                    <label for="<?php echo $for ?>" class="<?php echo $selected ? 'selected' : '' ?>">
                        <input type="hidden" name="<?php echo $id ?>__checkbox[<?php echo $option[0] ?>]" value="0" />
                        <input type="checkbox" name="<?php echo $id ?>[<?php echo $option[0] ?>]" id="<?php echo $for ?>" value="<?php echo $option[0] ?>" <?php echo $selected ? 'checked="checked" ' : '' ?> />
                        <?php echo $option[1] ?>
                    </label>
                </p>
            <?php endforeach ?>
        </fieldset>

        <p><?php echo $description ?></p>
    </div>
</div>
<!-- /Content checkbox <?php echo $id ?> -->