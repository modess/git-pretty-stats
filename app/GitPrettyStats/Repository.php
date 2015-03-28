<?php
namespace GitPrettyStats;

use Carbon\Carbon;
use GitPrettyStats\Charts\CommitsByContributorChart;
use GitPrettyStats\Charts\CommitsByDateChart;
use GitPrettyStats\Charts\CommitsByDayChart;
use GitPrettyStats\Charts\CommitsByHourChart;
use Gitter\Client;
use Config;
use Cache;
use Gitter\Repository as GitterRepository;

/**
 * Class Repository
 * @author Niklas Modess <niklas@codingswag.com>
 */
class Repository extends GitterRepository
{
    /** @var \GitPrettyStats\StatisticsFormatter */
    public $statisticsFormatter;

    /** @var array Author email aliases */
    public $emailAliases = array();

    /**
     * Constructor
     *
     * @param string              $path
     * @param Client              $client Git client
     * @param StatisticsFormatter $statisticsFormatter
     * @return \GitPrettyStats\Repository
     */
    public function __construct($path, Client $client = null, StatisticsFormatter $statisticsFormatter = null)
    {
        $this->statisticsFormatter = $statisticsFormatter ?: new StatisticsFormatter($this);

        $emailAliases       = Config::get('git-pretty-stats.emailAliases');
        $this->emailAliases = ($emailAliases && is_array($emailAliases)) ? $emailAliases : null;

        $client = ($client) ? $client : new Client;
        parent::__construct($path, $client);
    }

    /**
     * Get email alias
     *
     * @param string $email
     * @return string
     */
    public function getEmailAlias($email)
    {
        return isset($this->emailAliases[$email]) ? $this->emailAliases[$email] : $email;
    }

    /**
     * Returns array with statistics and graph data
     *
     * @return array
     */
    public function statistics()
    {
        $commitsByDateChart        = new CommitsByDateChart($this);
        $commitsByHourChart        = new CommitsByHourChart($this);
        $commitsByDayChart         = new CommitsByDayChart($this);
        $commitsByContributorChart = new CommitsByContributorChart($this);

        return array(
            'statistics' => array(
                $this->statisticsFormatter->totalNumberOfCommits(),
                $this->statisticsFormatter->contributors(),
                $this->statisticsFormatter->contributorsAverageCommits(),
                $this->statisticsFormatter->firstCommitDate(),
                $this->statisticsFormatter->latestCommitDate(),
                $this->statisticsFormatter->activeFor(),
                $this->statisticsFormatter->averageCommitsPerDay(),
            ),
            'charts' => array(
                'date'        => $commitsByDateChart->toArray(),
                'hour'        => $commitsByHourChart->toArray(),
                'day'         => $commitsByDayChart->toArray(),
                'contributor' => $commitsByContributorChart->toArray(),
            )
        );
    }

    /**
     * Get first commit date
     *
     * @return string
     */
    public function getFirstCommitDate ()
    {
        $statistics    = $this->getStatistics();
        $commitsByDate = $statistics['date'];
        $commits       = $commitsByDate->getItems();
        $firstDate     = key(array_slice($commits, 0, 1));

        return $firstDate;
    }

    /**
     * Get last commit date
     *
     * @return string
     */
    public function getLastCommitDate ()
    {
        $statistics    = $this->getStatistics();
        $commitsByDate = $statistics['date'];
        $commits       = $commitsByDate->getItems();
        $lastDate      = key(array_slice($commits, -1));

        return $lastDate;
    }

}
