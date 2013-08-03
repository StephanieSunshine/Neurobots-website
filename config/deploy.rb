default_run_options[:pty] = true
default_run_options[:shell] = "bash"


set :deploy_to, "/home/chuck/deploy"
set :current_path, "#{deploy_to}/public_html"

set :ssh_options, { forward_agent: true, paranoid: true, keys: "~/.ssh/id_rsa" }

set :scm, "git"
set :repository, "git@github.com:Neurobots/Womp.git"

set :user, "chuck"
set :use_sudo, false

role :web, "dev.neurobots.net", primary: true

set :copy_exclude, [".git", ".DS_Store", ".gitignore", ".gitmodules", "Capfile", "config/deploy.rb"]

task :finalize_update, :except => { :no_release => true } do
    transaction do
      run "chmod -R g+w #{releases_path}/#{release_name}"
      run "cd #{current_path} && chown -r www-data:www-data public_html/*"
    end
end 


namespace :deploy do
  task :restart, :except => { :no_release => true } do
   puts "nothing to restart"  
  end

  task :symlink_logs, :except => { :no_release => true } do
    run "ln -s #{deploy_to}/shared/logs/error_log #{current_path}/public_html/error_log"
    run "ln -s #{deploy_to}/shared/logs/access_log #{current_path}public_html/access_log"
  end

end

namespace :file do
  task :permissions do
    
  end
end


 task :brand, :except => { :no_release => true } do
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
after "deploy:restart", :brand 
after "deploy", "file:permissions", "deploy:symlink_logs"


