<?php
namespace GitPrettyStats;

/**
 * Statistics for repository
 *
 * @author Niklas Modess <niklas@codingswag.com>
 */
class Statistics
{
    /** @var GitPrettyStats\Repository */
    public $repository;

    /**
     * @param Repository $repository
     * @return void
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Statistics for number of contributors
     *
     * @return array
     */
    public function contributors ()
    {
        return array(
            'title' => 'Total contributors',
            'value' => number_format($this->repository->getNumberOfContributors())
        );
    }

    /**
     * Statistics for number of commits
     *
     * @return array
     */
    public function commits ()
    {
        return array(
            'title' => 'Total commits',
            'value' => number_format($this->repository->getNumberOfCommits())
        );
    }

    /**
     * Statistics for number of average commits per contributor
     *
     * @return array
     */
    public function contributorsAverageCommits ()
    {
        $value = ($this->repository->getNumberOfContributors() == 0) ?
            0 : $this->repository->getNumberOfCommits() / $this->repository->getNumberOfContributors();

        return array(
            'title' => 'Average commits per contributor',
            'value' => number_format($value, 2)
        );
    }

    /**
     * Statistics for first commit date
     *
     * @return array
     */
    public function firstCommit ()
    {
        return array(
            'title' => 'First commit date',
            'value' => $this->repository->getFirstCommitDate()->format('Y-m-d'),
        );
    }

    /**
     * Statistics for latest commit date
     *
     * @return array
     */
    public function latestCommit ()
    {
        return array(
            'title' => 'Latest commit date',
            'value' => $this->repository->getLastCommitDate()->format('Y-m-d'),
        );
    }

    /**
     * Statistics for how long the repository has been active
     *
     * @return array
     */
    public function activeFor ()
    {
        return array(
            'title' => 'Active for',
            'value' => number_format($this->repository->getDaysRepositoryBeenActive()) . " days"
        );
    }

    /**
     * Statistics for average commits per day
     *
     * @return array
     */
    public function averageCommitsPerDay ()
    {
        $value = ($this->repository->getDaysRepositoryBeenActive() == 0) ?
            0 : $this->repository->getNumberOfCommits() / $this->repository->getDaysRepositoryBeenActive();

        return array(
            'title' => 'Average commits per day',
            'value' => number_format($value, 2)
        );
    }
}
