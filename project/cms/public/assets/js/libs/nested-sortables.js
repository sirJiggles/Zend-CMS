/*
 * jQuery UI Nested Sortable
 * v 1.4 / 30 dec 2011
 * http://mjsarfatti.com/code/nestedSortable
 *
 * Depends on:
 *	 jquery.ui.sortable.js 1.8+
 *
 * Copyright (c) 2010-2012 Manuele J Sarfatti
 * Licensed under the MIT License
 * http://www.opensource.org/licenses/mit-license.php
 */

function performMouseDrag(object, event){
    //Compute the helpers position
    object.position = object._generatePosition(event);
    object.positionAbs = object._convertPositionTo("absolute");

    if (!object.lastPositionAbs) {
            object.lastPositionAbs = object.positionAbs;
    }

    //Do scrolling
    if(object.options.scroll) {
            var o = object.options, scrolled = false;
            if(object.scrollParent[0] != document && object.scrollParent[0].tagName != 'HTML') {

                    if((object.overflowOffset.top + object.scrollParent[0].offsetHeight) - event.pageY < o.scrollSensitivity)
                            object.scrollParent[0].scrollTop = scrolled = object.scrollParent[0].scrollTop + o.scrollSpeed;
                    else if(event.pageY - object.overflowOffset.top < o.scrollSensitivity)
                            object.scrollParent[0].scrollTop = scrolled = object.scrollParent[0].scrollTop - o.scrollSpeed;

                    if((object.overflowOffset.left + object.scrollParent[0].offsetWidth) - event.pageX < o.scrollSensitivity)
                            object.scrollParent[0].scrollLeft = scrolled = object.scrollParent[0].scrollLeft + o.scrollSpeed;
                    else if(event.pageX - object.overflowOffset.left < o.scrollSensitivity)
                            object.scrollParent[0].scrollLeft = scrolled = object.scrollParent[0].scrollLeft - o.scrollSpeed;

            } else {

                    if(event.pageY - $(document).scrollTop() < o.scrollSensitivity)
                            scrolled = $(document).scrollTop($(document).scrollTop() - o.scrollSpeed);
                    else if($(window).height() - (event.pageY - $(document).scrollTop()) < o.scrollSensitivity)
                            scrolled = $(document).scrollTop($(document).scrollTop() + o.scrollSpeed);

                    if(event.pageX - $(document).scrollLeft() < o.scrollSensitivity)
                            scrolled = $(document).scrollLeft($(document).scrollLeft() - o.scrollSpeed);
                    else if($(window).width() - (event.pageX - $(document).scrollLeft()) < o.scrollSensitivity)
                            scrolled = $(document).scrollLeft($(document).scrollLeft() + o.scrollSpeed);

            }

            if(scrolled !== false && $.ui.ddmanager && !o.dropBehaviour)
                    $.ui.ddmanager.prepareOffsets(object, event);
    }

    //Regenerate the absolute position used for position checks
    object.positionAbs = object._convertPositionTo("absolute");

    //Set the helper position
    if(!object.options.axis || object.options.axis != "y") object.helper[0].style.left = object.position.left+'px';
    if(!object.options.axis || object.options.axis != "x") object.helper[0].style.top = object.position.top+'px';

    //Rearrange
    for (var i = object.items.length - 1; i >= 0; i--) {

            //Cache variables and intersection, continue if no intersection
            var item = object.items[i], itemElement = item.item[0], intersection = object._intersectsWithPointer(item);
            if (!intersection) continue;

            if(itemElement != object.currentItem[0] //cannot intersect with itself
                    &&	object.placeholder[intersection == 1 ? "next" : "prev"]()[0] != itemElement //no useless actions that have been done before
                    &&	!$.contains(object.placeholder[0], itemElement) //no action if the item moved is the parent of the item checked
                    && (object.options.type == 'semi-dynamic' ? !$.contains(object.element[0], itemElement) : true)
                    //&& itemElement.parentNode == object.placeholder[0].parentNode // only rearrange items within the same container
            ) {

                    $(itemElement).mouseenter();

                    object.direction = intersection == 1 ? "down" : "up";

                    if (object.options.tolerance == "pointer" || object._intersectsWithSides(item)) {
                            $(itemElement).mouseleave();
                            object._rearrange(event, item);
                    } else {
                            break;
                    }

                    // Clear emtpy ul's/ol's
                    object._clearEmpty(itemElement);

                    object._trigger("change", event, object._uiHash());
                    break;
            }
    }

    var parentItem = (object.placeholder[0].parentNode.parentNode &&
                                        $(object.placeholder[0].parentNode.parentNode).closest('.ui-sortable').length)
                                    ? $(object.placeholder[0].parentNode.parentNode)
                                    : null,
        level = object._getLevel(object.placeholder),
        childLevels = object._getChildLevels(object.helper),
        previousItem = object.placeholder[0].previousSibling ? $(object.placeholder[0].previousSibling) : null;

    if (previousItem != null) {
            while (previousItem[0].nodeName.toLowerCase() != 'li' || previousItem[0] == object.currentItem[0]) {
                    if (previousItem[0].previousSibling) {
                            previousItem = $(previousItem[0].previousSibling);
                    } else {
                            previousItem = null;
                            break;
                    }
            }
    }

    var newList = document.createElement(o.listType);

    newList.setAttribute("class", 'subList'); //For Most Browsers
    newList.setAttribute("className", 'subList'); //For IE; harmless to other browsers.

    object.beyondMaxLevels = 0;

    // If the item is moved to the left, send it to its parent level
    if (parentItem != null &&
                    (o.rtl && (object.positionAbs.left + object.helper.outerWidth() > parentItem.offset().left + parentItem.outerWidth()) ||
                    !o.rtl && (object.positionAbs.left < parentItem.offset().left))) {
            parentItem.after(object.placeholder[0]);
            object._clearEmpty(parentItem[0]);
            object._trigger("change", event, object._uiHash());
    }
    // If the item is below another one and is moved to the right, make it a children of it
    else if (previousItem != null &&
                            (o.rtl && (object.positionAbs.left + object.helper.outerWidth() < previousItem.offset().left + previousItem.outerWidth() - o.tabSize) ||
                            !o.rtl && (object.positionAbs.left > previousItem.offset().left + o.tabSize))) {
            object._isAllowed(previousItem, level, level+childLevels+1);
            if (!previousItem.children(o.listType).length) {
                    previousItem[0].appendChild(newList);
            }
            previousItem.children(o.listType)[0].appendChild(object.placeholder[0]);
            object._trigger("change", event, object._uiHash());
    }
    else {
            object._isAllowed(parentItem, level, level+childLevels);
    }

    //Post events to containers
    object._contactContainers(event);

    //Interconnect with droppables
    if($.ui.ddmanager) $.ui.ddmanager.drag(object, event);

    //Call callbacks
    object._trigger('sort', event, object._uiHash());

    object.lastPositionAbs = object.positionAbs;
}

