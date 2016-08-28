<a name="Comments"></a>

## Comments[¶](#Comments)

To be more visible, don't put a block in comment with /*  
but comment each line with //

<a name="Indentation"></a>

## Indentation[¶](#Indentation)

3 spaces

Line width : 100

```PHP
<?php
// base level
    // level 1
        // level 2
    // level 1
// base level
```

STATE: accepted : 3 spaces / 100 chars max

<a name="Control-structures"></a>

## Control structures[¶](#Control-structures)

Multiple conditions in several idented lines

```PHP
<?php
if ($test1) {
     for ($i=0 ; $i<$end ; $i++) {
          echo "test ".( $i<10 ? "0$i" : $i )."<br>";
     }
}

if ($a==$b
    || ($c==$d && $e==$f)) {
   ...
} else if {
   ... 
}

switch ($test2) {
     case 1 :
          echo "Case 1";
          break;

     case 2 :
          echo "Case 2";
          // No break here : because...

     default :
          echo "Default Case";
}
```

STATE: accepted

<a name="Including-files"></a>

## Including files[¶](#Including-files)

include_once in order to include the file once and to raise warning if file does not exists

`include_once(GLPI_ROOT."/inc/includes.php");`

STATE: accepted

DONE: require_once , include, require -> include_once

<a name="PHP-tags"></a>

## PHP tags[¶](#PHP-tags)

Short tag not allowed. Use complet tags.

```PHP
<?php

// code

```

STATE : accepted

TODO : nothing

<a name="Functions"></a>

## Functions[¶](#Functions)

Function names should be written in camelBack, for example:

```PHP
<?php
function userName($a, $bldkjqmjk , $cldqkjmlqdsjkm, $ldqjlqdskj, $peaoizuoiauz, 
                  $lqdkjlqsdmj) {
}

```

<a name="Call-static-function"></a>

### Call static function[¶](#Call-static-function)

If the static function is  
- in the class => self::  
- in parent class => parent::  
- in another class => ClassName::

STATE : accepted

TODO : check all functions

<a name="Class"></a>

## Class[¶](#Class)

Class names should be written in [CamelCase](/projects/glpi/wiki/CamelCase?parent=CodingStandards), for example:

```PHP
<?php
class [[ExampleAuthentification]] {
}

```

STATE : accepted

TODO : check all classes

<a name="Variables"></a>

## Variables[¶](#Variables)

Variable names should be as descriptive and short as possible.

Normal variables should be written in lower case. In case of multiple words use the _ separator.

Global variables should be written in UPPER case. In case of multiple words use the _ separator.

Example:

```PHP
<?php
$user         = 'glpi';
$users        = array('glpi', 'glpi2', 'glpi3'); // put elements in alphabetic order
$users        = array('glpi1'  => 'valeur1',
                      'nexglpi => array('down' => '1',                                        
                                        'up'   => array('firstfield'
                                                         => 'newvalue)),  // if too long for width of colomns 
                      'glpi2   => 'valeur2');
$users_groups = array('glpi', 'glpi2', 'glpi3');

$CFG_GLPI = array();
```

STATE : accepted

<a name="Variable-types"></a>

## Variable types[¶](#Variable-types)

Variable types for use in [DocBlocks](/projects/glpi/wiki/DocBlocks?parent=CodingStandards) for Doxygen:

<table>

<tbody>

<tr>

<td>Type</td>

<td>Description</td>

</tr>

<tr>

<td>mixed</td>

<td>A variable with undefined (or multiple) type.</td>

</tr>

<tr>

<td>integer</td>

<td>Integer type variable (whole number).</td>

</tr>

<tr>

<td>float</td>

<td>Float type (point number).</td>

</tr>

<tr>

<td>boolean</td>

<td>Logical type (true or false).</td>

</tr>

<tr>

<td>string</td>

<td>String type (any value in "" or ' ').</td>

</tr>

<tr>

<td>array</td>

<td>Array type.</td>

</tr>

<tr>

<td>object</td>

<td>Object type.</td>

</tr>

<tr>

<td>ressource</td>

<td>Resource type (returned by for example mysql_connect()).</td>

</tr>

</tbody>

</table>

Inserting comment in source code for doxygen.  
Result : full doc for variables, functions, classes...

STATE : accepted

TODO : check all source code

<a name="quotes-double-quotes"></a>

## quotes / double quotes[¶](#quotes-double-quotes)

`echo 'dqmkdqmsl';
echo 'toto'.$test.' est vivant';
`

After reading bench about strings : [http://www.estvideo.net/dew/index/page/phpbench](http://www.estvideo.net/dew/index/page/phpbench)  
- Best choice seems to be simple quote.  
ex : echo 'toto' not echo "toto"  
- best is to concat vars and string.  
ex : echo 'toto'.$test.' est vivant.'  
- Best choice between echo and print is echo.

    - Best choice to construct string before make echo, result : decrease number of use echo.

Performance says to use simple quotes but it make using \n slower (using a constant)

**Conclusion : Use double quotes (more lisible)**

STATE : accepted

TODO : check all source code

<a name="Files"></a>

## Files[¶](#Files)

Name in lower case.

Maximum line length : 100 characters

STATE : accepted

TODO : check all files

<a name="Constants"></a>

## Constants[¶](#Constants)

Capital letter :

`COMPUTER_TYPE
`

STATE : accepted

TODO : check all constants

<a name="MySQL"></a>

## [MySQL](/projects/glpi/wiki/MySQL?parent=CodingStandards)[¶](#MySQL)

Queries must be written onto several lines, one [MySQL](/projects/glpi/wiki/MySQL?parent=CodingStandards) item by line.

All [MySQL](/projects/glpi/wiki/MySQL?parent=CodingStandards) words in UPPER case.

All item based must be slash protected (table name, field name, condition).

All values from variable, even integer should be single quoted

```SQL
$query = "SELECT *
          FROM `glpi_computers`
          LEFT JOIN `xyzt` ON (`glpi_computers`.`fk_xyzt` = `xyzt`.`id`
                               AND `xyzt`.`toto` = 'jk')
          WHERE @id@ = '32'
                AND ( `glpi_computers`.`name` LIKE '%toto%'
                      OR `glpi_computers`.`name` LIKE '%tata%' )
          ORDER BY `glpi_computers`.`date_mod` ASC
          LIMIT 1";
```

```SQL
$query = "INSERT INTO `glpi_alerts`
                (`itemtype`, `items_id`, `type`, `date`) // put field's names to avoid mistakes when names of fields change
          VALUE ('contract', '5', '2', NOW())";
```

STATE : accepted

TODO : check all source code

<a name="PHP"></a>

## [PHP](/projects/glpi/wiki/PHP?parent=CodingStandards)[¶](#PHP)

To optimize PHP7, it's better to not use string concatenation

`echo "<input type='hidden' name='" . $name . "' value='" . $options['_projecttasks_id'] . "'>";`

must be written  

`echo "<input type='hidden' name='$name' value='{$options['_projecttasks_id']}'>";`