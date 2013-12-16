<?php
namespace GitPrettyStats;

/**
 * Simple class for fetching all Git repositories from given path
 * @author Niklas Modess <niklas@codingswag.com>
 */
class RepositoryFactory
{
    /** @var \PHPGit_Repository */
    public $gitWrapper;

    /** @var string Path to repositories */
    public $path;

    /** @var mixed Configuration values */
    protected $config;

    /**
     * @param string $path Path to repositories
     * @return void
     */
    public function __construct($config = null)
    {
        $this->config = $config;
    }

    /**
     * Fetch all repositories
     *
     * @return array
     */
    public function all ()
    {
        $repositories        = array();
        $loadRepositoryPaths = array();

        // Config with array of paths to repositories
        if (is_array($this->path)) {
            foreach ($this->path as $repo) {
                $loadRepositoryPaths[] = realpath(__DIR__ . '/../../' . $repo . '/');
            }
        }
        // Config with path to directory of repositories
        elseif ($handle = opendir($this->path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                $loadRepositoryPaths[] = realpath(__DIR__ . '/../../' . $this->path . '/' . $entry);
            }
        }

        // Load repositories
        foreach ($loadRepositoryPaths as $loadRepositoryPath) {
            if ($repository = $this->loadRepository($loadRepositoryPath)) {
                $repositories[] = array(
                    'name'    => $repository->getName(),
                    'commits' => $repository->countCommitsFromGit(),
                    'branch'  => $repository->gitter->getCurrentBranch()
                );
            }
        }

        return $repositories;
    }

    /**
     * Load a repository for given path
     *
     * @param  string $path Location of reposittory
     * @return mixed
     */
    public function loadRepository ($path)
    {
        if ( !is_dir($path)) {
            return false;
        }

        try {
            $repository = new Repository($path);
            return $repository;
        } catch (Exception $e) {
            return false;
        }
    }
}
