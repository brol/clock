<?php
/**
 * @brief Clock, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Moe, Pierre Van Glabeke and contributors
 *
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    'Clock',
    'Display the date of a time zone with the strftime() format in a widget',
    'Moe (http://gniark.net/), Pierre Van Glabeke and contributors',
    '1.5-dev',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type'       => 'plugin',
        'support'    => 'http://lab.dotclear.org/wiki/plugin/clock',
        'details'    => 'https://plugins.dotaddict.org/dc2/details/' . basename(__DIR__),
    ]
);