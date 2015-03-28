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

        \Config::shouldReceive('get')
            ->once()
            ->with('git-pretty-stats.emailAliases')
            ->andReturn(array('author_1_another@email.com' => 'author_1@email.com'));

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
        $repo = new Repository($path, $this->client);

        $this->assertEquals(
            $expected,
            $repo->getName(),
            'Returned incorrect repository name'
        );
    }


    public function testGetClient ()
    {
        $repo = $this->createInstance();

        $this->assertEquals($this->client, $repo->getClient(), 'Invalid client returned');
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
