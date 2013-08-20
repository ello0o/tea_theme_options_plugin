<!-- Content radio <?php echo $id ?> -->
<div id="<?php echo $id ?>_radio_content" class="stuffbox">
    <h3>
        <label><?php echo $title ?></label>
    </h3>

    <div class="inside radio">
        <fieldset>
            <?php
                foreach ($options as $option):
                    if (empty($option[0]))
                    {
                        continue;
                    }

                    $selected = $option[0] == $val ? true : false;
                    $for = $id . '_' . $option[0];
                ?>
                <p>
                    <label for="<?php echo $for ?>" class="<?php echo $selected ? 'selected' : '' ?>">
                        <input type="radio" name="<?php echo $id ?>" id="<?php echo $for ?>" value="<?php echo $option[0] ?>" <?php echo $selected ? 'checked="checked" ' : '' ?> />
                        <?php echo $option[1] ?>
                    </label>
                </p>
            <?php endforeach ?>
        </fieldset>

        <p><?php echo $description ?></p>
    </div>
</div>
<!-- /Content radio <?php echo $id ?> -->