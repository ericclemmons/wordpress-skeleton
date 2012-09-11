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
