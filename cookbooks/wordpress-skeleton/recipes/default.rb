# WordPress dependencies
include_recipe "apt"
include_recipe "apache2"
include_recipe "mysql::server"
include_recipe "php"
include_recipe "php::module_mysql"
include_recipe "apache2::mod_php5"

# Disable default Apache site
apache_site "000-default" do
  enable false
end

# Create WordPress VHost
web_app "wordpress" do
  template "wordpress.conf.erb"
  docroot "#{node['wordpress']['dir']}"
  server_name node['wordpress']['server_name']
  server_aliases node['wordpress']['server_aliases']
end

# Copy WordPress DB permissions
template "#{node['mysql']['conf_dir']}/wp-grants.sql" do
  source "grants.sql.erb"
  owner "root"
  group "root"
  mode "0600"
  variables(
    :user     => node['wordpress']['db']['user'],
    :password => node['wordpress']['db']['password'],
    :database => node['wordpress']['db']['database']
  )
  # notifies :run, "execute[mysql-install-wp-privileges]", :immediately
end

# Setup WordPress DB permissions
execute "mysql-install-wp-privileges" do
  command "/usr/bin/mysql -u root -p\"#{node['mysql']['server_root_password']}\" < #{node['mysql']['conf_dir']}/wp-grants.sql"
end

# Create WordPress DB
execute "mysql-install-wp-database" do
  command "/usr/bin/mysqladmin -u root -p\"#{node['mysql']['server_root_password']}\" create #{node['wordpress']['db']['database']} 2> /dev/null"
end
