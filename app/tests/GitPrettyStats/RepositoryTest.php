<?php
namespace GitPrettyStats;

use Mockery as m;
use Carbon\Carbon;

/**
 * @covers GitPrettyStats\Repository
 */
class RepositoryTest extends \TestCase
{
    public $repository;
    public $client;
    public $gitter;

    public $commits = array(
        array('Author 1', 'author_1@email.com', '2013-03-01 14:55:47'),
        array('Author 1', 'author_1@email.com', '2013-03-02 12:13:54'),
        array('Author 2', 'author_2@email.com', '2013-03-02 14:55:47'),
        array('Author 3', 'author_3@email.com', '2013-03-03 09:13:47'),
        array('Author 2', 'author_2@email.com', '2013-03-03 19:55:47'),
        array('Author alias 1', 'author_1_another@email.com', '2013-03-03 22:32:19'),
        array('Author 4', 'author_4@email.com', '2013-03-04 10:32:00'),
        array('Author 4', 'author_4@email.com', '2013-03-04 14:55:47'),
        array('Author 5', 'author_5@email.com', '2013-03-05 09:55:47'),
        array('Author 1', 'author_1@email.com', '2013-03-05 18:14:30'),
    );

    public function setUp()
    {
        parent::setUp();

        $this->client = m::mock('\Gitter\Client');
        $this->gitter = m::mock('\Gitter\Repository');

        \Config::shouldReceive('get')
            ->once()
            ->with('git-pretty-stats.emailAliases')
            ->andReturn(array('author_1_another@email.com' => 'author_1@email.com'));

        $this->client
            ->shouldReceive('getRepository')
            ->andReturn($this->gitter);

        $commits = array();
        foreach ($this->commits as $commit) {
            $commits[] = $this->createCommit($commit[0], $commit[1], $commit[2]);
        }

        $this->commits = $commits;
    }

    public function createCommit($name, $email, $date)
    {
        $author = m::mock('stdClass');
        $author
            ->shouldReceive('getName')
            ->zeroOrMoreTimes()
            ->andReturn($name)
            ->shouldReceive('getEmail')
            ->zeroOrMoreTimes()
            ->andReturn($email);

        $commit = m::mock('stdClass');
        $commit
            ->shouldReceive('getCommiterDate')
            ->zeroOrMoreTimes()
            ->andReturn(new Carbon($date))
            ->shouldReceive('getAuthor')
            ->zeroOrMoreTimes()
            ->andReturn($author);

        return $commit;
    }

    public function tearDown()
    {
        m::close();
    }

    public function createInstance()
    {
        $repo = new Repository('.', $this->client);
        $repo->commits = $this->commits;

        return $repo;
    }

    public function repositoryNames ()
    {
        return array(
            array('./some/git/repository', 'repository'),
            array('sample.git', 'sample.git'),
            array('../../yet-another-repository', 'yet-another-repository'),
        );
    }

    /**
     * @dataProvider repositoryNames
     */
    public function testGetName($path, $expected)
    {
        $this->gitter
            ->shouldReceive('getPath')
            ->andReturn($path);

        $repo = $this->createInstance();

        $this->assertEquals(
            $expected,
            $repo->getName(),
            'Returned incorrect repository name'
        );
    }

    public function testLoadCommits ()
    {
        $this->gitter->shouldReceive('getCommits')->once();

        $repo = m::mock('GitPrettyStats\Repository[parseCommits]', array('.', $this->client));

        $repo->shouldReceive('parseCommits')->once();

        $repo->loadCommits();

    }

    public function testNumberOfCommitsAreCorrect()
    {
        $this->client
            ->shouldReceive('getRepository')
            ->andReturn($this->gitter);

        $repo = $this->createInstance();

        $this->assertEquals(
            10,
            $repo->getNumberOfCommits(),
            'Number of commits are incorrect'
        );
    }

    public function testNumberOfContributorsAreCorrect()
    {
        $this->client
            ->shouldReceive('getRepository')
            ->andReturn($this->gitter);

        $repo = $this->createInstance();
        $repo->parseCommits();

        $this->assertEquals(
            5,
            count($repo->commitsByContributor),
            'Number of contributors are incorrect'
        );
    }

    public function expectedStatistics()
    {
        return array(
            array('statistics'),
            array('charts'),
        );
    }

    /**
     * @dataProvider expectedStatistics
     */
    public function testgetStatistics($expectedKey)
    {
        $repo = $this->createInstance();
        $result = $repo->getStatistics();

        $this->assertArrayHasKey(
            $expectedKey,
            $result,
            'Statistics is missing ' . $expectedKey
        );
    }

    public function testFirstAndLastCommitDate()
    {
        $repo = $this->createInstance();
        $repo->parseCommits();

        $firstCommitDate = $this->commits[0]->getCommiterDate();
        $this->assertEquals(
            date('Y-m-d', strtotime($firstCommitDate)),
            $repo->getFirstCommitDate()->format('Y-m-d'),
            'First commit date not correct'
        );

        $lastCommitDate = $this->commits[count($this->commits) - 1]->getCommiterDate();
        $this->assertEquals(
            date('Y-m-d', strtotime($lastCommitDate . ' +1 day')),
            $repo->getLastCommitDate()->format('Y-m-d'),
            'Last commit date not correct'
        );
    }

    public function testGetClient ()
    {
        $repo = $this->createInstance();

        $this->assertEquals($this->client, $repo->getClient(), 'Invalid client returned');
    }

