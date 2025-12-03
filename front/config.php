<?php
include '../../../inc/includes.php';
Session::checkRight('config', READ);
$defaults = [
   'skip_when_user_empty' => 1,
   'trigger_when_group_empty' => 1,
   'trigger_when_user_changed' => 1,
   'skip_when_both_empty' => 1,
   'enabled_itemtypes' => ['Computer','Monitor','Peripheral','Phone','Printer','NetworkEquipment'],
   'enable_logging' => 1
];
$config = [];
if (class_exists('Config')) {
   $config = Config::getConfigurationValues('plugin:autogroupfromuser');
}
foreach ($defaults as $k => $v) {
   if (!isset($config[$k])) { $config[$k] = $v; }
}
// normalize enabled_itemtypes to array for rendering
$enabled_types = [];
if (isset($config['enabled_itemtypes'])) {
   if (is_array($config['enabled_itemtypes'])) {
      $enabled_types = $config['enabled_itemtypes'];
   } else {
      $enabled_types = array_filter(array_map('trim', explode(',', (string)$config['enabled_itemtypes'])));
   }
} else {
   $enabled_types = $defaults['enabled_itemtypes'];
}
if (isset($_POST['update'])) {
   Session::checkRight('config', UPDATE);
   $values = [
      'skip_when_user_empty' => isset($_POST['skip_when_user_empty']) ? 1 : 0,
      'trigger_when_group_empty' => isset($_POST['trigger_when_group_empty']) ? 1 : 0,
      'trigger_when_user_changed' => isset($_POST['trigger_when_user_changed']) ? 1 : 0,
      'skip_when_both_empty' => isset($_POST['skip_when_both_empty']) ? 1 : 0,
      'enabled_itemtypes' => isset($_POST['enabled_itemtypes']) && is_array($_POST['enabled_itemtypes']) ? implode(',', $_POST['enabled_itemtypes']) : '',
      'enable_logging' => isset($_POST['enable_logging']) ? 1 : 0
   ];
   if (class_exists('Config')) {
      Config::setConfigurationValues('plugin:autogroupfromuser', $values);
   }
   Html::redirect($CFG_GLPI['root_doc'].'/plugins/autogroupfromuser/front/config.php');
}
Html::header(__('Plugin configuration'), $_SERVER['PHP_SELF'], 'config', 'plugins');
echo '<form method="post">';
echo '<input type="hidden" name="_glpi_csrf_token" value="'.Session::getNewCSRFToken().'">';
echo '<table class="tab_cadre">';
echo '<tr><th>'.__('Rules').'</th></tr>';
echo '<tr><td><label><input type="checkbox" name="skip_when_user_empty"'.($config['skip_when_user_empty']?' checked':'').'>'.__('Skip when user empty').'</label></td></tr>';
echo '<tr><td><label><input type="checkbox" name="trigger_when_group_empty"'.($config['trigger_when_group_empty']?' checked':'').'>'.__('Trigger when group empty').'</label></td></tr>';
echo '<tr><td><label><input type="checkbox" name="trigger_when_user_changed"'.($config['trigger_when_user_changed']?' checked':'').'>'.__('Trigger when user changed').'</label></td></tr>';
echo '<tr><td><label><input type="checkbox" name="skip_when_both_empty"'.($config['skip_when_both_empty']?' checked':'').'>'.__('Skip when both user and group empty').'</label></td></tr>';
echo '<tr><th>'.__('Apply to assets').'</th></tr>';
$itemtypes = [
   'Computer' => __('Computer'),
   'Monitor' => __('Monitor'),
   'Peripheral' => __('Peripheral'),
   'Phone' => __('Phone'),
   'Printer' => __('Printer'),
   'NetworkEquipment' => __('Network equipment')
];
foreach ($itemtypes as $type => $label) {
   $checked = in_array($type, (array)$enabled_types) ? ' checked' : '';
   echo '<tr><td><label><input type="checkbox" name="enabled_itemtypes[]" value="'.$type.'"'.$checked.'>'.$label.'</label></td></tr>';
}
echo '<tr><th>'.__('Logging').'</th></tr>';
echo '<tr><td><label><input type="checkbox" name="enable_logging"'.($config['enable_logging']?' checked':'').'>'.__('Enable logging').'</label></td></tr>';
echo '<tr class="tab_bg_2"><td class="center">';
echo '<button type="submit" name="update" class="submit">'.__('Save').'</button>';
echo '</td></tr>';
echo '</table>';
echo '</form>';
Html::footer();
