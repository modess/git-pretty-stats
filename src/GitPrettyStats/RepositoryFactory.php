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
     * Paths to repositories
     *
     * @var  array
     */
    protected $paths;

    /**
     * Base directory for paths
     *
     * @var  string
     */
    protected $baseDir;

    /**
     * Configuration values
     *
     * @var  array|null
     */
    protected $config;

    /**
     * File system handler
     *
     * @var  Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * Loaded repositories
     *
     * @var  array
     */
    protected $repositories;

    /**
     * Create a new factory
     *
     * @param  array $config  Configuration values
     * @param  Symfony\Component\Finder\Finder File system handler
     * @param  string $baseDir Base directory for Git Pretty Stats
     * @return void
     */
    public function __construct($config = null, $finder = null, $baseDir = null)
    {
        $this->config  = $config;
        $this->finder  = ($finder !== null)  ? $finder : new Finder;
        $this->baseDir = ($baseDir !== null) ? $baseDir : __DIR__ . '/../../';
    }

    /**
     * Get all paths to repositories from config
     *
     * @return array
     */
    public function getPaths ()
    {
        $paths = array();

        if (!$this->paths)
        {
            // No config file exists or repositories path not set
            if (!isset($this->config['repositoriesPath']))
            {
                $repositoriesPath = 'repositories';
                $directories = $this->finder->depth(0)->directories()->in($this->baseDir . $repositoriesPath);
            }
            // Repositories are specified as array in config
            elseif (is_array($this->config['repositoriesPath']))
            {
                $paths = array();
                foreach ($this->config['repositoriesPath'] as $path) {
                    $paths[] = $path;
                }

                $directories = $this->finder->depth(0)->directories()->append($paths);
            }
            // Custom repository path
            elseif (isset($this->config['repositoriesPath']))
            {
                $repositoriesPath = $this->config['repositoriesPath'];
                $directories = $this->finder->depth(0)->directories()->in($this->baseDir . $repositoriesPath);
            }

            // Real paths for all repositories
            foreach ($directories as $key => $directory) {
                $this->paths[$key] = $directory->getRealPath();
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
        if ($this->repositories) {
            return $this->repositories;
        }

        // Load repositories
        foreach ($this->getPaths() as $path) {
            if ($repository = $this->load($path)) {
                $repositories[ $repository->getName() ] = $repository;
            }
        }

        $this->repositories = $repositories;

        return $repositories;
    }

    /**
     * Get repository from short name (top level directory)
     *
     * @param  string $name
     * @return GitPrettyStats\Repository|bool
     */
    public function fromName ($name)
    {
        return isset($this->repositories[$name]) ? $this->repositories[$name] : false;
    }

    /**
     * Load a repository for given path
     *
     * @param  string $path
     * @return GitPrettyStats\Repository|false
     */
    public function load ($path)
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

    /**
     * Set repositories
     *
     * @param array $repositories
     */
    public function setRepositories (array $repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Get repositories
     *
     * @return array
     */
    public function getRepositories ()
    {
        return $this->repositories;
    }

    /**
     * Return all repositories in array format
     *
     * @return array
     */
    public function toArray ()
    {
        $toArray = array();

        foreach ($this->all() as $repository) {
            $toArray[ $repository->getName() ] = array(
                'name'    => $repository->getName(),
                'commits' => $repository->countCommitsFromGit(),
                'branch'  => $repository->gitter->getCurrentBranch()
            );
        }

        return $toArray;
    }
}
