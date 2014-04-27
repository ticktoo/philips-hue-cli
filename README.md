philips-hue-cli
===============

Control your Philips Hue light system using a command line


Requirements
============

- php5-cli
- php5-curl
 
Preparations
============

Enter your Bridge IP and Username in lines 247 and 248.


Example usage
=============

Show all lamps and current Status
./huecli --list

# Turn all lights on
./huecli --all --on

# Turn specific lamps on
./huecli --lamp 1 --lamp 3 --on

# set Color
./huecli --lamp 4 --color blue

# set Brightness 100%
./huecli --lamp 4 --brightness 100

# set Brightness 50%
./huecli --lamp 4 --brightness 50

# combine actions (take care for the order of your arguments!)
./huecli --lamp 4 --on --color red --brightness 20

# Freak out:
./huecli --all --freakout

# Calm down (reverts the freakout)
./huecli --all --calmdown

# blink (blinks for 30 seconds)
./huecli --lamp 4 --color green --blink
