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
     * @var  array Loaded repositories
     */
    protected $repositories;

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
                $directories = $this->finder->depth(0)->directories()->in($this->baseDir . $repositoriesPath);
            }
            elseif (is_array($this->config['repositoriesPath']))
            {
                $paths = array();
                foreach ($this->config['repositoriesPath'] as $path) {
                    $paths[] = $path;
                }

                $directories = $this->finder->depth(0)->directories()->append($paths);
            }
            elseif (isset($this->config['repositoriesPath']))
            {
                $repositoriesPath = $this->config['repositoriesPath'];
                $directories = $this->finder->depth(0)->directories()->in($this->baseDir . $repositoriesPath);
            }

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

    public function fromName ($name)
    {
        return isset($this->repositories[$name]) ? $this->repositories[$name] : false;
    }

    /**
     * Load a repository for given path
     *
     * @param  string $path Location of reposittory
     * @return mixed
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
