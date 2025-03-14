/**
 * @file   modules/document/tpl/js/document_category.js
 * @author NAVER (developers@xpressengine.com)
 * @brief  document 모듈의 category tree javascript
 **/

function Tree(category_module_srl) {

	// clear tree;
	$('#menu > ul > li > ul').remove();

	if($("ul.simpleTree > li > a").size() == 0){
		$('<a href="#__category_info" class="add modalAnchor"><img src="' + default_url + 'common/js/plugins/ui.tree/images/iconAdd.gif" /></a>')
			.bind('before-open.mw', function(e){
				addNode(0,e);
			})
			.appendTo("ul.simpleTree > li")
			.xeModalWindow();
	}

	//ajax get data and transeform ul il
	exec_json('document.getDocumentCategoryTree', { module_srl: category_module_srl }, function(data) {
		var callback;
		callback = function(item) {
			var text = item.text;
			var node_srl = item.node_srl;
			var parent_srl = item.parent_srl;
			var color = item.color;
			var is_default = item.is_default;
			var url = item.url;

			// node
			var node = $('<li id="tree_'+node_srl+'"></li>');
			var title = $('<span></span>').text(text);
			if (color && color !='transparent') {
				title.css('color', color);
			}
			if (is_default == 'Y') {
				title.css('font-weight', 'bold');
			}
			node.append(title);

			// button
			$('<a href="#__category_info" class="add modalAnchor"><img src="' + default_url + 'common/js/plugins/ui.tree/images/iconAdd.gif" /></a>').bind("click",function(e){
				$("#tree_"+node_srl+" > span").click();
			})
			.bind('before-open.mw', function(e){
				addNode(node_srl,e);
			})
			.appendTo(node)
			.xeModalWindow();

			$('<a href="#__category_info" class="modify modalAnchor"><img src="' + default_url + 'common/js/plugins/ui.tree/images/iconModify.gif" /></a>').bind("click",function(e){
				$("#tree_"+node_srl+" > span").click();
			})
			.bind('before-open.mw', function(e){
				modifyNode(node_srl,e);
			})
			.appendTo(node)
			.xeModalWindow();

			$('<a href="#" class="delete"><img src="' + default_url + 'common/js/plugins/ui.tree/images/iconDel.gif" /></a>').bind("click",function(e){
				deleteNode(node_srl);
				return false;
			}).appendTo(node);

			// insert parent child
			if(parent_srl>0){
				if($('#tree_'+parent_srl+'>ul').length==0) $('#tree_'+parent_srl).append($('<ul>'));
				$('#tree_'+parent_srl+'> ul').append(node);
			}else{
				if($('#menu ul.simpleTree > li > ul').length==0) $("<ul>").appendTo('#menu ul.simpleTree > li');
				$('#menu ul.simpleTree > li > ul').append(node);
			}

			// look for children
			if (item.list) {
				item.list.forEach(function(child) {
					callback(child);
				});
			}
		};

		data.categories.forEach(function(item) {
			callback(item);
		});

		//button show hide
		$("#menu li").each(function(){
			if($(this).parents('ul').size() > max_menu_depth) $("a.add",this).hide();
			if($(">ul",this).size()>0) $(">a.delete",this).hide();
		});


		// draw tree
		simpleTreeCollection = $('.simpleTree').simpleTree({
			autoclose: false,
			afterClick:function(node){
				$('#category_info').html("");
				//alert("text-"+jQuery('span:first',node).text());
			},
			afterDblClick:function(node){
				//alert("text-"+jQuery('span:first',node).text());
			},
			afterMove:function(destination, source, pos){
				if(destination.size() == 0){
					Tree(category_module_srl);
					return;
				}
				var module_srl = $("#fo_category input[name=module_srl]").val();
				var parent_srl = destination.attr('id').replace(/.*_/g,'');
				var source_srl = source.attr('id').replace(/.*_/g,'');

				var target = source.prevAll("li:not([class^=line])");
				var target_srl = 0;
				if(target.length >0){
					target_srl = source.prevAll("li:not([class^=line])").get(0).id.replace(/.*_/g,'');
					parent_srl = 0;
				}

				$.exec_json("document.procDocumentMoveCategory",{ "module_srl":module_srl,"parent_srl":parent_srl,"target_srl":target_srl,"source_srl":source_srl},
				function(data){
					$('#category_info').html('');
				   if(data.error > 0) Tree(category_module_srl);
				});

			},

			// i want you !! made by sol
			beforeMovedToLine : function(destination, source, pos){
				return ($(destination).parents('ul').size() + jQuery('ul',source).size() <= max_menu_depth);
			},

			// i want you !! made by sol
			beforeMovedToFolder : function(destination, source, pos){
				return ($(destination).parents('ul').size() + jQuery('ul',source).size() <= max_menu_depth-1);
			},
			afterAjax:function()
			{
				//alert('Loaded');
			},
			animate:true
			,docToFolderConvert:true
		});

		// open all node
		nodeToggleAll();

	});
}

