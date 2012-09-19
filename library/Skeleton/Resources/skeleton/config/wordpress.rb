require 'pathname'

namespace :wp do
    namespace :db do
        desc "Creates WordPress database"
        task :create do
            run "#{latest_release}/skeleton wp:db:create --env=#{stage}"
        end

        desc "Drops WordPress database"
        task :drop do
            run "#{latest_release}/skeleton wp:db:drop --env=#{stage}"
        end
    end

    namespace :config do
        desc "Symlinks wp-config.php into WordPress root"
        task :symlink  do
            set :release_path,      Pathname.new(release_path)
            set :web_path,          Pathname.new("#{release_path}/web")
            set :wordpress_path,    Pathname.new("#{release_path}/vendor/wordpress/wordpress")
            set :config_path,       Pathname.new("#{release_path}/config/deploy/wp-config-#{stage}.php").relative_path_from(wordpress_path)

            run "cd #{web_path} && ln -fs #{config_path} wp-config.php"
        end

        desc "Copies wp-config.php into WordPress root"
        task :copy do
            set :target_dir,    Pathname.new("#{release_path}/web")
            set :target,        Pathname.new("#{target_dir}/wp-config.php") #.relative_path_from(target_dir)
            set :source,        Pathname.new("#{release_path}/config/deploy/wp-config-#{stage}.php") #.relative_path_from(target_dir)

            run "rm -f #{target} && cp #{source} #{target}"
        end
    end

    namespace :import do
        desc "Imports the Theme Unit Test into WordPress"
        task :theme_test do
            run "#{latest_release}/skeleton wp:import:theme-test --env=#{stage}"
        end
    end

    desc "Installs WordPress similar to /wp-admin/install.php"
    task :install do
        run "#{latest_release}/skeleton wp:install --env=#{stage}"
    end

    namespace :plugins do
        # Installs & activates plugins based on skeleton config
        task :install do
            run "#{latest_release}/skeleton wp:plugins:install --env=#{stage}"
        end
    end

    namespace :theme do
        desc "Activates theme"
        task :activate do
            run "#{latest_release}/skeleton wp:theme:activate --env=#{stage}"
        end

        desc "Symlinks theme into themes directory"
        task :symlink do
            set :wordpress_path,    Pathname.new("#{release_path}/vendor/wordpress/wordpress")
            set :themes_path,       Pathname.new("#{wordpress_path}/wp-content/themes")
            set :_s_path,           Pathname.new("#{release_path}/vendor/automattic/_s").relative_path_from(themes_path)
            set :src_path,          Pathname.new("#{release_path}/src").relative_path_from(themes_path)

            run "cd #{themes_path} && ln -fs #{_s_path} _s"
            run "cd #{themes_path} && ln -fs #{src_path} #{application}"
        end

        desc "Copies theme into themes directory"
        task :copy do
            set :target_dir,    Pathname.new("#{release_path}/vendor/wordpress/wordpress/wp-content/themes")
            set :target,        Pathname.new("#{target_dir}/#{application}") #.relative_path_from(target_dir)
            set :source,        Pathname.new("#{release_path}/src") #.relative_path_from(target_dir)

            run "cd #{release_path} && rm -rf #{target} && cp -r #{source} #{target}"
        end
    end
end
