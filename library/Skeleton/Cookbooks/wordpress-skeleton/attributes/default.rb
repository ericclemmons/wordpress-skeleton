override['mysql']['server_root_password']   = 'vagrant'

default['wordpress']['db']['database']      = "wordpress"
default['wordpress']['db']['user']          = "vagrant"
default['wordpress']['db']['password']      = "vagrant"
default['wordpress']['dir']                 = "/var/www/wordpress-skeleton/current/web"
default['wordpress']['server_name']         = node['fqdn']
default['wordpress']['server_aliases']      = [node['fqdn']]
