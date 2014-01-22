<?php
namespace GitPrettyStats;

use Carbon\Carbon;
use Gitter\Client;

/**
 * Class Repository
 * @author Niklas Modess <niklas@codingswag.com>
 */
class Repository
{
    /** @var \Gitter\Repository */
    public $gitter;

    /** @var \Gitter\Client */
    public $client;

    /** @var array Storage for "raw" commits */
    public $commits = array();

    /** @var array Storage for commits by date */
    public $commitsByDate = array();

    /** @var array Storage for commits by hour */
    public $commitsByHour = array();

    /** @var array Storage for commits by hour */
    public $commitsByDay = array();

    /** @var array Storage for commits by contributor */
    public $commitsByContributor = array();

    /** @var PrettyGitStats\Statistics */
    public $statistics;

    /** @var array Author email aliases */
    public $emailAliases = array();

    /**
     * Constructor
     *
     * @param \Gitter\Client $client  Git client
     * @return void
     */
    public function __construct($path, $client = null, $statistics = null, $emailAliases = array())
    {
        $this->client       = ($client) ? $client : new Client;
        $this->statistics   = ($statistics) ? $statistics : new Statistics($this);
        $this->gitter       = $this->client->getRepository($path);
        $this->emailAliases = $emailAliases;
    }

    /**
     * @return \Gitter\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get name of repository (top level directory)
     *
     * @return string
     */
    public function getName ()
    {
        $name = $this->gitter->getPath();

        if (strstr($name, '/')) {
            $name = substr($name, strrpos($name, '/') + 1);
        }

        return trim($name);
    }

    /**
     * Count all commits using the git binary
     *
     * @return int
     */
    public function countCommitsFromGit ()
    {
        return (version_compare($this->getClient()->getVersion(), '1.7.2', '>=')) ?
            $this->getClient()->run($this->gitter, 'rev-list --count HEAD') :
            $this->getClient()->run($this->gitter, 'rev-list HEAD | wc -l | tr -d "\n"');
    }

    /**
     * Load commits from git repo
     *
     * @return void
     */
    public function loadCommits()
    {
        $this->commits = $this->gitter->getCommits();

        $this->parseCommits();
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
     * Count number of contributors
     *
     * @return int
     */
    public function getNumberOfContributors()
    {
        return count($this->commitsByContributor);
    }

    /**
     * Parses commits and adds them to stats
     *
     * @return array
     */
    public function parseCommits()
    {
        foreach ($this->commits as $commit) {
            $date = $commit->getCommiterDate()->format('Y-m-d');
            $hour = $commit->getCommiterDate()->format('H');
            $day  = $commit->getCommiterDate()->format('N');

            $this->addCommitToStats($this->commitsByDate, $date);
            $this->addCommitToStats($this->commitsByHour, $hour);
            $this->addCommitToStats($this->commitsByDay, $day);

            $this->addCommitToContributor($commit);
        }
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
        $email = $commit->getAuthor()->getEmail();
        $name  = $commit->getAuthor()->getName();

        if (isset($this->emailAliases[$email])) {
            $email = $this->emailAliases[$email];
        }

        if (!isset($this->commitsByContributor[$email])) {
            $this->commitsByContributor[$email] = array(
                'name'    => $name,
                'commits' => array()
            );
        }

        $date = $commit->getCommiterDate()->format('Y-m-d');

        $this->commitsByContributor[$email]['commits'][$date][] = $commit;
    }

    public function getDaysRepositoryBeenActive ()
    {
        return $this->getFirstCommitDate()->diffInDays($this->getLastCommitDate());
    }

    /**
     * Returns array with statistics and graph data
     *
     * @return array
     */
    public function getStatistics()
    {
        $statistics = array(
            'statistics' => array(
                $this->statistics->commits(),
                $this->statistics->contributors(),
                $this->statistics->contributorsAverageCommits(),
                $this->statistics->firstCommit(),
                $this->statistics->latestCommit(),
                $this->statistics->activeFor(),
                $this->statistics->averageCommitsPerDay(),
            ),
            'charts' => array(
                'date'        => $this->getCommitsByDate(),
                'hour'        => $this->getCommitsByHour(),
                'day'         => $this->getCommitsByDay(),
                'contributor' => $this->getCommitsByContributor(),
            )
        );

        return $statistics;
    }

    /**
     * @return Carbon
     */
    public function getFirstCommitDate()
    {
        ksort($this->commitsByDate);

        $firstDate = array_slice($this->commitsByDate, 0, 1);

        return new Carbon(key($firstDate));
    }

    /**
     * @return Carbon
     */
    public function getLastCommitDate()
    {
        ksort($this->commitsByDate);

        $lastDate = key(array_slice($this->commitsByDate, count($this->commitsByDate) - 1, 1));

        return new Carbon(date("Y-m-d", strtotime($lastDate . ' +1 day')));
    }

    /**
     * Get statistics for commits by date
     *
     * @return array
     */
    public function getCommitsByDate()
    {
        $begin    = $this->getFirstCommitDate();
        $end      = $this->getLastCommitDate();
        $interval = \DateInterval::createFromDateString('1 day');
        $period   = new \DatePeriod($begin, $interval, $end);

        $data = array();
        foreach ($period as $date) {
            $dayFormatted = $date->format("Y-m-d");
            $value        = isset($this->commitsByDate[$dayFormatted]) ? $this->commitsByDate[$dayFormatted] : 0;
            $data['x'][]  = $dayFormatted;
            $data['y'][]  = $value;
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
            $begin    = $this->getFirstCommitDate();
            $end      = $this->getLastCommitDate();
            $interval = \DateInterval::createFromDateString('1 day');
            $period   = new \DatePeriod($begin, $interval, $end);

            $commitsData  = array();
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
                'name'    => $contributor['name'],
                'email'   => $email,
                'commits' => $totalCommits,
                'data'    => $commitsData,
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
