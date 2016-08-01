<?php
namespace CloudMunch;

use Exception;
class PSConnection{

    /**
    *    Execute a given command on a remote server using poershell 
    *    @param  string servername       :  server IP to run command at
    *    @param  string username         :  username on the server 
    *    @param  string password         :  password on the server 
    *    @param  string cmd              :  command to be executed
    *    @return array  output      :  console output of command execution
    *                   exit_status :  exit status of the command executed
    */
    public function executePowershellCommand($servername, $username, $password, $cmd, $live=true) {
        $powershell_interceptor = "python ".dirname(__FILE__)."/libraries/powershell-interceptor.py"; 

        while (@ ob_end_flush()); // end all output buffers if any
        $proc = popen("$powershell_interceptor -jsoninput '{\"servername\":\"$servername\",\"serverusername\":\"$username\" ,\"password\":\"$password\" ,\"command\":\"$cmd;\" }' 2>&1 ; echo Exit status : $?", 'r');

        $live_output     = "";
        $complete_output = "";

        while (!feof($proc)) {
            $live_output     = fread($proc, 4096);
            $complete_output = $complete_output . $live_output;
            if($live) echo "$live_output";
            @ flush();
        }

        pclose($proc);

        // get exit status
        preg_match('/[0-9]+$/', $complete_output, $matches);
        
        // return exit status and intended output
        return array (
                        'exit_status'  => $matches[0],
                        'output'       => str_replace("Exit status : " . $matches[0], '', $complete_output)
                     );
    }

}
?>
