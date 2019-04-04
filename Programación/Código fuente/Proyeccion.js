export class Projection {

    constructor(ns = []) {
        this.nameSpaces = [];
    }

    parserHTML(htmlString) {
        return (new DOMParser()).parseFromString(htmlString, "text/html");
    }

    parserXML(xmlString) {
        return (new DOMParser()).parseFromString(xmlString, "application/xml");
    }

    queryNodes(xpath, node, subquery) {
        let nodesResult = xpath.evaluate(
            subquery, node, null, XPathResult.ANY_TYPE, null);
        
        let nodesResultList = [];

        while (node = nodesResult.iterateNext()) {
            nodesResultList.push(node);
        }

        return nodesResultList;
    }

    queryNodesText(xpath, node, subquery) {
        let nodesResult = xpath.evaluate(
            subquery, node, null, XPathResult.ANY_TYPE, null);
        
        let nodesResultList = [];

        while (node = nodesResult.iterateNext()) {
            nodesResultList.push(node.textContent);
        }

        return nodesResultList;
    }

    recursiveAnalysis(xpath, rootNode, query) {
        let queryResult = [];
        let currentNodesSet = this.queryNodes(xpath, rootNode, query['>_FROOT']);

        currentNodesSet.forEach(node => {
            let nodeResult = {};

            Object.entries(query['>_MAP'])
            .forEach( ([metaKey, subquery]) => {
                if (typeof(subquery)==='string') {
                    let subQueryResult = this.queryNodesText(xpath, node, subquery);
                    if (subQueryResult.length===1) {
                        nodeResult[metaKey] = subQueryResult[0];
                    }
                    if (subQueryResult.length>1) {
                        nodeResult[metaKey] = subQueryResult;
                    }
                }
                if (  typeof(subquery)==='object'
                   && subquery!==null) {
                    if ('>_FROOT' in subquery && '>_MAP' in subquery) {
                        nodeResult[metaKey] = this.recursiveAnalysis(xpath, node, subquery);
                    }
                    if ('>_CONS' in subquery) {
                        nodeResult[metaKey] = subquery['>_CONS'];
                    }
                }
            });

            queryResult.push(nodeResult);
        });

        return queryResult;
    }

    run(document, query) {
        return this.recursiveAnalysis(document, document, query)
    }
}