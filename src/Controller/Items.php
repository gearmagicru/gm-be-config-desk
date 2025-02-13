<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Config\Desk\Controller;

use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Http\Response;
use Gm\Panel\Widget\TabWidget;
use Gm\Panel\Controller\BaseController;

/**
 * Контроллер элементов панели Конфигурации.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Config\Desk\Controller
 * @since 1.0
 */
class Items extends BaseController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Panel\Extension\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    protected string $defaultAction = 'view';

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabWidget
    {
        /** @var TabWidget $tab */
        $tab = new TabWidget();

        // панель вкладки компонента (Ext.tab.Panel SenchaJS)
        $tab->id = $this->module->viewId('tab'); 
        $tab->title = '#{name}';
        $tab->tooltip = [
            'icon'  => $tab->imageSrc('/icon.svg'),
            'title' => '#{name}',
            'text'  => '#{description}'
        ];
        $tab->icon = $tab->imageSrc('/icon_small.svg');
        $tab->dockedItems = [
            'dock'       => 'right',
            'xtype'      => 'toolbar',
            'cls'        => 'g-toolbar-flat',
            'style'      => 'background-color:#e7e7e7',
            'controller' => 'shortcuts',
            'items'      => [
                [
                    'xtype'   => 'button',
                    'cls'     => 'g-button-tool',
                    'tooltip' => '#Refresh',
                    'iconCls' => 'g-icon-tool g-icon-tool_default x-tool-refresh',
                    'handler' => 'refreshShortcuts'
                ]
            ]
        ];
        
        // элементы рабочего стола (Gm.view.shortcuts.Shortcuts Gm JS)
        $tab->items = [
            'id'     => $this->module->viewId('shortcuts'),
            'xtype'  => 'g-shortcuts',
            'router' => [
                'route' => $this->module->route(),
                'rules' => [
                    'data' => '{route}/items/data'
                ]
            ]
        ];

        $tab
            ->addCss('/desk.css')
            ->addRequire('Gm.view.shortcuts.Shortcuts');
        return $tab;
    }

    /**
     * Действие "view" выводит интерфейс панели.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var TabWidget $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании виджета
        if ($widget === false) {
            return $response;
        }

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }

    /**
     * Действие "data" выводи элементы панели.
     * 
     * @return Response
     */
    public function dataAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var \Gm\Backend\Config\Desk\Model\Items $shortcuts */
        $shortcuts = $this->getModel('Items');
        return $response->setContent($shortcuts->getItems());
    }
}
