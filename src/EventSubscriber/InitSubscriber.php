<?php /**
 * @file
 * Contains \Drupal\blackbox\EventSubscriber\InitSubscriber.
 */

namespace Drupal\blackbox\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InitSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent() {
    $user = \Drupal::currentUser();
    if (!isset($_SESSION)) {
      drupal_session_start();
    }

    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $values = (variable_get('blackbox')) ? variable_get('blackbox') : NULL;

    if ($user->uid == 0 && !empty($values)) {
      $str_time = time();
      $show_time = $str_time + $values['hours'] * 60 * 60 + $values['minutes'] * 60 + $values['seconds'];
      $current_nid = (arg(0) == 'node' && is_numeric(arg(1))) ? arg(1) : '';
      if (!isset($_SESSION['blackbox'])) {
        // if session exists, don't LOOP (popup may have already appeared)
        $_SESSION['blackbox'] = [
          'start' => $str_time,
          'show_time' => $show_time,
          'content' => $values['content'] = ($current_nid != $values['content']) ? $values['content'] : NULL,
          'width' => $values['width'],
          'height' => $values['height'],
          'show_link' => $values['show_link'],
        ];
      }
      else {
        $_SESSION['blackbox']['start'] = $str_time;
        $_SESSION['blackbox']['show_time'] = $show_time;
        $_SESSION['blackbox']['content'] = $values['content'] = ($current_nid != $values['content']) ? $values['content'] : NULL;
        $_SESSION['blackbox']['width'] = $values['width'];
        $_SESSION['blackbox']['height'] = $values['height'];
        $_SESSION['blackbox']['show_link'] = $values['show_link'];
      }
      // @FIXME
      // The Assets API has totally changed. CSS, JavaScript, and libraries are now
      // attached directly to render arrays using the #attached property.
      // 
      // 
      // @see https://www.drupal.org/node/2169605
      // @see https://www.drupal.org/node/2408597
      // drupal_add_js(array('blackbox' => array('start' => $str_time, 'showTime' => $show_time)), 'setting');

    }
  }

}
