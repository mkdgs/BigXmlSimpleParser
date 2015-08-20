# BigXmlSimpleParser

Big Xml Simple Parser, the easy way for parsing very large XML file with PHP

this class slices your XML file by the element you have defined and lets you process each subpart of your xml
- based on SAX Xml parser (http://php.net/manual/fr/function.xml-parse.php)
- no memory bloat 
- can handle nested element 


use:

```php

// defening your file and a element name <row>
$parser = new BigXmlSimpleParser(__DIR__ "/data.xml", 'ROW');
// each xml tag <row> will sliced and treated has a "line" of data by your own defined processhandler.
$parser->setLineHandler(function($line, $BigXmlSimpleParser) {
  echo '<pre>';
  echo 'element content'. "\r\n";
  echo 'line number: ' . $BigXmlSimpleParser->lineCounter . "\r\n";
  echo 'name : ' . $line->child[1]->data . "\r\n";
  echo 'surname : ' . $line->child[2]->data . "\r\n";
  // if you want all attributes (array)
  // echo 'attribute : ' . print_r($line->attribute, true). "\r\n";
  // if you want all children elements (array of element)
  // echo 'child : ' . print_r($line->child, true). "\r\n";
  print_r($line);
  echo '</pre>';
});

$parser->parse();
```

```
on:
<data>
  <row>
    <ID_ARTISTE>10</ID_ARTISTE>
    <VA_NOM>doe</VA_NOM>
    <VA_PRENOM>john</VA_PRENOM>
  </row>
  <row><ID_ARTISTE>11</ID_ARTISTE><VA_NOM>jane</VA_NOM><VA_PRENOM>doe</VA_PRENOM></row>
  <row><ID_ARTISTE>12</ID_ARTISTE><VA_NOM>foo</VA_NOM><VA_PRENOM>bar</VA_PRENOM></row>
</data>

will return:

element content
line number: 1
name       : doe
surname    : john

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
                    [data] => doe
                    [child] => Array
                    [parent] => stdClass Object *RECURSION*
           [2] => stdClass Object
                    [name] => VA_PRENOM
                    [attribute] => Array
                    [data] => john
                    [child] => Array
                    [parent] => stdClass Object *RECURSION*
.......

```
