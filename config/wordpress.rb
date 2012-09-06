namespace :wordpress do
    namespace :config do
        desc "Symlinks wp-config.php into WordPress"
        task :symlink do
            set :wp_config, "wp-config-#{stage}.php"
            set :source,    "#{current_path}/config/deploy/#{wp_config}"
            set :target,    "#{current_path}/web/wp-config.php"

            run "rm -f #{target} && ln -s #{source} #{target}"
        end
    end

    task :install do

    end
end
