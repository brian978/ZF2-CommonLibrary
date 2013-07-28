<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/NetworkAnalyzer
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Library\View\Helpers;

use Zend\Http\Request;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;

class MediaSource extends AbstractHelper
{
    /**
     * @var HelperPluginManager
     */
    protected $pluginManager;

    /**
     * This is used when unit testing
     *
     * @param HelperPluginManager $pluginManager
     * @return $this
     */
    public function setHelperPluginManager(HelperPluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;

        return $this;
    }

    /**
     * @return HelperPluginManager
     */
    protected function getHelperPluginManager()
    {
        if (empty($this->pluginManager) && $this->getView() !== null) {
            $this->pluginManager = $this->getView()->getHelperPluginManager();
        }

        return $this->pluginManager;
    }

    protected function getRequest()
    {
        return $this->getHelperPluginManager()->getServiceLocator()->get('Request');
    }

    /**
     * @return array
     */
    protected function getConfig()
    {
        $config = $this->getHelperPluginManager()->getServiceLocator()->get('Config');

        if(isset($config['view_helpers']['config']['mediaSource'])) {
            $config = $config['view_helpers']['config']['mediaSource'];
        } else {
            $config = null;
        }

        return $config;
    }

    /**
     * @param string $mediaType
     * @param string $mediaName
     * @return string
     */
    public function __invoke($mediaType, $mediaName)
    {
        $url     = '';
        $config  = $this->getConfig();

        // When unit testing we don't have a base path (or when the request is from Console)
        if ($this->getRequest() instanceof Request) {
            /** @var $basePath \Zend\View\Helper\BasePath */
            $basePath = $this->getHelperPluginManager()->get('BasePath');
            $url .= $basePath() . '/';
        }

        switch ($mediaType) {
            case 'image':
                if (isset($config['images_path'])) {
                    $url .= $config['images_path'];
                }
                break;

            case 'css':
                if (isset($config['css_path'])) {
                    $url .= $config['css_path'];
                }
                break;

            case 'js':
                if (isset($config['js_path'])) {
                    $url .= $config['js_path'];
                }
                break;
        }

        $url .= $mediaName;

        return $url;
    }
}
