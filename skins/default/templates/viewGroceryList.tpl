{include file="skin:header.tpl"}
<script type="text/javascript">
{literal}

var groceryCount = 0;

InPlaceEditor = Class.create();
InPlaceEditor.prototype = {
	initialize : function(e, ip) {
		this.input = Builder.node("input", {type: 'text'});
		this.element = e;
		this.formField = ip;
		Element.hide(this.input);
		e.parentNode.appendChild(this.input);
		Event.observe(e, "click", this.handleClick.bindAsEventListener(this));
		Event.observe(this.input, "blur", this.handleBlur.bindAsEventListener(this));
	},
	handleClick : function(evt) {
		this.oldInnerHTML = this.element.innerHTML;
		Element.hide(this.element);
		this.input.value = this.oldInnerHTML;
		Element.show(this.input);
		Field.activate(this.input);
	},
	handleBlur : function(evt) {
		this.element.innerHTML = this.input.value;
		this.formField.value = this.input.value;
		Element.hide(this.input);
		Element.show(this.element);
	}
};

function handleLoad() {
	$A($('editGroceryList').getElementsByTagName('li')).each(function(li) {
		var f = li.innerHTML;
		li.innerHTML = '';
		addGrocery(f, li);
	});
	makeSortable();
	Event.observe('addGroceryButton', "click", handleAdd);
	Event.observe('groceryAdd', 'keypress', handleGroceryAdd);
	Field.activate('groceryAdd');
}

function handleGroceryAdd(evt) {
	if(isReturn(evt)) {
		Event.stop(evt);
		handleAdd();
	}
}

function makeSortable() {
	Sortable.create("editGroceryList", {
		onChange: function() {
			markChanged();
		}
	});
}
function addGrocery(desc, initLi) {
	var t = document.createTextNode(desc);
	var sp = Builder.node("span", {});
	var cb = Builder.node("input", {id: 'gic' + groceryCount,
									className: 'giccb',
								   type: 'checkbox'});
	var li = initLi;
	if(!initLi) {
		li = Builder.node("li", {className: 'groceryListItem'});
	}
	var input = Builder.node("input", {id: 'gi' + groceryCount, 
										type: 'hidden', 
										className: 'giinput',
										value: desc, 
										name: 'gi' + groceryCount});
	if(!initLi) {
		$('editGroceryList').appendChild(li);
	}
	li.appendChild(cb);
	sp.appendChild(t);
	li.appendChild(input);
	li.appendChild(sp);
	Event.observe('gic' + groceryCount, 'click', function(evt) {
					var e = cb;
					if(cb.checked) {
						Element.setStyle(e.parentNode, {textDecoration: 'line-through'});
						new Effect.Fade(e.parentNode, {afterFinish: 
							function(e) { 
								$('editGroceryList').removeChild(li);
								Sortable.destroy('editGroceryList');
								makeSortable();
								markChanged();
							 }});
					}

					});
	new InPlaceEditor(sp,input);	
	groceryCount++;
}
function handleAdd() {
	addGrocery($F('groceryAdd'), false);
	$('groceryAdd').value = '';
	Sortable.destroy('editGroceryList');
	makeSortable();
}
function markChanged() {
	var foo = $('editGroceryList').getElementsByTagName('input');
	var count = 0;
	$A(foo).each(function(inp) {
		if(inp.getAttribute("type") == 'hidden') {
			inp.setAttribute("name", 'gi' + count);
     		count++;
		} 
	});
	groceryCount = count;
}

Event.observe(this, "load", handleLoad);

{/literal}
</script>
<h2>{getMessage key=GroceryList}</h2>
<p id="groceryListViewInstructions">{getMessage key=groceryListViewInstructions}</p>
{form action=save controller=grocery}
<ul id="editGroceryList" class="nolist">
{section name=groceryitem loop=$groceryList}
<li>{$groceryList[groceryitem].description}</li>
{/section}
</ul>
<input name="description" type="text" id="groceryAdd"/>
<input type="{if $is_mobile}submit{else}button{/if}" name="add" id="addGroceryButton" value="{getMessage key=Add}"/>
<div class="buttonRow" style="width: 40em;">
<input type="submit" id="saveGroceryButton" value="{getMessage key=Save}" name="save"/>
<input type="submit" id="clearListButton" value="{getMessage key=Clear}" name="clear"/>
</div>
{/form}

{include file="skin:footer.tpl"}