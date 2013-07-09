<?php
namespace GitPrettyStats;

use Mockery as m;

/**
 * @covers GitPrettyStats\Repository
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $repository;
    protected $gitWrapper;

    public $commits = array(
        array('Author 1', 'author_1@email.com', '2013-03-01 14:55:47 +0200'),
        array('Author 1', 'author_1@email.com', '2013-03-02 12:13:54 +0200'),
        array('Author 2', 'author_2@email.com', '2013-03-02 14:55:47 +0200'),
        array('Author 3', 'author_3@email.com', '2013-03-03 09:13:47 +0200'),
        array('Author 2', 'author_2@email.com', '2013-03-03 19:55:47 +0200'),
        array('Author alias 1', 'author_1@email.com', '2013-03-03 22:32:19 +0200'),
        array('Author 4', 'author_4@email.com', '2013-03-04 10:32:00 +0200'),
        array('Author 4', 'author_4@email.com', '2013-03-04 14:55:47 +0200'),
        array('Author 5', 'author_5@email.com', '2013-03-05 09:55:47 +0200'),
        array('Author 1', 'author_1@email.com', '2013-03-05 18:14:30 +0200'),
    );

    public function setUp()
    {
        $this->gitWrapper = m::mock('\PHPGit_Repository');

        $logFormat = array(
            'commiter' => '%cn',
            'commiterEmail' => '%ce',
            'commitDate' => '%cd',
        );

        $logArg = sprintf(
            '--no-pager log -n %d --date=%s --format=format:"%s" --reverse',
            -1,
            'iso',
            implode('|', $logFormat)
        );

        $rawCommits = "";
        foreach ($this->commits as $commit) {
            $rawCommits .= "{$commit[0]}|{$commit[1]}|{$commit[2]}\n";
        }

        $this->gitWrapper
            ->shouldReceive('git')
            ->zeroOrMoreTimes()
            ->with($logArg)
            ->andReturn($rawCommits);
    }

    public function tearDown()
    {
        m::close();
    }

    public function createInstance()
    {
        $repo = new Repository($this->gitWrapper);
        return $repo;
    }

    public function testGetName()
    {
        $this->gitWrapper
            ->shouldReceive('git')
            ->with('rev-parse --show-toplevel')
            ->andReturn('some/path/to/git-repo');

        $repo = $this->createInstance();
        $this->assertEquals(
            'git-repo',
            $repo->getName(),
            'Name not correct'
        );
    }

    public function testNumberOfCommitsFromGitAreCorrect()
    {
        $this->gitWrapper
            ->shouldReceive('git')
            ->with('git rev-list --count HEAD')
            ->andReturn(5);

        $repo = $this->createInstance();
        $this->assertEquals(
            5,
            $repo->countCommitsFromGit(),
            'Number of commits are incorrect from git'
        );
    }

    public function testNumberOfCommitsAreCorrect()
    {
        $repo = $this->createInstance();
        $repo->loadCommits();
        $this->assertEquals(
            10,
            $repo->getNumberOfCommits(),
            'Number of commits are incorrect'
        );
    }

    public function testNumberOfContributorsAreCorrect()
    {
        $repo = $this->createInstance();
        $repo->loadCommits();
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
        $repo->loadCommits();

        $firstCommitDate = $this->commits[0][2];
        $this->assertEquals(
            date('Y-m-d', strtotime($firstCommitDate)),
            $repo->getFirstCommitDate()->format('Y-m-d'),
            'First commit date not correct'
        );

        $lastCommitDate = $this->commits[count($this->commits) - 1][2];
        $this->assertEquals(
            date('Y-m-d', strtotime($lastCommitDate . ' +1 day')),
            $repo->getLastCommitDate()->format('Y-m-d'),
            'Last commit date not correct'
        );
    }

    public function testAddingCommitToStats()
    {
        $repo = $this->createInstance();

        $commits = array();
        $key = 'commit';

        $repo->addCommitToStats($commits, $key);
        $this->assertEquals($commits[$key], 1);

        $repo->addCommitToStats($commits, $key);
        $this->assertEquals($commits[$key], 2);
    }

    public function testAddingCommitToContributor()
    {
        $commit = array(
            'commiter' => 'Jane Doe',
            'commiterEmail' => 'jane@doe.com',
            'commitDate' => '2013-04-01'
        );

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
            $repo->commitsByContributor[$commit['commiterEmail']],
            'Contributor formatted incorrectly'
        );

        $commit['commitDate'] = '2013-05-01';
        $repo->addCommitToContributor($commit);
        $this->assertEquals(
            2,
            count($repo->commitsByContributor[$commit['commiterEmail']]['commits']),
            'Incorrect number of commits for contributor'
        );
    }

    public function testGetCommitsByDate()
    {
        $repo = $this->createInstance();
        $repo->loadCommits();

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
        $repo->loadCommits();

        $expected = array(
            'x' => array('08', '09', 11, 13, 17, 18, 21),
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
        $repo->loadCommits();

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
        $repo->loadCommits();

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
}
