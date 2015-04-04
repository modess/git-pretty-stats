<?php
namespace GitPrettyStats;

use Mockery as m;

/**
 * @covers GitPrettyStats\RepositoryFactory
 */
class RepositoryFactoryTest extends \TestCase
{
    protected $finder;

    public function setUp ()
    {
        parent::setUp();

        $this->finder = m::mock('Symfony\Component\Finder\Finder');

        \Config::shouldReceive('get')
            ->zeroOrMoreTimes()
            ->with('git-pretty-stats.emailAliases')
            ->andReturn(array());
    }

    public function testGetPathsWithoutConfig ()
    {
        $firstRepository = m::mock('stdClass');
        $firstRepository->shouldReceive('getRealPath')->once()->andReturn('/absolute/path/repository');

        $secondRepository = m::mock('stdClass');
        $secondRepository->shouldReceive('getRealPath')->once()->andReturn('/absolute/path/other-repository');

        \Config::shouldReceive('get')
            ->once()
            ->with('git-pretty-stats.repositoriesPath')
            ->andReturn('repositories');

        $this->finder->shouldReceive('depth->directories->in')
            ->once()
            ->with('/var/www/git-pretty-stats/repositories')
            ->andReturn(array($firstRepository, $secondRepository));

        $factory = new RepositoryFactory($this->finder, '/var/www/git-pretty-stats');

        $this->assertEquals(
            array('/absolute/path/repository', '/absolute/path/other-repository'),
            $factory->getPaths(),
            'Did not load repositories path without config'
        );
    }

    public function testGetPathsWithConfig ()
    {
        \Config::shouldReceive('get')
            ->once()
            ->with('git-pretty-stats.repositoriesPath')
            ->andReturn('non-default-dir');

        $firstRepository = m::mock('stdClass');
        $firstRepository->shouldReceive('getRealPath')->once()->andReturn('/absolute/path/repository');

        $secondRepository = m::mock('stdClass');
        $secondRepository->shouldReceive('getRealPath')->once()->andReturn('/absolute/path/other-repository');

        $this->finder->shouldReceive('depth->directories->in')
            ->once()
            ->with('/var/www/git-pretty-stats/non-default-dir')
            ->andReturn(array($firstRepository, $secondRepository));

        $factory = new RepositoryFactory($this->finder, '/var/www/git-pretty-stats');

        $this->assertEquals(
            array('/absolute/path/repository', '/absolute/path/other-repository'),
            $factory->getPaths(),
            'Did not load repositories path with repository path set in config'
        );
    }

    public function testGetPathsWithConfigArray ()
    {
        $firstRepositoryPath  = '/path/to/first-repo';
        $secondRepositoryPath = '/path/to/second-repo';
        $thirdRepositoryPath  = 'relative/path';

        \Config::shouldReceive('get')
            ->once()
            ->with('git-pretty-stats.repositoriesPath')
            ->andReturn(array($firstRepositoryPath, $secondRepositoryPath, $thirdRepositoryPath));

        $firstRepository = m::mock('stdClass');
        $firstRepository->shouldReceive('getRealPath')->once()->andReturn($firstRepositoryPath);

        $secondRepository = m::mock('stdClass');
        $secondRepository->shouldReceive('getRealPath')->once()->andReturn($secondRepositoryPath);

        $thirdRepository = m::mock('stdClass');
        $thirdRepository->shouldReceive('getRealPath')->once()->andReturn($thirdRepositoryPath);

        $this->finder
            ->shouldReceive('depth->directories->append')
            ->once()
            ->andReturn(array($firstRepository, $secondRepository, $thirdRepository));

        $factory = new RepositoryFactory($this->finder, '/var/www/git-pretty-stats');

        $this->assertEquals(
            array($firstRepositoryPath, $secondRepositoryPath, $thirdRepositoryPath),
            $factory->getPaths(),
            'Did not load repositories paths correctly with repository array set in config'
        );
    }

    public function testRepositoriesSetterAndGetter ()
    {
        $factory = new RepositoryFactory($this->finder);

        $repositories = array('first-repo', 'second-repo');

        $factory->setRepositories($repositories);

        $this->assertEquals(
            $repositories,
            $factory->getRepositories(),
            'Repositories setter and getter failed'
        );
    }

    public function testAll ()
    {
        $firstRepository = m::mock('stdClass');
        $firstRepository->shouldReceive('getName')->once()->andReturn('first-repo');

        $secondRepository = m::mock('stdClass');
        $secondRepository->shouldReceive('getName')->once()->andReturn('second-repo');

        $factory = m::mock('GitPrettyStats\RepositoryFactory[getPaths,load]', array($this->finder));
        $factory
            ->shouldReceive('getPaths')
            ->once()
            ->andReturn(array('/first/path', '/second/path'))
            ->shouldReceive('load')
            ->once()
            ->with('/first/path', false)
            ->andReturn($firstRepository)
            ->shouldReceive('load')
            ->once()
            ->with('/second/path', false)
            ->andReturn($secondRepository);

        $this->assertEquals(
            array('first-repo' => $firstRepository, 'second-repo' => $secondRepository),
            $factory->all(),
            'Did not return all repositories'
        );
    }

    public function testAllLazyLoad ()
    {
        $factory = new RepositoryFactory($this->finder);

        $repositories = array(
            'first-repo' => '/path',
            'second-repo' => '/other-path'
        );

        $factory->setRepositories($repositories);

        $this->assertEquals(
            $repositories,
            $factory->all(),
            'Lazy load for repositories failed'
        );
    }

    public function testFromName ()
    {
        $factory = new RepositoryFactory($this->finder);

        $repositories = array(
            'first-repo' => '/path',
            'second-repo' => '/other-path'
        );

        $factory->setRepositories($repositories);

        $this->assertEquals(
            '/path',
            $factory->fromName('first-repo'),
            'From name returned incorrect value'
        );
    }

    public function testGetFullPath ()
    {
        $factory = new RepositoryFactory($this->finder, '/var/www/dev');

        $paths = array(
            '/base-path'      => '/base-path',
            'relative-path'   => '/var/www/dev/relative-path',
            './relative-repo' => '/var/www/dev/./relative-repo'
        );

        foreach ($paths as $path => $expected) {
            $this->assertEquals(
                $expected,
                $factory->getFullPath($path)
            );
        }
    }

    public function testToArray ()
    {
        $firstRepository = m::mock('stdClass');
        $firstRepository
            ->shouldReceive('getName')
            ->twice()
            ->andReturn('first-repo')
            ->shouldReceive('getCurrentBranch')
            ->once()
            ->andReturn('master')
            ->shouldReceive('getTotalCommits')
            ->once()
            ->andReturn(271);

        $secondRepository = m::mock('stdClass');
        $secondRepository
            ->shouldReceive('getName')
            ->twice()
            ->andReturn('second-repo')
            ->shouldReceive('getCurrentBranch')
            ->once()
            ->andReturn('master')
            ->shouldReceive('getTotalCommits')
            ->once()
            ->andReturn(173);

        $factory = m::mock('GitPrettyStats\RepositoryFactory[all]', array($this->finder));
        $factory->shouldReceive('all')->once()->andReturn(array($firstRepository, $secondRepository));

        $this->assertEquals(
            array(
                'first-repo' => array(
                    'name'    => 'first-repo',
                    'commits' => 271,
                    'branch'  => 'master'
                ),
                'second-repo' => array(
                    'name'    => 'second-repo',
                    'commits' => 173,
                    'branch'  => 'master'
                )
            ),
            $factory->toArray(),
            'Did not return all repositories'
        );
    }
}
