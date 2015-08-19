<?php
/**
* Copyright Desgranges Mickael 
* mickael@mkdgs.fr
* 
* Ce logiciel est un programme informatique servant à la création d'application web. 
* 
* Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
* respectant les principes de diffusion des logiciels libres. Vous pouvez
* utiliser, modifier et/ou redistribuer ce programme sous les conditions
* de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA 
* sur le site "http://www.cecill.info".
* 
* En contrepartie de l'accessibilité au code source et des droits de copie,
* de modification et de redistribution accordés par cette licence, il n'est
* offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
* seule une responsabilité restreinte pèse sur l'auteur du programme,  le
* titulaire des droits patrimoniaux et les concédants successifs.
* 
* A cet égard  l'attention de l'utilisateur est attirée sur les risques
* associés au chargement,  à l'utilisation,  à la modification et/ou au
* développement et à la reproduction du logiciel par l'utilisateur étant 
* donné sa spécificité de logiciel libre, qui peut le rendre complexe à 
* manipuler et qui le réserve donc à des développeurs et des professionnels
* avertis possédant  des  connaissances  informatiques approfondies.  Les
* utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
* logiciel à leurs besoins dans des conditions permettant d'assurer la
* sécurité de leurs systèmes et ou de leurs données et, plus généralement, 
* à l'utiliser et l'exploiter dans les mêmes conditions de sécurité. 
* 
* Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
* pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
* termes.
*
* @author		Desgranges Mickael
* @license		CeciLL-B
* @link			http://mkdgs.fr
*/

class BigXmlSimpleParser {

    protected $_file = "";
    protected $_parser = null;
    protected $_current = null;

    public function __construct($file, $lineElementName = '', $lineHandler = null) {
        $this->_file = $file;
        $this->_parser = xml_parser_create("UTF-8");
        xml_set_object($this->_parser, $this);
        xml_set_element_handler($this->_parser, "startTag", "endTag");
        xml_set_character_data_handler($this->_parser, "characterData");
        $this->lineCounter = 0;
        $this->lineElementName = $lineElementName;
        $this->lineHandler = $lineHandler;
        $this->endHandler  = null;
    }

    public function setLineHandler($lineHandler) {
        $this->lineHandler = $lineHandler;
    }

    public function setEndHandler($endHandler) {
        $this->endHandler = $endHandler;
    }
    
    protected function process($line) {
        $this->lineCounter++;
        call_user_func($this->lineHandler, $line, $this);
    }
    
    protected function endHandler() {
        if ( $this->endHandler ) {
            call_user_func($this->endHandler, $line, $this);
        }
    } 

    protected function addElement($name, $attr) {
        $el = new \stdClass();
        $el->name = $name;
        $el->attribute = $attr;
        $el->data = null;
        $el->child = array();
        $el->parent = ( $this->_current ) ? $this->_current : null;
        $this->_current = $el;

        if ($el->parent) {
            $el->parent->child[] = $el;
        }
    }

    public function startTag($parser, $name, $attr) {
        if ($this->_current || ( $name == $this->lineElementName )) {
            $this->addElement($name, $attr);
        }
    }

    public function endTag($parser, $name) {
        if (!$this->_current)
            return;
        if ($name == $this->lineElementName && ( $this->_current->parent == null )) {
            $this->process($this->_current);
            $this->_current = null;
        } else {
            $this->_current = $this->_current->parent;
        }
    }

    public function characterData($parser, $data) {
        if ($this->_current)
            $this->_current->data .= trim($data);
    }

    public function parse() {
        $fh = fopen($this->_file, "r");
        if (!$fh) {
            throw new \Exception('can\'t open file:' . $this->file);
        }

        while (!feof($fh)) {
            $data = fread($fh, 4096);
            xml_parse($this->_parser, $data, feof($fh));
        }
        
        $this->endHandler($this);
        xml_parser_free($this->_parser);
    }
}
