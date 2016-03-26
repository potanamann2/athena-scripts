## Instructions ##
  1. `ulimit -c unlimited`
  1. `nano /etc/profile`
```
# No core files by default
# ulimit -S -c 0 > /dev/null 2>&1
```
  1. `nano /etc/security/limits.conf`
```
*               soft    core            unlimited
```


## See also ##
  * http://rathena.org/wiki/GDB
  * http://nn.nachtwolke.com/m/eawm/index.php?title=GDB
  * http://en.wikipedia.org/wiki/Core_dump