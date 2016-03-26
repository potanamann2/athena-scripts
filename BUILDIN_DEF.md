
```
// arg must follow the pattern: (v|s|i|r|l)*\?*\*?
// 
// 'v' - value (either string or int or reference)
// 's' - string
// 'i' - int
// 'r' - reference (of a variable)
// 'l' - label
// '?' - one optional parameter
// '*' - unknown number of optional parameters
```

## Examples ##
```
BUILDIN_DEF(getskilllv,"v"),
BUILDIN_DEF(mes,"s"),
BUILDIN_DEF(countitem,"i"),
BUILDIN_DEF(getarraysize,"r"),
BUILDIN_DEF(goto,"l"),
BUILDIN_DEF(return,"?"),
BUILDIN_DEF(atcommand,"*"),
```

## See also ##
  * [add\_buildin\_func()](http://rathena.googlecode.com/svn/trunk/src/map/script.c)