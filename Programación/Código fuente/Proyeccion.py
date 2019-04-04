import lxml import html, etree

from pathlib import Path

class Projection(object):
    """
     This class is an represents the XML/HTML Projection Engine and it operational
     parts. Before use this tool is RECOMMENDED  read  the XML Projection
     Manual to undestand XML Proyection sintaxis and logical structures.
     
     This program is free software: you can redistribute it and/or modify it
     under  the terms  of the GNU General Public License as published by the
     Free  Software Foundation,  either version 3 of the License,  or  (at
     your option)  any later version. This  program  is  distributed in  the
     hope that it will be useful, but WITHOUT ANY WARRANTY; without even  the
     implied  warranty of MERCHANTABILITY  or  FITNESS  FOR  A PARTICULAR
     PURPOSE. See the GNU General  Public License for more details. You should
     have received a copy of the GNU General Public License  along with this
     program.
    """
    nameSpaces = []

    def __init__(self, ns=[]):
        self.nameSpaces = ns

    def parser_html(html_str):
        return html.fromstring(html_str)

    def parser_xml(xml_str):
        return etree.parse(xml_str)

    def recursive_analysis(self, root_node, query):
        current_nodes_set = root_node.xpath(query['>_FROOT'])
        query_result = []
        
        for node in current_nodes_set:
            node_result = {}

            for meta_key, subquery in query['>_MAP'].items():
                if isinstance(subquery, str):
                    sub_query_result = node.xpath(subquery)
                    if len(sub_query_result)==1:
                        node_result[meta_key] = sub_query_result[0]
                    if len(sub_query_result)>1:
                        node_result[meta_key] = sub_query_result
                
                if isinstance(subquery, dict):
                    if '>_FROOT' in subquery and '>_MAP' in subquery:
                        node_result[meta_key] = self.recursive_analysis(node, subquery)
                    if '>_CONS' in subquery:
                        node_result[meta_key] = subquery['>_CONS']
            query_result.append(node_result)
        
        return query_result
    
    def run(self, document, query):
        return self.recursive_analysis(document, query)