<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Config\Desk\Model;

use Gm;
use Gm\Helper\Str;
use Gm\Data\Model\DataModel;
use Gm\Mvc\Module\BaseModule;

/**
 * Модель данных элементов панели Конфигурации.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Config\Desk\Model
 * @since 1.0
 */
class Items extends DataModel
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\Config\Desk\Extension
     */
    public BaseModule $module;

    /**
     * Длина заголовка расширения модуля.
     * 
     * @var int
     */
    public int $titleLength = 43;

    /**
     * Возвращает элементы панели.
     * 
     * @return array
     */
    public function getItems(): array
    {
        $items = [];
        // текущий идентификатор расширения
        $extensionId = $this->module->id;
        /** @var int|null Идентификатор модуля расширения в базе данных */
        $moduleRowId = $this->module->parent->getInstalledParam('rowId');

        /**
         * @var array $extensions Все доступные пользователю расширения модулей.
         * Даже если пользователь имеет хотя бы одно любое разрешение для расширения модуля. 
         * Имеет вид: `['extension_id1' => [...], 'extension_id2' => [...], ...]`. 
         */
        $extensions = Gm::$app->extensions->getRegistry()->getListInfo(true, true, 'id');
        // убираем расширение которое делает сам вывод
        unset($extensions[$extensionId]);
        foreach ($extensions as $extension) {
            // все доступные расширения, только для текущего модуля
            if ($extension['moduleRowId'] == $moduleRowId && $extension['enabled']) {
                $items[] = [
                    'title'       => $extension['name'],
                    'description' => Str::ellipsis($extension['description'], 0, $this->titleLength),
                    'tooltip'     => $extension['description'],
                    'widgetUrl'   => '@backend/' . $extension['baseRoute'],
                    'icon'        => $extension['icon']
                ];
            }
        }

        /**
         * @var array $modules Все доступные пользователю модули.
         * Даже если пользователь имеет хотя бы одно любое разрешение для модуля. 
         * Имеет вид: `['module_id1' => [...], 'module_id2' => [...], ...]`. 
         */
        $modules = Gm::$app->modules->getRegistry()->getListInfo(true, true, 'id');
        foreach ($modules as $module) {
            if ($module['hasSettings']) {
                $items[] = [
                    'title'       => $module['name'],
                    'description' => Str::ellipsis($module['description'], 0, $this->titleLength),
                    'tooltip'     => $module['description'],
                    'widgetUrl'   => '@backend/' . $module['route'] . '/settings/view',
                    'icon'        => $module['icon'],
                    'cls'         => 'gm-config-desk__thumb_module'
                ];
            }
        }

        /**
         * @var array $widgets Все установленные виджеты.
         * Имеет вид: `['widget_id1' => [...], 'widget_id2' => [...], ...]`. 
         */
        $widgets = Gm::$app->widgets->getRegistry()->getListInfo(true, true, 'id');
        foreach ($widgets as $widget) {
            if ($widget['hasSettings']) {
                $items[] = [
                    'title'       => $widget['name'],
                    'description' => Str::ellipsis($widget['description'], 0, $this->titleLength),
                    'tooltip'     => $widget['description'],
                    'widgetUrl'   => '@backend/marketplace/wmanager/wsettings/view/' . $widget['rowId'],
                    'icon'        => $widget['icon'],
                    'cls'         => 'gm-config-desk__thumb_widget'
                ];
            }
        }
        return $items;
    }
}
