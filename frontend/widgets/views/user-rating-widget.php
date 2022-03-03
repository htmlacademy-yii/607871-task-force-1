<?php
/**
 * * @var float $userRating
 */
?>
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <?php if ($i <= round($userRating)): ?>
            <span></span>
        <?php else: ?>
            <span class="star-disabled"></span>
        <?php endif; ?>
    <?php endfor; ?>
    <b><?= $userRating; ?></b>

