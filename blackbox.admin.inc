<?php

/**
 * @file
 * Administration settings page for entity iframe.
 */

/**
 * Implements hook_admin_settings().
 */
function blackboxadmin_settings() {
    $form = array();

    $form['blackbox_content'] = array(
        '#title' => t('Content to display'),
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        '#required' => TRUE,
    );

    // List of all entities
    $entities = _blackbox_entities_list();
    $form['blackbox_content']['blackbox_entities'] = array(
        '#type' => 'select',
        '#title' => t('Choose an existing entities'),
        '#multiple' => FALSE,
        '#options' => $entities,
        '#default_value' => get_value('content'),
    );

    $form['blackbox_content']['blackbox_select'] = array(
        '#type' => 'checkbox',
        '#title' => t('Select an entity by using autocompletion'),
    );

    $form['blackbox_content']['blackbox_search'] = array(
        '#type' => 'textfield',
        '#description' => t('Start taping something...'),
        '#autocomplete_path' => 'blackbox/autocomplete/node_title',
        '#states' => array(
            'visible' => array(
                array(':input[name="blackbox_select"]' => array('checked' => TRUE)),
            ),
        ),
    );

    $form['blackbox_duration'] = array(
        '#title' => t('Duration before apparition'),
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        '#required' => TRUE,
    );

    $form['blackbox_duration']['blackbox_durationhours'] = array(
        '#type' => 'select',
        '#multiple' => FALSE,
        '#options' => select_duration('hours'),
        '#field_suffix' => t('hours'),
        '#default_value' => get_value('hours'),
    );

    $form['blackbox_duration']['blackbox_durationminutes'] = array(
        '#type' => 'select',
        '#multiple' => FALSE,
        '#options' => select_duration('minutes'),
        '#field_suffix' => t('minutes'),
        '#default_value' => get_value('minutes'),
    );

    $form['blackbox_duration']['blackbox_durationseconds'] = array(
        '#type' => 'select',
        '#multiple' => FALSE,
        '#options' => select_duration('seconds'),
        '#field_suffix' => t('seconds'),
        '#default_value' => get_value('seconds'),
    );

    $form['blackbox_size'] = array(
        '#type' => 'checkbox',
        '#title' => t('Change the default size values (400px x 400px)'),
        '#default_value' => get_value('size'),
    );

    $form['blackbox_width'] = array(
        '#type' => 'textfield',
        '#title' => t('Width'),
        '#size' => 8,
        '#maxlength' => 5,
        '#field_suffix' => 'px',
        '#default_value' => get_value('width'),
        '#states' => array(
            'visible' => array(
                array(':input[name="blackbox_size"]' => array('checked' => TRUE)),
            ),
        ),
    );

    $form['blackbox_height'] = array(
        '#type' => 'textfield',
        '#title' => t('Height'),
        '#size' => 8,
        '#maxlength' => 5,
        '#field_suffix' => 'px',
        '#default_value' => get_value('height'),
        '#states' => array(
            'visible' => array(
                array(':input[name="blackbox_size"]' => array('checked' => TRUE)),
            ),
        ),
    );

    $form['#validate'][] = 'blackbox_admin_settings_validate';
    $form['#submit'][] = 'blackbox_admin_settings_submit';
    return system_settings_form($form);
}

/**
 * Form callback for admin_settings.
 */
function blackbox_admin_settings_validate($form, &$form_state) {
    // Check if there is a content
    if (empty($form_state['values']['blackbox_entities']) && empty($form_state['values']['blackbox_search'])) {
        form_set_error('blackbox_content', t('Please choose a content'));
    }
    // Check if duration is not 0
    if ($form_state['values']['blackbox_durationhours'] == 0 && $form_state['values']['blackbox_durationminutes'] == 0 && $form_state['values']['blackbox_durationseconds'] == 0) {
        form_set_error('blackbox_duration', t('Please indicate a duration'));
    }
    // Check if size is greater than 0
    if ($form_state['values']['blackbox_size'] == TRUE && ($form_state['values']['blackbox_width'] <= 0 || $form_state['values']['blackbox_height'] <= 0)) {
        form_set_error('blackbox_size', t('Please indicate a value greater than 0'));
    }
}

/**
 * Form callback for admin_settings.
 */
function blackbox_admin_settings_submit($form, &$form_state) {
    $values = array();
    if (!empty($form_state['values']['blackbox_entities'])) {  // liste déroulante
        $values['content'] = $form_state['values']['blackbox_entities'];
    }
    if (!empty($form_state['values']['blackbox_search'])) {  // autocomplete
        $nid = str_replace(')', '', strrchr($form_state['values']['blackbox_search'], '('));
        $nid = str_replace('(', '', $nid);
        $values['content'] = $nid;
    }
    if ($form_state['values']['blackbox_size'] == TRUE) {
        $values['size'] = ($form_state['values']['blackbox_width'] == BLACKBOX_WIDTH && $form_state['values']['blackbox_height'] == BLACKBOX_HEIGHT) ? FALSE : TRUE;
        $values['width'] = ($form_state['values']['blackbox_width'] > 0) ? $form_state['values']['blackbox_width'] : BLACKBOX_WIDTH;
        $values['height'] = ($form_state['values']['blackbox_height'] > 0) ? $form_state['values']['blackbox_height'] : BLACKBOX_HEIGHT;
    } else {
        $values['size'] = FALSE;
        $values['width'] = BLACKBOX_WIDTH;
        $values['height'] = BLACKBOX_HEIGHT;
    }
    $values['hours'] = $form_state['values']['blackbox_durationhours'];
    $values['minutes'] = $form_state['values']['blackbox_durationminutes'];
    $values['seconds'] = $form_state['values']['blackbox_durationseconds'];

    variable_set('blackbox', $values);
}

/**
 * Function to get saved values (default values for input)
 * @param type $field
 * @return type
 */
function get_value($field = '') {
    $value = NULL;
    $blackbox_array = variable_get('blackbox');
    if ($field != '' && is_array($blackbox_array)) {
        switch ($field):
            case 'content':
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
