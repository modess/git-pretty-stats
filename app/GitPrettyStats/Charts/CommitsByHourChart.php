<?php namespace GitPrettyStats\Charts;

class CommitsByHourChart extends AbstractChart
{
    /**
     * Generate chart
     *
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'x' => array(),
            'y' => array()
        );


        $statistics    = $this->getRepository()->getStatistics();
        $commitsByHour = $statistics['hour']->getItems();

        foreach ($commitsByHour as $hour => $commits) {
            $data['x'][] = $hour;
            $data['y'][] = count($commits);
        }

        return $data;
    }
}
