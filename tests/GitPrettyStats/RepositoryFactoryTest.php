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

        $finder->shouldReceive('depth->directories->in')
            ->once()
            ->with('/var/www/git-pretty-stats/repositories')
            ->andReturn(array($firstRepository, $secondRepository));

        $factory = new RepositoryFactory(null, $finder, '/var/www/git-pretty-stats/');

        $this->assertEquals(
            array('/absolute/path/repository', '/absolute/path/other-repository'),
            $factory->getPaths(),
            'Did not load repositories path without config'
        );
    }

    public function testGetPathsWithConfig ()
    {
        $finder = m::mock('stdClass');
        $config = array(
            'repositoriesPath' => 'non-default-dir'
        );

        $firstRepository = m::mock('stdClass');
        $firstRepository->shouldReceive('getRealPath')->once()->andReturn('/absolute/path/repository');

        $secondRepository = m::mock('stdClass');
        $secondRepository->shouldReceive('getRealPath')->once()->andReturn('/absolute/path/other-repository');

        $finder->shouldReceive('depth->directories->in')
            ->once()
            ->with('/var/www/git-pretty-stats/non-default-dir')
            ->andReturn(array($firstRepository, $secondRepository));

        $factory = new RepositoryFactory($config, $finder, '/var/www/git-pretty-stats/');

        $this->assertEquals(
            array('/absolute/path/repository', '/absolute/path/other-repository'),
            $factory->getPaths(),
            'Did not load repositories path with repository path set in config'
        );
    }

    public function testGetPathsWithConfigArray ()
    {
        $finder = m::mock('stdClass');

        $firstRepositoryPath = '/path/to/first-repo';
        $secondRepositoryPath = '/path/to/second-repo';

        $config = array(
            'repositoriesPath' => array(
                $firstRepositoryPath,
                $secondRepositoryPath
            )
        );

        $firstRepository = m::mock('stdClass');
        $firstRepository->shouldReceive('getRealPath')->once()->andReturn($firstRepositoryPath);

        $secondRepository = m::mock('stdClass');
        $secondRepository->shouldReceive('getRealPath')->once()->andReturn($secondRepositoryPath);

        $finder
            ->shouldReceive('depth->directories->append')
            ->once()
            ->andReturn(array($firstRepository, $secondRepository));

        $factory = new RepositoryFactory($config, $finder, '/var/www/git-pretty-stats/');

        $this->assertEquals(
            array($firstRepositoryPath, $secondRepositoryPath),
            $factory->getPaths(),
            'Did not load repositories paths correctly with repository array set in config'
        );
    }

}
