<?php namespace GitPrettyStats\Charts;

class CommitsByDayChart extends AbstractChart
{
    /**
     * Generate chart
     *
     * @return array
     */
    public function toArray()
    {
        $statistics   = $this->getRepository()->getStatistics();
        $commitsByDay = $statistics['day']->getItems();

        $data = array();
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

        foreach ($commitsByDay as $weekday => $commits) {
            $data[] = array($days[$weekday - 1], count($commits));
        }

        return $data;
    }
}
