<?php

namespace Skeleton\Command;

use Skeleton\Console\Helper\DialogHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigureSkeletonCommand extends SkeletonCommand
{
    private $config;

    protected function configure()
    {
        $this->setDescription('Configures skeleton for development & deployment');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('no-interaction')) {
            throw new \Exception('This command must be ran interactively');
        }

        $output->writeln('Generating...');

        $generated = $this->skeleton->generateSkeleton($this->config);

        foreach ($generated as $file) {
            $output->writeln(sprintf("\tGenerated <info>%s</info>", $file));
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        $dialog->writeSection($output, 'Welcome to the WordPress Skeleton Configurator!');

        // Initial Skeleton with local environment
        $this->config = $this->askProjectInformation($input, $output);
        $this->config += array(
            'wordpress'             => array(
                'local'             => array(
                    'salts'         => $this->guessSalts(),
                    'db'            => array(
                        'name'      => 'wordpress_local',
                        'user'      => 'vagrant',
                        'password'  => 'vagrant',
                        'host'      => 'localhost',
                    ),
                ),
            ),
            'deploy'                => array(
                'local'             => array(
                    'web'           => array(
                        'host'      => 'local.'.$this->config['domain'],
                        'ip'        => $this->skeleton->get('deploy.local.web.ip') ?: $this->guessIp('local.'.$this->config['domain']),
                        'user'      => 'vagrant',
                        'password'  => 'vagrant',
                    ),
                    'db'            => array(
                        'user'      => 'root',
                        'password'  => 'vagrant',
                    ),
                ),
            ),
        );

        foreach (array('stage', 'prod') as $env) {
            $continue = $dialog->askConfirmation($output, $dialog->getQuestion(sprintf('Setup <info>%s</info> environment?', $env), 'y'));

            if (!$continue) {
                continue;
            }

            $this->config['deploy'][$env]       = $this->askDeploymentInformation($input, $output, $env);
            $this->config['wordpress'][$env]    = $this->askWordPressInformation($input, $output, $env);
        }
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');

        if (!$dialog || !$dialog instanceof DialogHelper) {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

    private function askDeploymentInformation(InputInterface $intput, OutputInterface $output, $env)
    {
        $dialog = $this->getDialogHelper();

        $dialog->writeSection($output, sprintf('%s SSH Connection Settings', ucfirst($env)));

        $defaultHost        = $this->skeleton->get('deploy.%s.web.host', $env) ?: sprintf('%s.%s', ($env === 'prod') ? 'www' : $env, $this->config['domain']);
        $webHost            = $dialog->askAndValidate($output, $dialog->getQuestion('Host Name', $defaultHost), array('Skeleton\Command\Validators', 'validateHost'), false, $defaultHost);

        $defaultIp          = $this->skeleton->get('deploy.%s.web.ip', $env) ?: $this->guessIp($webHost) ?: null;
        $webIp              = $dialog->askAndValidate($output, $dialog->getQuestion('IP Address', $defaultIp), array('Skeleton\Command\Validators', 'validateIp'), false, $defaultIp);

        $defaultUser        = $this->skeleton->get('deploy.%s.web.user', $env);
        $webUser            = $dialog->ask($output, $dialog->getQuestion('SSH User', $defaultUser), $defaultUser);

        $defaultPassword    = $this->skeleton->get('deploy.%s.web.password', $env);
        $webPassword        = $dialog->ask($output, $dialog->getQuestion('SSH Password', $defaultPassword), $defaultPassword);

        $dialog->writeSection($output, sprintf('%s Database Administration Settings', ucfirst($env)));

        $defaultDbUser      = $this->skeleton->get('deploy.%s.db.user', $env) ?: $webUser;
        $dbUser             = $dialog->ask($output, $dialog->getQuestion('Database Administrator User', $defaultDbUser), $defaultDbUser);

        $defaultDbPassword  = $this->skeleton->get('deploy.%s.db.password', $env) ?: $webPassword;
        $dbPassword         = $dialog->ask($output, $dialog->getQuestion('Database Administrator Password', $defaultDbPassword), $defaultDbPassword);

        $output->writeln('');

        return array(
            'web'   => array(
                'host'      => $webHost,
                'ip'        => $webIp,
                'user'      => $webUser,
                'password'  => $webPassword,
            ),
            'db'    => array(
                'user'      => $dbUser,
                'password'  => $dbPassword,
            ),
        );
    }

    private function askProjectInformation(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        $repo   = $this->guessRepository();
        $repo   = $dialog->askAndValidate($output, $dialog->getQuestion('Repository URL', $repo), array('Skeleton\Command\Validators', 'validateRepository'), false, $repo);

        $name   = $this->guessName($repo);
        $name   = $dialog->askAndValidate($output, $dialog->getQuestion('Project name', $name), array('Skeleton\Command\Validators', 'validateName'), false, $name);

        $domain = $this->guessDomain($repo);
        $domain = $dialog->askAndValidate($output, $dialog->getQuestion('Destination domain', $domain), array('Skeleton\Command\Validators', 'validateHost'), false, $domain);

        $author = $this->guessAuthor();
        $author = $dialog->ask($output, $dialog->getQuestion('Author name', $author), $author);

        $output->writeln('');

        return array(
            'name'                  => $name,
            'description'           => 'Generated by WordPress Skeleton',
            'author'                => $author,
            'domain'                => $domain,
            'repository'            => $repo,
        );
    }

    private function askWordPressInformation(InputInterface $input, OutputInterface $output, $env)
    {
        $dialog = $this->getDialogHelper();

        $dialog->writeSection($output, sprintf('%s WordPress Settings (wp-config.php)', ucfirst($env)));

        $defaultHost        = $this->skeleton->get('wordpress.%s.db.host', $env) ?: 'localhost';
        $dbHost             = $dialog->ask($output, $dialog->getQuestion('Database Host', $defaultHost), $defaultHost);

        $defaultDbName      = $this->skeleton->get('wordpress.%s.db.name', $env) ?: 'wordpress_'.$env;
        $dbName             = $dialog->ask($output, $dialog->getQuestion('Database Name', $defaultDbName), $defaultDbName);

        $defaultDbUser      = $this->skeleton->get('wordpress.%s.db.user', $env) ?: $this->config['deploy'][$env]['db']['user'] ?: 'admin';
        $dbUser             = $dialog->ask($output, $dialog->getQuestion('Database User', $defaultDbUser), $defaultDbUser);

        $defaultDbPassword  = $this->skeleton->get('wordpress.%s.db.password', $env) ?: $this->config['deploy'][$env]['db']['password'] ?: $this->guessPassword();
        $dbPassword         = $dialog->ask($output, $dialog->getQuestion('Database Password', $defaultDbPassword), $defaultDbPassword);

        $output->writeln('');

        return array(
            'salts'         => $this->skeleton->get('wordpress.%s.salts', $env) ?: $this->guessSalts(),
            'db'            => array(
                'name'      => $dbName,
                'user'      => $dbUser,
                'password'  => $dbPassword,
                'host'      => $dbHost,
            ),
        );
    }

    private function guessAuthor()
    {
        if ($this->skeleton->has('author')) {
            return $this->skeleton->get('author');
        }

        return trim(`git config user.name`) ?: null;
    }

    private function guessDomain($repo)
    {
        if ($this->skeleton->has('domain')) {
            return $this->skeleton->get('domain');
        }

        $name   = strtolower(basename($repo, '.git'));
        $domain = sprintf('%s.%s',
            pathinfo($name, PATHINFO_FILENAME),
            pathinfo($name, PATHINFO_EXTENSION) ?: 'com'
        );

        return $domain;
    }

    private function getEnvs()
    {
        return array('local', 'stage', 'prod');
    }

    private function guessIp($host = null)
    {
        if ($ip = filter_var(gethostbyname($host), FILTER_VALIDATE_IP)) {
            return $ip;
        }

        $blocks = array(
            array('10.0.0.0', '10.255.255.255'),
            array('172.16.0.0', '172.31.255.255'),
            array('192.168.0.0', '192.168.255.255'),
        );

        $block  = $blocks[array_rand($blocks)];
        $range  = array_map('ip2long', $block);

        $long   = rand(current($range) + 1, end($range) - 1);
        $ip     = long2ip($long);

        return $ip;
    }

    private function guessName($repo)
    {
        if ($this->skeleton->has('name')) {
            return $this->skeleton->get('name');
        }

        $filename   = pathinfo($repo, PATHINFO_FILENAME);
        $name       = ucwords(preg_replace('/[-]+/', ' ', $filename));

        return $name ?: null;
    }

    private function guessPassword()
    {
        return strip_tags(file_get_contents('http://www.zimplicit.se/api/password/12/1'));
    }

    private function guessRepository()
    {
        if ($this->skeleton->has('repository')) {
            return $this->skeleton->get('repository');
        }

        $parts = explode('Fetch URL: ', trim(`git remote show -n origin | grep "Fetch URL"`));

        return end($parts) ?: null;
    }

    private function guessSalts()
    {
        return Validators::validateSalts(file_get_contents('https://api.wordpress.org/secret-key/1.1/salt/'));
    }
}