function clearValue(){
	var $ = jQuery;
	var $w = $('#__category_info');

	// clear value
	$w.find('input[type="text"], textarea').val('');
	$w.find('.x_inline.checked').removeClass('checked');
	$w.find('input[type="checkbox"]').prop('checked', false);
	$w.find('.lang_code').trigger('reload-multilingual');
	$w.find('.color-indicator').trigger('keyup');
	$w.find('.rx-spectrum').trigger('keyup');
}

function addNode(node,e){
	var $ = jQuery;
	var $w = $('#__category_info');

	clearValue();

	// set value
	$w.find('input[name="category_srl"]').val(0);
	$w.find('input[name="parent_srl"]').val(node);

	if(node){
		$('#__parent_category_info').show().next('.x_control-group').css('borderTop','1px dotted #ddd');
		$('#__parent_category_title').text($('#tree_' + node + ' > span').text());
	}else{
		$('#__parent_category_info').hide().next('.x_control-group').css('borderTop','0');
	}
}

function modifyNode(node,e){
	var $ = jQuery;
	var $w = $('#__category_info');

	clearValue();
	// set value
	$w.find('input[name="category_srl"]').val(node);

	var module_srl = $w.find('input[name="module_srl"]').val();

	$.exec_json('document.getDocumentCategoryTplInfo', {'module_srl': module_srl, 'category_srl': node}, function(data){
		if(!data || !data.category_info) return;

		if(data.error){
			alert(data.message);
			return;
		}

		$w.find('input[name="parent_srl"]').val(data.category_info.parent_srl);
		$w.find('input[name="category_title"]').val(data.category_info.title).trigger('reload-multilingual');
		$w.find('input[name="category_color"]').val(data.category_info.color).trigger('keyup').spectrum('set', data.category_info.color);
		$w.find('textarea[name="category_description"]').val(data.category_info.description).trigger('reload-multilingual');
		for(var i in data.category_info.group_srls){
			var group_srl = data.category_info.group_srls[i];
			$w.find('input[name="group_srls[]"][value="' + group_srl + '"]').prop('checked', true)
				.parent().addClass('checked');
		}
		if(data.category_info.expand == 'Y'){
			$w.find('input[name="expand"]').prop('checked', true).parent().addClass('checked');
		}
		if(data.category_info.is_default == 'Y'){
			$w.find('input[name="is_default"]').prop('checked', true).parent().addClass('checked');
		}
	});

	$('#__parent_category_info').hide().next('.x_control-group').css('borderTop','0');
}


function nodeToggleAll(){
	jQuery("[class*=close]", simpleTreeCollection[0]).each(function(){
		simpleTreeCollection[0].nodeToggle(this);
	});
}

function deleteNode(node){
	if(confirm(lang_confirm_delete)){
		jQuery('#category_info').html("");
		var params ={
				"category_srl":node
				,"parent_srl":0
				,"module_srl":jQuery("#fo_category [name=module_srl]").val()
				};

		exec_json('document.procDocumentDeleteCategory', params, function(data){
			if(data.error==0) Tree(category_module_srl);
		});
	}
}

/* 카테고리 아이템 입력후 */
function completeInsertCategory(ret_obj) {
	jQuery('#category_info').html("");
	Tree(category_module_srl);
}

function hideCategoryInfo() {
	jQuery('#category_info').html("");
}

/* 카테고리 목록 갱신 */
function doReloadTreeCategory(module_srl) {
	exec_json('document.procDocumentMakeXmlFile', { module_srl: module_srl }, completeInsertCategory);
}
