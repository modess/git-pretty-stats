<?php

namespace PrettyGit;

/**
 * Class GitRepository
 * @author Niklas Modess <niklas@codingswag.com>
 */
class GitRepository extends \PHPGit_Repository
{
    /**
     * Storage for "raw" commits
     *
     * @var array
     */
    protected $commits = array();

    /**
     * Date format
     *
     * @var string
     */
    protected $dateFormat = 'iso';

    /**
     * Mapper for fetching information about commits
     *
     * @var array
     */
    protected $logFormat = array(
        'commiter' => '%cn',
        'commiterEmail' => '%ce',
        'commitDate' => '%cd',
    );

    /**
     * Array for storing commits by date
     *
     * @var array
     */
    protected $commitsByDate = array();

    /**
     * Array for storing commits by hour
     *
     * @var array
     */
    protected $commitsByHour = array();

    /**
     * Array for storing commits by hour
     *
     * @var array
     */
    protected $commitsByDay = array();

    /**
     * Constructor
     *
     * @param string $path Path to repository
     * @return void
     */
    public function __construct($path)
    {
        parent::__construct($path);

        $this->_loadCommits();
    }

    /**
     * Return name of git repo (top level folder)
     *
     * @return string
     */
    public function getName()
    {
        $topLevelDir = $this->git('rev-parse --show-toplevel');
        $topLevelDir = substr($topLevelDir, strrpos($topLevelDir, '/') + 1);

        return $topLevelDir;
    }

    /**
     * Load commits from git repo
     *
     * @return void
     */
    private function _loadCommits()
    {
        $this->commits = $this->getCommits(-1);
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
    public function getCommits($nbCommits = 10)
    {
        $output = $this->git(
            sprintf(
                '--no-pager log -n %d --date=%s --format=format:"%s" --reverse',
                $nbCommits,
                $this->dateFormat,
                implode('|',$this->logFormat)
            )
        );
        return $this->parseLogsIntoArray($output);
    }

    /**
     * Convert a formatted log string into an array
     * @param string $logOutput The output from a `git log` command formated using $this->logFormat
     */
    private function parseLogsIntoArray($logOutput)
    {
        $commits = array();

        foreach(explode("\n", $logOutput) as $line) {
            $commitInfo = explode('|', $line);
            $commit = array();

            $i = 0;
            foreach ($this->logFormat as $key => $value) {
                $commit[$key] = $commitInfo[$i];
                $i++;
            }

            $commits[] = $commit;

            $commitDate = date('Y-m-d', strtotime($commit['commitDate']));
            $commitHour = date('H', strtotime($commit['commitDate']));
            $commitDay = date('N', strtotime($commit['commitDate']));
            $this->_addCommitToStats($commit, $this->commitsByDate, $commitDate);
            $this->_addCommitToStats($commit, $this->commitsByHour, $commitHour);
            $this->_addCommitToStats($commit, $this->commitsByDay, $commitDay);
        }
        return $commits;
    }

    private function _addCommitToStats($commit, &$stats, $key) {
        if (!isset($stats[$key])) {
            $stats[$key] = 0;
        }
        $stats[$key]++;
    }

    /**
     * Returns array for index page with statistics for charts
     *
     * @return array
     */
    public function getStatisticsForIndex()
    {
        $statistics = array(
            'commits_by_date' => $this->_getCommitsByDate(),
            'commits_by_hour' => $this->_getCommitsByHour(),
            'commits_by_day' => $this->_getCommitsByDay(),
        );

        return $statistics;
    }

    /**
     * Get statistics for commits by date
     *
     * @return array
     */
    private function _getCommitsByDate()
    {
        $firstDate = array_slice($this->commitsByDate, 0, 1);
        $lastDate = array_slice($this->commitsByDate, count($this->commitsByDate) - 1, 1);

        $begin = new \DateTime(key($firstDate));
        $end = new \DateTime(key($lastDate));
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);

        $labels = array();
        $data = array();
        $i = 0;
        foreach ($period as $date) {
            $dayFormatted = $date->format("Y-m-d");
            $value = isset($this->commitsByDate[$dayFormatted]) ? $this->commitsByDate[$dayFormatted] : 0;
            $data[] = array(
                'y' => $value,
                'x' => sprintf(
                    'new Date(%s, %s, %s)',
                    $date->format("Y"),
                    $date->format("n"),
                    $date->format("d")
                )
            );
        }
        return $data;
    }

    /**
     * Get statistics for commits by hour of day
     *
     * @return array
     */
    private function _getCommitsByHour()
    {
        $data = array();
        foreach ($this->commitsByHour as $hour => $numberOfCommits) {
            $data[] = array(
                'y' => $numberOfCommits,
                'x' => $hour,
            );
        }
        return $data;
    }

    /**
     * Get statistics for commits by day of week
     *
     * @return array
     */
    private function _getCommitsByDay()
    {
        $data = array();
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        foreach ($this->commitsByDay as $weekday => $numberOfCommits) {
            $data[] = array(
                'label' => $days[$weekday],
                'y' => $numberOfCommits,
                'x' => $weekday,
            );
        }
        return $data;
    }

}

