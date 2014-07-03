<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace AcamarTests\View\Helpers;

use Tests\TestHelpers\AbstractTest;

class FriendlyUrlTest extends AbstractTest
{
    public function testRetrieveHelper()
    {
        /** @var $helperPluginManager \Zend\View\HelperPluginManager */
        $helperPluginManager = $this->serviceManager->get('ViewHelperManager');

        /** @var $friendlyUrl \Acamar\View\Helpers\FriendlyUrl */
        $friendlyUrl = $helperPluginManager->get('createFriendlyUrl');

        $this->assertInstanceOf('\Acamar\View\Helpers\FriendlyUrl', $friendlyUrl);

        return $friendlyUrl;
    }

    /**
     * @depends testRetrieveHelper
     * @param \Acamar\View\Helpers\FriendlyUrl $friendlyUrlHelper
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
     * @param \Acamar\View\Helpers\FriendlyUrl $friendlyUrlHelper
     */
    public function testCreateAnotherFriendlyUrlString($friendlyUrlHelper)
    {
        $this->assertEquals(
            'friendly-product-name-22',
            $friendlyUrlHelper('Friendly  -product /][;=-name 22')
        );
    }
}
