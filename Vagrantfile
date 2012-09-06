Vagrant::Config.run do |config|
  config.vm.box       = "lucid32"
  config.vm.box_url   = "http://files.vagrantup.com/lucid32.box"
  config.vm.host_name = "wordpress-skeleton.dev"

  # Dedicated IP to avoid conflicts (and no port fowarding!)
  config.vm.network :hostonly, "33.33.33.32"

  # Remount the default shared folder as NFS for caching & speed
  config.vm.share_folder("v-root", "/var/www", "vendor/wordpress/wordpress", :nfs => true)

  config.vm.provision :chef_solo do |chef|
    chef.cookbooks_path = ["cookbooks", "vendor/cookbooks"]

    chef.add_recipe("wordpress-skeleton");
  end
end
