/*!
 * Craft by Pixel & Tonic
 *
 * @package   Craft
 * @author    Pixel & Tonic, Inc.
 * @copyright Copyright (c) 2013, Pixel & Tonic, Inc.
 * @license   http://buildwithcraft.com/license1.0.html Craft License
 * @link      http://buildwithcraft.com
 */

// define the Assets global
if (typeof Assets == 'undefined')
{
	Assets = {};
}

/**
 * Asset Operation Manager
 */
Assets.IndexingManager = Garnish.Base.extend({

	$startOperationsButton: null,
	$sourceMasterCheckbox: null,
	$sourceCheckboxes: null,
	$indexCheckbox: null,
	$transformsCheckbox: null,
	$transformMasterCheckbox: null,
	$transformCheckboxes: null,
	$progressBarContainer: null,


	sessionId: null,
	queue: null,

	modal: null,

	missingFolders: [],

	init: function()
	{
		this.$startOperationsButton = $('#start-operations');

		this.$sourceMasterCheckbox = $('.assets-sources input[type=checkbox].all');
		this.$sourceCheckboxes = $('.assets-sources input[type=checkbox]').not('.all');

		this.$progressBarContainer = $('.operation-progress');

		this.$modalContainerDiv = null;

		this.addListener(this.$startOperationsButton, 'click', 'startOperations');
	},

	startOperations: function ()
	{
		if (this.$startOperationsButton.hasClass('disabled'))
		{
			return;
		}

		var checkedSources = this.$sourceCheckboxes.filter(':checked');

		if (checkedSources.length == 0)
		{
			this.$startOperationsButton.removeClass('disabled');
			return;
		}

		this.$startOperationsButton.addClass('disabled');

		this.$sourceMasterCheckbox.prop('disabled', true);
		this.$sourceCheckboxes.prop('disabled', true);

		Craft.postActionRequest('assetIndexing/getSessionId', $.proxy(function(data){
			this.sessionId = data.sessionId;
			this.missingFolders = [];
			this.queue = new AjaxQueueManager(10, this.displayIndexingReport, this);

			this.$progressBarContainer.empty();
			var _t = this;

			checkedSources.each(function () {
                var $checkbox = $(this);
                var sourceName = $checkbox.parent().text();
                var progress_bar = $('<div class="progress-bar"><label>' + sourceName + '</label><span></span></div>').appendTo(_t.$progressBarContainer);
                var params = {
					sourceId: $checkbox.val(),
					session: _t.sessionId
				};

				_t.queue.addItem(Craft.getActionUrl('assetIndexing/startIndex'), params, $.proxy(function (data) {

                    if (typeof data != "object")
                    {
                        $checkbox.prop('checked', false);
                        alert(Craft.t('There was an error while indexing {source}: {message}', {source: sourceName.trim(), message: data}));
                        return;
                    }

					progress_bar.attr('total', data.total).attr('current', 0);
					for (var i = 0; i < data.total; i++) {
						params = {
							session: this.sessionId,
							sourceId: data.sourceId,
							offset: i
						};

						this.queue.addItem(Craft.getActionUrl('assetIndexing/performIndex'), params, function () {
							progress_bar.attr('current', parseInt(progress_bar.attr('current'), 10) + 1);
							progress_bar.find('>span').html(progress_bar.attr('current') + ' / ' + progress_bar.attr('total'));
						});
					}

					if (typeof data.missingFolders != "undefined") {
						for (var folder_id in data.missingFolders) {
							this.missingFolders.push({folder_id: folder_id, folder_name: data.missingFolders[folder_id]});
						}
					}
				}, _t));
			});
			this.queue.startQueue();
		}, this));
	},

	/**
	 * Display Indexing report after all is done
	 */
	displayIndexingReport: function () {

		this.$startOperationsButton.removeClass('disabled');
		this.$progressBarContainer.html('');

		var checkedSources = [];

		this.$sourceCheckboxes.filter(':checked').each(function () {
			checkedSources.push($(this).val());
		});

		var params = {
			sessionId: this.sessionId,
			command: JSON.stringify({command: 'statistics'}),
			sources: checkedSources.join(",")
		};

		$.post(Craft.getActionUrl('assetIndexing/finishIndex'), params, $.proxy(function (data) {
			var html = '';

			if (typeof data.files != "undefined" || this.missingFolders.length > 0) {

                if (this.$modalContainerDiv == null) {
                    this.$modalContainerDiv = $('<div class="modal index-report"></div>').addClass().appendTo(Garnish.$bod);
                }

                if (this.modal == null) {
                    this.modal = new Garnish.Modal();
                    this.modal.sessionId = this.sessionId;
                    this.modal.OperationManager = this;
                    this.modal.hide();
                }


                html += '<div class="body"><p>' + Craft.t('The following items were found in the database that do not have a physical match.') +  '</p>';

				if (this.missingFolders.length > 0) {
					html += '<div class="report-part"><strong>' + Craft.t('Folders') + '</strong>';
					for (var i = 0; i < this.missingFolders.length; i++) {
						html += '<div><label><input type="checkbox" checked="checked" class="delete_folder" value="' + this.missingFolders[i].folder_id + '" /> ' + this.missingFolders[i].folder_name + '</label></div>';
					}
					html += '</div>'
				}

				if (typeof data.files != "undefined") {
					html += '<div class="report-part"><strong>' + Craft.t('Files') + '</strong>';
					for (var file_id in data.files) {
						html += '<div><label><input type="checkbox" checked="checked" class="delete_file" value="' + file_id + '" /> ' + data.files[file_id] + '</label></div>';
					}
					html += '</div>'
				}

				html += '</div>';
				html += '<footer class="footer"><ul class="right">';
				html += '<li><input type="button" class="btn cancel" value="' + Craft.t('Cancel') + '"></li>';
				html += '<li><input type="button" class="btn submit delete" value="' + Craft.t('Delete') + '"></li>';
				html += '</ul></footer>';

				this.$modalContainerDiv.empty().append(html);
				this.modal.setContainer(this.$modalContainerDiv);

				this.modal.show();
				this.modal.removeListener(Garnish.Modal.$shade, 'click');

				this.modal.addListener(this.modal.$container.find('.btn.cancel'), 'click', function () {
					this.OperationManager.releaseLock();
					this.hide();
				});

				this.modal.addListener(this.modal.$container.find('.btn.delete'), 'click', function () {

					var command = {};
					command.command = 'delete';
					command.folderIds = [];
					command.fileIds = [];

					this.$container.find('input.delete_folder:checked').each(function (){
						command.folderIds.push($(this).val());
					});

					this.$container.find('input.delete_file:checked').each(function (){
						command.fileIds.push($(this).val());
					});

					var sources = [];
					this.OperationManager.$sourceCheckboxes.filter(':checked').each(function () {
						sources.push($(this).val());
					});

					var params = {
						sessionId: this.sessionId,
						command: JSON.stringify(command),
						sources: sources.join(",")
					};

					$.post(Craft.getActionUrl('assetIndexing/finishIndex'), params, $.proxy(function(data) {
						this.hide();

						this.OperationManager.releaseLock();

					}, this));
				});
			} else {
				this.releaseLock();
			}


		}, this));

	},

	releaseLock: function () {
		this.$sourceMasterCheckbox.prop('disabled', false);
		if (this.$sourceMasterCheckbox.prop('checked')) {
			this.$sourceMasterCheckbox.prop('disabled', false);
		} else {
			this.$sourceCheckboxes.prop('disabled', false);
		}
	}


});

new Assets.IndexingManager();
