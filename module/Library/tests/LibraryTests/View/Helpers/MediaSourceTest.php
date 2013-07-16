<?php
/**
 * ZF2-CommonLibrary
 *
 * @link      https://github.com/brian978/ZF2-CommonLibrary
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\View\Helpers;

use TestHelpers\AbstractTest;

class MediaSourceTest extends AbstractTest
{
    public function testRetrieveMediaPath()
    {
        /** @var $viewHelperPluginManager \Zend\View\HelperPluginManager */
        $viewHelperPluginManager = $this->serviceManager->get('ViewHelperManager');

        /** @var $mediaSource \Library\View\Helpers\MediaSource */
        $mediaSource = $viewHelperPluginManager->get('mediaSource');

        $this->assertInstanceOf('\Library\View\Helpers\MediaSource', $mediaSource);

        // Adding the manager to the helper
        $mediaSource->setHelperPluginManager($viewHelperPluginManager);

        return $mediaSource;
    }

    /**
     * @depends testRetrieveMediaPath
     * @param \Library\View\Helpers\MediaSource $mediaSourceHelper
     */
    public function testGetMediaPathForImage($mediaSourceHelper)
    {
        $this->assertEquals('images/foo.jpg', $mediaSourceHelper('image', 'foo.jpg', true));
    }
}
