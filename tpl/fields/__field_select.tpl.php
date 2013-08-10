<!-- Content select <?php echo $id ?> -->
<div id="<?php echo $id ?>_select_content" class="stuffbox">
    <h3>
        <label for="<?php echo $id ?>"><?php echo $title ?></label>
    </h3>

    <div class="inside select">
        <select name="<?php echo $id ?>" id="<?php echo $id ?>">
            <?php
                foreach ($options as $option):
                    if (empty($option[0]))
                    {
                        continue;
                    }

                    $selected = $option[0] == $val ? true : false;
            ?>
                <option value="<?php echo $option[0] ?>" <?php echo $selected ? 'selected="selected" ' : '' ?>><?php echo $option[1] ?></option>
            <?php endforeach ?>
        </select>

        <p><?php echo $description ?></p>
    </div>
</div>
<!-- /Content select <?php echo $id ?> -->