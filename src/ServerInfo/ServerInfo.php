<?php

namespace InnStudio\Prober\ServerInfo;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class ServerInfo
{
    private $ID = 'serverInfo';

    public function __construct()
    {
        Events::patch('mods', array($this, 'filter'), 200);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18n::_('Server information'),
            'tinyTitle' => I18n::_('Info'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        echo <<<HTML
<div class="row">
    {$this->getContent()}
</div>
HTML;
    }

    private function getDiskInfo()
    {
        if ( ! Helper::getDiskTotalSpace()) {
            return I18n::_('Unavailable');
        }

        $percent    = \sprintf('%01.2f', (1 - (Helper::getDiskFreeSpace() / Helper::getDiskTotalSpace())) * 100);
        $hunamUsed  = Helper::formatBytes(Helper::getDiskTotalSpace() - Helper::getDiskFreeSpace());
        $hunamTotal = Helper::getDiskTotalSpace(true);

        return <<<HTML
<div class="progress-container">
    <div class="percent" id="diskUsagePercent">{$percent}%</div>
    <div class="number">
        <span id="diskUsage">
            {$hunamUsed} / {$hunamTotal}
        </span>
    </div>
    <div class="progress" id="diskUsageProgress">
        <div id="diskUsageProgressValue" class="progress-value" style="width: {$percent}%"></div>
    </div>
</div>
HTML;
    }

    private function getContent()
    {
        $items = array(
            array(
                'label'   => $this->_('Server name'),
                'content' => $this->getServerInfo('SERVER_NAME'),
            ),
            array(
                'id'      => 'serverInfoTime',
                'label'   => $this->_('Server time'),
                'content' => Helper::getServerTime(),
            ),
            array(
                'id'      => 'serverUpTime',
                'label'   => $this->_('Server uptime'),
                'content' => Helper::getServerUpTime(),
            ),
            array(
                'label'   => $this->_('Server IP'),
                'content' => $this->getServerInfo('SERVER_ADDR'),
            ),
            array(
                'label'   => $this->_('Server software'),
                'content' => $this->getServerInfo('SERVER_SOFTWARE'),
            ),
            array(
                'label'   => $this->_('PHP version'),
                'content' => PHP_VERSION,
            ),
            array(
                'col'     => '1-1',
                'label'   => $this->_('CPU model'),
                'content' => Helper::getCpuModel(),
            ),
            array(
                'col'     => '1-1',
                'label'   => $this->_('Server OS'),
                'content' => \php_uname(),
            ),
            array(
                'id'      => 'scriptPath',
                'col'     => '1-1',
                'label'   => $this->_('Script path'),
                'content' => __FILE__,
            ),
            array(
                'col'     => '1-1',
                'label'   => $this->_('Disk usage'),
                'content' => $this->getDiskInfo(),
            ),
        );

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-3';
            $id    = isset($item['id']) ? "id=\"{$item['id']}\"" : '';

            $content .= <<<HTML
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$item['label']}</div>
        <div class="group-content" {$id} {$title}>{$item['content']}</div>
    </div> 
</div>
HTML;
        }

        return $content;
    }

    private function _($str)
    {
        return I18n::_($str);
    }

    private function getServerInfo($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }
}
