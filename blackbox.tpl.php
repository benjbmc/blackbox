<?php
/**
 * @file
 * Template file for Blackbox content.
 *
 * Available variables:
 * - $_SESSION['blackbox']['content']: The entity ID to show in the box.
 * - $_SESSION['blackbox']['width']: The width of the box.
 * - $_SESSION['blackbox']['height']: The height of the box.
 */
?>

<?php if (isset($_SESSION['blackbox']['content'])): ?>

    <div id="blackbox">

        <a id="blackbox_call" class="colorbox-node" href="<?php print url('node/' . $_SESSION['blackbox']['content'], array('query' => array('width' => $_SESSION['blackbox']['width'], 'height' => $_SESSION['blackbox']['height']))); ?>"><i class="fa fa-phone-square fa-4x"></i></a>

    </div>

<?php endif; ?>
