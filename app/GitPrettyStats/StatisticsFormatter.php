<?php
namespace GitPrettyStats;

use Carbon\Carbon;

/**
 * Statistics formatter for repository
 *
 * @author Niklas Modess <niklas@codingswag.com>
 */
class StatisticsFormatter
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var array Statistics
     */
    protected $statistics;

    /**
     * @param Repository $repository
     */
    public function __construct (Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get certain type of statistics
     *
     * @param $type
     * @return mixed
     */
    public function statistics ($type)
    {
        if ($this->statistics === null) {
            $this->statistics = $this->repository->getStatistics();
        }

        return $this->statistics[$type];
    }

    /**
     * Statistics for number of contributors
     *
     * @return array
     */
    public function contributors ()
    {
        $contributors = $this->statistics('contributors');

        return array(
            'title' => 'Total contributors',
            'value' => number_format(count($contributors))
        );
    }

    /**
     * Statistics for number of commits
     *
     * @return array
     */
    public function totalNumberOfCommits ()
    {
        return array(
            'title' => 'Total commits',
            'value' => number_format($this->repository->getTotalCommits())
        );
    }

    /**
     * Statistics for number of average commits per contributor
     *
     * @return array
     */
    public function contributorsAverageCommits ()
    {
        $contributors = $this->statistics('contributors');

        return array(
            'title' => 'Average commits per contributor',
            'value' => number_format($this->repository->getTotalCommits() / count($contributors), 2)
        );
    }

    /**
     * Statistics for first commit date
     *
     * @return array
     */
    public function firstCommitDate ()
    {
        return array(
            'title' => 'First commit date',
            'value' => $this->repository->getFirstCommitDate(),
        );
    }

    /**
     * Statistics for latest commit date
     *
     * @return array
     */
    public function latestCommitDate ()
    {
        return array(
            'title' => 'Latest commit date',
            'value' => $this->repository->getLastCommitDate(),
        );
    }

    /**
     * Statistics for how long the repository has been active
     *
     * @return array
     */
    public function activeFor ()
    {
        $firstCommitDate = new Carbon($this->repository->getFirstCommitDate());
        $lastCommitDate  = new Carbon($this->repository->getLastCommitDate());

        return array(
            'title' => 'Active for',
            'value' => number_format($firstCommitDate->diffInDays($lastCommitDate)) . " days"
        );
    }

    /**
     * Statistics for average commits per day
     *
     * @return array
     */
    public function averageCommitsPerDay ()
    {
        $commitsByDate = $this->statistics('date');

        return array(
            'title' => 'Average commits per day',
            'value' => number_format(count($commitsByDate) / $this->repository->getTotalCommits(), 2)
        );
    }
}
