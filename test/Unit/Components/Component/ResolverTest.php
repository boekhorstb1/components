<?php
/**
 * Test the component resolver.
 *
 * PHP version 5
 *
 * @category   Horde
 * @package    Components
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */

/**
 * Test the component resolver.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category   Horde
 * @package    Components
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Components_Unit_Components_Component_ResolverTest
extends Components_TestCase
{
    public function testResolveName()
    {
        $resolver = $this->_getResolver();
        $this->assertInstanceOf(
            'Components_Component',
            $resolver->resolveName('Install', 'pear.horde.org', array('git'))
        );
    }

    private function _getResolver()
    {
        return new Components_Component_Resolver(
            new Components_Helper_Root(
                null, null, __DIR__ . '/../../../fixture/framework'
            ),
            $this->getComponentFactory()
        );
    }
}
