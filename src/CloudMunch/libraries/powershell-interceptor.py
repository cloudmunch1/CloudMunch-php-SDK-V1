#!/usr/bin/python

import argparse, sys
import json
from pprint import pprint
import winrm
import sys

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

print "Parameters received : "+args.jsoninput[0]
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
script = "\n"+script            

s = winrm.Session(data["servername"], auth=(data["serverusername"], data["password"]))
r = s.run_ps(script)

print r.std_out

if r.std_err:
    print r.std_err

sys.exit(r.status_code)