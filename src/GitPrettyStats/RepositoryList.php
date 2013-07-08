<?php
namespace GitPrettyStats;

/**
 * Simple class for fetching all Git repositories from given path
 * @author Niklas Modess <niklas@codingswag.com>
 */
class RepositoryList
{
    /** @var \PHPGit_Repository */
    public $gitWrapper;

    /** @var string Path to repositories */
    public $path;

    /**
     * @param string $path Path to repositories
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Fetch all repositories
     *
     * @return array
     */
    public function getRepositories ()
    {
        $repositories = array();

        if ($handle = opendir($this->path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                try {
                    $gitWrapper = new \PHPGit_Repository(__DIR__ . '/../../' . $this->path . '/' . $entry);
                    $repository = new Repository($gitWrapper);
                    $repositories[] = array(
                        'name' => $repository->getName(),
                        'commits' => $repository->countCommitsFromGit(),
                        'branch' => $repository->getGitWrapper()->getCurrentBranch()
                    );
                } catch (Exception $e) {
                    // Not a valid repository
                }
            }
        }

        return $repositories;
    }
}
