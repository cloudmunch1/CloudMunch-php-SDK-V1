#!/usr/bin/python
import argparse, sys
import json
from pprint import pprint
from winrm.protocol import Protocol
import base64

parser = argparse.ArgumentParser(description='PS Interceptor')
parser.add_argument('-jsoninput', metavar='jsoninput parameter', nargs='+',
                   help='parameters for the PS interceptor')
args = parser.parse_args()
if args.jsoninput is None:
    print "App received no parameters. Exiting.."
    sys.exit(100)
#print "Parameters received : "+args.jsoninput[0]
data = json.loads(str(args.jsoninput[0]))
if not 'servername' in data.keys() or not 'serverusername' in data.keys() or not 'password' in data.keys() or (not data["servername"]) or (not data["serverusername"]) or (not data["password"]): 
    print "App received insufficient parameters for execution. Exiting.."
    sys.exit(100)

if 'command' in data.keys():
    script=str(data["command"])
else:
    with open ("/var/cloudbox/CBApp/PowershellApps/"+str(data["appSCname"])+".ps1", "r") as myfile:
        script=myfile.read()

script = script.decode('utf-8')

for key,value in data.iteritems():
    if key:
        if key == "params":
            paramlist = value.split("--")
            for param in paramlist:
                itemlist = param.split()
                if 1 < len(itemlist): 
                    script = script.replace("$$"+itemlist[0], itemlist[1])
        else:
            script = script.replace("$$"+key, value)
script = "\n"+script            
script = base64.b64encode(script.encode("utf-16"))

cmd = "powershell -ExecutionPolicy Bypass -encodedcommand %s" % (script)
p = Protocol(
    endpoint='http://'+data["servername"]+':5985/wsman',
    transport='plaintext',
    username=data["serverusername"],
    password=data["password"])

shell_id = p.open_shell()
command_id = p.run_command(shell_id, cmd)
std_out, std_err, status_code = p.get_command_output(shell_id, command_id)
p.cleanup_command(shell_id, command_id)
p.close_shell(shell_id)
print std_out
if 'erroroi' in data.keys() : print std_err

std_err = std_err.replace("#< CLIXML", "")
error_msg = ""
import xml.etree.ElementTree as ET
root = ET.fromstring(std_err)
i=0;
for child in root:
    i+=1
    if(i>10): 
        if child.text: 
            error_msg += (child.text).replace("_x000D_", "\n").replace("_x000A_", "\r")
print error_msg
if(status_code != 0): print "App execution returned failed status!. Exiting"
sys.exit(status_code)

