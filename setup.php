<?php
require_once __DIR__ . '/hook.php';
function plugin_version_autogroupfromuser() {
   return [
       'name' => 'Auto Group from User',
       'version' => '1.0.1',
       'author' => 'KISSWAM',
       'license' => 'GPLv2+',
       'homepage' => '',
       'minGlpiVersion' => '11.0'
   ];
}
function plugin_autogroupfromuser_check_prerequisites() {
   if (defined('GLPI_VERSION') && version_compare(GLPI_VERSION, '11.0', '>=')) {
       return true;
   }
   return false;
}
function plugin_init_autogroupfromuser() {
   global $PLUGIN_HOOKS;
   $PLUGIN_HOOKS['csrf_compliant']['autogroupfromuser'] = true;
   $PLUGIN_HOOKS['pre_item_add']['autogroupfromuser'] = [
      'Computer' => 'plugin_autogroupfromuser_pre_item_add',
      'Monitor' => 'plugin_autogroupfromuser_pre_item_add',
      'Peripheral' => 'plugin_autogroupfromuser_pre_item_add',
      'Phone' => 'plugin_autogroupfromuser_pre_item_add',
      'Printer' => 'plugin_autogroupfromuser_pre_item_add',
      'NetworkEquipment' => 'plugin_autogroupfromuser_pre_item_add'
   ];
   $PLUGIN_HOOKS['pre_item_update']['autogroupfromuser'] = [
      'Computer' => 'plugin_autogroupfromuser_pre_item_update',
      'Monitor' => 'plugin_autogroupfromuser_pre_item_update',
      'Peripheral' => 'plugin_autogroupfromuser_pre_item_update',
      'Phone' => 'plugin_autogroupfromuser_pre_item_update',
      'Printer' => 'plugin_autogroupfromuser_pre_item_update',
      'NetworkEquipment' => 'plugin_autogroupfromuser_pre_item_update'
   ];
   $PLUGIN_HOOKS['config_page']['autogroupfromuser'] = 'front/config.php';
   autogroupfromuser_log('setup_init', ['ts' => date('Y-m-d H:i:s')]);
}
function plugin_autogroupfromuser_install() {
   return true;
}
function plugin_autogroupfromuser_uninstall() {
   return true;
}
