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
    public function __construct($config = null, $finder = null)
    {
        $this->config = $config;
        $this->finder = ($finder) ? $finder : new Finder;
    }

    public function getPaths ()
    {
        $paths = array();

        if (!$this->paths) {
            if (is_array($this->config)) {
                foreach ($this->config as $repo) {
                    $paths[] = realpath(__DIR__ . '/../../' . $repo . '/');
                }
            }
            // Empty config
            else {
                $baseDirectory = (isset($this->config['repositoriesPath'])) ?
                    $this->config['repositoriesPath'] : 'repositories';

                $directories = $this->finder->directories()->in(__DIR__ . '/../../' . $baseDirectory);

                foreach ($directories as $directory) {
                    $paths[] = $directory->getRealPath();
                }
            }

            $this->paths = $paths;
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
