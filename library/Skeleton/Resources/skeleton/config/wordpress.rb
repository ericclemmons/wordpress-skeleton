require 'pathname'

namespace :wordpress do
    namespace :db do
        desc "Creates WordPress database"
        task :create do
            run "#{latest_release}/bin/console wordpress:db:create --env=#{stage}"
        end

        desc "Drops WordPress database"
        task :drop do
            run "#{latest_release}/bin/console wordpress:db:drop --env=#{stage}"
        end
    end

    namespace :config do
        desc "Symlinks wp-config.php into WordPress root"
        task :symlink  do
            set :wordpress_dir, "#{release_path}/vendor/wordpress/wordpress"
            set :web_dir,       "#{release_path}/web"
            set :config_path,   "#{latest_release}/web/wp-config.php"

            run "rm -f #{web_dir} && ln -s  #{wordpress_dir} #{web_dir}"
            run "rm -f #{config_path} && ln -s #{release_path}/config/deploy/wp-config-#{stage}.php #{config_path}"
        end

        desc "Copies wp-config.php into WordPress root"
        task :copy do
            set :target_dir,    Pathname.new("#{release_path}/web")
            set :target,        Pathname.new("#{target_dir}/wp-config.php") #.relative_path_from(target_dir)
            set :source,        Pathname.new("#{release_path}/config/deploy/wp-config-#{stage}.php") #.relative_path_from(target_dir)

            run "rm -f #{target} && cp #{source} #{target}"
        end
    end

    desc "Installs WordPress similar to /wp-admin/install.php"
    task :install do
        run "#{latest_release}/bin/console wordpress:install --env=#{stage}"
    end

    desc "Symlinks WordPress root to /web"
    task :symlink  do
        set :wordpress_dir, "#{release_path}/vendor/wordpress/wordpress"
        set :web_dir,       "#{release_path}/web"

        run "rm -f #{web_dir} && ln -s  #{wordpress_dir} #{web_dir}"
    end


    namespace :theme do
        desc "Activates theme"
        task :activate do
            run "#{latest_release}/bin/console wordpress:theme:activate --env=#{stage}"
        end

        desc "Symlinks theme into themes directory"
        task :symlink do
            set :theme_dir, Pathname.new("#{release_path}/vendor/wordpress/wordpress/wp-content/themes")

            run "rm -f #{theme_dir}/_s && ln -s #{release_path}/vendor/automattic/_s #{theme_dir}/_s"
            run "rm -f #{theme_dir}/#{application} && ln -s #{release_path}/src #{theme_dir}/#{application}"
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
