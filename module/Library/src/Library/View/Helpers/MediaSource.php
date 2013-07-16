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

    /**
     * @param string $mediaType
     * @param string $mediaName
     * @return string
     */
    public function __invoke($mediaType, $mediaName)
    {
        $pluginManager = $this->getHelperPluginManager();

        /** @var $serviceLocator \Zend\ServiceManager\ServiceLocatorInterface */
        $serviceLocator = $pluginManager->getServiceLocator();

        $url     = '';
        $config  = $serviceLocator->get('Config');
        $request = $serviceLocator->get('Request');

        // When unit testing we don't have a base path (or when the request is from Console)
        if ($request instanceof Request) {
            /** @var $basePath \Zend\View\Helper\BasePath */
            $basePath = $pluginManager->get('BasePath');
            $url .= $basePath() . '/';
        }

        switch ($mediaType) {
            case 'image':
                if (isset($config['view_helpers']['config']['images_path'])) {
                    $url .= $config['view_helpers']['config']['images_path'];
                }
                break;

            case 'css':
                if (isset($config['view_helpers']['config']['css_path'])) {
                    $url .= $config['view_helpers']['config']['css_path'];
                }
                break;

            case 'js':
                if (isset($config['view_helpers']['config']['js_path'])) {
                    $url .= $config['view_helpers']['config']['js_path'];
                }
                break;
        }

        $url .= $mediaName;

        return $url;
    }
}
