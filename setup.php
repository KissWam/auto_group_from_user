<?php
require_once __DIR__ . '/hook.php';

define('PLUGIN_AUTOGROUPFROMUSER_VERSION', '1.0.2');

function plugin_version_autogroupfromuser() {
   return [
       'name' => 'Auto Group from User',
       'version' => PLUGIN_AUTOGROUPFROMUSER_VERSION,
       'author' => 'KISSWAM',
       'license' => 'GPLv2+',
       'homepage' => 'https://github.com/KissWam/auto_group_from_user',
       'requirements' => [
           'glpi' => [
               'min' => '10.0',
               'max' => '12.0.99'
           ]
       ]
   ];
}

function plugin_autogroupfromuser_check_prerequisites() {
   if (defined('GLPI_VERSION') && version_compare(GLPI_VERSION, '10.0', '>=')) {
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

function plugin_autogroupfromuser_check_config($verbose = false) {
   return true;
}

function plugin_autogroupfromuser_install() {
   return true;
}

function plugin_autogroupfromuser_uninstall() {
   return true;
}
