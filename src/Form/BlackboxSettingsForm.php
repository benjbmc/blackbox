<?php

namespace Drupal\blackbox\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigForm.
 */
class BlackboxSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'blackbox.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('blackbox.config');

    $form['blackbox_content'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Content to display'),
    ];

    // List of all entities
    $entities = $this->_blackbox_entities_list();
    $form['blackbox_content']['blackbox_entities'] = [
      '#type' => 'select',
      '#title' => $this->t('Choose an existing entities'),
      '#options' => $entities,
      '#multiple' => FALSE,
      '#default_value' => $config->get('blackbox_entities'),
    ];

    $form['blackbox_content']['blackbox_select'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Select an entity by using autocompletion'),
      '#default_value' => $config->get('blackbox_select'),
    ];

    $form['blackbox_content']['blackbox_search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Start taping something...'),
      '#autocomplete_path' => 'blackbox/autocomplete/node_title',
      '#states' => [
        'visible' => [
          [
            ':input[name="blackbox_select"]' => [
              'checked' => TRUE
            ]
          ]
        ]
      ],
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('blackbox_search'),
    ];


    $form['blackbox_duration'] = [
      '#title' => t('Duration before apparition'),
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
      '#required' => TRUE,
    ];

    $form['blackbox_duration']['blackbox_durationseconds'] = [
      '#type' => 'select',
      '#multiple' => FALSE,
      '#options' => $this->select_duration('seconds'),
      '#field_suffix' => t('seconds'),
      '#default_value' => $this->_blackbox_get_value('seconds'),
    ];

    $form['blackbox_duration']['blackbox_durationminutes'] = [
      '#type' => 'select',
      '#multiple' => FALSE,
      '#options' => $this->select_duration('minutes'),
      '#field_suffix' => t('minutes'),
      '#default_value' => $this->_blackbox_get_value('minutes'),
    ];

    $form['blackbox_duration']['blackbox_durationhours'] = [
      '#type' => 'select',
      '#multiple' => FALSE,
      '#options' => $this->select_duration('hours'),
      '#field_suffix' => t('hours'),
      '#default_value' => $this->_blackbox_get_value('hours'),
    ];

    $form['blackbox_show_link'] = [
      '#type' => 'checkbox',
      '#title' => t('Show the link to call manually the popup (phone picto in position fixed on the right)'),
      '#default_value' => $this->_blackbox_get_value('show_link'),
    ];

    $form['blackbox_size'] = [
      '#type' => 'checkbox',
      '#title' => t('Change the default size values (400px x 400px)'),
      '#default_value' => $this->_blackbox_get_value('size'),
    ];

    $form['blackbox_width'] = [
      '#type' => 'textfield',
      '#title' => t('Width'),
      '#size' => 8,
      '#maxlength' => 5,
      '#field_suffix' => 'px',
      '#default_value' => $this->_blackbox_get_value('width'),
      '#states' => [
        'visible' => [
          [
            ':input[name="blackbox_size"]' => [
              'checked' => TRUE
            ]
          ]
        ]
      ],
    ];

    $form['blackbox_height'] = [
      '#type' => 'textfield',
      '#title' => t('Height'),
      '#size' => 8,
      '#maxlength' => 5,
      '#field_suffix' => 'px',
      '#default_value' => $this->_blackbox_get_value('height'),
      '#states' => [
        'visible' => [
          [
            ':input[name="blackbox_size"]' => [
              'checked' => TRUE
            ]
          ]
        ]
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('blackbox.config')
      ->set('blackbox_content', $form_state->getValue('blackbox_content'))
      ->set('blackbox_entities', $form_state->getValue('blackbox_entities'))
      ->set('blackbox_select', $form_state->getValue('blackbox_select'))
      ->set('blackbox_search', $form_state->getValue('blackbox_search'))
      ->save();
  }

  /**
   * Helper function to return all entities as an array.
   */
  function _blackbox_entities_list($entity_id = '') {
    $types = \Drupal::entityTypeManager()->getDefinitions();
    $options = array(0 => t('- None -'));
    $types_ok = array('node', 'taxonomy_term');
    foreach ($types as $key => $type) {
      if ($type->get('id') && in_array($type->get('id'), $types_ok)) {

        $query =  \Drupal::entityQuery($type->get('id'));
        $query->condition('status', \Drupal\node\NodeInterface::PUBLISHED)
          ->addMetaData('account', \Drupal::entityTypeManager()->getStorage('user')->load(1)); // Run the query as user 1.
        $result = $query->execute();

        if (!empty($result)) {
          $items = \Drupal\node\Entity\Node::loadMultiple($result);
          foreach ($items as $item) {
            if ($entity_id == '') {  // full list
              $options[$item->nid->value] = $item->title->value . ' - (' . $item->type->value . ')';
            } else {  // only one entity
              if ($entity_id == $item->nid->value)
                $options[$item->nid->value] = $item->title->value . ' - (' . $item->type->value . ')';
            }
          }
        }
      }
    }
    return $options;
  }

  /**
   * Function to select form duration
   * @param type $unit
   * @return int
   */
  public function select_duration($unit) {
    $options = array();
    switch ($unit):
      case 'hours':
        for ($i = 0; $i <= 24; $i++) {
          $options[$i] = $i;
        }
        break;
      case 'minutes':
        for ($i = 0; $i < 60; $i++) {
          $options[$i] = $i;
        }
        break;
      case 'seconds':
        for ($i = 5; $i < 60; $i += 5) {
          $options[$i] = $i;
        }
        break;
    endswitch;
    return $options;
  }


  /**
   * Function to get saved values (default values for input)
   * @param type $field
   * @return type
   */
  public function _blackbox_get_value($field = '') {
    $value = NULL;
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $blackbox_array = variable_get('blackbox');

    if ($field != '' && is_array($blackbox_array)) {
      switch ($field):
        case 'content':
        case 'show_link':
          $value = (isset($blackbox_array[$field])) ? $blackbox_array[$field] : 0;
          break;
        case 'hours':
        case 'minutes':
        case 'seconds':
          $value = (isset($blackbox_array[$field])) ? array($blackbox_array[$field], $blackbox_array[$field]) : 0;
          break;
        case 'size':
          $value = (isset($blackbox_array[$field])) ? $blackbox_array[$field] : BLACKBOX_SIZE;
          break;
        case 'width':
          $value = (isset($blackbox_array[$field])) ? $blackbox_array[$field] : BLACKBOX_WIDTH;
          break;
        case 'height':
          $value = (isset($blackbox_array[$field])) ? $blackbox_array[$field] : BLACKBOX_HEIGHT;
          break;
        default;
          break;
      endswitch;
    }
    return $value;
  }


}
