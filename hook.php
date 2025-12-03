<?php
function autogroupfromuser_log($msg, array $ctx = []) {
    $cfg = autogroupfromuser_get_config();
    if (empty($cfg['enable_logging'])) { return; }
    $ts = date('c');
    $line = $ts . ' ' . $msg;
    foreach ($ctx as $k => $v) {
        $line .= ' ' . $k . '=' . $v;
    }
    Toolbox::logInFile('autogroupfromuser', $line);
}
function plugin_autogroupfromuser_pre_item_add(CommonDBTM $item) {
    return plugin_autogroupfromuser_apply($item, 'add');
}
function plugin_autogroupfromuser_pre_item_update(CommonDBTM $item) {
    return plugin_autogroupfromuser_apply($item, 'update');
}
function autogroupfromuser_supported_itemtypes() {
    return ['Computer','Monitor','Peripheral','Phone','Printer','NetworkEquipment'];
}
function autogroupfromuser_has_field(CommonDBTM $item, $field) {
    if (method_exists($item, 'isField')) {
        if ($item->isField($field)) { return true; }
    }
    if (isset($item->fields) && array_key_exists($field, $item->fields)) { return true; }
    if (isset($item->input) && array_key_exists($field, $item->input)) { return true; }
    return false;
}
function autogroupfromuser_get_config() {
    $defaults = [
        'skip_when_user_empty' => 1,
        'trigger_when_group_empty' => 1,
        'trigger_when_user_changed' => 1,
        'skip_when_both_empty' => 1,
        'enabled_itemtypes' => autogroupfromuser_supported_itemtypes(),
        'enable_logging' => 1
    ];
    $cfg = $defaults;
    if (class_exists('Config')) {
        $vals = Config::getConfigurationValues('plugin:autogroupfromuser');
        if (is_array($vals)) {
            foreach ($defaults as $k => $v) {
                if (!isset($vals[$k])) { continue; }
                if ($k === 'enabled_itemtypes') {
                    if (is_array($vals[$k])) {
                        $cfg[$k] = $vals[$k];
                    } else {
                        $cfg[$k] = array_filter(array_map('trim', explode(',', (string)$vals[$k])));
                    }
                } else {
                    $cfg[$k] = (int)$vals[$k];
                }
            }
        }
    }
    return $cfg;
}
function autogroupfromuser_itemtype_enabled($item, $cfg) {
    $type = get_class($item);
    if (!in_array($type, autogroupfromuser_supported_itemtypes())) { return false; }
    if (!in_array($type, (array)$cfg['enabled_itemtypes'])) { return false; }
    return true;
}
function plugin_autogroupfromuser_apply(CommonDBTM $item, $op) {
    $cfg = autogroupfromuser_get_config();
    if (!autogroupfromuser_itemtype_enabled($item, $cfg)) {
        autogroupfromuser_log('cfg_skip_itemtype_disabled', ['op' => $op, 'itemtype' => get_class($item)]);
        return true;
    }
    autogroupfromuser_log('enter', ['op' => $op, 'itemtype' => get_class($item), 'itemid' => (int)$item->getID()]);
    if (!autogroupfromuser_has_field($item, 'users_id')) {
        autogroupfromuser_log('skip_missing_field_users_id', ['itemtype' => get_class($item)]);
        return true;
    }
    if (!autogroupfromuser_has_field($item, 'groups_id')) {
        autogroupfromuser_log('skip_missing_field_groups_id', ['itemtype' => get_class($item)]);
        return true;
    }
    $users_id = 0;
    if (isset($item->input['users_id'])) {
        $users_id = (int)$item->input['users_id'];
    } elseif (isset($item->fields['users_id'])) {
        $users_id = (int)$item->fields['users_id'];
    }
    $existing_users_id = isset($item->fields['users_id']) ? (int)$item->fields['users_id'] : 0;
    if ($users_id <= 0) {
        $current_group_id_tmp = 0;
        if (isset($item->input['groups_id'])) {
            $current_group_id_tmp = (int)$item->input['groups_id'];
        } elseif (isset($item->fields['groups_id'])) {
            $current_group_id_tmp = (int)$item->fields['groups_id'];
        }
        if ($cfg['skip_when_both_empty'] && $current_group_id_tmp <= 0) {
            autogroupfromuser_log('cfg_skip_both_empty', ['op' => $op, 'itemid' => (int)$item->getID()]);
            return true;
        }
        if ($cfg['skip_when_user_empty']) {
            autogroupfromuser_log('cfg_skip_user_empty', ['op' => $op, 'itemid' => (int)$item->getID()]);
            return true;
        }
    }
    $user = new User();
    if (!$user->getFromDB($users_id)) {
        autogroupfromuser_log('skip_user_not_found', ['op' => $op, 'users_id' => $users_id]);
        return true;
    }
    $default_group_id = 0;
    if (isset($user->fields['groups_id'])) {
        $default_group_id = (int)$user->fields['groups_id'];
    }
    if ($default_group_id <= 0) {
        autogroupfromuser_log('skip_no_default_group', ['op' => $op, 'users_id' => $users_id]);
        return true;
    }
    $current_group_id = 0;
    if (isset($item->input['groups_id'])) {
        $current_group_id = (int)$item->input['groups_id'];
    } elseif (isset($item->fields['groups_id'])) {
        $current_group_id = (int)$item->fields['groups_id'];
    }
    if ($op === 'update' && $cfg['trigger_when_user_changed'] && $existing_users_id !== $users_id && $users_id > 0) {
        $item->input['groups_id'] = $default_group_id;
        autogroupfromuser_log('cfg_trigger_user_changed_set_group', ['itemid' => (int)$item->getID(), 'prev_user' => $existing_users_id, 'users_id' => $users_id, 'group_id' => $default_group_id]);
        return true;
    }
    if ($cfg['trigger_when_group_empty'] && $current_group_id <= 0) {
        $item->input['groups_id'] = $default_group_id;
        autogroupfromuser_log('cfg_trigger_group_empty_set_group', ['op' => $op, 'itemid' => (int)$item->getID(), 'users_id' => $users_id, 'group_id' => $default_group_id]);
    } else {
        autogroupfromuser_log('skip_group_present', ['op' => $op, 'itemid' => (int)$item->getID(), 'users_id' => $users_id, 'group_id' => $current_group_id]);
    }
    return true;
}
