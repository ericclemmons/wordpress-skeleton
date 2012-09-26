require 'pathname'

namespace :wp do
    namespace :db do
        desc "Creates WordPress database"
        task :create do
            pretty_print "Creating database"
            run "#{latest_release}/skeleton wp:db:create --env=#{stage}"
            puts_ok
        end

        desc "Drops WordPress database"
        task :drop do
            pretty_print "Dropping database"
            run "#{latest_release}/skeleton wp:db:drop --env=#{stage}"
            puts_ok
        end

        desc "Backups database locally to /backups directory"
        task :backup do
            pretty_print "Backing up database"
            run "#{latest_release}/skeleton wp:db:backup --env=#{stage}"
            puts_ok

            set :backups_path,      "#{current_release}/backups"
            set :current_backup,    capture("ls -t1 #{backups_path} | head -n1").chomp
            set :backup_path,       File.dirname(File.dirname(__FILE__)) + "/backups/#{current_backup}"

            pretty_print "Downloading #{current_backup}"
            download "#{backups_path}/#{current_backup}", backup_path
            puts_ok

            pretty_print "Decompressing"
            run_locally("gzip -d #{backup_path}")
            puts_ok
        end

        desc "Restores latest local backup to remote database"
        task :restore do
            set :backups_path,      File.dirname(File.dirname(__FILE__)) + "/backups"
            set :current_backup,    run_locally("ls -t1 #{backups_path} | head -n1").chomp
            set :restore_path,       "#{current_release}/backups/restore.#{current_backup}"

            if Capistrano::CLI.ui.ask("Are you sure restore the #{stage} database to #{current_backup}? (y/N) ") == 'y'
                pretty_print "Uploading #{current_backup}"
                upload "#{backups_path}/#{current_backup}", restore_path
                puts_ok

                pretty_print "Restoring database backup"
                run "#{latest_release}/skeleton wp:db:restore --env=#{stage} --file=#{restore_path}"
                puts_ok

                pretty_print "Cleaning up restore files"
                run "rm #{restore_path}"
                puts_ok
            end
        end
    end

    namespace :import do
        desc "Imports the Theme Unit Test into WordPress"
        task :theme_test do
            pretty_print "Importing WordPress Theme Unit Test"
            run "#{latest_release}/skeleton wp:import:theme-test --env=#{stage}"
            puts_ok
        end
    end

    desc "Installs WordPress similar to /wp-admin/install.php"
    task :install do
        pretty_print "Installing WordPress"
        run "#{latest_release}/skeleton wp:install --env=#{stage}"
        puts_ok
    end

    namespace :plugins do
        desc "Installs & activates plugins based on skeleton config"
        task :install do
            pretty_print "Installing WordPress plugins"
            run "#{latest_release}/skeleton wp:plugins:install --env=#{stage}"
            puts_ok
        end
    end

    desc "Symlinks all resources in /src into /web"
    task :symlink do
        wp.theme.symlink

        shared_files = Dir.chdir("src") do
            Dir.glob("**/*").reject { |f| File.directory?(f) || f[application]}
        end

        pretty_print "Symlinking project resources into WordPress"
        shared_files.each do |link|
            basename    = File.basename(link)
            wp_path     = Pathname.new("#{latest_release}/vendor/wordpress/wordpress/#{link}")
            wp_dir      = Pathname.new(File.dirname(wp_path))
            src_path    = Pathname.new("#{latest_release}/src/#{link}").relative_path_from(wp_dir)

            run "mkdir -p #{wp_dir}"
            run "cd #{wp_dir} && ln -nfs #{src_path} #{basename}"
        end
        puts_ok
    end

    namespace :theme do
        desc "Activates theme"
        task :activate do
            pretty_print "Activating theme"
            run "#{latest_release}/skeleton wp:theme:activate --env=#{stage}"
            puts_ok
        end

        desc "Symlinks theme into themes directory"
        task :symlink do
            set :wordpress_path,    Pathname.new("#{latest_release}/vendor/wordpress/wordpress")
            set :themes_path,       Pathname.new("#{wordpress_path}/wp-content/themes")
            set :_s_path,           Pathname.new("#{latest_release}/vendor/automattic/_s").relative_path_from(themes_path)
            set :theme_path,        Pathname.new("#{latest_release}/src/wp-content/themes/#{application}").relative_path_from(themes_path)

            pretty_print "Symlinking _s parent theme"
            run "cd #{themes_path} && ln -nfs #{_s_path} _s"
            puts_ok

            pretty_print "Symlinking #{application} child theme"
            run "cd #{themes_path} && ln -nfs #{theme_path} #{application}"
            puts_ok
        end
    end
end
