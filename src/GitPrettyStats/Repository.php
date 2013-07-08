<?php
namespace GitPrettyStats;

/**
 * Class Repository
 * @author Niklas Modess <niklas@codingswag.com>
 */
class Repository
{
    /** @var \PHPGit_Repository */
    public $gitWrapper;

    /** @var array Storage for "raw" commits */
    public $commits = array();

    /** @var string Date format */
    public $dateFormat = 'iso';

    /** @var array Mapper for fetching information about commits */
    public $logFormat = array(
        'commiter' => '%cn',
        'commiterEmail' => '%ce',
        'commitDate' => '%cd',
    );

    /** @var array Storage for commits by date */
    public $commitsByDate = array();

    /** @var array Storage for commits by hour */
    public $commitsByHour = array();

    /** @var array Storage for commits by hour */
    public $commitsByDay = array();

    /** @var array Storage for commits by contributor */
    public $commitsByContributor = array();

    /**
     * Constructor
     *
     * @param string $path Path to repository
     * @return void
     */
    public function __construct(\PHPGit_Repository $gitWrapper)
    {
        $this->gitWrapper = $gitWrapper;
    }

    /**
     * @return \PHPGit_Repository
     */
    public function getGitWrapper()
    {
        return $this->gitWrapper;
    }

    /**
     * Get name of repository (top level directory)
     *
     * @return string
     */
    public function getName ()
    {
        $path = $this->getGitWrapper()->git('rev-parse --show-toplevel');
        $name = substr($path, strrpos($path, '/') + 1);
        return $name;
    }

    /**
     * Count all commits using the git binary
     *
     * @return int
     */
    public function countCommitsFromGit ()
    {
        return $this->getGitWrapper()->git('git rev-list --count HEAD');
    }

    /**
     * Load commits from git repo
     *
     * @return void
     */
    public function loadCommits()
    {
        $rawCommits = $this->getCommits(-1);
        $this->commits = $this->parseLogsIntoArray(trim($rawCommits));
    }

    /**
     * Count number of commits
     *
     * @return int
     */
    public function getNumberOfCommits()
    {
        return count($this->commits);
    }


    /**
     * Return the result of `git log` formatted in a PHP array
     *
     * @return array list of commits and their properties
     **/
    public function getCommits($numberOfCommits = 10)
    {
        $output = $this->getGitWrapper()->git(
            sprintf(
                '--no-pager log -n %d --date=%s --format=format:"%s" --reverse',
                $numberOfCommits,
                $this->dateFormat,
                implode('|', $this->logFormat)
            )
        );
        return $output;
    }

    /**
     * Convert a formatted log string into an array
     *
     * @param string $logOutput The output from a `git log` command formated using $this->logFormat
     * @return array
     */
    public function parseLogsIntoArray($logOutput)
    {
        $commits = array();

        foreach (explode("\n", $logOutput) as $line) {
            $commitInfo = explode('|', $line);
            $commit = array();

            $i = 0;
            foreach (array_keys($this->logFormat) as $key) {
                $commit[$key] = $commitInfo[$i];
                $i++;
            }

            $commits[] = $commit;

            $commitDate = date('Y-m-d', strtotime($commit['commitDate']));
            $commitHour = date('H', strtotime($commit['commitDate']));
            $commitDay = date('N', strtotime($commit['commitDate']));
            $this->addCommitToStats($this->commitsByDate, $commitDate);
            $this->addCommitToStats($this->commitsByHour, $commitHour);
            $this->addCommitToStats($this->commitsByDay, $commitDay);

            $this->addCommitToContributor($commit);
        }

        return $commits;
    }

    /**
     * Increment commit statistics
     *
     * @param array $stats The array of commits
     * @param string $key Key to increment
     * @return void
     */
    public function addCommitToStats(&$stats, $key)
    {
        if (!isset($stats[$key])) {
            $stats[$key] = 0;
        }
        $stats[$key]++;
    }

