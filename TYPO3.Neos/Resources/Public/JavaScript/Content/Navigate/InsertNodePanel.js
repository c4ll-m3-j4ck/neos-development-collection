define(
	[
		'emberjs',
		'Content/Components/AbstractInsertNodePanel',
		'Shared/NodeTypeService',
		'Shared/I18n'
	],
	function(
		Ember,
		AbstractInsertNodePanel,
		NodeTypeService,
		I18n
	) {
		return AbstractInsertNodePanel.extend({
			// List of allowed node types (strings); with constraints already evaluated.
			allowedNodeTypes: Ember.required,

			init: function() {
				this._super();
				var nodeTypeGroups = this.get('nodeTypeGroups');
				this.get('allowedNodeTypes').forEach(function(nodeTypeName) {
					var nodeType = NodeTypeService.getNodeTypeDefinition(nodeTypeName);

					if (!nodeType || !nodeType.ui) {
						return;
					}

					var helpMessage = '';
					if (nodeType.ui.help && nodeType.ui.help.message) {
						helpMessage = nodeType.ui.help.message;
					}

					var groupName = nodeType.ui.group || 'general';
					nodeTypeGroups.findBy('name', groupName).get('nodeTypes').pushObject({
						'nodeType': nodeTypeName,
						'label': I18n.translate(nodeType.ui.label),
						'helpMessage': helpMessage,
						'icon': nodeType.ui.icon || 'icon-file',
						'position': nodeType.ui.position
					});
				});
			}
		});
	}
);
