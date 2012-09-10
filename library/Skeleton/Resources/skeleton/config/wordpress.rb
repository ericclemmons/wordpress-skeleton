require 'pathname'

namespace :wordpress do
    namespace :config do
        desc "Symlinks wp-config.php into WordPress root"
        task :symlink do
            set :target_dir,    Pathname.new("#{current_path}/web")
            set :target,        Pathname.new("#{target_dir}/wp-config.php") #.relative_path_from(target_dir)
            set :source,        Pathname.new("#{current_path}/config/deploy/wp-config-#{stage}.php") #.relative_path_from(target_dir)

            run "cd #{target_dir} && rm -f #{target} && ln -s #{source} #{target}"
        end

        desc "Copies wp-config.php into WordPress root"
        task :copy do
            set :target_dir,    Pathname.new("#{current_path}/web")
            set :target,        Pathname.new("#{target_dir}/wp-config.php") #.relative_path_from(target_dir)
            set :source,        Pathname.new("#{current_path}/config/deploy/wp-config-#{stage}.php") #.relative_path_from(target_dir)

            run "rm -f #{target} && cp #{source} #{target}"
        end
    end

    task :install do

    end

    namespace :theme do
        desc "Symlinks theme into themes directory"
        task :symlink do
            set :target_dir,    Pathname.new("#{current_path}/vendor/wordpress/wordpress/wp-content/themes")
            set :target,        Pathname.new("#{target_dir}/#{application}") #.relative_path_from(target_dir)
            set :source,        Pathname.new("#{current_path}/src") #.relative_path_from(target_dir)

            run "cd #{current_path} && rm -f #{target} && ln -s #{source} #{target}"
        end

        desc "Copies theme into themes directory"
        task :copy do
            set :target_dir,    Pathname.new("#{current_path}/vendor/wordpress/wordpress/wp-content/themes")
            set :target,        Pathname.new("#{target_dir}/#{application}") #.relative_path_from(target_dir)
            set :source,        Pathname.new("#{current_path}/src") #.relative_path_from(target_dir)

            run "cd #{current_path} && rm -rf #{target} && cp -r #{source} #{target}"
        end
    end
end