    /**
     * Add a commit to contributor statistics
     *
     * @param array $commit
     * @return void
     */
    public function addCommitToContributor($commit)
    {
        $email = trim($commit['commiterEmail']);

        if (!isset($this->commitsByContributor[$email])) {
            $this->commitsByContributor[$email] = array(
                'name' => trim($commit['commiter']),
                'commits' => array()
            );
        }

        $commitDate = date('Y-m-d', strtotime($commit['commitDate']));
        $this->commitsByContributor[$email]['commits'][$commitDate][] = $commit;
    }

    /**
     * Returns array with statistics
     *
     * @return array
     */
    public function getStatistics()
    {
        $statistics = array(
            'charts' => array(
                'date' => $this->getCommitsByDate(),
                'hour' => $this->getCommitsByHour(),
                'day' => $this->getCommitsByDay(),
                'contributor' => $this->getCommitsByContributor(),
            )
        );

        return $statistics;
    }

    /**
     * @return \DateTime
     */
    public function getFirstCommitDate()
    {
        $firstDate = array_slice($this->commitsByDate, 0, 1);
        return new \DateTime(key($firstDate));
    }

    /**
     * @return \DateTime
     */
    public function getLastCommitDate()
    {
        $lastDate = key(array_slice($this->commitsByDate, count($this->commitsByDate) - 1, 1));
        return new \DateTime(date("Y-m-d", strtotime($lastDate . ' +1 day')));
    }

    /**
     * Get statistics for commits by date
     *
     * @return array
     */
    public function getCommitsByDate()
    {
        $begin = $this->getFirstCommitDate();
        $end = $this->getLastCommitDate();
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $data = array();
        foreach ($period as $date) {
            $dayFormatted = $date->format("Y-m-d");
            $value = isset($this->commitsByDate[$dayFormatted]) ? $this->commitsByDate[$dayFormatted] : 0;
            $data['x'][] = $dayFormatted;
            $data['y'][] = $value;
        }
        return $data;
    }

    /**
     * Get statistics for commits by hour of day
     *
     * @return array
     */
    public function getCommitsByHour()
    {
        $data = array();
        ksort($this->commitsByHour);
        foreach ($this->commitsByHour as $hour => $numberOfCommits) {
            $data['x'][] = $hour;
            $data['y'][] = $numberOfCommits;
        }
        return $data;
    }

    /**
     * Get statistics for commits by day of week
     *
     * @return array
     */
    public function getCommitsByDay()
    {
        $data = array();
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

        ksort($this->commitsByDay);
        foreach ($this->commitsByDay as $weekday => $numberOfCommits) {
            $data[] = array($days[$weekday - 1], $numberOfCommits);
        }
        return $data;
    }

    /**
     * Get statistics for contributors
     *
     * @return array
     */
    public function getCommitsByContributor()
    {
        $data = array();

        foreach ($this->commitsByContributor as $email => $contributor) {
            $begin = $this->getFirstCommitDate();
            $end = $this->getLastCommitDate();
            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            $commitsData = array();
            $totalCommits = 0;
            foreach ($period as $date) {
                $dayFormatted = $date->format("Y-m-d");
                $value = isset($contributor['commits'][$dayFormatted]) ?
                    count($contributor['commits'][$dayFormatted]) : 0;
                $totalCommits += $value;

                $commitsData['x'][] = $dayFormatted;
                $commitsData['y'][] = $value;
            }

            $data[] = array(
                'name' => $contributor['name'],
                'email' => $email,
                'commits' => $totalCommits,
                'data' => $commitsData,
            );
        }

        usort($data, array($this, 'sortContributorsByCommits'));

        return $data;
    }

    /**
     * Sort contributors by number of commits they have in descending order
     *
     * @param array $sortA
     * @param array $sortB
     * @return bool
     */
    public function sortContributorsByCommits($sortA, $sortB)
    {
        if ($sortA['commits'] == $sortB['commits']) {
            return 0;
        }
        return ($sortA['commits'] > $sortB['commits']) ? -1 : 1;
    }
}
