/*!
 * VisualEditor DataModel MWTable class.
 *
 * @copyright 2011-2018 VisualEditor Team and others; see AUTHORS.txt
 * @license The MIT License (MIT); see LICENSE.txt
 */

/**
 * DataModel MediaWiki table node.
 *
 * @class
 * @extends ve.dm.TableNode
 * @mixins ve.dm.ClassAttributeNode
 *
 * @constructor
 * @param {Object} [element] Reference to element in linear model
 * @param {ve.dm.Node[]} [children]
 */
ve.dm.MWTableNode = function VeDmMWTableNode() {
	// Parent constructor
	ve.dm.MWTableNode.super.apply( this, arguments );

	// Mixin constructors
	ve.dm.ClassAttributeNode.call( this );
};

/* Inheritance */

OO.inheritClass( ve.dm.MWTableNode, ve.dm.TableNode );

OO.mixinClass( ve.dm.MWTableNode, ve.dm.ClassAttributeNode );

/* Static Properties */

ve.dm.MWTableNode.static.name = 'mwTable';

ve.dm.MWTableNode.static.classAttributes = {
	wikitable: { wikitable: true },
	sortable: { sortable: true },
	'mw-collapsible': { collapsible: true },
	'mw-collapsed': { collapsed: true }
};

// Tables in wikitext only work in some contexts, they're impossible e.g. in list items
ve.dm.MWTableNode.static.suggestedParentNodeTypes = [ 'document', 'div', 'tableCell', 'tableCaption', 'mwImageCaption' ];

// HACK: users of parentNodeTypes should be fixed to check for inherited classes.
ve.dm.TableSectionNode.static.parentNodeTypes.push( 'mwTable' );
ve.dm.TableCaptionNode.static.parentNodeTypes.push( 'mwTable' );
ve.dm.TableRowNode.static.childNodeTypes.push( 'mwTransclusionTableCell' );

ve.dm.MWTableNode.static.toDataElement = function ( domElements ) {
	var attributes = {},
		dataElement = { type: this.name },
		classAttr = domElements[ 0 ].getAttribute( 'class' );

	this.setClassAttributes( attributes, classAttr );

	if ( !ve.isEmptyObject( attributes ) ) {
		dataElement.attributes = attributes;
	}
	return dataElement;
};

ve.dm.MWTableNode.static.toDomElements = function ( dataElement, doc ) {
	var element = doc.createElement( 'table' ),
		classAttr = dataElement.attributes && this.getClassAttrFromAttributes( dataElement.attributes );

	if ( classAttr ) {
		element.className = classAttr;
	}

	return [ element ];
};

ve.dm.MWTableNode.static.sanitize = function ( dataElement ) {
	// Mixin method
	ve.dm.ClassAttributeNode.static.sanitize.call( this, dataElement );

	ve.setProp( dataElement, 'attributes', 'wikitable', true );
};

/* Registration */

ve.dm.modelRegistry.register( ve.dm.MWTableNode );
