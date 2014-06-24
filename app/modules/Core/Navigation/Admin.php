<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 PhalconEye Team (http://phalconeye.com/)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  | Author: Piotr Gasiorowski <p.gasiorowski@vipserv.org>                  |
  +------------------------------------------------------------------------+
*/

namespace Core\Navigation;

use Core\Model\Package;
use Engine\Navigation\Item;
use Engine\Package\Manager;

/**
 * Admin Navigation.
 *
 * @category  PhalconEye
 * @package   Core\Navigation
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @author    Piotr Gasiorowski <p.gasiorowski@vipserv.org>
 * @copyright 2013-2014 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Admin extends Core
{
    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return array_merge(parent::getParameters(), [
            'listClass' => 'nav nav-categories',
            'dropDownItemClass' => 'nav-category',
            'dropDownItemMenuClass' => 'nav'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        // todo: re-organize
        $path = explode('/', $this->_activeItem);

        $activeItem = '';
        $limit = (count($path) > 3 ? 1 : 0);
        for ($i = 1, $count = count($path); $i < $count - $limit && $i < 3; $i++) {
            $activeItem .= $path[$i] . '/';
        }
        $activeItem = substr($activeItem, 0, -1);
        // $this->setActiveItem($activeItem);

        // Dashboard
        $this->appendItem(new Item('Dashboard', 'admin', [
            'prepend' => '<i class="glyphicon glyphicon-home"></i>'
        ]));

        // Manage
        $this->appendItem($mangeItem = new Item('Manage'));

        $mangeItem->setItems([
            new Item('Users and Roles', 'admin/users', [
                'prepend' => '<i class="glyphicon glyphicon-user"></i>'
            ]),
            new Item('Pages', 'admin/pages', [
                'prepend' => '<i class="glyphicon glyphicon-list-alt"></i>'
            ]),
            new Item('Menus', 'admin/menus', [
                'prepend' => '<i class="glyphicon glyphicon-th-list"></i>'
            ]),
            new Item('Languages', 'admin/languages', [
                'prepend' => '<i class="glyphicon glyphicon-globe"></i>'
            ]),
            new Item('Files', 'admin/files', [
                'prepend' => '<i class="glyphicon glyphicon-file"></i>'
            ]),
            new Item('Packages', 'admin/packages', [
                'prepend' => '<i class="glyphicon glyphicon-th"></i>'
            ])
        ]);

        // Settings
        $this->appendItem($settingsItem = new Item('Settings'));

        $settingsItem->setItems([
            new Item('System', 'admin/settings', [
                'prepend' => '<i class="glyphicon glyphicon-cog"></i>'
            ]),
            new Item('Performance', 'admin/performance', [
                'prepend' => '<i class="glyphicon glyphicon-signal"></i>'
            ]),
            $acl = new Item('Access Rights', 'admin/access', [
                'prepend' => '<i class="glyphicon glyphicon-lock"></i>'
            ])
        ]);

        // Dynamic modules
        $modules = Package::findByType(Manager::PACKAGE_TYPE_MODULE, 1);
        if ($modules->count()) {
            $modulesMenuItem = null;
            foreach ($modules as $module) {
                if ($module->is_system) {
                    continue;
                }

                if (!$modulesMenuItem) {
                    $modulesMenuItem = new Item('Modules');
                    $this->appendItem($modulesMenuItem);
                }

                $modulesMenuItem->appendItem(
                    new Item($module->title, 'admin/module/' . $module->name, [
                        'prepend' => '<i class="glyphicon glyphicon-th-large"></i>'
                    ])
                );

                if ($activeItem == 'admin/module' && (string) $path[3] == $module->name) {
                    $this->setActiveItem('admin/module/' . $module->name);
                }
            }
        }
    }
}
