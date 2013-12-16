<?php
namespace GitPrettyStats;

use Mockery as m;

/**
 * @covers GitPrettyStats\RepositoryFactory
 */
class RepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPathsWithoutConfig ()
    {
        $finder = m::mock('stdClass');

        $firstRepository = m::mock('stdClass');
        $firstRepository->shouldReceive('getRealPath')->once()->andReturn('/absolute/path/repository');

        $secondRepository = m::mock('stdClass');
        $secondRepository->shouldReceive('getRealPath')->once()->andReturn('/absolute/path/other-repository');

        $finder->shouldReceive('directories->in')->once()->andReturn(array($firstRepository, $secondRepository));

        $factory = new RepositoryFactory(null, $finder);

        $this->assertEquals(
            array('/absolute/path/repository', '/absolute/path/other-repository'),
            $factory->getPaths(),
            'Did not load repositories path without config'
        );
    }
}
