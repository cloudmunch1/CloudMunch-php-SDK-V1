#!/usr/bin/python

import argparse, sys
import json
from pprint import pprint
import winrm
import sys
from winrm.protocol import Protocol
import base64

try:
    # parse and validate parameters to the interceptor
    parser = argparse.ArgumentParser(description='PS Interceptor')
    parser.add_argument('-jsoninput', metavar='jsoninput parameter', nargs='+',
                       help='parameters for the PS interceptor')
    args = parser.parse_args()
    if args.jsoninput is None:
        print "App received no parameters. Exiting.."
        sys.exit(100)
    args.jsoninput[0] = args.jsoninput[0].replace('\\', '\\\\')
except ValueError:
    print "Could not parse command parameters for starting winrm session."
    sys.exit(100)
except:
    print "Unexpected error:", sys.exc_info()[0]
    raise
try:
    data = json.loads(str(args.jsoninput[0]))
except ValueError:
    print "Could not parse json input for starting winrm session."
    sys.exit(100)
except:
    print "Unexpected error:", sys.exc_info()[0]
    raise

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
script = script.replace("\n", ";")

if "\\" in data["serverusername"]:
    #script = base64.b64encode(script.encode("utf-16"))
    cmd = "powershell -ExecutionPolicy Bypass -command %s" % (script)
    p = Protocol(
        endpoint='http://'+data["servername"]+':5985/wsman',
        transport='ntlm',
        username=data["serverusername"],
        password=data["password"],
        server_cert_validation='ignore')
    shell_id = p.open_shell()
    command_id = p.run_command(shell_id, cmd)
    std_out, std_err, status_code = p.get_command_output(shell_id, command_id)
    p.cleanup_command(shell_id, command_id)
    p.close_shell(shell_id)
else:
    script = "\n"+script
    s = winrm.Session(data["servername"], auth=(data["serverusername"], data["password"]))
    try:
        r = s.run_ps(script)
#    except winrm.exceptions.UnauthorizedError:
#        print "Could not authenticate on the remote server:", sys.exc_info()[1]
#        sys.exit(100)
    except:
        print "Unexpected error"
        raise

    std_out = r.std_out
    std_err = r.std_err
    status_code = r.status_code

print std_out

if std_err:
    print std_err

sys.exit(status_code)