<?php
namespace GitPrettyStats;

use Symfony\Component\Finder\Finder;

/**
 * Factory for repositories
 *
 * @author Niklas Modess <niklas@codingswag.com>
 */
class RepositoryFactory
{
    /**
     * @var \PHPGit_Repository  Git binary wrapper
     */
    public $gitWrapper;

    /**
     * @var string  Paths to repositories
     */
    public $paths;

    /**
     * @var string  Base directory for paths
     */
    protected $baseDir;

    /**
     * @var mixed  Configuration values
     */
    protected $config;

    /**
     * @var Symfony\Component\Finder\Finder  File system handler
     */
    protected $finder;

    /**
     * @param string $path Path to repositories
     * @return void
     */
    public function __construct($config = null, $finder = null, $baseDir = null)
    {
        $this->config  = $config;
        $this->finder  = ($finder !== null)  ? $finder : new Finder;
        $this->baseDir = ($baseDir !== null) ? $baseDir : __DIR__ . '/../../';
    }

    public function getPaths ()
    {
        $paths = array();

        if (!$this->paths)
        {
            if (!isset($this->config['repositoriesPath']))
            {
                $repositoriesPath = 'repositories';
            }
            elseif (isset($this->config['repositoriesPath']))
            {
                $repositoriesPath = $this->config['repositoriesPath'];
            }
            elseif (is_array($this->config['repositoriesPath']))
            {
                foreach ($this->config as $repo) {
                    $paths[] = realpath(__DIR__ . '/../../' . $repo . '/');
                }
            }

            $directories = $this->finder->directories()->in($this->baseDir . $repositoriesPath);

            foreach ($directories as $directory) {
                $this->paths[] = $directory->getRealPath();
            }
        }

        return $this->paths;
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
