<?php namespace GitPrettyStats\Charts;

class CommitsByDateChart extends AbstractChart
{
    /**
     * Generate chart
     *
     * @return array
     */
    public function toArray()
    {
        $begin    = new \DateTime($this->getRepository()->getFirstCommitDate());
        $end      = new \DateTime($this->getRepository()->getLastCommitDate());
        $interval = \DateInterval::createFromDateString('1 day');
        $period   = new \DatePeriod($begin, $interval, $end);

        $statistics    = $this->getRepository()->getStatistics();
        $commitsByDate = $statistics['date']->getItems();

        $data = array(
            'x' => array(),
            'y' => array()
        );

        foreach ($period as $date) {
            $dayFormatted = $date->format("Y-m-d");
            $value        = isset($commitsByDate[$dayFormatted]) ? count($commitsByDate[$dayFormatted]) : 0;
            $data['x'][]  = $dayFormatted;
            $data['y'][]  = $value;
        }

        return $data;
    }
}
