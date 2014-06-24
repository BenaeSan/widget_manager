<?php

/**
 * Events for widget manager
 */

/**
 * Sets the widget manager tool option. This is needed because in some situation the tooloption is not available.
 *
 * @param string $event       name of the system event
 * @param string $object_type type of the event
 * @param mixed  $object      object related to the event
 *
 * @return void
 */
function widget_manager_create_group_event_handler($event, $object_type, $object) {
	if ($object instanceof ElggGroup) {
		if (elgg_get_plugin_setting("group_option_default_enabled", "widget_manager") == "yes") {
			$object->widget_manager_enable = "yes";
		}
	}
}

/**
 * Sets the fixed parent guid to default widgets to be used when cloning, so relationship can stay intact.
 *
 * @param string $event       name of the system event
 * @param string $object_type type of the event
 * @param mixed  $object      object related to the event
 *
 * @return void
 */
function widget_manager_update_widget($event, $object_type, $object) {
	if (($object instanceof ElggWidget) && in_array($event, array("create", "update", "delete"))) {
		if (stristr($_SERVER["HTTP_REFERER"], "/admin/appearance/default_widgets")) {
			// on create set a parent guid
			if ($event == "create") {
				$object->fixed_parent_guid = $object->guid;
			}
			
			// update time stamp
			$context = $object->context;
			if (empty($context)) {
				// only situation is on create probably, as context is metadata and saved after creation of the object, this is the fallback
				$context = get_input("context", false);
			}
			
			if ($context) {
				elgg_set_plugin_setting($context . "_fixed_ts", time(), "widget_manager");
			}
		}
	}
}

/**
 * Adds a relation between a widget and a multidashboard object
 *
 * @param string $event       name of the system event
 * @param string $object_type type of the event
 * @param mixed  $object      object related to the event
 *
 * @return void
 */
function widget_manager_create_object_handler($event, $object_type, $object) {
	if (elgg_instanceof($object, "object", "widget", "ElggWidget")) {
		if ($dashboard_guid = get_input("multi_dashboard_guid")) {
			if (($dashboard = get_entity($dashboard_guid)) && elgg_instanceof($dashboard, "object", MultiDashboard::SUBTYPE, "MultiDashboard")) {
				add_entity_relationship($object->getGUID(), MultiDashboard::WIDGET_RELATIONSHIP, $dashboard->getGUID());
			}
		}
	}
}

/**
 * Sets the widget manager tool option. This is needed because in some situation the tooloption is not available.
 *
 * @param string $event       name of the system event
 * @param string $object_type type of the event
 * @param mixed  $object      object related to the event
 *
 * @return void
 */
function widget_manager_edit_group_event_handler($event, $object_type, $object) {
	
	if ($object instanceof ElggGroup) {
		if (elgg_get_plugin_setting("group_enable", "widget_manager") == "forced") {
			$object->widget_manager_enable = "yes";
		}
	}
}
