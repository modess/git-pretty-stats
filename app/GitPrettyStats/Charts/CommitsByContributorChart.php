<?php namespace GitPrettyStats\Charts;

class CommitsByContributorChart extends AbstractChart
{
    /**
     * Generate chart
     *
     * @return array
     */
    public function toArray()
    {
        $data = array();

        $statistics           = $this->getRepository()->getStatistics();
        $commitsByContributor = $statistics['contributors']->getItems();

        foreach ($commitsByContributor as $contributor) {
            $contributorInfo = null;
            $begin           = new \DateTime($this->getRepository()->getFirstCommitDate());
            $end             = new \DateTime($this->getRepository()->getLastCommitDate());
            $interval        = \DateInterval::createFromDateString('1 day');
            $period          = new \DatePeriod($begin, $interval, $end);

            $commitsData  = array();
            $totalCommits = 0;

            $contributorCommits = $contributor->getItems();

            foreach ($period as $date) {
                $dayFormatted = $date->format("Y-m-d");

                $value = isset($contributorCommits[$dayFormatted]) ? count($contributorCommits[$dayFormatted]) : 0;

                if ($value > 0 && $contributorInfo === null) {
                    $commit = $contributorCommits[$dayFormatted][0];
                    $contributorInfo = array(
                        'name'  => $commit->getAuthor()->getName(),
                        'email' => $commit->getAuthor()->getEmail(),
                    );
                }

                $totalCommits += $value;

                $commitsData['x'][] = $dayFormatted;
                $commitsData['y'][] = $value;
            }

            $data[] = array(
                'name'    => $contributorInfo['name'],
                'email'   => $contributorInfo['email'],
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
