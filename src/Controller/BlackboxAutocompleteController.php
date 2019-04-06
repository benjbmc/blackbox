<?php

namespace Drupal\blackbox\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class BlackboxAutocompleteController.
 */
class BlackboxAutocompleteController extends ControllerBase {


  /**
   * @param $name
   *
   * @return array
   */
  public function blackboxAutocompleteNodeTitle($key = '') {
    $matches = [];

    $query = db_select('node', 'n');
    $result = $query
      ->fields('n', ['title', 'nid'])
      ->condition('n.title', '%' . strtoupper(db_like($key)) . '%', 'LIKE')
      ->condition('status', '1')
      ->distinct('n.nid')
      ->range(0, 5)
      ->orderBy('n.title', 'ASC')
      ->execute();

    // Generation of results list to send
    foreach ($result as $record) {
      $matches[$record->title . ' (' . $record->nid . ')'] = check_plain($record->title);
    }

    // Convert results to JSON, then exit to avoid to return something to the theme
    drupal_json_output($matches);
    exit();
  }

}
