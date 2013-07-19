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

class FriendlyUrlTest extends AbstractTest
{
    public function testRetrieveHelper()
    {
        /** @var $viewHelperPluginManager \Zend\View\HelperPluginManager */
        $viewHelperPluginManager = $this->serviceManager->get('ViewHelperManager');

        /** @var $friendlyUrl \Library\View\Helpers\FriendlyUrl */
        $friendlyUrl = $viewHelperPluginManager->get('friendify');

        $this->assertInstanceOf('\Library\View\Helpers\FriendlyUrl', $friendlyUrl);

        return $friendlyUrl;
    }

    /**
     * @depends testRetrieveHelper
     * @param \Library\View\Helpers\FriendlyUrl $friendlyUrlHelper
     */
    public function testFriendifyString($friendlyUrlHelper)
    {
        $this->assertEquals('friendly-product-name-22', $friendlyUrlHelper('"Friendly product name 22"'));
    }
}
