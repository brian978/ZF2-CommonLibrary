<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace LibraryTests\View\Helpers;

use Tests\TestHelpers\AbstractTest;

class MediaSourceTest extends AbstractTest
{
    public function testRetrieveHelper()
    {
        /** @var $helperPluginManager \Zend\View\HelperPluginManager */
        $helperPluginManager = $this->serviceManager->get('ViewHelperManager');

        /** @var $mediaSource \Library\View\Helpers\MediaSource */
        $mediaSource = $helperPluginManager->get('mediaSource');

        $this->assertInstanceOf('\Library\View\Helpers\MediaSource', $mediaSource);

        // Adding the manager to the helper
        $mediaSource->setHelperPluginManager($helperPluginManager);

        return $mediaSource;
    }

    /**
     * @depends testRetrieveHelper
     * @param \Library\View\Helpers\MediaSource $mediaSourceHelper
     */
    public function testGetMediaPathForImage($mediaSourceHelper)
    {
        $this->assertEquals('images/foo.jpg', $mediaSourceHelper('image', 'foo.jpg', true));
    }
}
