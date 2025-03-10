<?php
/**
 * Copyright 2011-2020 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category   Horde
 * @package    Components
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @author     Jan Schneider <jan@horde.org>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Components\Unit\Components\Release\Task;
use Horde\Components\Test\TestCase;
use Horde\Components\Component\Source;
use Horde\Components\Helper\Commit as HelperCommit;

/**
 * Test the changelog release task.
 *
 * @category   Horde
 * @package    Components
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @author     Jan Schneider <jan@horde.org>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class ChangelogTest extends TestCase
{
    protected $_fixture;

    public function setUp(): void
    {
        $this->_fixture = __DIR__ . '/../../../../fixture/simple';
    }

    public function testPreValidateSucceeds()
    {
        $package = $this->getComponent($this->_fixture);
        $task = $this->getReleaseTask('changelog', $package);
        $this->assertEquals(array(), $task->preValidate(array()));
    }

    public function testPreValidateFails()
    {
        $package = $this->getComponent($this->_fixture . '/NO_SUCH_PACKAGE');
        $task = $this->getReleaseTask('changelog', $package);
        $this->assertFalse($task->preValidate(array()) === array());
    }

    public function testPostValidateFails()
    {
        $package = $this->getComponent($this->_fixture);
        $task = $this->getReleaseTask('changelog', $package);
        $this->assertFalse($task->postValidate(array()) === array());
    }

    public function testRunTaskWithoutCommit()
    {
        $tasks = $this->getReleaseTasks();
        $package = $this->_getValidPackage();
        $package->expects($this->once())
            ->method('sync');
        $tasks->run(array('changelog'), $package);
    }

    public function testPretend()
    {
        $this->markTestSkipped('Release no longer possible with outdated package.xml');
        $tasks = $this->getReleaseTasks();
        $package = $this->getComponent($this->_fixture);
        $tasks->run(
            array('Timestamp', 'CommitPreRelease'),
            $package,
            array(
                'pretend' => true,
                'commit' => new HelperCommit(
                    $this->_output,
                    array('pretend' => true)
                )
            )
        );
        $this->assertEquals(
            array(
                sprintf('Would timestamp "%s" now and synchronize its change log.', realpath($this->_fixture . '/package.xml')),
                sprintf('Would run "git add %s" now.', realpath($this->_fixture . '/package.xml')),
                'Would run "git commit -m "Released Fixture-0.0.1"" now.'
            ),
            $this->_output->getOutput()
        );
    }

    private function _getValidPackage()
    {
        $package = $this->getMockBuilder('Horde\Components\Component\Source')
        ->disableOriginalConstructor()
        ->getMock();
        $package->expects($this->any())
            ->method('hasLocalPackageXml')
            ->will($this->returnValue(true));
        return $package;
    }
}