function performMouseStop(object, event, noPropagation){
    // If the item is in a position not allowed, send it back
    if (object.beyondMaxLevels) {

            object.placeholder.removeClass(object.options.errorClass);

            if (object.domPosition.prev) {
                    $(object.domPosition.prev).after(object.placeholder);
            } else {
                    $(object.domPosition.parent).prepend(object.placeholder);
            }

            object._trigger("revert", event, object._uiHash());

    }

    // Clean last empty ul/ol
    for (var i = object.items.length - 1; i >= 0; i--) {
            var item = object.items[i].item[0];
            object._clearEmpty(item);
    }

    $.ui.sortable.prototype._mouseStop.apply(object, arguments);

}

(function($) {
        
        var proto =  $.ui.mouse.prototype,
        _mouseInit = proto._mouseInit;
        
	$.widget("mjs.nestedSortable", $.extend({}, $.ui.sortable.prototype, {

		options: {
			tabSize: 20,
			disableNesting: 'mjs-nestedSortable-no-nesting',
			errorClass: 'mjs-nestedSortable-error',
			listType: 'ol',
			maxLevels: 0,
			protectRoot: false,
			rootID: null,
			rtl: false,
			isAllowed: function(item, parent) { return true; }
		},
                
                

		_create: function() {
			this.element.data('sortable', this.element.data('nestedSortable'));

			if (!this.element.is(this.options.listType))
				throw new Error('nestedSortable: Please check the listType option is set to your actual list type');

			return $.ui.sortable.prototype._create.apply(this, arguments);
		},

		destroy: function() {
			this.element
				.removeData("nestedSortable")
				.unbind(".nestedSortable");
			return $.ui.sortable.prototype.destroy.apply(this, arguments);
		},

		_mouseDrag: function(event){
                        
                        performMouseDrag(this, event);
			return false;

		},

		_mouseStop: function(event, noPropagation) {
                        
                        performMouseStop(this, event, noPropagation);
			
		},
                
                _mouseInit: function() {
                    this.element
                    .bind( "touchstart." + this.widgetName, $.proxy( this, "_touchStart" ) );
                    _mouseInit.apply( this, arguments );
                },

                _touchStart: function( event ) {
                    if ( event.originalEvent.targetTouches.length != 1 ) {
                        return false;
                    }

                    this.element
                    .bind( "touchmove." + this.widgetName, $.proxy( this, "_touchMove" ) )
                    .bind( "touchend." + this.widgetName, $.proxy( this, "_touchEnd" ) );

                    this._modifyEvent( event );

                    $( document ).trigger($.Event("mouseup")); //reset mouseHandled flag in ui.mouse
                    this._mouseDown( event );

                    return false;           
                },

                _touchMove: function( event ) {
                    this._modifyEvent( event );
                    this._mouseDrag( event );   
                },

                _touchEnd: function( event ) {
                    this.element
                    .unbind( "touchmove." + this.widgetName )
                    .unbind( "touchend." + this.widgetName );
                    this._mouseStop( event ); 
                },

                _modifyEvent: function( event ) {
                    event.which = 1;
                    var target = event.originalEvent.targetTouches[0];
                    event.pageX = target.clientX;
                    event.pageY = target.clientY;
                },
                
		serialize: function(options) {

			var o = $.extend({}, this.options, options),
				items = this._getItemsAsjQuery(o && o.connected),
			    str = [];

			$(items).each(function() {
				var res = ($(o.item || this).attr(o.attribute || 'id') || '')
						.match(o.expression || (/(.+)[-=_](.+)/)),
				    pid = ($(o.item || this).parent(o.listType)
						.parent(o.items)
						.attr(o.attribute || 'id') || '')
						.match(o.expression || (/(.+)[-=_](.+)/));

				if (res) {
					str.push(((o.key || res[1]) + '[' + (o.key && o.expression ? res[1] : res[2]) + ']')
						+ '='
						+ (pid ? (o.key && o.expression ? pid[1] : pid[2]) : o.rootID));
				}
			});

			if(!str.length && o.key) {
				str.push(o.key + '=');
			}

			return str.join('&');

		},

		toHierarchy: function(options) {

			var o = $.extend({}, this.options, options),
				sDepth = o.startDepthCount || 0,
			    ret = [];

			$(this.element).children(o.items).each(function () {
				var level = _recursiveItems(this);
				ret.push(level);
			});

			return ret;

			function _recursiveItems(item) {
				var id = ($(item).attr(o.attribute || 'id') || '').match(o.expression || (/(.+)[-=_](.+)/));
				if (id) {
					var currentItem = {"id" : id[2]};
					if ($(item).children(o.listType).children(o.items).length > 0) {
						currentItem.children = [];
						$(item).children(o.listType).children(o.items).each(function() {
							var level = _recursiveItems(this);
							currentItem.children.push(level);
						});
					}
					return currentItem;
				}
			}
		},

		toArray: function(options) {

			var o = $.extend({}, this.options, options),
				sDepth = o.startDepthCount || 0,
			    ret = [],
			    left = 2;

			ret.push({
				"item_id": o.rootID,
				"parent_id": 'none',
				"depth": sDepth,
				"left": '1',
				"right": ($(o.items, this.element).length + 1) * 2
			});

			$(this.element).children(o.items).each(function () {
				left = _recursiveArray(this, sDepth + 1, left);
			});

			ret = ret.sort(function(a,b){ return (a.left - b.left); });

			return ret;

			function _recursiveArray(item, depth, left) {

				var right = left + 1,
				    id,
				    pid;

				if ($(item).children(o.listType).children(o.items).length > 0) {
					depth ++;
					$(item).children(o.listType).children(o.items).each(function () {
						right = _recursiveArray($(this), depth, right);
					});
					depth --;
				}

				id = ($(item).attr(o.attribute || 'id')).match(o.expression || (/(.+)[-=_](.+)/));

				if (depth === sDepth + 1) {
					pid = o.rootID;
				} else {
					var parentItem = ($(item).parent(o.listType)
											 .parent(o.items)
											 .attr(o.attribute || 'id'))
											 .match(o.expression || (/(.+)[-=_](.+)/));
					pid = parentItem[2];
				}

				if (id) {
						ret.push({"item_id": id[2], "parent_id": pid, "depth": depth, "left": left, "right": right});
				}

				left = right + 1;
				return left;
			}

		},

		_clearEmpty: function(item) {

			var emptyList = $(item).children(this.options.listType);
			if (emptyList.length && !emptyList.children().length) {
				emptyList.remove();
			}

		},

		_getLevel: function(item) {

			var level = 1;

			if (this.options.listType) {
				var list = item.closest(this.options.listType);
				while (!list.is('.ui-sortable')) {
					level++;
					list = list.parent().closest(this.options.listType);
				}
			}

			return level;
		},

		_getChildLevels: function(parent, depth) {
			var self = this,
			    o = this.options,
			    result = 0;
			depth = depth || 0;

			$(parent).children(o.listType).children(o.items).each(function (index, child) {
					result = Math.max(self._getChildLevels(child, depth + 1), result);
			});

			return depth ? result + 1 : result;
		},

		_isAllowed: function(parentItem, level, levels) {
			var o = this.options,
				isRoot = $(this.domPosition.parent).hasClass('ui-sortable') ? true : false;

			// Is the root protected?
			// Are we trying to nest under a no-nest?
			// Are we nesting too deep?
			if (!o.isAllowed(parentItem, this.placeholder) ||
				parentItem && parentItem.hasClass(o.disableNesting) ||
				o.protectRoot && (parentItem == null && !isRoot || isRoot && level > 1)) {
					this.placeholder.addClass(o.errorClass);
					if (o.maxLevels < levels && o.maxLevels != 0) {
						this.beyondMaxLevels = levels - o.maxLevels;
					} else {
						this.beyondMaxLevels = 1;
					}
			} else {
				if (o.maxLevels < levels && o.maxLevels != 0) {
					this.placeholder.addClass(o.errorClass);
					this.beyondMaxLevels = levels - o.maxLevels;
				} else {
					this.placeholder.removeClass(o.errorClass);
					this.beyondMaxLevels = 0;
				}
			}
		}

	}));

	$.mjs.nestedSortable.prototype.options = $.extend({}, $.ui.sortable.prototype.options, $.mjs.nestedSortable.prototype.options);
})(jQuery);