    public function testCountCommitsFromGit ()
    {
        $this->client
            ->shouldReceive('getVersion')
            ->once()
            ->andReturn('1.8.2')
            ->shouldReceive('run')
            ->with($this->gitter, 'rev-list --count HEAD')
            ->andReturn(5);

        $repo = $this->createInstance();
        $this->assertEquals(5, $repo->countCommitsFromGit(), 'Invalid commit count from git');
    }

    public function testCountCommitsFromGitBackwardsCompatible ()
    {
        $this->client
            ->shouldReceive('getVersion')
            ->once()
            ->andReturn('1.7.0')
            ->shouldReceive('run')
            ->with($this->gitter, 'rev-list HEAD | wc -l | tr -d "\n"')
            ->andReturn(5);

        $repo = $this->createInstance();
        $this->assertEquals(5, $repo->countCommitsFromGit(), 'Invalid commit count from git');
    }

    public function testAddingCommitToStats()
    {
        $repo = $this->createInstance();
        $repo->parseCommits();

        $commits = array();
        $key = 'commit';

        $repo->addCommitToStats($commits, $key);
        $this->assertEquals($commits[$key], 1);

        $repo->addCommitToStats($commits, $key);
        $this->assertEquals($commits[$key], 2);
    }

    public function testAddingCommitToContributor()
    {
        $commit = $this->createCommit('Jane Doe', 'jane@doe.com', '2013-04-01');

        $repo = $this->createInstance();
        $repo->addCommitToContributor($commit);

        $this->assertEquals(
            1,
            count($repo->commitsByContributor),
            'Commit not added to contributor'
        );

        $expected = array(
            'name' => 'Jane Doe',
            'commits' => array('2013-04-01' => array($commit))
        );
        $this->assertEquals(
            $expected,
            $repo->commitsByContributor['jane@doe.com'],
            'Contributor formatted incorrectly'
        );

        $commit = $this->createCommit('Jane Doe', 'jane@doe.com', '2013-05-01');

        $repo->addCommitToContributor($commit);
        $this->assertEquals(
            2,
            count($repo->commitsByContributor['jane@doe.com']['commits']),
            'Incorrect number of commits for contributor'
        );
    }

    public function testGetCommitsByDate()
    {
        $repo = $this->createInstance();
        $repo->parseCommits();

        $expected = array(
            'x' => array('2013-03-01','2013-03-02','2013-03-03','2013-03-04', '2013-03-05'),
            'y' => array(1, 2, 3, 2, 2)
        );

        $this->assertEquals(
            $expected,
            $repo->getCommitsByDate(),
            'Unexpected data from commits by date'
        );
    }

    public function testGetCommitsByHour()
    {
        $repo = $this->createInstance();
        $repo->parseCommits();

        $expected = array(
            'x' => array('09', 10, 12, 14, 18, 19, 22),
            'y' => array(2, 1, 1, 3, 1, 1, 1)
        );

        $this->assertEquals(
            $expected,
            $repo->getCommitsByHour(),
            'Unexpected data from commits by hour'
        );
    }

    public function testGetCommitsByDay()
    {
        $repo = $this->createInstance();
        $repo->parseCommits();

        $expected = array(
            array("Monday", 2),
            array("Tuesday", 2),
            array("Friday", 1),
            array("Saturday", 2),
            array("Sunday", 3),
        );

        $this->assertEquals(
            $expected,
            $repo->getCommitsByDay(),
            'Unexpected data from commits by day'
        );
    }

    public function testGetCommitsByContributor()
    {
        $repo = $this->createInstance();
        $repo->parseCommits();

        $expected = array(
            array(
                'name' => 'Author 1',
                'email' => 'author_1@email.com',
                'commits' => 4,
                'data' => array(
                    'x' => array('2013-03-01', '2013-03-02', '2013-03-03', '2013-03-04', '2013-03-05'),
                    'y' => array(1, 1, 1, 0, 1)
                )
            ),
            array(
                'name' => 'Author 4',
                'email' => 'author_4@email.com',
                'commits' => 2,
                'data' => array(
                    'x' => array('2013-03-01', '2013-03-02', '2013-03-03', '2013-03-04', '2013-03-05'),
                    'y' => array(0, 0, 0, 2, 0)
                )
            ),
            array(
                'name' => 'Author 2',
                'email' => 'author_2@email.com',
                'commits' => 2,
                'data' => array(
                    'x' => array('2013-03-01', '2013-03-02', '2013-03-03', '2013-03-04', '2013-03-05'),
                    'y' => array(0, 1, 1, 0, 0)
                )
            ),
            array(
                'name' => 'Author 5',
                'email' => 'author_5@email.com',
                'commits' => 1,
                'data' => array(
                    'x' => array('2013-03-01', '2013-03-02', '2013-03-03', '2013-03-04', '2013-03-05'),
                    'y' => array(0, 0, 0, 0, 1)
                )
            ),
            array(
                'name' => 'Author 3',
                'email' => 'author_3@email.com',
                'commits' => 1,
                'data' => array(
                    'x' => array('2013-03-01', '2013-03-02', '2013-03-03', '2013-03-04', '2013-03-05'),
                    'y' => array(0, 0, 1, 0, 0)
                )
            ),
        );

        $this->assertEquals(
            $expected,
            $repo->getCommitsByContributor(),
            'Unexpected data from commits by author'
        );
    }

    public function testGetEmailAlias ()
    {
        $repo = $this->createInstance();

        $this->assertEquals(
            'email_without@alias.com',
            $repo->getEmailAlias('email_without@alias.com'),
            'E-mail without alias should be returned'
        );

        $this->assertEquals(
            'author_1@email.com',
            $repo->getEmailAlias('author_1_another@email.com'),
            'E-mail with alias should return original'
        );
    }
}
