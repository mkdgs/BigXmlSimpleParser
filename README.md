# BigXmlSimpleParser
The easy way for parsing big Xml file with PHP

- no memory bloat 
- can handle nested level

use:

```php

defening your file and a element name.
These elements will Treated  has a "line" of data by your own defined processhandler.

$parser = new BigXmlSimpleParser(__DIR__ "/data.xml", 'ROW');
$parser->setHandler(function($line, $BigXmlSimpleParser) {
  echo '<pre>
  echo 'line number: '.$BigXmlSimpleParser->lineCounter;  
  print_r($line);
  echo '</pre>;
});

$parser->parse();
```

```
on:
<data>
  <row>
    <ID_ARTISTE>10</ID_ARTISTE>
    <VA_NOM>john</VA_NOM>
    <VA_PRENOM>doe</VA_PRENOM>
  </row>
  <row><ID_ARTISTE>11</ID_ARTISTE><VA_NOM>jane</VA_NOM><VA_PRENOM>doe</VA_PRENOM></row>
  <row><ID_ARTISTE>12</ID_ARTISTE><VA_NOM>foo</VA_NOM><VA_PRENOM>bar</VA_PRENOM></row>
</data>

will return:

line number: 1
stdClass Object
(
    [name] => ROW
    [attribute] => Array
    [data] => 
    [child] => Array
            [0] => stdClass Object
                    [name] => ID_ARTISTE
                    [attribute] => Array
                    [data] => 10
                    [child] => Array
                    [parent] => stdClass Object *RECURSION*
            )

            [1] => stdClass Object
                    [name] => VA_NOM
                    [attribute] => Array
                    [data] => john
                    [child] => Array
                    [parent] => stdClass Object *RECURSION*
           [2] => stdClass Object
                    [name] => VA_PRENOM
                    [attribute] => Array
                    [data] => doe
                    [child] => Array
                    [parent] => stdClass Object *RECURSION*
.......

```
