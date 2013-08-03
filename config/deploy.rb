default_run_options[:pty] = true
default_run_options[:shell] = "bash"


set :deploy_to, "/srv/www/dev.neurobots.net"
set :current_path, "#{deploy_to}/public_html"
set :shared_path, "#{deploy_to}/shared"


set :scm, "git"
set :repository, "git@github.com:Neurobots/Womp.git"

set :user, "chuck"
set :use_sudo, false
set :ssh_options, { forward_agent: true, paranoid: true, keys: "~/.ssh/id_rsa" }

role :web, "dev.neurobots.net", primary: true

set :copy_exclude, [".git", ".DS_Store", ".gitignore", ".gitmodules", "Capfile", "config/deploy.rb"]

task :finalize_update, :except => { :no_release => true } do
    transaction do
      run "chmod -R g+w #{releases_path}/#{release_name}"
    end
end 


namespace :deploy do
  task :restart, :except => { :no_release => true } do
   puts "nothing to restart"  
  end

  task :symlink_logs, :except => { :no_release => true } do
    run "if [[ -e #{shared_path}/log/error_log ]]; then  
      ln -s #{current_path}/public_html/error_log #{deploy_to}/shared/log/error_log; 
    else
      touch #{deploy_to}/shared/log/error_log;
      ln -s #{current_path}/public_html/error_log #{deploy_to}/shared/log/error_log; 
    fi"
  end

  task :reset_owner, :except => { :no_release => true } do
    run "#{sudo} chown -R www-data:www-data #{current_path}"
  end

end

namespace :file do
  task :permissions do
    
  end
end


 task :brand do
   puts "****     **                                 **                 **          "
   puts "/**/**   /**                                /**                /**         " 
   puts "/**//**  /**  *****  **   ** ******  ****** /**       ******  ******  ******"
   puts "/** //** /** **///**/**  /**//**//* **////**/******  **////**///**/  **//// "
   puts "/**  //**/**/*******/**  /** /** / /**   /**/**///**/**   /**  /**  //***** "
   puts "/**   //****/**//// /**  /** /**   /**   /**/**  /**/**   /**  /**   /////**"
   puts "/**    //***//******//******/***   //****** /****** //******   //**  ****** "
   puts "//     ///  //////  ////// ///     //////  /////    //////     //  //////  "
  end

#Callbacks
after "deploy", "file:permissions", "deploy:reset_owner", :brand


