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

class FriendlyUrlTest extends AbstractTest
{
    public function testRetrieveHelper()
    {
        /** @var $helperPluginManager \Zend\View\HelperPluginManager */
        $helperPluginManager = $this->serviceManager->get('ViewHelperManager');

        /** @var $friendlyUrl \Library\View\Helpers\FriendlyUrl */
        $friendlyUrl = $helperPluginManager->get('createFriendlyUrl');

        $this->assertInstanceOf('\Library\View\Helpers\FriendlyUrl', $friendlyUrl);

        return $friendlyUrl;
    }

    /**
     * @depends testRetrieveHelper
     * @param \Library\View\Helpers\FriendlyUrl $friendlyUrlHelper
     */
    public function testCreateFriendlyUrlString($friendlyUrlHelper)
    {
        $this->assertEquals(
            'friendly-product-name-22',
            $friendlyUrlHelper('"Friendly  -product /][;=-name 22"')
        );
    }

    /**
     * @depends testRetrieveHelper
     * @param \Library\View\Helpers\FriendlyUrl $friendlyUrlHelper
     */
    public function testCreateAnotherFriendlyUrlString($friendlyUrlHelper)
    {
        $this->assertEquals(
            'friendly-product-name-22',
            $friendlyUrlHelper('Friendly  -product /][;=-name 22')
        );
    }
}
