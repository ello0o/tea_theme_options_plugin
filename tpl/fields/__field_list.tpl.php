<ul class="listing">
    <?php
        foreach ($li as $l):
            if (empty($l))
            {
                continue;
            }
    ?>
        <li><?php echo $l ?></li>
    <?php endforeach ?>
</ul>