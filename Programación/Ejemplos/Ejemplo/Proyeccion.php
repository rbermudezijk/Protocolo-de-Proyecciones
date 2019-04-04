<?php 
namespace IntegrationAPI

/**
 * This class is an represents the XML Projection Engine and it operational
 * parts. Before use this tool is RECOMMENDED read the XML Projection Manual to
 * undestand XML Proyection sintaxis and logical structures.
 * 
 * This program is free software: you can redistribute it and/or modify it
 * under  the terms  of the GNU General Public License as published by the Free
 * Software Foundation,  either version 3 of the License,  or  (at your option)
 * any later version. This  program  is  distributed in  the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even  the  implied  warranty of
 * MERCHANTABILITY  or  FITNESS  FOR  A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details. You should have received a copy of the  GNU
 * General Public License  along with this program.
 * 
 * @author    Ricardo Bermudez Bermudez <ricardob.sistemas@mig.com.mx>
 * @category  IntegrationAPI Core L2
 * @package   IntegrationAPI
 * @copyright GNU General Public License <http://www.gnu.org/licenses/>
 * @version   PXML 1.0
 * @since     f.a. April 2nd, 2019.
 * @link      https://www.ietf.org/rfc/rfc2119.txt Key words for use in RFCs to
                                                   Indicate Requirement Levels
 */
class Projection {
    
    public $nameSpaces;

    /**
     * This function set the nameSpace attribute to use in
     * @link{XMLProjection::projection()} to set XML namespace in DOMDocument.
     * 
     * @param  array[] Asociative array with pairs XML namespace prefix (key or
     *                 index) and URI (value).
     * @return void
     */
    public function __construct($ns = [])
    {
        $this->nameSpaces = $ns;
    }

    /**
     * This function implements >_CONS instruction, to more information read
     * the XML Projection manual.
     * 
     * @param DOMNode  $node     Current node to apply operation.
     * @param DOMXpath $xpath    XPath query engine.
     * @param mixed    $constant Constant value to map in current key/index of
     *                           final structure.
     * 
     * return mixed   Constant to map over result structure.
     */
    private function _CONS($node, $xpath, $constant)
    {
        return $constant;
    }
    
    /**
     * This function implements >_CSET instruction, to more information read
     * the XML Projection.
     * 
     * @param DOMNode  $node  Current node to apply operation.
     * @param DOMXpath $xpath XPath query engine.
     * @param mixed    $args  Array with xpath expression and token string(this
                              argument are optional).
     * 
     * @return array|string  Array or string with data set.
     */
    private function _CSET($node, $xpath, $args)
    {
        $q_nodes = $xpath->query($args[0],$node);

        $divResult = [];
        foreach($q_nodes as $q_node){
            $divResult[] = $q_node->textContent;
        }
        
        if(isset($args[1])){
            $divResult=implode($args[1], $divResult);
        }
        
        return $divResult;
    }
    
    /**
     * Prototype of >_GROUP instruction (Actually is not in use).
     */
    private function _GROUP($node, $xpath, $args)
    {
        $groupResult=[];
        
        $divToken=null;
        
        foreach($args as $index=>$path){
            $groupResult[$index] = $this->_DIV($node, $xpath, $path);
        }
        
        return $groupResult;
    }

    public function parserXML($xmlString)
    {
        $document = new DOMDocument();
        $document->loadXML($xmlString);
        return $document;
    }
    
    public function parserHTML($htmlString)
    {
        $document = new DOMDocument();
        $document->loadHTML($htmlString);
        return $document;
    }
    
    /**
     * This funtion represents the recursive projection operation (For parser
     * and execute subprojections).
     * 
     * @param  DOMXPath $xpath    XPath Query Engine bind to root document.
     * @param  DOMNode  $rootNode Node to apply the current projection.
     * @param  array    $query    Projection descriptor.
     * 
     * @throw  Exception  If TP XPath expression of first level of deep in
     *                    projection descriptor return empty. 
     * 
     * @return array      Query result with projection structure.
     */
    public function recursiveAnalysis($xpath, $rootNode, $query)
    {
        $currentNodesSet = $xpath->query($query['>_FROOT'], $rootNode);
        $queryResult = [];
        
        foreach($currentNodesSet as $node){
            $nodeResult = [];
            
            foreach ($query['>_MAP'] as $indexName => $indexValue) {
                /* Query path. */
                if (is_string($indexValue)) {
                    $q_nodes = $xpath->evaluate($indexValue, $node);
                    
                    if ($q_nodes instanceof DOMNodeList) {
                        if ($q_nodes->length==1) {
                            $nodeResult[$indexName] = $q_nodes->item(0)->textContent;
                        }
                        if ($q_nodes->length>1) {
                            foreach($q_nodes as $q_node){
                                $nodeResult[$indexName][] = $q_node->textContent;
                            }
                        }
                    } else { /**This conditional executes in case of xpath expresion not return nodes.*/
                        $nodeResult[$indexName] = $q_nodes;
                    }
                }
                /* Execute operator. */
                if (is_array($indexValue)) {
                    /*Recursive call function definition*/
                    if (array_key_exists('>_FROOT', $indexValue)
                     && array_key_exists('>_MAP',   $indexValue)) {
                        $nodeResult[$indexName] = $this->recursiveAnalysis($node, $xpath, $indexValue);
                    }
                    /*linear operators*/
                    else{
                        foreach($indexValue as $operation => $args){
                            $nameOper = preg_replace('/>(\w*)/i', '${1}', $operation);
                            $nodeResult[$indexName] = $this->$nameOper($node, $xpath, $args); 
                        }
                    }
                }
            }
            $queryResult[] = $nodeResult;
        }
        
        return $queryResult;
    }
    
    /**
     * This funtion represents the projection operation. For more information
     * read the XML Projection manual.
     * 
     * @param array  $query This array follows tho sintaxis of projection
     *                            descriptor.
     * @param string $xml         Content the XML document to parser.
     * 
     * @throw Exception           If TP XPath expression of first level of deep
                                  in projection descriptor return empty. 
     * 
     * @return array Result of projection operation.
     */
    public function run($document, $query)
    {
        $xpath = new DOMXPath($document);
        
        foreach($this->nameSpaces as $xmlns_key => $xmlns_link){
            $xpath->registerNamespace($xmlns_key,$xmlns_link);
        }
        
        return $this->recursiveAnalysis($xpath, $document, $query);
    }
}