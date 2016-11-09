Gamification Plugin for Moodle for use with Exacomp and DAKORA
------------------------------------------

This is an experimental plugin for Moodle. it adds gamification elements to the DAKORA app and requires an installed Exabis Competencies plugin (https://github.com/gtn/exacomp).

##Setup
1- install the plugin in moodle/local/gamification/

2- Add the following to Moodle's configuration file:   

```
$CFG->gamificationmode = 3; 	// Settings for the gamification plugin.
			    				// 0 = No visible changes
								// 1 = Points only
								// 2 = Badges only
								// 3 = Points and Badges
								// Note: 	This is based on a bitmask, allowing for hassle-free addition of binary options.
```
