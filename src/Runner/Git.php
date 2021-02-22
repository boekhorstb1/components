<?php
/**
 * Horde\Components\Runner\Git:: runner for git operations.
 *
 * PHP Version 7
 *
 * @category Horde
 * @package  Components
 * @author   Ralf Lang <lang@b1-systems.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Components\Runner;
use Horde\Components\Config;
use Horde\Components\Exception;
use Horde\Components\Output;
use Horde\Components\Helper\Git as GitHelper;

/**
 * Horde\Components\Runner\Git:: runner for git operations.
 *
 * Copyright 2020-2021 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @package  Components
 * @author   Ralf Lang <lang@b1-systems.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
class Git
{
    /**
     * The configuration for the current job.
     *
     * @var Config
     */
    private $config;

    /**
     * The output handler.
     *
     * @var Output
     */
    private $output;

    /**
     * The repo base url.
     *
     * @var string
     */
    private $gitRepoBase;

    /**
     * Constructor.
     *
     * @param Config    $config  The configuration for the current job.
     * @param Output    $output  The output handler.
     * @param GitHelper $git     The output handler.
     */
    public function __construct(
        Config $config,
        Output $output,
        GitHelper $git
    ) {
        $this->config  = $config;
        $this->output  = $output;
        $this->gitHelper = $git;
        $options = $this->config->getOptions();
        $this->gitRepoBase = $options['git_repo_base'] ?? 
        'https://github.com/horde/';
        $this->localCheckoutDir = $options['checkout_dir'] ?? '/srv/git/';

    }

    public function run()
    {
        $arguments = $this->config->getArguments();
        if (count($arguments) == 1) {
            $this->output->help('For usage help, run: horde-components help git');
            return;
        }
        if ($arguments[1] == 'clone' && count($arguments) > 1) {
            /** 
             * TODO: Mind cwd
             * TODO: Mind Pretend Mode
             */
            if (empty($arguments[2])) {
                $this->output->help('Provide a component name.');
                $this->output->help('Cloning all components has not yet been ported from git-tools');
                return;    
            }
            $component = $arguments[2];
            $branch = $arguments[4] ?? '';
            $componentDir = $this->localCheckoutDir . $component . '/';
            $cloneUrl = $this->gitRepoBase . '/' .  $component . '.git';
            // Achieved fixed format, delegate to helper
            return $this->gitHelper->workflowClone(
                $this->output,
                $cloneUrl,
                $componentDir,
                $branch
            );
        }
        if ($arguments[1] == 'checkout') {
            if (count($arguments) != 4) {
                $this->output->help('checkout currently only supports a fixed format');
                $this->output->help('checkout component branch');    
            }
            list($git, $action, $component, $branch) = $arguments;
            $componentDir = $this->localCheckoutDir . $component . '/';
            return $this->gitHelper->workflowCheckout(
                $this->output,
                $componentDir,
                $branch
            );
        }
        if ($arguments[1] == 'fetch') {
            if (count($arguments) != 3) {
                $this->output->help('fetch currently only supports a fixed format');
                $this->output->help('fetch [component]');
                return;
            }
            list($git, $action, $component) = $arguments;
            $componentDir = $this->localCheckoutDir . $component . '/';
            $this->gitHelper->fetch($componentDir);
        }
        if ($arguments[1] == 'branch') {
            if (count($arguments) != 5) {
                $this->output->help('branch currently only supports a fixed format');
                $this->output->help('branch [component] [branch] [source branch]');
            }            
            list($git, $action, $component, $branch, $source) = $arguments;
            $componentDir = $this->localCheckoutDir . $component . '/';
            $this->gitHelper(
                $this->output,
                $componentDir,
                $branch,
                $source                
            );
        }
        if ($arguments[1] == 'tag') {
            if (count($arguments) != 6) {
                $this->output->help('tag currently only supports a fixed format');
                $this->output->help('tag component branch tagname comment');
            }            
            list($git, $action, $component, $branch, $tag, $comment) = $arguments;
            $componentDir = $this->localCheckoutDir . $component . '/';
            if (!$this->gitHelper->localBranchExists($componentDir, $branch)) {
                $this->output->warn("Cannot tag, local branch does not exist");
                return;
            }
            $this->gitHelper->checkoutBranch($componentDir, $branch);
            // Do we really want to update existing tags?
            $this->gitHelper->tag($componentDir, $tag, $comment);
            return;
        }
        if ($arguments[1] == 'push') {
            if (count($arguments) != 3) {
                $this->output->help('push currently only supports a fixed format');
                $this->output->help('push component');
            }
            list($git, $action, $component) = $arguments;
            $componentDir = $this->localCheckoutDir . $component . '/';            
            $this->gitHelper->push($componentDir);
            return;
        }
        $this->output->warn("Could not understand your command:");
        $this->output->warn(implode(" ", $arguments));
    }
}
