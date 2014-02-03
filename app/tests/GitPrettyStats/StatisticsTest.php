<?php
namespace GitPrettyStats;

use Mockery as m;

class StatisticsTest extends \PHPUnit_Framework_TestCase
{
    public $statistics;

    public function setUp()
    {
        $this->statistics = new Statistics(m::mock('\GitPrettyStats\Repository'));
    }

    public function formatExpected ($title, $value)
    {
        return array(
            'title' => $title,
            'value' => $value
        );
    }

    public function testContributors ()
    {
        $contributors = 3;
        $this->statistics->repository
            ->shouldReceive('getNumberOfContributors')
            ->andReturn($contributors);

        $this->assertEquals(
            $this->formatExpected('Total contributors', 3),
            $this->statistics->contributors()
        );
    }

    public function testCommits ()
    {
        $commits = 1241;
        $this->statistics->repository
            ->shouldReceive('getNumberOfCommits')
            ->andReturn($commits);

        $this->assertEquals(
            $this->formatExpected('Total commits', '1,241'),
            $this->statistics->commits()
        );
    }

    public function testContributorsAverageCommits ()
    {
        $commits = 10;
        $contributors = 4;
        $this->statistics->repository
            ->shouldReceive('getNumberOfCommits')
            ->andReturn($commits)
            ->shouldReceive('getNumberOfContributors')
            ->andReturn($contributors);

        $this->assertEquals(
            $this->formatExpected('Average commits per contributor', '2.5'),
            $this->statistics->contributorsAverageCommits()
        );
    }

    public function testFirstCommit ()
    {
        $date = m::mock('\Carbon\Carbon');
        $date->shouldReceive('format')
            ->andReturn('2013-01-01');
        $this->statistics->repository
            ->shouldReceive('getFirstCommitDate')
            ->andReturn($date);

        $this->assertEquals(
            $this->formatExpected('First commit date', '2013-01-01'),
            $this->statistics->firstCommit()
        );
    }

    public function testLatestCommit ()
    {
        $date = m::mock('\Carbon\Carbon');
        $date->shouldReceive('format')
            ->andReturn('2013-01-31');
        $this->statistics->repository
            ->shouldReceive('getLastCommitDate')
            ->andReturn($date);

        $this->assertEquals(
            $this->formatExpected('Latest commit date', '2013-01-31'),
            $this->statistics->latestCommit()
        );
    }

    public function testActiveFor ()
    {
        $this->statistics->repository
            ->shouldReceive('getDaysRepositoryBeenActive')
            ->andReturn(30);

        $this->assertEquals(
            $this->formatExpected('Active for', '30 days'),
            $this->statistics->activeFor()
        );
    }

    public function testAverageCommitsPerDay ()
    {
        $commits = 75;
        $days = 30;
        $this->statistics->repository
            ->shouldReceive('getNumberOfCommits')
            ->andReturn($commits)
            ->shouldReceive('getDaysRepositoryBeenActive')
            ->andReturn($days);

        $this->assertEquals(
            $this->formatExpected('Average commits per day', '2.5'),
            $this->statistics->averageCommitsPerDay()
        );
    }
}
