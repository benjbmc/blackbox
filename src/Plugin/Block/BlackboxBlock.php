<?php

namespace Drupal\blackbox\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BlackboxBlock' block.
 *
 * @Block(
 *  id = "blackbox_block",
 *  admin_label = @Translation("Blackbox block"),
 * )
 */
class BlackboxBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['blackbox_block']['#markup'] = 'Implement BlackboxBlock.';

    return $build;
  }

}
